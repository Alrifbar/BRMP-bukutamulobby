<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use App\Models\Setting;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Jobs\AppendPengunjungCsv;

class PengunjungController extends Controller
{
    public function index()
    {
        $pengunjung = Pengunjung::orderBy('created_at', 'desc')->paginate(20);
        
        $fields = FormField::whereIn('name', ['keperluan_kategori', 'pendidikan'])->get()->keyBy('name');
        
        $labelKeperluan = $fields->has('keperluan_kategori') ? $fields['keperluan_kategori']->options : [
            1 => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            2 => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            3 => 'Layanan Perpustakaan',
            4 => 'Layanan Magang',
            5 => 'Layanan Informasi dan Dokumentasi',
            6 => 'Layanan Publikasi Warta',
            7 => 'Rapat/Pertemuan',
            8 => 'Lainnya',
        ];
        
        return view('pengunjung.index', compact('pengunjung', 'labelKeperluan'));
    }

    public function create()
    {
        $fields = FormField::where('is_visible', true)->orderBy('order')->get();
        return view('buku-tamu.create', compact('fields'));
    }

    public function store(Request $request)
    {
        $formFields = FormField::where('is_visible', true)->orderBy('order')->get();
        
        $rules = [];
        foreach ($formFields as $field) {
            $fieldRules = [$field->is_required ? 'required' : 'nullable'];
            
            switch ($field->type) {
                case 'email': $fieldRules[] = 'email'; break;
                case 'number': $fieldRules[] = 'numeric'; break;
                case 'tel': $fieldRules[] = 'string'; break;
                case 'file': $fieldRules[] = 'string'; break; // Handled as base64 string usually in this app
                default: $fieldRules[] = 'string'; break;
            }
            
            $rules[$field->name] = $fieldRules;
        }

        $validated = $request->validate($rules);

        // Core and metadata separation
        $coreColumns = [
            'nama', 'usia', 'no_hp', 'email', 'instansi', 'pendidikan', 
            'yang_ditemui', 'keperluan_kategori', 'keperluan_lainnya', 
            'tanggal_kunjungan', 'gender', 'selfie_photo'
        ];
        
        $data = [];
        $metadata = [];
        
        foreach ($validated as $key => $value) {
            if (in_array($key, $coreColumns)) {
                $data[$key] = $value;
            } else {
                $metadata[$key] = $value;
            }
        }
        
        $data['metadata'] = $metadata;

        // Process selfie photo specifically if it exists in form
        if (isset($data['selfie_photo']) && !empty($data['selfie_photo'])) {
            $photoData = $data['selfie_photo'];
            // Check if it's a base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                $decodedData = substr($photoData, strpos($photoData, ',') + 1);
                $decodedData = base64_decode($decodedData);
                
                if ($decodedData !== false) {
                    // Kompres foto ke 500KB
                    $image = @imagecreatefromstring($decodedData);
                    if ($image) {
                        ob_start();
                        $quality = 80;
                        imagejpeg($image, null, $quality);
                        $compressedData = ob_get_clean();
                        while (strlen($compressedData) > 512000 && $quality > 10) {
                            $quality -= 10;
                            ob_start();
                            imagejpeg($image, null, $quality);
                            $compressedData = ob_get_clean();
                        }
                        $decodedData = $compressedData;
                        imagedestroy($image);
                    }

                    $filename = 'selfie_' . time() . '_' . Str::random(8) . '.jpg';
                    $path = 'selfies/' . $filename;
                    
                    Storage::disk('public')->put($path, $decodedData);

                    $driveDir = Setting::getValue('drive_sync_dir') ?: env('DRIVE_SYNC_DIR');
                    if ($driveDir && is_dir($driveDir)) {
                        $driveSelfies = rtrim($driveDir, '\\/') . DIRECTORY_SEPARATOR . 'selfies';
                        if (!is_dir($driveSelfies)) @mkdir($driveSelfies, 0775, true);
                        @file_put_contents($driveSelfies . DIRECTORY_SEPARATOR . $filename, $decodedData);
                    }
                    $data['selfie_photo'] = $filename;
                }
            }
        }

        // Handle specific logic for 'Lainnya' if these core fields exist
        if (isset($data['keperluan_kategori']) && (int) $data['keperluan_kategori'] !== 8) {
            $data['keperluan_lainnya'] = null;
        }

        // Generate unique token for editing
        $data['unique_token'] = Str::random(32);
        $data['edit_attempts'] = 3;
        $data['tanggal_kunjungan'] = now();

        $pengunjung = Pengunjung::create($data);

        AppendPengunjungCsv::dispatchSync($pengunjung->id);

        return redirect()->route('buku-tamu.create')
            ->with('success', 'Data Anda sudah disimpan. Terima kasih telah mengisi formulir!');
    }

    public function terimaKasih()
    {
        return view('buku-tamu.terima-kasih');
    }

    public function edit($token)
    {
        $pengunjung = Pengunjung::where('unique_token', $token)->firstOrFail();
        
        if ($pengunjung->edit_attempts <= 0) {
            return redirect()->route('buku-tamu.create')
                ->with('error', 'Link edit tidak valid atau sudah mencapai batas maksimal edit.');
        }

        return view('buku-tamu.edit', compact('pengunjung'));
    }

    public function update(Request $request, $token)
    {
        $pengunjung = Pengunjung::where('unique_token', $token)->firstOrFail();
        
        if ($pengunjung->edit_attempts <= 0) {
            return redirect()->route('buku-tamu.create')
                ->with('error', 'Link edit tidak valid atau sudah mencapai batas maksimal edit.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'usia' => ['required', 'integer', 'min:1'],
            'gender' => ['required', 'string', 'in:L,P'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'instansi' => ['required', 'string', 'max:150'],
            'pendidikan' => ['required', 'string', 'max:50'],
            'pendidikan_lainnya' => ['nullable', 'required_if:pendidikan,Lainnya', 'string', 'max:100'],
            'yang_ditemui' => ['required', 'string', 'max:100'],
            'keperluan_kategori' => ['required', 'integer', 'between:1,8'],
            'keperluan_lainnya' => ['nullable', 'required_if:keperluan_kategori,8', 'string', 'max:255'],
        ]);

        if ((int) $validated['keperluan_kategori'] !== 8) {
            $validated['keperluan_lainnya'] = null;
        }

        if ($validated['pendidikan'] !== 'Lainnya') {
            $validated['pendidikan_lainnya'] = null;
        }

        $pengunjung->update($validated);
        $pengunjung->edit_attempts = $pengunjung->edit_attempts - 1;
        $pengunjung->save();

        return redirect()->route('buku-tamu.terima-kasih', ['token' => $token])
            ->with('success', 'Data berhasil diperbarui. Sisa edit: ' . $pengunjung->edit_attempts);
    }
}

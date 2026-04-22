<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengunjung;
use App\Models\FormField;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\ExportService;

class RekapController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function exportExcel($year, $month = null)
    {
        return $this->exportService->exportExcel($year, $month);
    }

    public function exportPdf($year, $month = null)
    {
        return $this->exportService->exportPdf($year, $month);
    }

    public function editPengunjung(Pengunjung $pengunjung, Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        return view('admin.pengunjung-edit', compact('pengunjung', 'year', 'month'));
    }

    public function updatePengunjung(Request $request, Pengunjung $pengunjung)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'usia' => 'required|integer|min:0|max:100',
            'gender' => 'required|in:L,P',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'instansi' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'yang_ditemui' => 'required|string|max:255',
            'keperluan_kategori' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $pengunjung->update($data);

        $redirectYear = $request->input('redirect_year');
        $redirectMonth = $request->input('redirect_month');

        return redirect()->route('admin.rekap-pengunjung', [
            'year' => $redirectYear,
            'month' => $redirectMonth > 0 ? $redirectMonth : null
        ])->with('success', 'Data pengunjung berhasil diperbarui');
    }

    public function updateGender(Request $request, Pengunjung $pengunjung)
    {
        $request->validate([
            'gender' => 'required|in:L,P'
        ]);

        $pengunjung->update(['gender' => $request->gender]);

        return response()->json([
            'success' => true,
            'message' => 'Gender berhasil diperbarui'
        ]);
    }

    public function getGenderData()
    {
        $data = [
            'Laki-laki' => Pengunjung::where('gender', 'L')->count(),
            'Perempuan' => Pengunjung::where('gender', 'P')->count(),
            'Tidak Diketahui' => Pengunjung::whereNull('gender')->orWhere('gender', '')->count()
        ];

        return response()->json($data);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = [
            'Nama', 'Usia', 'Gender (L/P)', 'No HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 
            'Kategori Keperluan (1-8)', 'Keperluan Lainnya', 'Tanggal Kunjungan (dd/mm/yyyy)', 'Pendidikan Lainnya'
        ];
        $sheet->fromArray($headers, NULL, 'A1');
        
        $example = [
            'Budi Santoso', 25, 'L', '081234567890', 'budi@example.com', 'Umum', 'S1', 'Kepala Dinas', 
            1, '', now()->format('d/m/Y'), ''
        ];
        $sheet->fromArray($example, NULL, 'A2');
        
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'template_import_pengunjung.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        
        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240',
            'import_year' => 'required|integer|min:2020|max:2035',
            'import_month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $file = $request->file('excel_file');
            $importYear = (int) $request->input('import_year');
            $importMonth = (int) $request->input('import_month');
            $updateExisting = $request->has('update_existing');

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $headers = array_map('strtolower', array_map('trim', $rows[0] ?? []));
            array_shift($rows);

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue;

                try {
                    $columnMap = $this->mapColumns($headers);
                    $data = $this->extractDataFromRow($row, $columnMap, $importYear, $importMonth);

                    if (empty($data['nama']) || empty($data['tanggal_kunjungan'])) {
                        $errors[] = "Baris " . ($index + 2) . ": Nama dan tanggal kunjungan wajib diisi";
                        $skippedCount++;
                        continue;
                    }

                    $existing = Pengunjung::where('nama', $data['nama'])
                        ->whereDate('tanggal_kunjungan', $data['tanggal_kunjungan'])
                        ->first();

                    if ($existing) {
                        if ($updateExisting) {
                            $existing->update($data);
                            $updatedCount++;
                        } else {
                            $skippedCount++;
                        }
                        continue;
                    }

                    Pengunjung::create($data);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $skippedCount++;
                }
            }

            $message = "Import selesai! Berhasil: {$importedCount}, Diperbarui: {$updatedCount}, Dilewati: {$skippedCount}";
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }

    private function mapColumns($headers)
    {
        $map = [
            'nama' => -1, 'usia' => -1, 'gender' => -1, 'no. hp' => -1, 'email' => -1, 
            'instansi' => -1, 'pendidikan' => -1, 'yang ditemui' => -1, 'keperluan' => -1, 'tanggal' => -1, 'waktu' => -1
        ];

        foreach ($headers as $index => $header) {
            if (strpos($header, 'nama') !== false) $map['nama'] = $index;
            elseif (strpos($header, 'usia') !== false) $map['usia'] = $index;
            elseif (strpos($header, 'gender') !== false || strpos($header, 'jenis kelamin') !== false) $map['gender'] = $index;
            elseif (strpos($header, 'hp') !== false || strpos($header, 'phone') !== false) $map['no. hp'] = $index;
            elseif (strpos($header, 'email') !== false) $map['email'] = $index;
            elseif (strpos($header, 'instansi') !== false) $map['instansi'] = $index;
            elseif (strpos($header, 'pendidikan') !== false) $map['pendidikan'] = $index;
            elseif (strpos($header, 'ditemui') !== false) $map['yang ditemui'] = $index;
            elseif (strpos($header, 'keperluan') !== false) $map['keperluan'] = $index;
            elseif (strpos($header, 'tanggal') !== false || strpos($header, 'tgl') !== false) $map['tanggal'] = $index;
            elseif (strpos($header, 'waktu') !== false) $map['waktu'] = $index;
        }
        return $map;
    }

    private function extractDataFromRow($row, $map, $importYear, $importMonth)
    {
        $tanggalValue = trim($row[$map['tanggal']] ?? '');
        $waktuValue = trim($row[$map['waktu']] ?? '');
        $dateTimeString = $tanggalValue . ($waktuValue ? ' ' . $waktuValue : '');
        
        $tanggal = $this->parseDate($dateTimeString, $importYear, $importMonth);
        
        $genderValue = strtoupper(trim($row[$map['gender']] ?? ''));
        $gender = ($genderValue === 'L' || strpos($genderValue, 'LAKI') !== false) ? 'L' : 
                 (($genderValue === 'P' || strpos($genderValue, 'PEREMPUAN') !== false) ? 'P' : null);

        $keperluanValue = trim($row[$map['keperluan']] ?? '');
        $keperluan_kategori = 8;
        $keperluan_lainnya = $keperluanValue;
        
        if (is_numeric($keperluanValue) && $keperluanValue >= 1 && $keperluanValue <= 8) {
            $keperluan_kategori = (int)$keperluanValue;
            $keperluan_lainnya = '';
        }

        return [
            'nama' => trim($row[$map['nama']] ?? ''),
            'usia' => (int)($row[$map['usia']] ?? 0),
            'gender' => $gender,
            'no_hp' => trim($row[$map['no. hp']] ?? ''),
            'email' => trim($row[$map['email']] ?? ''),
            'instansi' => trim($row[$map['instansi']] ?? ''),
            'pendidikan' => trim($row[$map['pendidikan']] ?? ''),
            'yang_ditemui' => trim($row[$map['yang ditemui']] ?? ''),
            'keperluan_kategori' => $keperluan_kategori,
            'keperluan_lainnya' => $keperluan_lainnya,
            'tanggal_kunjungan' => $tanggal,
        ];
    }

    private function parseDate($dateStr, $year, $month)
    {
        if (empty($dateStr)) return Carbon::create($year, $month, 1)->format('Y-m-d');
        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::create($year, $month, 1)->format('Y-m-d');
        }
    }

    public function deleteData(Request $request)
    {
        $year = $request->input('delete_year');
        $month = $request->input('delete_month');
        
        $query = Pengunjung::whereYear('tanggal_kunjungan', $year);
        if ($month) {
            $query->whereMonth('tanggal_kunjungan', $month);
        }
        
        $count = $query->count();
        $query->delete();

        return redirect()->back()->with('success', "{$count} data berhasil dihapus.");
    }

    public function bulkExportPengunjung(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
            
            $pengunjung = Pengunjung::whereIn('id', $ids)->orderBy('tanggal_kunjungan', 'desc')->get();
            
            // Ambil field kustom yang aktif
            $customFields = FormField::where('is_core', false)
                ->where('is_visible', true)
                ->orderBy('order')
                ->get();

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'Usia', 'Gender', 'No. HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 'Keperluan'];
            
            // Tambahkan header kustom
            foreach ($customFields as $cf) {
                $headers[] = $cf->label;
            }

            $sheet->fromArray($headers, NULL, 'A1');
            
            // Hitung kolom terakhir
            $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle('A1:' . $lastColumnLetter . '1')->getFont()->setBold(true);
            
            $row = 2;
            foreach ($pengunjung as $index => $p) {
                $keperluanText = $p->keperluan_kategori ? ($this->getLabelKeperluan($p->keperluan_kategori) ?? 'Lainnya') : ($p->keperluan_lainnya ?: '-');
                
                $rowData = [
                    $index + 1,
                    Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y'),
                    Carbon::parse($p->tanggal_kunjungan)->format('H:i'),
                    $p->nama, $p->usia ?? '-',
                    $p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : '-'),
                    $p->no_hp ?? '-', $p->email ?? '-', $p->instansi ?? '-', $p->pendidikan ?? '-', $p->yang_ditemui ?? '-', $keperluanText
                ];

                // Tambahkan data kustom
                foreach ($customFields as $cf) {
                    $val = $p->metadata[$cf->name] ?? '-';
                    $rowData[] = is_array($val) ? implode(', ', $val) : $val;
                }

                $sheet->fromArray($rowData, NULL, 'A' . $row);
                $row++;
            }
            
            // Auto-size columns
            $lastColIndex = count($headers);
            for ($i = 1; $i <= $lastColIndex; $i++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }
            
            $filename = 'Data_Terpilih_' . date('Ymd_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    private function getLabelKeperluan($kategori)
    {
        $labels = [
            '1' => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            '2' => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            '3' => 'Layanan Perpustakaan',
            '4' => 'Layanan Magang',
            '5' => 'Layanan Informasi dan Dokumentasi',
            '6' => 'Layanan Publikasi Warta',
            '7' => 'Rapat/Pertemuan',
            '8' => 'Lainnya'
        ];
        return $labels[$kategori] ?? $kategori;
    }
    public function rekapPengunjung(Request $request)
    {
        $year = (int) $request->query('year', (int) now()->format('Y'));
        $selectedMonth = (int) $request->query('month', 0);
        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = null;
        }

        $countsByMonth = Pengunjung::query()
            ->selectRaw('MONTH(tanggal_kunjungan) as month, COUNT(*) as total')
            ->whereYear('tanggal_kunjungan', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        $rekap = [];
        $totalSetahun = 0;
        for ($m = 1; $m <= 12; $m++) {
            $jumlah = (int) ($countsByMonth[$m] ?? 0);
            $rekap[$m] = $jumlah;
            $totalSetahun += $jumlah;
        }

        $minYear = Pengunjung::query()->min('tanggal_kunjungan');
        $maxYear = Pengunjung::query()->max('tanggal_kunjungan');

        $minYear = $minYear ? (int) Carbon::parse($minYear)->format('Y') : $year;
        $maxYear = $maxYear ? (int) Carbon::parse($maxYear)->format('Y') : $year;
        
        $futureYears = 10;
        $maxYear = max($maxYear, (int) now()->format('Y') + $futureYears, $year);

        $years = range(min($minYear, $year), $maxYear);
        rsort($years);

        $pengunjungBulan = null;
        if ($selectedMonth) {
            $pengunjungBulan = Pengunjung::query()
                ->whereYear('tanggal_kunjungan', $year)
                ->whereMonth('tanggal_kunjungan', $selectedMonth)
                ->orderBy('tanggal_kunjungan')
                ->paginate(50);
        }

        $labelKeperluan = [
            '1' => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            '2' => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            '3' => 'Layanan Perpustakaan',
            '4' => 'Layanan Magang',
            '5' => 'Layanan Informasi dan Dokumentasi',
            '6' => 'Layanan Publikasi Warta',
            '7' => 'Rapat/Pertemuan',
            '8' => 'Lainnya'
        ];

        // Ambil field tambahan yang aktif
        $customFields = FormField::where('is_core', false)
            ->where('is_visible', true)
            ->orderBy('order')
            ->get();

        return view('admin.rekap-pengunjung', [
            'year' => $year,
            'years' => $years,
            'rekap' => $rekap,
            'monthlyData' => $rekap,
            'totalSetahun' => $totalSetahun,
            'selectedMonth' => $selectedMonth,
            'pengunjungBulan' => $pengunjungBulan,
            'labelKeperluan' => $labelKeperluan,
            'customFields' => $customFields,
        ]);
    }

    public function getMonthDetailsApi(Request $request)
    {
        $year = (int) $request->query('year', (int) now()->format('Y'));
        $month = (int) $request->query('month', 0);
        
        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid month'], 400);
        }

        $pengunjung = Pengunjung::query()
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->orderBy('tanggal_kunjungan')
            ->get(['id', 'nama', 'usia', 'gender', 'no_hp', 'instansi', 'keperluan_kategori', 'keperluan_lainnya', 'tanggal_kunjungan']);

        return response()->json($pengunjung);
    }

    public function indexPengunjung(Request $request)
    {
        $year = (int) $request->query('year', (int) now()->format('Y'));
        $month = (int) $request->query('month', 0);
        
        $years = Pengunjung::selectRaw('YEAR(tanggal_kunjungan) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [(int) now()->format('Y')];
        }
        
        $query = Pengunjung::query()->whereYear('tanggal_kunjungan', $year);
        
        if ($month >= 1 && $month <= 12) {
            $query->whereMonth('tanggal_kunjungan', $month);
        }
        
        $pengunjung = $query->orderByDesc('tanggal_kunjungan')->paginate(50);

        return view('admin.pengunjung.index', compact('pengunjung', 'years', 'year', 'month'));
    }

    public function destroyPengunjung(Request $request, Pengunjung $pengunjung)
    {
        $pengunjung->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data tamu dipindahkan ke Sampah.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Data tamu dipindahkan ke Sampah. Dapat dipulihkan di Pengaturan dalam 30 hari.');
    }

    public function bulkDeletePengunjung(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih.'
            ], 400);
        }

        Pengunjung::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' data tamu berhasil dipindahkan ke Sampah.'
        ]);
    }
}

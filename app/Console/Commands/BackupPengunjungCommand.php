<?php

namespace App\Console\Commands;

use App\Models\Pengunjung;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BackupPengunjungCommand extends Command
{
    protected $signature = 'backup:pengunjung {date?} {--format=xlsx}';
    protected $description = 'Export data pengunjung ke file backup untuk tanggal tertentu';

    public function handle(): int
    {
        $dateInput = $this->argument('date');
        $format = strtolower((string) $this->option('format'));
        $date = $dateInput ? Carbon::parse($dateInput) : now();

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $data = Pengunjung::whereBetween('tanggal_kunjungan', [$start, $end])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get();

        $dir = rtrim(env('AUTO_BACKUP_DIR', 'backups/pengunjung'), '/');
        $folder = $dir . '/exports/' . $date->format('Y/m');
        $storagePath = storage_path('app/' . $folder);
        if (!is_dir($storagePath)) {
            @mkdir($storagePath, 0775, true);
        }

        if ($format === 'csv') {
            $relative = $folder . '/Rekapan_Pengunjung_' . $date->format('Y-m-d') . '.csv';
            $file = $storagePath . '/Rekapan_Pengunjung_' . $date->format('Y-m-d') . '.csv';
            $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'Usia', 'Gender', 'No. HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 'Keperluan', 'Foto Selfie'];
            $fp = @fopen($file, 'w');
            if ($fp !== false) {
                fputcsv($fp, $headers);
                foreach ($data as $index => $p) {
                    $tanggal = Carbon::parse($p->tanggal_kunjungan);
                    fputcsv($fp, [
                        $index + 1,
                        $tanggal->format('d/m/Y'),
                        $tanggal->format('H:i'),
                        $p->nama,
                        $p->usia ?? '',
                        $p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : ''),
                        $p->no_hp ?? '',
                        $p->email ?? '',
                        $p->instansi ?? '',
                        $p->pendidikan ?? '',
                        $p->yang_ditemui ?? '',
                        ((int)($p->keperluan_kategori) === 8) ? ($p->keperluan_lainnya ?: 'Lainnya') : $this->labelKeperluan($p->keperluan_kategori),
                        $p->selfie_photo ? 'Ada' : 'Tidak ada',
                    ]);
                }
                fclose($fp);
                $this->info($file);
                return self::SUCCESS;
            }
            // Fallback: tulis ke memory lalu simpan via Storage agar lebih toleran lingkungan Windows
            $temp = fopen('php://temp', 'r+');
            fputcsv($temp, $headers);
            foreach ($data as $index => $p) {
                $tanggal = Carbon::parse($p->tanggal_kunjungan);
                fputcsv($temp, [
                    $index + 1,
                    $tanggal->format('d/m/Y'),
                    $tanggal->format('H:i'),
                    $p->nama,
                    $p->usia ?? '',
                    $p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : ''),
                    $p->no_hp ?? '',
                    $p->email ?? '',
                    $p->instansi ?? '',
                    $p->pendidikan ?? '',
                    $p->yang_ditemui ?? '',
                    ((int)($p->keperluan_kategori) === 8) ? ($p->keperluan_lainnya ?: 'Lainnya') : $this->labelKeperluan($p->keperluan_kategori),
                    $p->selfie_photo ? 'Ada' : 'Tidak ada',
                ]);
            }
            rewind($temp);
            $content = stream_get_contents($temp);
            fclose($temp);
            // gunakan Storage agar path relatif dibuat otomatis
            \Illuminate\Support\Facades\Storage::disk('local')->put($relative, $content);
            $this->info($file);
            return self::SUCCESS;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'Usia', 'Gender', 'No. HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 'Keperluan', 'Foto Selfie'];
        $sheet->fromArray($headers, null, 'A1');
        $row = 2;
        foreach ($data as $index => $p) {
            $tanggal = Carbon::parse($p->tanggal_kunjungan);
            $sheet->fromArray([
                $index + 1,
                $tanggal->format('d/m/Y'),
                $tanggal->format('H:i'),
                $p->nama,
                $p->usia ?? '',
                $p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : ''),
                $p->no_hp ?? '',
                $p->email ?? '',
                $p->instansi ?? '',
                $p->pendidikan ?? '',
                $p->yang_ditemui ?? '',
                ((int)($p->keperluan_kategori) === 8) ? ($p->keperluan_lainnya ?: 'Lainnya') : $this->labelKeperluan($p->keperluan_kategori),
                $p->selfie_photo ? 'Ada' : 'Tidak ada',
            ], null, 'A' . $row);
            $row++;
        }
        $relativeX = $folder . '/Rekapan_Pengunjung_' . $date->format('Y-m-d') . '.xlsx';
        $file = $storagePath . '/Rekapan_Pengunjung_' . $date->format('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        try {
            $writer->save($file);
            $this->info($file);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            // Fallback: tulis ke output buffer lalu simpan via Storage
            try {
                ob_start();
                $writer->save('php://output');
                $excelData = ob_get_clean();
                \Illuminate\Support\Facades\Storage::disk('local')->put($relativeX, $excelData);
                $this->info($file);
                return self::SUCCESS;
            } catch (\Throwable $e2) {
                $this->error('Gagal menyimpan XLSX: ' . $e2->getMessage());
                return self::FAILURE;
            }
        }
    }

    protected function labelKeperluan($kategori): string
    {
        $map = [
            1 => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            2 => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            3 => 'Layanan Perpustakaan',
            4 => 'Layanan Magang',
            5 => 'Layanan Informasi dan Dokumentasi',
            6 => 'Layanan Publikasi Warta',
            7 => 'Rapat/Pertemuan',
            8 => 'Lainnya',
        ];
        return $map[(int)$kategori] ?? (string)$kategori;
    }
}

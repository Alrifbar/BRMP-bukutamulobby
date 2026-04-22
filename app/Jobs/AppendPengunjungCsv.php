<?php

namespace App\Jobs;

use App\Models\Pengunjung;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AppendPengunjungCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $pengunjungId;

    public function __construct(int $pengunjungId)
    {
        $this->pengunjungId = $pengunjungId;
    }

    public function handle(): void
    {
        $p = Pengunjung::find($this->pengunjungId);
        if (!$p) {
            return;
        }

        $tanggal = $p->tanggal_kunjungan ? Carbon::parse($p->tanggal_kunjungan) : now();
        $dir = rtrim(env('AUTO_BACKUP_DIR', 'backups/pengunjung'), '/');
        $folder = $dir . '/' . $tanggal->format('Y/m');
        $filename = 'pengunjung_' . $tanggal->format('Y-m-d') . '.csv';
        $path = storage_path('app/' . $folder);

        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }

        $fullpath = $path . '/' . $filename;
        $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'Usia', 'Gender', 'No. HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 'Keperluan', 'Foto Selfie'];
        if (!file_exists($fullpath)) {
            $this->appendLine($fullpath, $headers);
        }

        $row = [
            '',
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
        ];

        $this->appendLine($fullpath, $row);
    }

    protected function appendLine(string $filepath, array $columns): void
    {
        $fp = fopen($filepath, file_exists($filepath) ? 'a' : 'w');
        if ($fp === false) {
            return;
        }
        flock($fp, LOCK_EX);
        fputcsv($fp, $columns);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
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


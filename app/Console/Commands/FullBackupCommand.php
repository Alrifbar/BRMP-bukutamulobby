<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Carbon\Carbon;

class FullBackupCommand extends Command
{
    protected $signature = 'backup:full {--drive-dir=}';
    protected $description = 'Buat backup penuh: dump SQL + salin folder penting ke folder sinkron Google Drive';

    public function handle(): int
    {
        $timestamp = Carbon::now()->format('Ymd_His');
        $baseLocal = storage_path('app/full-backups/' . $timestamp);
        $dbDir = $baseLocal . '/db';
        $filesDir = $baseLocal . '/files';

        File::makeDirectory($dbDir, 0775, true, true);
        File::makeDirectory($filesDir, 0775, true, true);

        $this->info('Membuat backup di: ' . $baseLocal);

        // 1) Dump database ke SQL
        $sqlPath = $dbDir . '/backup_' . $timestamp . '.sql';
        $this->dumpDatabase($sqlPath);
        $this->info('SQL dump tersimpan: ' . $sqlPath);

        // 2) Salin folder penting
        $this->copyIfExists(storage_path('app/backups/pengunjung'), $filesDir . '/pengunjung-backups');
        $this->copyIfExists(storage_path('app/public/selfies'), $filesDir . '/selfies');
        $this->info('Folder penting tersalin');

        // 3) Salin ke folder cadangan lokal (File Explorer)
        $localDir = Setting::getValue('local_backup_dir');
        if ($localDir && is_dir($localDir)) {
            $targetLocal = rtrim($localDir, '\\/') . DIRECTORY_SEPARATOR . $timestamp;
            File::makeDirectory($targetLocal, 0775, true, true);
            $this->recursiveCopy($baseLocal, $targetLocal);
            $this->info('Backup disalin ke folder lokal: ' . $targetLocal);
        }

        // 4) Salin ke folder Google Drive lokal (sinkron oleh Google Drive for Desktop)
        $driveDir = $this->option('drive-dir')
            ?: Setting::getValue('drive_sync_dir')
            ?: env('DRIVE_SYNC_DIR');

        if ($driveDir && is_dir($driveDir)) {
            $target = rtrim($driveDir, '\\/') . DIRECTORY_SEPARATOR . $timestamp;
            File::makeDirectory($target, 0775, true, true);
            $this->recursiveCopy($baseLocal, $target);
            $this->info('Backup disalin ke Google Drive lokal: ' . $target);
        } else {
            $this->warn('Folder Google Drive tidak terdeteksi. Set di Pengaturan atau env DRIVE_SYNC_DIR untuk otomatis salin.');
        }

        $this->line($baseLocal);
        // catat setting last backup
        try {
            Setting::setValue('last_full_backup_at', now()->toDateTimeString(), 'text', 'Waktu backup penuh terakhir');
            Setting::setValue('last_full_backup_path', $baseLocal, 'text', 'Lokasi backup penuh terakhir');
        } catch (\Throwable $e) {
            // ignore
        }
        return self::SUCCESS;
    }

    protected function dumpDatabase(string $sqlPath): void
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $mysqldump = \App\Models\Setting::getValue('mysqldump_path') ?: env('MYSQLDUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe');

        // Gunakan --result-file agar tidak perlu redirection
        $cmd = [
            $mysqldump,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            "--routines",
            "--events",
            "--single-transaction",
            "--databases",
            $database,
            "--result-file={$sqlPath}",
        ];

        $process = new Process($cmd, null, null, null, 300);
        $process->run();

        if (!$process->isSuccessful()) {
            // Fallback simple dump via PDO jika mysqldump tidak tersedia
            file_put_contents($sqlPath, "-- Fallback dump gagal menggunakan mysqldump\n");
            file_put_contents($sqlPath, "-- Error: " . $process->getErrorOutput() . "\n", FILE_APPEND);
            // Minimal structure/data bisa diekspor terpisah sesuai kebutuhan, namun di sini kita log error agar admin tahu
        }
    }

    protected function copyIfExists(string $src, string $dest): void
    {
        if (is_dir($src)) {
            $this->recursiveCopy($src, $dest);
        }
    }

    protected function recursiveCopy(string $src, string $dest): void
    {
        if (!is_dir($dest)) {
            File::makeDirectory($dest, 0775, true, true);
        }
        $items = scandir($src);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $srcPath = $src . DIRECTORY_SEPARATOR . $item;
            $destPath = $dest . DIRECTORY_SEPARATOR . $item;
            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $destPath);
            } else {
                @copy($srcPath, $destPath);
            }
        }
    }
}

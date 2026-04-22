<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SqlBackupCommand extends Command
{
    protected $signature = 'backup:sql {--dest=}';
    protected $description = 'Dump database ke file .sql di folder atau ke path file tetap';

    public function handle(): int
    {
        $dest = $this->option('dest') ?: Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql');
        $isFile = is_string($dest) && preg_match('/\\.sql$/i', $dest);
        if ($isFile) {
            $dir = dirname($dest);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $sqlPath = $dest; // tulis ke file tetap, akan ditimpa tiap kali
        } else {
            $destDir = $dest;
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0775, true);
            }
            $timestamp = now()->format('Ymd_His');
            $sqlPath = rtrim($destDir, '\\/').DIRECTORY_SEPARATOR."backup_{$timestamp}.sql";
        }

        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $mysqldump = Setting::getValue('mysqldump_path') ?: env('MYSQLDUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe');
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

        try {
            $process = new Process($cmd, null, null, null, 300);
            $process->run();
        } catch (\Throwable $e) {
            file_put_contents($sqlPath, "-- Gagal menjalankan mysqldump: ".$e->getMessage());
            $this->error('mysqldump exception: '.$e->getMessage());
        }

        if (isset($process) && !$process->isSuccessful()) {
            $error = trim($process->getErrorOutput()."\n".$process->getOutput());
            if ($error) {
                @file_put_contents($sqlPath, "-- Gagal menjalankan mysqldump\n-- ".$error);
            }
            $this->error('mysqldump gagal dieksekusi');
        }

        if (!file_exists($sqlPath) || filesize($sqlPath) === 0) {
            @is_file($sqlPath) && @filesize($sqlPath) === 0 ? @unlink($sqlPath) : null;
            $this->error('Gagal membuat file SQL');
            return self::FAILURE;
        }

        $this->info('SQL dump tersimpan: '.$sqlPath);
        return self::SUCCESS;
    }
}

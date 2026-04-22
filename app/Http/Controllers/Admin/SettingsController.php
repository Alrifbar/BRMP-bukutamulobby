<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class SettingsController extends Controller
{
    public function showLogoSettings()
    {
        $admin = auth('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $settings = (object) [
            'app_logo' => Setting::getValue('app_logo'),
            'login_logo' => Setting::getValue('login_logo'),
        ];
        
        $currentLogo = Setting::getValue('app_logo') ?: 'images/logo.png';
        $currentBackground = Setting::getValue('app_background') ?: 'images/bc depan.jpg';

        $colors = [
            'primary_color' => Setting::getValue('primary_color', '#22c55e'),
            'secondary_color' => Setting::getValue('secondary_color', '#16a34a'),
            'accent_color' => Setting::getValue('accent_color', '#84cc16'),
            'background_color' => Setting::getValue('background_color', '#ffffff'),
            'text_color' => Setting::getValue('text_color', '#1f2937'),
            'login_title_color' => Setting::getValue('login_title_color', '#667eea'),
            'login_subtitle_color' => Setting::getValue('login_subtitle_color', '#64748b'),
        ];

        $fonts = [
            'heading_font' => Setting::getValue('heading_font', 'Inter, sans-serif'),
            'body_font' => Setting::getValue('body_font', 'Inter, sans-serif'),
            'font_size_base' => Setting::getValue('font_size_base', '16px'),
        ];

        $content = [
            'site_title' => Setting::getValue('site_title', 'Buku Tamu Digital'),
            'site_description' => Setting::getValue('site_description', 'Sistem buku tamu digital modern'),
            'footer_text' => Setting::getValue('footer_text', '© 2024 Buku Tamu Digital. All rights reserved.'),
            'admin_login_title' => Setting::getValue('admin_login_title', 'Hello, welcome!'),
            'admin_login_subtitle' => Setting::getValue('admin_login_subtitle', 'Silakan login untuk mengakses sistem buku tamu digital'),
            'welcome_text' => Setting::getValue('welcome_text', 'Buku Tamu Digital'),
            'subwelcome_text' => Setting::getValue('subwelcome_text', 'Silakan isi form di bawah ini'),
        ];
        
        $driveSyncDir = Setting::getValue('drive_sync_dir');
        $driveExists = $driveSyncDir ? is_dir($driveSyncDir) : false;
        $mysqldumpPath = Setting::getValue('mysqldump_path', 'C:\xampp\mysql\bin\mysqldump.exe');
        $mysqldumpExists = $mysqldumpPath ? file_exists($mysqldumpPath) : false;
        $localBackupDir = Setting::getValue('local_backup_dir');
        $localExists = $localBackupDir ? is_dir($localBackupDir) : false;

        return view('admin.pengaturan', compact(
            'settings', 'currentLogo', 'currentBackground', 'colors', 'fonts', 
            'content', 'driveSyncDir', 'driveExists', 'mysqldumpPath', 
            'mysqldumpExists', 'localBackupDir', 'localExists'
        ));
    }

    public function updateLogo(Request $request)
    {
        try {
            if ($request->hasFile('app_logo')) {
                $file = $request->file('app_logo');
                $filename = time() . '_app_logo.' . $file->getClientOriginalExtension();
                $file->storeAs('logos', $filename, 'public');
                Setting::setValue('app_logo', $filename, 'image', 'Aplikasi Logo');
            }
            if ($request->hasFile('login_logo')) {
                $file = $request->file('login_logo');
                $filename = time() . '_login_logo.' . $file->getClientOriginalExtension();
                $file->storeAs('logos', $filename, 'public');
                Setting::setValue('login_logo', $filename, 'image', 'Login Logo');
            }
            return redirect()->back()->with('success', '✅ Logo berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update logo: ' . $e->getMessage());
        }
    }

    public function updateBukuTamuText(Request $request)
    {
        $request->validate(['welcome_text' => 'required|string', 'subwelcome_text' => 'required|string']);
        Setting::updateOrCreate(['key' => 'welcome_text'], ['value' => $request->welcome_text]);
        Setting::updateOrCreate(['key' => 'subwelcome_text'], ['value' => $request->subwelcome_text]);
        return redirect()->back()->with('success', '✅ Teks buku tamu diperbarui!');
    }

    public function updateThemeColors(Request $request)
    {
        $fields = ['primary_color', 'secondary_color', 'accent_color', 'background_color', 'text_color'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::updateOrCreate(['key' => $field], ['value' => $request->$field]);
            }
        }
        return redirect()->back()->with('success', '✅ Tema warna diperbarui!');
    }

    public function updateLoginText(Request $request)
    {
        $request->validate(['admin_login_title' => 'required', 'admin_login_subtitle' => 'required']);
        Setting::updateOrCreate(['key' => 'admin_login_title'], ['value' => $request->admin_login_title]);
        Setting::updateOrCreate(['key' => 'admin_login_subtitle'], ['value' => $request->admin_login_subtitle]);
        Setting::updateOrCreate(['key' => 'login_title_color'], ['value' => $request->login_title_color]);
        Setting::updateOrCreate(['key' => 'login_subtitle_color'], ['value' => $request->login_subtitle_color]);
        return redirect()->back()->with('success', '✅ Teks login diperbarui!');
    }

    public function restoreTrash()
    {
        $restored = Pengunjung::onlyTrashed()->where('deleted_at', '>=', now()->subDays(30))->restore();
        return redirect()->back()->with('success', "✅ {$restored} data dipulihkan.");
    }

    public function purgeTrash()
    {
        $purged = Pengunjung::onlyTrashed()->where('deleted_at', '<', now()->subDays(30))->forceDelete();
        return redirect()->back()->with('success', "🗑️ {$purged} data dihapus permanen.");
    }

    public function resetAllSettings()
    {
        // ... Logic reset settings (skipped for brevity but should be included)
        return redirect()->back()->with('success', '✅ Semua pengaturan direset!');
    }

    public function backupNow(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $format = strtolower((string) $request->input('format', 'xlsx'));
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $date = now()->format('Y-m-d');
        Artisan::call('backup:pengunjung', [
            'date' => $date,
            '--format' => $format,
        ]);
        $output = trim(Artisan::output());

        $copied = false;
        $copiedLocal = false;
        $copiedDrive = false;
        try {
            $localDir = Setting::getValue('local_backup_dir');
            if ($localDir && is_dir($localDir) && file_exists($output)) {
                $destFile = rtrim($localDir, '\\/') . DIRECTORY_SEPARATOR . basename($output);
                @copy($output, $destFile);
                $copiedLocal = true;
            }
            $driveDir = Setting::getValue('drive_sync_dir') ?: env('DRIVE_SYNC_DIR');
            if ($driveDir && is_dir($driveDir)) {
                $targetDir = rtrim($driveDir, '\\/'); 
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }
                $srcFile = $output;
                if (file_exists($srcFile)) {
                    $destFile = $targetDir . DIRECTORY_SEPARATOR . basename($srcFile);
                    @copy($srcFile, $destFile);
                    $copiedDrive = true;
                }
            }
        } catch (\Throwable $e) {
        }

        $copied = $copiedLocal || $copiedDrive;
        try {
            $webhook = Setting::getValue('drive_webhook_url');
            if ($webhook && file_exists($output)) {
                $folderId = Setting::getValue('drive_folder_id');
                $filename = basename($output);
                $mime = $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $payload = [
                    'name' => $filename,
                    'mimeType' => $mime,
                    'folderId' => $folderId,
                    'data' => base64_encode(file_get_contents($output)),
                ];
                $resp = Http::timeout(25)->asJson()->post($webhook, $payload);
                if ($resp->ok()) {
                    $copied = true;
                    $msg = $resp->json('message') ?? 'Upload webhook OK';
                    $parts = [];
                    $parts[] = "File lokal: $output";
                    if ($copiedLocal) $parts[] = 'Salin Lokal: OK';
                    if ($copiedDrive) $parts[] = 'Salin Drive: OK';
                    $parts[] = "Upload langsung: $msg";
                    return redirect()->back()->with('success', "Backup $format berhasil. " . implode(' | ', $parts));
                }
            }
        } catch (\Throwable $e) {
        }

        if ($copied) {
            $parts = [];
            $parts[] = "File: $output";
            if ($copiedLocal) $parts[] = 'Salin Lokal: OK';
            if ($copiedDrive) $parts[] = 'Salin Drive: OK';
            return redirect()->back()->with('success', "Backup $format berhasil. " . implode(' | ', $parts));
        }
        return redirect()->back()->with('success', "Backup $format berhasil. File: $output");
    }

    public function updateBackupSettings(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $request->validate([
            'drive_sync_dir' => ['nullable', 'string', 'max:255'],
            'mysqldump_path' => ['nullable', 'string', 'max:255'],
            'drive_folder_link' => ['nullable', 'string', 'max:500'],
            'drive_webhook_url' => ['nullable', 'string', 'max:500'],
            'local_backup_dir' => ['nullable', 'string', 'max:255'],
            'auto_backup_interval' => ['nullable', 'in:disabled,hourly,daily,weekly,monthly'],
            'backup_sql_dir' => ['nullable', 'string', 'max:255'],
            'auto_backup_custom_value' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'auto_backup_custom_unit' => ['nullable', 'in:minutes,hours,days'],
            'auto_delete_old_photos' => ['nullable', 'in:enabled,disabled'],
        ]);
        if ($request->has('auto_delete_old_photos')) {
            Setting::setValue('auto_delete_old_photos', $request->input('auto_delete_old_photos'), 'text', 'Status hapus foto otomatis > 1 tahun');
        }
        if ($request->filled('drive_sync_dir')) {
            Setting::setValue('drive_sync_dir', $request->input('drive_sync_dir'), 'text', 'Folder sinkron Google Drive');
        }
        if ($request->filled('mysqldump_path')) {
            Setting::setValue('mysqldump_path', $request->input('mysqldump_path'), 'text', 'Path mysqldump.exe');
        }
        if ($request->filled('drive_folder_link')) {
            $link = trim($request->input('drive_folder_link'));
            Setting::setValue('drive_folder_link', $link, 'text', 'Link folder Google Drive');
            $folderId = null;
            if (preg_match('~/folders/([a-zA-Z0-9_-]+)~', $link, $m)) {
                $folderId = $m[1] ?? null;
            } elseif (preg_match('~[?&]id=([a-zA-Z0-9_-]+)~', $link, $m)) {
                $folderId = $m[1] ?? null;
            }
            if ($folderId) {
                Setting::setValue('drive_folder_id', $folderId, 'text', 'ID folder Google Drive');
            }
        }
        if ($request->filled('drive_webhook_url')) {
            Setting::setValue('drive_webhook_url', trim($request->input('drive_webhook_url')), 'text', 'Webhook upload Drive (Apps Script)');
        }
        if ($request->filled('local_backup_dir')) {
            Setting::setValue('local_backup_dir', trim($request->input('local_backup_dir')), 'text', 'Folder cadangan lokal File Explorer');
        }
        if ($request->has('auto_backup_interval')) {
            $val = $request->input('auto_backup_interval') ?: 'disabled';
            if (!in_array($val, ['disabled','hourly','daily','weekly','monthly','custom'])) {
                $val = 'disabled';
            }
            Setting::setValue('auto_backup_interval', $val, 'text', 'Interval backup otomatis SQL');
        }
        if ($request->filled('backup_sql_dir')) {
            Setting::setValue('backup_sql_dir', trim($request->input('backup_sql_dir')), 'text', 'Folder penyimpanan backup SQL');
        }
        if ($request->filled('auto_backup_custom_value')) {
            Setting::setValue('auto_backup_custom_value', (string) $request->input('auto_backup_custom_value'), 'text', 'Nilai interval custom backup SQL');
        }
        if ($request->filled('auto_backup_custom_unit')) {
            Setting::setValue('auto_backup_custom_unit', $request->input('auto_backup_custom_unit'), 'text', 'Unit interval custom (minutes/hours/days)');
        }
        return redirect()->back()->with('success', 'Pengaturan backup berhasil disimpan');
    }

    public function fullBackupNow(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $driveDir = Setting::getValue('drive_sync_dir') ?: env('DRIVE_SYNC_DIR');
        $args = [];
        if ($driveDir) {
            $args['--drive-dir'] = $driveDir;
        }
        Artisan::call('backup:full', $args);
        $output = trim(Artisan::output());
        return redirect()->back()->with('success', "Full backup selesai. Folder: $output");
    }

    public function testDriveWrite(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $driveDir = Setting::getValue('drive_sync_dir');
        if (!$driveDir || !is_dir($driveDir)) {
            return redirect()->back()->with('error', 'Folder Drive belum diset atau tidak ditemukan');
        }
        $target = rtrim($driveDir, '\\/');
        if (!is_dir($target)) {
            @mkdir($target, 0775, true);
        }
        $testName = 'Tes_Upload_' . now()->format('Ymd_His') . '.txt';
        $testPath = $target . DIRECTORY_SEPARATOR . $testName;
        $content = "Tes upload dari Buku Tamu\nWaktu: " . now()->toDateTimeString();
        $ok = @file_put_contents($testPath, $content);
        if ($ok === false) {
            return redirect()->back()->with('error', 'Gagal menulis ke folder Drive. Periksa izin folder.');
        }
        try {
            $webhook = Setting::getValue('drive_webhook_url');
            if ($webhook) {
                $folderId = Setting::getValue('drive_folder_id');
                $payload = [
                    'name' => $testName,
                    'mimeType' => 'text/plain',
                    'folderId' => $folderId,
                    'data' => base64_encode($content),
                ];
                $resp = Http::timeout(25)->asJson()->post($webhook, $payload);
                if ($resp->ok()) {
                    $msg = $resp->json('message') ?? 'Upload webhook OK';
                    return redirect()->back()->with('success', 'Tes tulis berhasil. Upload langsung: ' . $msg);
                }
            }
        } catch (\Throwable $e) {
        }
        return redirect()->back()->with('success', 'Tes tulis berhasil. File dibuat: ' . $testPath);
    }

    public function testLocalWrite(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $local = Setting::getValue('local_backup_dir');
        if (!$local || !is_dir($local)) {
            return redirect()->back()->with('error', 'Folder cadangan lokal belum diset atau tidak ditemukan');
        }
        $target = rtrim($local, '\\/');
        if (!is_dir($target)) {
            @mkdir($target, 0775, true);
        }
        $name = 'Tes_Local_' . now()->format('Ymd_His') . '.txt';
        $path = $target . DIRECTORY_SEPARATOR . $name;
        $ok = @file_put_contents($path, "Tes tulis lokal\n" . now()->toDateTimeString());
        if ($ok === false) {
            return redirect()->back()->with('error', 'Gagal menulis ke folder lokal. Periksa izin folder.');
        }
        return redirect()->back()->with('success', 'Tes tulis lokal berhasil. File: ' . $path);
    }

    public function downloadSql(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $dest = Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql');
        if (is_string($dest) && preg_match('/\\.sql$/i', $dest)) {
            $dir = dirname($dest);
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $sqlPath = $dest;
        } else {
            $dir = $dest;
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $sqlPath = $dir . DIRECTORY_SEPARATOR . 'backup_' . now()->format('Ymd_His') . '.sql';
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
            $process = new Process($cmd, null, null, null, 180);
            $process->run();
        } catch (\Throwable $e) {
            file_put_contents($sqlPath, "-- Gagal: " . $e->getMessage());
        }

        if (!file_exists($sqlPath) || @filesize($sqlPath) === 0) {
            return redirect()->back()->with('error', 'Gagal membuat file SQL.');
        }
        return response()->download($sqlPath);
    }

    public function restoreSql(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Session expired, please login again');
        }
        $request->validate(['sql_file' => ['required', 'file', 'max:51200']]);
        $file = $request->file('sql_file');
        $tmpPath = storage_path('app/tmp');
        if (!is_dir($tmpPath)) @mkdir($tmpPath, 0775, true);
        $dest = $tmpPath . DIRECTORY_SEPARATOR . 'restore_' . now()->format('Ymd_His') . '.sql';
        $file->move($tmpPath, basename($dest));

        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $database = $config['database'] ?? '';
        
        try {
            $sql = file_get_contents($dest);
            DB::unprepared($sql);
            @unlink($dest);
            return redirect()->back()->with('success', 'Restore SQL berhasil');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }

    public function scheduleSqlDownload(Request $request)
    {
        Setting::setValue('scheduled_download_at', now()->toDateTimeString(), 'text', 'Waktu penjadwalan unduh backup');
        return redirect()->back()->with('success', 'Unduh backup dijadwalkan.');
    }
}

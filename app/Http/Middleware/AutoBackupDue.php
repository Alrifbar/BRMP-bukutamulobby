<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Pengunjung;

class AutoBackupDue
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $interval = Setting::getValue('auto_backup_interval', 'hourly');
            
            // Proses hapus foto otomatis jika diaktifkan
            try {
                if (Setting::getValue('auto_delete_old_photos') === 'enabled') {
                    $oneYearAgo = now()->subYear();
                    $oldVisitors = Pengunjung::where('tanggal_kunjungan', '<', $oneYearAgo)
                        ->whereNotNull('selfie_photo')
                        ->where('selfie_photo', '!=', '')
                        ->get();
                    
                    foreach ($oldVisitors as $visitor) {
                        $photo = $visitor->selfie_photo;
                        if (Storage::disk('public')->exists('selfies/' . $photo)) {
                            Storage::disk('public')->delete('selfies/' . $photo);
                        }
                        // Update database agar tidak dicek lagi
                        $visitor->update(['selfie_photo' => null]);
                    }
                }
            } catch (\Throwable $e) {
                // Jangan hentikan proses jika gagal
            }

            if ($interval && $interval !== 'disabled') {
                $now = Carbon::now();
                $last = Setting::getValue('auto_backup_last_run_at');
                $lastAt = $last ? Carbon::parse($last) : null;
                $due = false;

                if ($interval === 'hourly') {
                    $threshold = (clone $now)->startOfHour();
                    $due = !$lastAt || $lastAt->lt($threshold);
                } elseif ($interval === 'daily') {
                    $threshold = (clone $now)->startOfDay(); // 00:00
                    $due = !$lastAt || $lastAt->lt($threshold);
                } elseif ($interval === 'weekly') {
                    $threshold = (clone $now)->startOfWeek(Carbon::MONDAY)->startOfDay();
                    $due = !$lastAt || $lastAt->lt($threshold);
                } elseif ($interval === 'monthly') {
                    $threshold = (clone $now)->startOfMonth()->startOfDay();
                    $due = !$lastAt || $lastAt->lt($threshold);
                } elseif ($interval === 'custom') {
                    $n = (int) (Setting::getValue('auto_backup_custom_value') ?: 0);
                    $unit = Setting::getValue('auto_backup_custom_unit', 'minutes');
                    if ($n > 0) {
                        $base = $lastAt ?: $now->copy()->sub($n, $unit);
                        $due = $base->add($n, $unit)->lte($now);
                    }
                }

                if ($due) {
                    // lock to avoid concurrent run within 5 minutes
                    if (Cache::add('auto_backup_lock', 1, 300)) {
                        try {
                            $dest = Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql');
                            $exit = Artisan::call('backup:sql', ['--dest' => $dest]);
                            Setting::setValue('auto_backup_last_run_at', now()->toDateTimeString(), 'text', 'Terakhir auto backup SQL');
                            if ($exit === 0) {
                                session()->flash('auto_backup_ready', 1);
                            }
                        } catch (\Throwable $e) {
                            // swallow errors, do not break request
                        } finally {
                            Cache::forget('auto_backup_lock');
                        }
                    }
                }
            }

            // proses jadwal unduh tertunda: scheduled_download_at +1 jam
            try {
                $scheduledAt = Setting::getValue('scheduled_download_at');
                if ($scheduledAt) {
                    $dueTime = Carbon::parse($scheduledAt)->addHour();
                    if (Carbon::now()->greaterThanOrEqualTo($dueTime)) {
                        if (Cache::add('scheduled_download_lock', 1, 300)) {
                            try {
                                $dest = Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql');
                                $exit = Artisan::call('backup:sql', ['--dest' => $dest]);
                                if ($exit === 0) {
                                    session()->flash('scheduled_download_ready', 1);
                                }
                                Setting::setValue('scheduled_download_at', '', 'text', 'Jadwal unduh (kosong=tidak ada)');
                            } catch (\Throwable $e) {
                                // abaikan error
                            } finally {
                                Cache::forget('scheduled_download_lock');
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // abaikan
            }
        } catch (\Throwable $e) {
            // ignore any error to keep request flow safe
        }

        return $next($request);
    }
}

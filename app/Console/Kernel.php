<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Setting;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup:pengunjung --format=xlsx')->hourly()->withoutOverlapping();

        try {
            $interval = Setting::getValue('auto_backup_interval', 'hourly');
            if ($interval && $interval !== 'disabled') {
                $dest = Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql');
                $cmdName = 'backup:sql';
                $cmdArgs = ['--dest' => $dest];
                if ($interval === 'hourly') {
                    $schedule->command($cmdName, $cmdArgs)->hourly()->withoutOverlapping();
                } elseif ($interval === 'daily') {
                    $schedule->command($cmdName, $cmdArgs)->daily()->withoutOverlapping();
                } elseif ($interval === 'weekly') {
                    $schedule->command($cmdName, $cmdArgs)->weekly()->withoutOverlapping();
                } elseif ($interval === 'monthly') {
                    $schedule->command($cmdName, $cmdArgs)->monthly()->withoutOverlapping();
                } elseif ($interval === 'custom') {
                    $n = (int) (Setting::getValue('auto_backup_custom_value') ?: 0);
                    $unit = Setting::getValue('auto_backup_custom_unit') ?: 'minutes';
                    if ($n > 0) {
                        $cron = '* * * * *';
                        if ($unit === 'minutes') {
                            $cron = "*/{$n} * * * *";
                        } elseif ($unit === 'hours') {
                            $cron = "0 */{$n} * * *";
                        } elseif ($unit === 'days') {
                            $cron = "0 0 */{$n} * *";
                        }
                        $schedule->command($cmdName, $cmdArgs)->cron($cron)->withoutOverlapping();
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore schedule errors
        }
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}

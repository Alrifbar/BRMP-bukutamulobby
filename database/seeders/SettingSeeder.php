<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dir = 'C:\\xampp\\htdocs\\buku-tamu\\public\\storage';
        @mkdir($dir, 0775, true);
        @mkdir($dir . DIRECTORY_SEPARATOR . 'selfies', 0775, true);
        Setting::setValue('drive_sync_dir', $dir, 'text', 'Folder sinkron Google Drive/OneDrive untuk file');
    }
}

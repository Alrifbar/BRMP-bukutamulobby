<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default admin (restore / ensure exists)
        $email = 'bisip.kementan@gmail.com';
        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => Hash::make('brmpph#22'),
            ]
        );
        Admin::where('email', 'adminbrmp.official')->delete();
    }
}

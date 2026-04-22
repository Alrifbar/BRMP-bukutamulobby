<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['name' => 'nama', 'label' => 'Nama Lengkap', 'placeholder' => 'Masukkan nama lengkap Anda', 'type' => 'text', 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 1],
            ['name' => 'usia', 'label' => 'Usia', 'placeholder' => '-- Pilih Usia --', 'type' => 'number', 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 2],
            ['name' => 'gender', 'label' => 'Jenis Kelamin', 'placeholder' => '-- Pilih Jenis Kelamin --', 'type' => 'select', 'options' => json_encode(['L' => 'Laki-laki', 'P' => 'Perempuan']), 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 3],
            ['name' => 'no_hp', 'label' => 'No. Handphone', 'placeholder' => '0812-3456-7890', 'type' => 'tel', 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 4],
            ['name' => 'keperluan_kategori', 'label' => 'Keperluan', 'placeholder' => '-- Pilih Keperluan --', 'type' => 'select', 'options' => json_encode([
                '1' => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
                '2' => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
                '3' => 'Layanan Perpustakaan',
                '4' => 'Layanan Magang',
                '5' => 'Layanan Informasi dan Dokumentasi',
                '6' => 'Layanan Publikasi Warta',
                '7' => 'Rapat/Pertemuan',
                '8' => 'Lainnya'
            ]), 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 5],
            ['name' => 'instansi', 'label' => 'Instansi', 'placeholder' => 'Nama kantor, sekolah, atau organisasi', 'type' => 'text', 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 6],
            ['name' => 'pendidikan', 'label' => 'Pendidikan/Pekerjaan', 'placeholder' => '-- Pilih Pendidikan/Pekerjaan --', 'type' => 'select', 'options' => json_encode(['SD' => 'SD', 'SMP' => 'SMP', 'SMA/SMK' => 'SMA/SMK', 'D3' => 'D3', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3', 'Lainnya' => 'Lainnya']), 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 7],
            ['name' => 'yang_ditemui', 'label' => 'Yang Ditemui', 'placeholder' => 'Nama orang yang Anda temui', 'type' => 'text', 'is_required' => true, 'is_visible' => true, 'is_core' => true, 'order' => 8],
            ['name' => 'email', 'label' => 'E-mail', 'placeholder' => 'email@example.com', 'type' => 'email', 'is_required' => false, 'is_visible' => true, 'is_core' => true, 'order' => 9],
            ['name' => 'selfie_photo', 'label' => 'Selfie', 'placeholder' => null, 'type' => 'file', 'is_required' => false, 'is_visible' => true, 'is_core' => true, 'order' => 10],
        ];

        foreach ($fields as $field) {
            DB::table('form_fields')->updateOrInsert(['name' => $field['name']], $field);
        }
    }
}

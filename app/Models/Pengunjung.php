<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengunjung extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'usia',
        'no_hp',
        'email',
        'instansi',
        'pendidikan',
        'yang_ditemui',
        'keperluan_kategori',
        'keperluan_lainnya',
        'tanggal_kunjungan',
        'edit_attempts',
        'unique_token',
        'gender',
        'selfie_photo',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tanggal_kunjungan' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

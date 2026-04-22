<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'placeholder',
        'type',
        'options',
        'is_required',
        'is_visible',
        'is_core',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
        'is_core' => 'boolean',
    ];
}

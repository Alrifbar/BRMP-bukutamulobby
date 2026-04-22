<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value, $type = 'text', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Get logo URL
     */
    public static function getLogoUrl()
    {
        $logo = static::getValue('app_logo');
        
        if ($logo && Storage::disk('public')->exists('logos/' . $logo)) {
            // Jika logo kustom ada di storage
            return Storage::disk('public')->url('logos/' . $logo);
        }
        
        // Fallback ke logo default di public/images/logo.png
        if (file_exists(public_path('images/logo.png'))) {
            return asset('images/logo.png');
        }
        
        // Ultimate fallback jika tidak ada logo sama sekali
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiByeD0iMjAiIGZpbGw9IiMxMGI5ODEiLz4KPHR5cGUNCg0KCQkJCQkJCQkJaWQ9dDpkZWZhdWx0DQoJCQkJCQkJCWZvbnQtZmFtaWx5PSJJbnRlciwgc2Fucy1zZXJpZiINCgkJCQkJCQkJZm9udC1zaXplPSI0MCINCgkJCQkJCQkJZmlsbD0iI2ZmZmZmZiINCgkJCQkJCQkJdGV4dC1hbmNob3I9Im1pZGRsZSINCgkJCQkJCQkJZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+CgkJCQkJCTx0c3BhbiB4PSI0MCIgeT0iNTAiPkJUPC90c3Bhbj4KPC90ZXh0Pgo8L3N2Zz4=';
    }

    /**
     * Get login logo URL
     */
    public static function getLoginLogoUrl()
    {
        // Selalu gunakan logo utama agar konsisten di semua halaman
        return static::getLogoUrl();
    }

    /**
     * Get background URL
     */
    public static function getBackgroundUrl()
    {
        $bgPath = static::getValue('app_background', 'images/default-bg.jpg');
        return asset($bgPath);
    }
}

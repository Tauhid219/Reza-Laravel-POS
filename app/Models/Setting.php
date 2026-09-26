<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_settings');
    }

    /**
     * Get all settings grouped by key => value.
     */
    public static function getAll(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('app_settings');
    }
}

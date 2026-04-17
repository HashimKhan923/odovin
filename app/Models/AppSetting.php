<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['group', 'key', 'label', 'value', 'type', 'description', 'unit'];

    // ── Core get/set with caching ──────────────────────────────────

    /**
     * Get a setting value by key. Returns $default if not found.
     * Cached for 1 hour — cache busts automatically on save.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });

        $value = $settings[$key] ?? null;

        if ($value === null) return $default;

        return $value;
    }

    /**
     * Get as integer
     */
    public static function int(string $key, int $default = 0): int
    {
        return (int) static::get($key, $default);
    }

    /**
     * Get as boolean
     */
    public static function bool(string $key, bool $default = false): bool
    {
        $val = static::get($key, $default ? '1' : '0');
        return in_array($val, ['1', 'true', 'yes', true], true);
    }

    /**
     * Get as string
     */
    public static function str(string $key, string $default = ''): string
    {
        return (string) static::get($key, $default);
    }

    /**
     * Set a value and bust the cache
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget('app_settings_all');
    }

    /**
     * Get all settings for a group, keyed by key
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)->get()->keyBy('key')->toArray();
    }

    /**
     * Bust cache when any setting is saved
     */
    protected static function boot(): void
    {
        parent::boot();
        static::saved(fn() => Cache::forget('app_settings_all'));
        static::deleted(fn() => Cache::forget('app_settings_all'));
    }
}
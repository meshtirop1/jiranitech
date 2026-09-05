<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Database-backed site settings.
 *
 * The deployment target has no shell, so the account owner cannot edit .env. Anything
 * an administrator must be able to change from a browser lives here, and
 * config/company.php reads this table first, falling back to env() when a key has not
 * been set. That is what makes publication gate G-07 closeable from the console.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    private const CACHE_KEY = 'settings.all';

    /**
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::values()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.map'));
        static::deleted(fn () => Cache::forget('settings.map'));
    }

    /** key => value for the whole table, cached until something changes. */
    public static function map(): Collection
    {
        return Cache::rememberForever('settings.map', fn () => static::query()->pluck('value', 'key'));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::map()->get($key);

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * URL for an uploaded media setting, falling back to a bundled asset.
     *
     * Uploads are stored on the public disk, so they need asset('storage/...'),
     * while the shipped defaults live in public/images. Callers should not have
     * to know which of the two they are getting — hence this.
     *
     * Returns null when there is neither, so a view can decide whether to render
     * the element at all instead of emitting a broken <img>.
     */
    public static function media(string $key, ?string $fallback = null): ?string
    {
        $value = static::get($key);

        if (! $value) {
            return $fallback ? asset($fallback) : null;
        }

        return str_starts_with($value, 'http') ? $value : asset('storage/' . $value);
    }
}

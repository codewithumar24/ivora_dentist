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
}

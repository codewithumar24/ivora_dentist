<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    protected $fillable = ['title', 'category', 'image', 'before_image', 'caption', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function url(?string $field = 'image'): string
    {
        $value = $this->{$field};
        if (! $value) {
            return '';
        }

        return Str::startsWith($value, 'http') ? $value : asset('storage/'.$value);
    }
}

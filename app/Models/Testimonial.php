<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'role', 'avatar', 'quote', 'rating', 'treatment', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function getAvatarUrlAttribute(): string
    {
        if (! $this->avatar) {
            return 'https://ui-avatars.com/api/?background=A8E6D8&color=10403B&name='.urlencode($this->name);
        }

        return Str::startsWith($this->avatar, 'http') ? $this->avatar : asset('storage/'.$this->avatar);
    }
}

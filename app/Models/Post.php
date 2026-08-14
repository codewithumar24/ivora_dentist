<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_category_id', 'author_id', 'title', 'slug', 'cover_image', 'excerpt',
        'body', 'read_minutes', 'views', 'status', 'published_at',
        'meta_title', 'meta_description',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getCoverUrlAttribute(): string
    {
        if (! $this->cover_image) {
            return 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=1200&q=80';
        }

        return Str::startsWith($this->cover_image, 'http') ? $this->cover_image : asset('storage/'.$this->cover_image);
    }
}

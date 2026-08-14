<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'speciality_id', 'name', 'slug', 'icon', 'image', 'summary', 'description',
        'price_from', 'price_to', 'duration_minutes', 'includes', 'is_featured',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'includes' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
    ];

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=900&q=80';
        }

        return Str::startsWith($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }

    public function getPriceLabelAttribute(): string
    {
        $currency = Setting::get('currency_symbol', 'Rs');

        return $this->price_to
            ? "{$currency} ".number_format((float) $this->price_from)." – {$currency} ".number_format((float) $this->price_to)
            : "From {$currency} ".number_format((float) $this->price_from);
    }
}

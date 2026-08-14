<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'speciality_id', 'title', 'slug', 'designation', 'qualifications',
        'license_no', 'experience_years', 'consultation_fee', 'short_bio', 'biography',
        'languages', 'socials', 'rating', 'reviews_count', 'is_featured',
        'accepts_online_booking', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'languages' => 'array',
        'socials' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'accepts_online_booking' => 'boolean',
        'consultation_fee' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function timeOffs(): HasMany
    {
        return $this->hasMany(DoctorTimeOff::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeBookable($q)
    {
        return $q->where('is_active', true)->where('accepts_online_booking', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->title.' '.$this->user?->name);
    }

    public function getPhotoAttribute(): string
    {
        return $this->user?->avatar_url ?? '';
    }

    public function refreshRating(): void
    {
        $approved = $this->reviews()->where('is_approved', true);
        $this->forceFill([
            'rating' => round((float) $approved->avg('rating') ?: 5, 2),
            'reviews_count' => $approved->count(),
        ])->save();
    }
}

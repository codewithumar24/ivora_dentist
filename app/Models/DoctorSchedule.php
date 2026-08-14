<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id', 'day_of_week', 'starts_at', 'ends_at', 'break_starts_at',
        'break_ends_at', 'slot_minutes', 'patients_per_slot', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '—';
    }

    public function getWindowAttribute(): string
    {
        return date('g:i A', strtotime($this->starts_at)).' – '.date('g:i A', strtotime($this->ends_at));
    }
}

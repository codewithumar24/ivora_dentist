<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorTimeOff extends Model
{
    protected $fillable = ['doctor_id', 'starts_on', 'ends_on', 'starts_at', 'ends_at', 'reason'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** A full-day closure blocks the calendar; a partial one only blocks its window. */
    public function isFullDay(): bool
    {
        return is_null($this->starts_at) && is_null($this->ends_at);
    }
}

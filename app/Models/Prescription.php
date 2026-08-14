<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    protected $fillable = ['medical_record_id', 'patient_id', 'doctor_id', 'reference', 'issued_on', 'notes'];

    protected $casts = ['issued_on' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public static function nextReference(): string
    {
        return 'RX-'.now()->format('y').'-'.str_pad((string) (static::count() + 1), 5, '0', STR_PAD_LEFT);
    }
}

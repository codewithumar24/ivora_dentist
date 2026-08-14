<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mrn', 'blood_group', 'allergies', 'medical_conditions',
        'current_medications', 'emergency_contact_name', 'emergency_contact_phone',
        'insurance_provider', 'insurance_number', 'is_smoker', 'notes',
    ];

    protected $casts = ['is_smoker' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function nextMrn(): string
    {
        $count = static::withoutGlobalScopes()->count() + 1;

        return 'IVD-P'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}

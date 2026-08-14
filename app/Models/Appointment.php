<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const NO_SHOW = 'no_show';

    protected $fillable = [
        'reference', 'patient_id', 'doctor_id', 'service_id', 'scheduled_on',
        'starts_at', 'ends_at', 'status', 'source', 'visit_type', 'reason',
        'symptoms', 'staff_notes', 'fee', 'payment_status', 'confirmed_at',
        'checked_in_at', 'completed_at', 'cancelled_at', 'cancelled_by',
        'cancel_reason', 'reminder_sent_at', 'created_by',
    ];

    protected $casts = [
        'scheduled_on' => 'date',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'fee' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public static function nextReference(): string
    {
        $year = now()->year;
        $count = static::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return sprintf('IVD-%d-%05d', $year, $count);
    }

    public function getStartsAtCarbonAttribute(): Carbon
    {
        return Carbon::parse($this->scheduled_on->toDateString().' '.$this->starts_at);
    }

    public function getTimeLabelAttribute(): string
    {
        return Carbon::parse($this->starts_at)->format('g:i A').' – '.Carbon::parse($this->ends_at)->format('g:i A');
    }

    public function getStatusToneAttribute(): string
    {
        return config("clinic.appointment_statuses.{$this->status}.tone", 'slate');
    }

    public function getStatusLabelAttribute(): string
    {
        return config("clinic.appointment_statuses.{$this->status}.label", ucfirst($this->status));
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at_carbon->isFuture()
            && in_array($this->status, [self::PENDING, self::CONFIRMED], true);
    }

    /** Patients may cancel until the notice window closes. */
    public function isCancellableByPatient(): bool
    {
        return $this->isUpcoming()
            && $this->starts_at_carbon->diffInHours(now(), true) >= 0
            && now()->lessThan($this->starts_at_carbon->copy()->subHours(config('clinic.cancel_window_hours')));
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereDate('scheduled_on', '>=', today())
            ->whereIn('status', [self::PENDING, self::CONFIRMED]);
    }

    public function scopeToday(Builder $q): Builder
    {
        return $q->whereDate('scheduled_on', today());
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        return $q->when($status, fn ($q) => $q->where('status', $status));
    }

    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        return $q->whereBetween('scheduled_on', [$from, $to]);
    }
}

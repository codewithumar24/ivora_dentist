<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number', 'patient_id', 'appointment_id', 'doctor_id', 'issued_on', 'due_on',
        'subtotal', 'discount', 'tax', 'total', 'amount_paid', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'due_on' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public static function nextNumber(): string
    {
        return 'INV-'.now()->format('Y').'-'.str_pad((string) (static::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT);
    }

    public function getBalanceAttribute(): float
    {
        return round((float) $this->total - (float) $this->amount_paid, 2);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $total = $subtotal - (float) $this->discount + (float) $this->tax;
        $paid = $this->payments()->sum('amount');

        $this->forceFill([
            'subtotal' => $subtotal,
            'total' => $total,
            'amount_paid' => $paid,
            'status' => match (true) {
                $this->status === 'void' => 'void',
                $paid <= 0 => 'unpaid',
                $paid < $total => 'partial',
                default => 'paid',
            },
        ])->save();
    }

    public function scopeUnsettled($q)
    {
        return $q->whereIn('status', ['unpaid', 'partial']);
    }
}

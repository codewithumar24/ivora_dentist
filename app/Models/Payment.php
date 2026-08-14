<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'patient_id', 'reference', 'amount', 'method',
        'paid_at', 'transaction_id', 'notes', 'received_by',
    ];

    protected $casts = ['paid_at' => 'datetime', 'amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public static function nextReference(): string
    {
        return 'PAY-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -5));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancePayment extends Model
{
    protected $fillable = [
        'mess_id', 'member_id', 'transaction_type',
        'amount', 'transaction_date', 'notes', 'processed_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MessMember::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getTransactionTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'received' => 'Received',
            'refunded' => 'Refunded',
            'adjusted' => 'Adjusted to Rent',
            default    => ucfirst($this->transaction_type),
        };
    }

    /** Positive amounts increase the balance held, negative reduce it */
    public function getSignedAmountAttribute(): float
    {
        return $this->transaction_type === 'received'
            ? (float) $this->amount
            : -(float) $this->amount;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentPayment extends Model
{
    protected $fillable = [
        'mess_id', 'member_id', 'month', 'year',
        'amount', 'payment_type', 'payment_method',
        'payment_date', 'notes', 'received_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
        'month'        => 'integer',
        'year'         => 'integer',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MessMember::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'rent'       => 'Rent',
            'penalty'    => 'Penalty',
            'discount'   => 'Discount',
            'adjustment' => 'Adjustment',
            default      => ucfirst($this->payment_type),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'          => 'Cash',
            'bkash'         => 'bKash',
            'nagad'         => 'Nagad',
            'bank_transfer' => 'Bank Transfer',
            'other'         => 'Other',
            default         => ucfirst($this->payment_method),
        };
    }

    /** True if this payment reduces the due (not a discount) */
    public function isCredit(): bool
    {
        return in_array($this->payment_type, ['rent', 'penalty', 'adjustment']);
    }
}

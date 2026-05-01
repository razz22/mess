<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualMarketPurchase extends Model
{
    protected $fillable = [
        'mess_id', 'added_by', 'member_id', 'expense_date', 'description',
        'items', 'total_amount', 'status', 'approved_by', 'approved_at',
        'reject_reason', 'expense_id',
    ];

    protected $casts = [
        'items'        => 'array',
        'expense_date' => 'date',
        'approved_at'  => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}

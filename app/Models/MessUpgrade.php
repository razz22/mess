<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SubscriptionPlan;

class MessUpgrade extends Model
{
    protected $fillable = [
        'mess_id', 'plan_id', 'user_id', 'current_limit', 'requested_limit',
        'amount', 'bkash_number', 'transaction_id',
        'status', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'warning',
        };
    }
}

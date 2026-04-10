<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessSubscription extends Model
{
    protected $fillable = [
        'mess_id', 'plan_id', 'user_id', 'plan', 'max_members', 'amount',
        'starts_at', 'expires_at', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_members' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}

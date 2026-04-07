<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketRoutineExchange extends Model
{
    protected $fillable = [
        'routine_id', 'from_user_id', 'to_user_id', 'reason', 'status', 'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function routine(): BelongsTo
    {
        return $this->belongsTo(MarketRoutine::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}

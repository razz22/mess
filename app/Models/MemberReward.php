<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberReward extends Model
{
    protected $fillable = [
        'mess_id', 'user_id', 'month', 'year', 'type',
        'points', 'gift_description', 'reason', 'awarded_by',
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}

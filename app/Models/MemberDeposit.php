<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDeposit extends Model
{
    protected $fillable = [
        'mess_id', 'user_id', 'amount', 'month', 'year', 'deposit_date', 'note', 'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deposit_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketListItem extends Model
{
    protected $fillable = [
        'routine_id', 'mess_id', 'item_name', 'quantity', 'unit',
        'estimated_cost', 'actual_cost', 'purchased', 'added_by',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'purchased' => 'boolean',
    ];

    public function routine(): BelongsTo
    {
        return $this->belongsTo(MarketRoutine::class);
    }

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}

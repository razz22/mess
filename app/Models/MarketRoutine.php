<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketRoutine extends Model
{
    protected $fillable = [
        'mess_id', 'start_date', 'end_date', 'assigned_to', 'assigned_by', 'status', 'notes', 'total_spent',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_spent' => 'decimal:2',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function listItems(): HasMany
    {
        return $this->hasMany(MarketListItem::class, 'routine_id');
    }

    public function exchange(): HasOne
    {
        return $this->hasOne(MarketRoutineExchange::class, 'routine_id')->latest();
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(MarketRoutineExchange::class, 'routine_id');
    }
}

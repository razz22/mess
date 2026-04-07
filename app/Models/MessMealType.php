<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMealType extends Model
{
    protected $table = 'mess_meal_types';

    protected $fillable = [
        'mess_id', 'name', 'close_time', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function isExpired(): bool
    {
        if (!$this->close_time) return false;
        return now()->format('H:i:s') > $this->close_time;
    }
}

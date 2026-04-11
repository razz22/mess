<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMealType extends Model
{
    protected $table = 'mess_meal_types';

    protected $fillable = [
        'mess_id', 'name', 'close_time', 'rate', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function isExpired(string $date = null): bool
    {
        if (!$this->close_time) return false;

        $date = $date ?? now()->toDateString();

        if ($date > now()->toDateString()) return false; // future date — never expired
        if ($date < now()->toDateString()) return true;  // past date — always expired

        // Today: compare current time against close_time
        return now()->format('H:i:s') > $this->close_time;
    }
}

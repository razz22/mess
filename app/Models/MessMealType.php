<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMealType extends Model
{
    protected $table = 'mess_meal_types';

    protected $fillable = [
        'mess_id', 'name', 'close_time', 'closes_previous_day', 'rate', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'closes_previous_day' => 'boolean',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function isExpired(?string $date = null): bool
    {
        if (!$this->close_time) return false;

        $date  = $date ?? now()->toDateString();
        $today = now()->toDateString();

        if ($this->closes_previous_day) {
            // Cutoff is close_time on the day BEFORE the meal date
            $cutoffDate = \Carbon\Carbon::parse($date)->subDay()->toDateString();

            if ($today < $cutoffDate) return false; // cutoff day hasn't arrived yet
            if ($today > $cutoffDate) return true;  // past the cutoff day

            // Today IS the cutoff day — compare current time
            return now()->format('H:i:s') > $this->close_time;
        }

        // Same-day cutoff (original behaviour)
        if ($date > $today) return false; // future meal date — not expired
        if ($date < $today) return true;  // past meal date — always expired

        return now()->format('H:i:s') > $this->close_time;
    }
}

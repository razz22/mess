<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMealType extends Model
{
    protected $table = 'mess_meal_types';

    protected $fillable = [
        'mess_id', 'name', 'close_time', 'close_days_before', 'rate', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'close_days_before' => 'integer',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function isExpired(string $date = null): bool
    {
        if (!$this->close_time) return false;

        $date = $date ?? now()->toDateString();
        $daysBefore = $this->close_days_before ?? 0;

        // The deadline is: (meal date - close_days_before) at close_time
        $deadline = \Carbon\Carbon::parse($date)
            ->subDays($daysBefore)
            ->setTimeFromTimeString($this->close_time);

        return now() > $deadline;
    }

    /**
     * Human-readable close deadline label, e.g. "by 10:00 PM (prev. day)".
     */
    public function closeLabel(): string
    {
        if (!$this->close_time) return '';
        $time  = \Carbon\Carbon::parse($this->close_time)->format('g:i A');
        $days  = $this->close_days_before ?? 0;
        $suffix = match (true) {
            $days === 0 => '',
            $days === 1 => ' (prev. day)',
            default     => " ({$days} days before)",
        };
        return $time . $suffix;
    }
}

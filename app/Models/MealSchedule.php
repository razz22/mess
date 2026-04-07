<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealSchedule extends Model
{
    protected $fillable = [
        'mess_id', 'date', 'type', 'status', 'meal_cost', 'closed_by', 'closed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'meal_cost' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(MealAttendance::class);
    }

    public function presentCount(): int
    {
        return $this->attendances()->where('status', 'on')->count();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isExpired(): bool
    {
        $setting = $this->mess->settings;
        if (!$setting) return false;

        $closeTime = match($this->type) {
            'breakfast' => $setting->breakfast_close,
            'lunch' => $setting->lunch_close,
            'dinner' => $setting->dinner_close,
            default => '23:59:00',
        };

        return now() > \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $closeTime);
    }
}

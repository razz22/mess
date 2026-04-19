<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealAttendance extends Model
{
    protected $fillable = [
        'meal_schedule_id', 'mess_id', 'user_id', 'status', 'quantity', 'full_qty', 'half_qty', 'marked_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
        'quantity'  => 'float',
        'full_qty'  => 'integer',
        'half_qty'  => 'integer',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MealSchedule::class, 'meal_schedule_id');
    }

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

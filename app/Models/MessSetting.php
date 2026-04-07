<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessSetting extends Model
{
    protected $fillable = [
        'mess_id', 'breakfast_close', 'lunch_close', 'dinner_close',
        'monthly_rate', 'allow_meal_off', 'auto_meal_on',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
        'allow_meal_off' => 'boolean',
        'auto_meal_on' => 'boolean',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }
}

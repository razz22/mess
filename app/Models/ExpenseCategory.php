<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'mess_id', 'name', 'icon', 'color', 'is_default', 'is_active', 'is_recurring', 'recurring_amount',
    ];

    protected $casts = [
        'is_default'       => 'boolean',
        'is_active'        => 'boolean',
        'is_recurring'     => 'boolean',
        'recurring_amount' => 'decimal:2',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}

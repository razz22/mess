<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberMonthlySummary extends Model
{
    protected $fillable = [
        'mess_id', 'user_id', 'month', 'year',
        'total_meal_days', 'meal_cost', 'total_expenses', 'market_expense',
        'total_deposit', 'carry_forward_in', 'total_payable',
        'due_amount', 'carry_forward_out', 'status', 'exclude_from_shared', 'generated_at',
    ];

    protected $casts = [
        'meal_cost' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'market_expense' => 'decimal:2',
        'total_deposit' => 'decimal:2',
        'carry_forward_in' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'carry_forward_out' => 'decimal:2',
        'generated_at'       => 'datetime',
        'exclude_from_shared' => 'boolean',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

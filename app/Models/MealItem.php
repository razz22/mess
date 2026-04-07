<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealItem extends Model
{
    protected $fillable = [
        'mess_id', 'name', 'description', 'category', 'status', 'sort_order', 'created_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'description', 'max_members', 'price', 'duration_months', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'max_members'     => 'integer',
        'price'           => 'decimal:2',
        'duration_months' => 'integer',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function upgrades(): HasMany
    {
        return $this->hasMany(MessUpgrade::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('price');
    }
}

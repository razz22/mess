<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'description', 'max_members', 'price', 'duration_months',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'max_members'     => 'integer',
        'price'           => 'decimal:2',
        'duration_months' => 'integer',
        'is_active'       => 'boolean',
        'is_featured'     => 'boolean',
        'sort_order'      => 'integer',
    ];

    /** Return description lines as an array of bullet strings. */
    public function getFeatureLinesAttribute(): array
    {
        if (! $this->description) return [];
        return array_filter(array_map('trim', explode("\n", $this->description)));
    }

    public function upgrades(): HasMany
    {
        return $this->hasMany(MessUpgrade::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('price');
    }
}

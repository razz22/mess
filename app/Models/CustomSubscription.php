<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomSubscription extends Model
{
    protected $fillable = [
        'created_by', 'label', 'max_members', 'price', 'is_free',
        'mess_ids', 'starts_at', 'expires_at', 'status', 'notes',
    ];

    protected $casts = [
        'mess_ids'   => 'array',
        'is_free'    => 'boolean',
        'price'      => 'decimal:2',
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at->isPast()
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public function getMessNamesAttribute(): string
    {
        if (empty($this->mess_ids)) return '—';
        return Mess::whereIn('id', $this->mess_ids)->pluck('name')->join(', ');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeForMess($query, int $messId)
    {
        return $query->where(fn($q) =>
            $q->whereJsonContains('mess_ids', $messId)
              ->orWhereJsonContains('mess_ids', (string) $messId)
        );
    }
}

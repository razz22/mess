<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['created_by', 'title', 'body', 'audience', 'mess_ids', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime', 'mess_ids' => 'array'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return is_null($this->expires_at) || $this->expires_at->isFuture();
    }

    public function getMessNamesAttribute(): string
    {
        if ($this->audience === 'all' || empty($this->mess_ids)) return '';
        return \App\Models\Mess::whereIn('id', $this->mess_ids)->pluck('name')->join(', ');
    }

    public function scopeActive($query)
    {
        return $query->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeForMess($query, int $messId)
    {
        return $query->where(fn($q) =>
            $q->where('audience', 'all')
              ->orWhere(fn($q2) => $q2->where('audience', 'individual')
                  ->where(fn($q3) => $q3
                      ->whereJsonContains('mess_ids', $messId)
                      ->orWhereJsonContains('mess_ids', (string) $messId)
                  ))
        );
    }
}

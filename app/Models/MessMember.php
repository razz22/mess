<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMember extends Model
{
    protected $fillable = [
        'mess_id', 'user_id', 'role', 'is_active', 'carry_forward', 'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'carry_forward' => 'decimal:2',
        'joined_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'author']);
    }

    public function canManage(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'author']);
    }
}

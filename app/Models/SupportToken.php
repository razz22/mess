<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportToken extends Model
{
    protected $fillable = ['mess_id', 'user_id', 'token', 'subject', 'status', 'user_message_count', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function mess(): BelongsTo    { return $this->belongsTo(Mess::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function messages(): HasMany  { return $this->hasMany(SupportMessage::class); }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }

    public function userCanMessage(): bool
    {
        return !$this->isExpired() && $this->status === 'open' && $this->user_message_count < 2;
    }

    public function getRoleAttribute(): string
    {
        $member = MessMember::where('mess_id', $this->mess_id)->where('user_id', $this->user_id)->first();
        if (!$member) return 'owner';
        return $member->role ?? 'member';
    }
}

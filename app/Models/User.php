<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'nid_document', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Messes this user owns
    public function ownedMesses(): HasMany
    {
        return $this->hasMany(Mess::class, 'owner_id');
    }

    // Mess memberships
    public function messMembers(): HasMany
    {
        return $this->hasMany(MessMember::class);
    }

    // All messes this user is part of (including owned)
    public function messes()
    {
        return Mess::whereHas('members', fn($q) => $q->where('user_id', $this->id)->where('is_active', true));
    }

    public function getActiveMess(): ?Mess
    {
        $messId = session('active_mess_id');
        if (!$messId) return null;

        return Mess::whereHas('members', fn($q) => $q->where('user_id', $this->id)->where('is_active', true))
            ->find($messId);
    }

    public function getMembershipIn(int $messId): ?MessMember
    {
        return $this->messMembers()->where('mess_id', $messId)->where('is_active', true)->first();
    }

    public function isManagerOf(int $messId): bool
    {
        $member = $this->getMembershipIn($messId);
        return $member && in_array($member->role, ['owner', 'manager', 'author']);
    }

    public function isOwnerOf(int $messId): bool
    {
        $member = $this->getMembershipIn($messId);
        return $member && $member->role === 'owner';
    }

    public function isBasicMemberOf(int $messId): bool
    {
        $member = $this->getMembershipIn($messId);
        return $member && $member->role === 'member';
    }

    public function canOwnMoreMess(): bool
    {
        return $this->ownedMesses()->count() < 2;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('build/img/customer/customer15.jpg');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(MemberDeposit::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(MemberReward::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MemberReport::class, 'reporter_id');
    }
}

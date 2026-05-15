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
        'blood_group', 'address', 'occupation_type', 'organization',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'date_of_birth', 'gender', 'is_super_admin', 'last_login_at', 'last_login_ip', 'max_messes',
        'google_id', 'email_verified_at',
        'is_blocked', 'blocked_at', 'blocked_until', 'block_reason',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth'     => 'date',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_super_admin'    => 'boolean',
            'is_blocked'        => 'boolean',
            'blocked_at'        => 'datetime',
            'blocked_until'     => 'datetime',
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

        // Super admin can enter any mess without being a member
        if ($this->is_super_admin) {
            return Mess::find($messId);
        }

        return Mess::whereHas('members', fn($q) => $q->where('user_id', $this->id)->where('is_active', true))
            ->find($messId);
    }

    public function getMembershipIn(int $messId): ?MessMember
    {
        // Super admin: use their real membership if it exists, otherwise return a
        // virtual owner membership so all permission checks pass transparently.
        if ($this->is_super_admin) {
            $real = $this->messMembers()->where('mess_id', $messId)->where('is_active', true)->first();
            if ($real) return $real;

            $virtual = new MessMember([
                'mess_id'   => $messId,
                'user_id'   => $this->id,
                'role'      => 'owner',
                'is_active' => true,
                'joined_at' => now(),
            ]);
            // Prevents "no primary key" errors in views that read $member->id
            $virtual->exists = false;
            return $virtual;
        }

        return $this->messMembers()->where('mess_id', $messId)->where('is_active', true)->first();
    }

    public function isManagerOf(int $messId): bool
    {
        if ($this->is_super_admin) return true;

        $member = $this->getMembershipIn($messId);
        if (!$member) return false;

        if ($member->role === 'owner') return true;

        // manager/author: only active if assigned for the current month
        if (in_array($member->role, ['manager', 'author'])) {
            return ManagerRotation::where('mess_id', $messId)
                ->where('user_id', $this->id)
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->where('is_current', true)
                ->exists();
        }

        return false;
    }

    public function isOwnerOf(int $messId): bool
    {
        if ($this->is_super_admin) return true;

        $member = $this->getMembershipIn($messId);
        return $member && $member->role === 'owner';
    }

    public function isBasicMemberOf(int $messId): bool
    {
        // Super admin is never treated as a basic member — they are never restricted
        if ($this->is_super_admin) return false;

        $member = $this->getMembershipIn($messId);
        return $member && $member->role === 'member';
    }

    public function canOwnMoreMess(): bool
    {
        if ($this->is_super_admin) return true;
        $default = \App\Models\SystemSetting::instance()->default_max_messes;
        return $this->ownedMesses()->count() < ($this->max_messes ?? $default);
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

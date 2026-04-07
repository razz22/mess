<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Mess extends Model
{
    protected $fillable = [
        'owner_id', 'name', 'description', 'address', 'avatar',
        'max_members', 'is_premium', 'status', 'invite_code',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'max_members' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($mess) {
            $mess->invite_code = strtoupper(Str::random(8));
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(MessMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(MessMember::class)->where('is_active', true);
    }

    public function currentManager(): HasOne
    {
        return $this->hasOne(ManagerRotation::class)->where('is_current', true)->latest();
    }

    public function mealSchedules(): HasMany
    {
        return $this->hasMany(MealSchedule::class);
    }

    public function mealTypes(): HasMany
    {
        return $this->hasMany(MessMealType::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function mealItems(): HasMany
    {
        return $this->hasMany(MealItem::class);
    }

    public function marketRoutines(): HasMany
    {
        return $this->hasMany(MarketRoutine::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(MemberDeposit::class);
    }

    public function monthlySummaries(): HasMany
    {
        return $this->hasMany(MemberMonthlySummary::class);
    }

    public function managerRotations(): HasMany
    {
        return $this->hasMany(ManagerRotation::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(MemberReward::class);
    }

    public function memberReports(): HasMany
    {
        return $this->hasMany(MemberReport::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(MessSetting::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(MessSubscription::class)->where('status', 'active')->latest();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(MessInvitation::class);
    }

    public function getEffectiveMaxMembers(): int
    {
        if ($this->subscription) {
            return $this->subscription->max_members;
        }
        return $this->max_members;
    }

    public function getMemberCount(): int
    {
        return $this->activeMembers()->count();
    }
}

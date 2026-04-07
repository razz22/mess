<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerRotation extends Model
{
    protected $fillable = [
        'mess_id', 'user_id', 'month', 'year', 'is_current', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ManagerVote::class, 'rotation_id');
    }

    public function getAverageRating(): float
    {
        $count = $this->votes()->count();
        if ($count === 0) return 0;
        return round($this->votes()->avg('rating'), 1);
    }
}

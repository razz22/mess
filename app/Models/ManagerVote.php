<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerVote extends Model
{
    protected $fillable = [
        'mess_id', 'rotation_id', 'voter_id', 'rating', 'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function rotation(): BelongsTo
    {
        return $this->belongsTo(ManagerRotation::class);
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}

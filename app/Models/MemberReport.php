<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberReport extends Model
{
    protected $fillable = [
        'mess_id', 'reporter_id', 'reported_id', 'reason', 'details',
        'status', 'points_awarded', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'points_awarded' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reported(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

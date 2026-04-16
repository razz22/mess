<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessShowCause extends Model
{
    protected $fillable = [
        'mess_id', 'member_id', 'issued_by',
        'subject', 'body',
        'member_reply', 'final_reply',
        'status', 'issued_at', 'replied_at', 'final_replied_at',
    ];

    protected $casts = [
        'issued_at'        => 'datetime',
        'replied_at'       => 'datetime',
        'final_replied_at' => 'datetime',
    ];

    public function mess(): BelongsTo     { return $this->belongsTo(Mess::class); }
    public function member(): BelongsTo   { return $this->belongsTo(MessMember::class, 'member_id'); }
    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }

    public function statusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending Reply</span>',
            'replied' => '<span class="badge bg-info text-dark">Member Replied</span>',
            'closed'  => '<span class="badge bg-success">Closed</span>',
            default   => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}

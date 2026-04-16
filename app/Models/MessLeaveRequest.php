<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MessLeaveRequest extends Model
{
    protected $fillable = [
        'mess_id', 'member_id', 'applied_at', 'last_date', 'reason',
        'notice_months_required', 'status', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'applied_at'  => 'date',
        'last_date'   => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function mess()       { return $this->belongsTo(Mess::class); }
    public function member()     { return $this->belongsTo(MessMember::class, 'member_id'); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function statusBadge(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved'  => '<span class="badge bg-success">Approved</span>',
            'rejected'  => '<span class="badge bg-danger">Rejected</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Compute the earliest allowed last_date for a given application date and notice months.
     * e.g. applying on 2025-04-01 with 1 month notice → min last_date = 2025-04-30
     *      applying on 2025-04-01 with 2 months notice → min last_date = 2025-05-31
     */
    public static function minLastDate(Carbon $appliedAt, int $noticeMonths): Carbon
    {
        return $appliedAt->copy()->addMonths($noticeMonths - 1)->endOfMonth()->startOfDay();
    }
}

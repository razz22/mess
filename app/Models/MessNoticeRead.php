<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessNoticeRead extends Model
{
    public $timestamps = false;

    protected $fillable = ['notice_id', 'user_id', 'read_at'];

    public function notice(): BelongsTo
    {
        return $this->belongsTo(MessNotice::class, 'notice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

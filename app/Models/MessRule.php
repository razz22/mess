<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessRule extends Model
{
    protected $fillable = ['mess_id', 'title', 'description', 'sort_order', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = ['support_token_id', 'sender_id', 'sender_type', 'message', 'image_path', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public function supportToken(): BelongsTo { return $this->belongsTo(SupportToken::class); }
    public function sender(): BelongsTo       { return $this->belongsTo(User::class, 'sender_id'); }
}

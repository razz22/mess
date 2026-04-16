<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessFundTransaction extends Model
{
    protected $fillable = [
        'fund_id', 'mess_id', 'type', 'amount',
        'source', 'notes', 'transaction_date', 'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function fund()       { return $this->belongsTo(MessFund::class, 'fund_id'); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}

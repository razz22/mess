<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessRentFundTransaction extends Model
{
    protected $fillable = [
        'mess_id', 'type', 'amount', 'description', 'note',
        'transaction_date', 'source', 'invoice_id', 'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function mess()       { return $this->belongsTo(Mess::class); }
    public function invoice()    { return $this->belongsTo(MessRentInvoice::class, 'invoice_id'); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}

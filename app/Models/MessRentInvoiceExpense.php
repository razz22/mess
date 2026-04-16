<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessRentInvoiceExpense extends Model
{
    protected $fillable = [
        'invoice_id', 'mess_id', 'description', 'amount', 'expense_date', 'recorded_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice()    { return $this->belongsTo(MessRentInvoice::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}

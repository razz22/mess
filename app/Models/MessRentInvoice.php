<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessRentInvoice extends Model
{
    protected $fillable = [
        'mess_id', 'invoice_no', 'month', 'year',
        'house_owner_name', 'house_owner_phone', 'property_address',
        'rent_amount', 'invoice_date', 'due_date', 'notes',
        'status', 'issued_by', 'paid_by', 'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'paid_at'      => 'datetime',
        'rent_amount'  => 'decimal:2',
    ];

    public function mess()     { return $this->belongsTo(Mess::class); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
    public function paidBy()   { return $this->belongsTo(User::class, 'paid_by'); }
    public function expenses() { return $this->hasMany(MessRentInvoiceExpense::class, 'invoice_id'); }

    public function statusBadge(): string
    {
        return match($this->status) {
            'paid'      => '<span class="badge bg-success">Paid</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            default     => '<span class="badge bg-warning text-dark">Draft</span>',
        };
    }

    public static function generateNo(int $messId, int $month, int $year): string
    {
        $seq = static::where('mess_id', $messId)
                ->where('month', $month)->where('year', $year)
                ->count() + 1;
        return sprintf('RENT-%d-%04d%02d-%03d', $messId, $year, $month, $seq);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessFund extends Model
{
    protected $fillable = ['mess_id', 'name', 'description'];

    public function mess()         { return $this->belongsTo(Mess::class); }
    public function transactions() { return $this->hasMany(MessFundTransaction::class, 'fund_id'); }

    public function balance(): float
    {
        $credit = $this->transactions()->where('type', 'credit')->sum('amount');
        $debit  = $this->transactions()->where('type', 'debit')->sum('amount');
        return (float)($credit - $debit);
    }
}

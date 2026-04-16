<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessContact extends Model
{
    protected $fillable = ['mess_id', 'name', 'phone', 'label', 'notes'];

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    /** Formatted phone for wa.me — strips non-digits */
    public function waPhone(): string
    {
        return preg_replace('/\D/', '', $this->phone);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerNominationVote extends Model
{
    protected $fillable = ['nomination_id', 'voter_id', 'mess_id'];

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function nomination()
    {
        return $this->belongsTo(ManagerNomination::class, 'nomination_id');
    }
}

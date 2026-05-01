<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerNomination extends Model
{
    protected $fillable = ['mess_id', 'user_id', 'nominated_by', 'month', 'year'];

    public function nominee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nominator()
    {
        return $this->belongsTo(User::class, 'nominated_by');
    }

    public function votes()
    {
        return $this->hasMany(ManagerNominationVote::class, 'nomination_id');
    }

    public function voteCount(): int
    {
        return $this->votes()->count();
    }
}

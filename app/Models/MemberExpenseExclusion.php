<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberExpenseExclusion extends Model
{
    protected $fillable = ['mess_id', 'user_id', 'category_id', 'month', 'year'];
}

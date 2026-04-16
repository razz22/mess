<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessMealRoutine extends Model
{
    protected $fillable = ['mess_id', 'meal_type', 'week_no', 'day_of_week', 'items', 'updated_by'];

    public static array $dayNames = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public static array $weekLabels = [
        1 => '1st Week',
        2 => '2nd Week',
        3 => '3rd Week',
        4 => '4th Week',
    ];

    public function mess()      { return $this->belongsTo(Mess::class); }
    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }

    /**
     * Get current week number (1-4) for a given date.
     */
    public static function weekNoForDate(\DateTime|\Carbon\Carbon $date): int
    {
        $day = (int) $date->format('j');
        return min(4, (int) ceil($day / 7));
    }
}

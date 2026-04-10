<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'default_max_members',
        'default_max_messes',
    ];

    protected $casts = [
        'default_max_members' => 'integer',
        'default_max_messes'  => 'integer',
    ];

    /**
     * Get (or create) the singleton settings row.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'default_max_members' => 20,
            'default_max_messes'  => 2,
        ]);
    }
}

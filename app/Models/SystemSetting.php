<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'default_max_members',
        'default_max_messes',
        'google_client_id',
        'google_client_secret',
        'google_login_enabled',
        'bkash_number',
    ];

    protected $casts = [
        'default_max_members'  => 'integer',
        'default_max_messes'   => 'integer',
        'google_login_enabled' => 'boolean',
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

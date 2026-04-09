<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantRegistrationForm extends Model
{
    protected $fillable = [
        'mess_id', 'member_id',
        'flat_floor', 'house_holding', 'road', 'area', 'post_code', 'division', 'police_station',
        'tenant_name', 'father_name', 'date_of_birth', 'marital_status', 'permanent_address',
        'profession_workplace', 'religion', 'education', 'mobile', 'email', 'nid_number', 'passport_number',
        'emergency_name', 'emergency_relation', 'emergency_address', 'emergency_mobile',
        'family_members',
        'housekeeper_name', 'housekeeper_nid', 'housekeeper_mobile', 'housekeeper_address',
        'driver_name', 'driver_nid', 'driver_mobile', 'driver_address',
        'prev_landlord_name', 'prev_landlord_mobile', 'prev_landlord_address', 'reason_leaving',
        'curr_landlord_name', 'curr_landlord_mobile', 'living_since',
        'passport_photo', 'submitted_at',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'living_since'   => 'date',
        'submitted_at'   => 'datetime',
        'family_members' => 'array',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MessMember::class, 'member_id');
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}

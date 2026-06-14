<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiometricDevice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'device_code',
        'device_name',
        'device_type',
        'manufacturer',
        'model',
        'location_description',
        'latitude',
        'longitude',
        'ip_address',
        'mac_address',
        'port',
        'api_endpoint',
        'api_key',
        'status',
        'last_sync_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_sync_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}

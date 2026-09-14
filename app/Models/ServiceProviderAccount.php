<?php

namespace App\Models;

class ServiceProviderAccount extends HortonModel
{
    protected $casts = ['credentials' => 'encrypted:array', 'configuration' => 'array', 'priority' => 'integer', 'is_active' => 'boolean', 'last_health_check_at' => 'datetime'];

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'service_provider_account_id');
    }

    public function operations()
    {
        return $this->hasMany(ServiceOperation::class, 'service_provider_account_id');
    }
}

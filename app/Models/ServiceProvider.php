<?php

namespace App\Models;

class ServiceProvider extends HortonModel
{
    protected $casts = ['configuration' => 'array', 'priority' => 'integer', 'is_active' => 'boolean', 'last_health_check_at' => 'datetime'];

    public function accounts()
    {
        return $this->hasMany(ServiceProviderAccount::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'service_provider_id');
    }

    public function operations()
    {
        return $this->hasMany(ServiceOperation::class, 'service_provider_id');
    }
}

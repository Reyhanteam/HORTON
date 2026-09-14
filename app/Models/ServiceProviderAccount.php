<?php

namespace App\Models;

class ServiceProviderAccount extends HortonModel
{
    protected $casts = ['metadata' => 'array', 'priority' => 'integer'];

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_account_id');
    }

    public function operations()
    {
        return $this->hasMany(ServiceOperation::class, 'provider_account_id');
    }
}

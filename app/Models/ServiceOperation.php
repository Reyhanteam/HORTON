<?php

namespace App\Models;

class ServiceOperation extends HortonModel
{
    protected $casts = ['request' => 'array', 'response' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function providerAccount()
    {
        return $this->belongsTo(ServiceProviderAccount::class, 'service_provider_account_id');
    }
}

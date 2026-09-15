<?php

declare(strict_types=1);

namespace App\Models;

class ServiceProvider extends HortonModel
{
    protected $casts = ['configuration' => 'array', 'metadata' => 'array', 'priority' => 'integer', 'max_users' => 'integer', 'max_services' => 'integer', 'max_traffic_bytes' => 'integer', 'last_health_check_at' => 'datetime'];
    public function accounts() { return $this->hasMany(ServiceProviderAccount::class); }
    public function services() { return $this->hasMany(Service::class, 'service_provider_id'); }
    public function operations() { return $this->hasMany(ServiceOperation::class, 'service_provider_id'); }
    public function policy() { return $this->hasOne(ProviderPolicy::class, 'service_provider_id'); }
    public function capabilities() { return $this->hasMany(ProviderCapability::class, 'service_provider_id'); }
    public function poolItems() { return $this->hasMany(ProviderPoolItem::class, 'service_provider_id'); }
    public function inbounds() { return $this->hasMany(ProviderInbound::class, 'service_provider_id'); }
    public function healthChecks() { return $this->hasMany(ProviderHealthCheck::class, 'service_provider_id'); }
}

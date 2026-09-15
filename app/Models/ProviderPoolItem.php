<?php

declare(strict_types=1);

namespace App\Models;

class ProviderPoolItem extends HortonModel
{
    protected $casts = ['priority' => 'integer', 'is_active' => 'boolean'];

    public function pool() { return $this->belongsTo(ProviderPool::class, 'provider_pool_id'); }
    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
}

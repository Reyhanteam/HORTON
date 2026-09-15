<?php

declare(strict_types=1);

namespace App\Models;

class ProviderHealthCheck extends HortonModel
{
    protected $casts = ['latency_ms' => 'integer', 'checked_at' => 'datetime', 'metadata' => 'array'];

    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
}

<?php

declare(strict_types=1);

namespace App\Models;

class ProviderInbound extends HortonModel
{
    protected $casts = ['is_active' => 'boolean', 'configuration' => 'array'];

    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
}

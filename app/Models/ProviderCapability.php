<?php

declare(strict_types=1);

namespace App\Models;

class ProviderCapability extends HortonModel
{
    protected $casts = ['supported' => 'boolean', 'metadata' => 'array'];

    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
}

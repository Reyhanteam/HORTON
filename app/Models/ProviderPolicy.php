<?php

declare(strict_types=1);

namespace App\Models;

class ProviderPolicy extends HortonModel
{
    protected $casts = [
        'allow_create' => 'boolean',
        'allow_trial' => 'boolean',
        'allow_renew' => 'boolean',
        'allow_extend' => 'boolean',
        'allow_add_capacity' => 'boolean',
        'allow_disable' => 'boolean',
        'allow_delete' => 'boolean',
        'max_services_per_user' => 'integer',
        'max_capacity_per_service' => 'integer',
        'max_duration_days' => 'integer',
        'rules' => 'array',
    ];

    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
}

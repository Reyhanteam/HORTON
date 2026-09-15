<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class ServiceProviderAccount extends HortonModel
{
    protected $casts = ['metadata' => 'array', 'priority' => 'integer'];

    public function provider() { return $this->belongsTo(ServiceProvider::class, 'service_provider_id'); }
    public function services() { return $this->hasMany(Service::class, 'provider_account_id'); }
    public function operations() { return $this->hasMany(ServiceOperation::class, 'provider_account_id'); }

    public function setSecureCredentials(array $credentials): void
    {
        $this->credentials = Crypt::encryptString(json_encode($credentials, JSON_THROW_ON_ERROR));
    }

    public function secureCredentials(): array
    {
        if (! is_string($this->credentials) || $this->credentials === '') return [];
        $decoded = json_decode(Crypt::decryptString($this->credentials), true, 512, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : [];
    }
}

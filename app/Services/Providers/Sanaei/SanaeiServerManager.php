<?php

declare(strict_types=1);

namespace App\Services\Providers\Sanaei;

use App\DTOs\ServiceProviderContext;
use App\Exceptions\SanaeiApiException;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class SanaeiServerManager
{
    public function create(array $data): ServiceProviderAccount
    {
        $provider = $this->provider();

        $account = new ServiceProviderAccount([
            'service_provider_id' => $provider->getKey(),
            'name' => trim((string) ($data['name'] ?? '')),
            'status' => $data['status'] ?? 'active',
            'priority' => (int) ($data['priority'] ?? 100),
            'metadata' => $this->metadata($data),
        ]);

        $account->setSecureCredentials($this->credentials($data));
        $account->save();

        return $account;
    }

    public function update(ServiceProviderAccount $account, array $data): ServiceProviderAccount
    {
        $this->assertSanaeiAccount($account);

        $account->name = trim((string) ($data['name'] ?? $account->name));
        $account->status = $data['status'] ?? $account->status;
        $account->priority = array_key_exists('priority', $data)
            ? (int) $data['priority']
            : (int) ($account->priority ?? 100);
        $account->metadata = [...($account->metadata ?? []), ...$this->metadata($data)];

        $credentials = $account->secureCredentials();
        foreach (['base_url', 'token', 'timeout', 'connect_timeout', 'retry_times'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                $credentials[$key] = $data[$key];
            }
        }
        $this->validateCredentials($credentials, requireToken: false);

        $account->setSecureCredentials($credentials);
        $account->save();

        return $account->refresh();
    }

    public function activate(ServiceProviderAccount $account): ServiceProviderAccount
    {
        return $this->setStatus($account, 'active');
    }

    public function disable(ServiceProviderAccount $account): ServiceProviderAccount
    {
        return $this->setStatus($account, 'disabled');
    }

    public function setMaintenance(ServiceProviderAccount $account): ServiceProviderAccount
    {
        return $this->setStatus($account, 'maintenance');
    }

    public function delete(ServiceProviderAccount $account): void
    {
        $this->assertSanaeiAccount($account);

        if ($account->services()->exists() || $account->operations()->exists()) {
            throw ValidationException::withMessages([
                'server' => 'This Sanaei server cannot be deleted while it has services or recorded provider operations. Disable it instead.',
            ]);
        }

        $account->delete();
    }

    public function connectionTest(ServiceProviderAccount $account): array
    {
        $this->assertSanaeiAccount($account);

        $started = microtime(true);
        try {
            $result = (new SanaeiProvider)->connectionTest(
                new ServiceProviderContext(provider: $account->provider, account: $account)
            );

            return [
                'healthy' => true,
                'latency_ms' => (int) round((microtime(true) - $started) * 1000),
                'message' => 'Sanaei connection is available.',
                'data' => $result->data,
            ];
        } catch (SanaeiApiException $e) {
            return [
                'healthy' => false,
                'latency_ms' => (int) round((microtime(true) - $started) * 1000),
                'message' => $e->getMessage(),
            ];
        }
    }

    public function healthCheck(ServiceProviderAccount $account): array
    {
        $this->assertSanaeiAccount($account);

        $result = $this->connectionTest($account);
        $metadata = $account->metadata ?? [];
        $metadata['health'] = [
            'status' => $result['healthy'] ? 'healthy' : 'unhealthy',
            'latency_ms' => $result['latency_ms'],
            'checked_at' => now()->toISOString(),
            'last_error' => $result['healthy'] ? null : $result['message'],
        ];

        $account->metadata = $metadata;
        $account->save();

        return $result;
    }

    private function provider(): ServiceProvider
    {
        $provider = ServiceProvider::query()->where('driver', 'sanaei')->first();

        if (! $provider) {
            throw ValidationException::withMessages([
                'provider' => 'The Sanaei provider has not been configured.',
            ]);
        }

        return $provider;
    }

    private function assertSanaeiAccount(ServiceProviderAccount $account): void
    {
        if (! $account->exists || $account->provider?->driver !== 'sanaei') {
            throw ValidationException::withMessages([
                'server' => 'The selected provider account is not a Sanaei server.',
            ]);
        }
    }

    private function setStatus(ServiceProviderAccount $account, string $status): ServiceProviderAccount
    {
        $this->assertSanaeiAccount($account);
        $account->status = $status;
        $account->save();

        return $account->refresh();
    }

    private function credentials(array $data): array
    {
        $credentials = [
            'base_url' => rtrim(trim((string) ($data['base_url'] ?? '')), '/'),
            'token' => trim((string) ($data['token'] ?? '')),
            'timeout' => max(1, (int) ($data['timeout'] ?? 15)),
            'connect_timeout' => max(1, (int) ($data['connect_timeout'] ?? 5)),
            'retry_times' => max(0, (int) ($data['retry_times'] ?? 2)),
        ];

        $this->validateCredentials($credentials, requireToken: true);
        return $credentials;
    }

    private function validateCredentials(array $credentials, bool $requireToken): void
    {
        $errors = [];
        if (($credentials['base_url'] ?? '') === '') $errors['base_url'] = 'Sanaei API Base URL is required.';
        if ($requireToken && ($credentials['token'] ?? '') === '') $errors['token'] = 'Sanaei API token is required.';

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function metadata(array $data): array
    {
        return [
            'inbound_ids' => array_values(array_unique(array_map('intval', Arr::wrap($data['inbound_ids'] ?? [])))),
            'region' => isset($data['region']) ? trim((string) $data['region']) : null,
            'notes' => isset($data['notes']) ? trim((string) $data['notes']) : null,
        ];
    }
}

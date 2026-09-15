<?php

declare(strict_types=1);

namespace App\Services\Providers\Sanaei;

use App\DTOs\ServiceProviderContext;
use App\DTOs\ServiceProviderResult;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\SanaeiApiException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;
use App\Services\Providers\AbstractServiceProvider;
use Illuminate\Support\Str;

final class SanaeiProvider extends AbstractServiceProvider
{
    protected function doCreate(TelegramAccount $account, Plan $plan, ServiceProviderContext $context): ServiceProviderResult
    {
        $client = $this->client($context);
        $email = (string) ($context->attributes['email'] ?? 'horton-'.$account->getKey().'-'.Str::lower(Str::random(8)));
        $inboundIds = array_values(array_map('intval', $context->inbounds ?: ($context->attributes['inbound_ids'] ?? [])));

        if ($inboundIds === []) {
            throw new SanaeiApiException('Sanaei creation requires at least one inbound ID.');
        }

        $payload = [
            'client' => [
                'email' => $email,
                'totalGB' => (int) $plan->capacity,
                'expiryTime' => now()->addDays((int) $plan->duration)->getTimestampMs(),
                'tgId' => (int) ($account->telegram_user_id ?? 0),
                'enable' => true,
            ],
            'inboundIds' => $inboundIds,
        ];

        $data = $client->post('/panel/api/clients/add', $payload, 'Create client')->requireSuccess('Create client');
        $remote = $client->get('/panel/api/clients/get/'.rawurlencode($email), 'Read created client')->requireSuccess('Read created client');

        return ServiceProviderResult::success(
            ServiceProviderOperation::CREATE,
            ['email' => $email, 'remote' => $remote, 'response' => $data],
            is_array($remote) ? (string) ($remote['id'] ?? $email) : $email,
            $context->idempotencyKey,
        );
    }

    protected function doGet(Service $service, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $remote = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client')->requireSuccess('Read client');

        return ServiceProviderResult::success(ServiceProviderOperation::GET, ['remote' => $remote], $this->externalReference($remote, $email), $context->idempotencyKey);
    }

    protected function doRenew(Service $service, Plan $plan, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $current = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client before renewal')->requireSuccess('Read client before renewal');
        $current = is_array($current) ? $current : [];
        $expiry = now()->addDays((int) $plan->duration)->getTimestampMs();
        $payload = [...$current, 'totalGB' => (int) $plan->capacity, 'expiryTime' => $expiry, 'enable' => true];
        unset($payload['inboundIds'], $payload['traffic']);

        $data = $this->client($context)->post('/panel/api/clients/update/'.rawurlencode($email), $payload, 'Renew client')->requireSuccess('Renew client');

        return ServiceProviderResult::success(ServiceProviderOperation::RENEW, ['response' => $data, 'expires_at' => now()->addDays((int) $plan->duration)->toISOString()], $email, $context->idempotencyKey);
    }

    protected function doExtend(Service $service, int $days, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $current = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client before extension')->requireSuccess('Read client before extension');
        $current = is_array($current) ? $current : [];
        $currentExpiry = (int) ($current['expiryTime'] ?? 0);
        $base = $currentExpiry > 0 ? max($currentExpiry, now()->getTimestampMs()) : now()->getTimestampMs();
        $newExpiry = $base + ($days * 86400000);
        $payload = [...$current, 'expiryTime' => $newExpiry];
        unset($payload['inboundIds'], $payload['traffic']);

        $data = $this->client($context)->post('/panel/api/clients/update/'.rawurlencode($email), $payload, 'Extend client')->requireSuccess('Extend client');

        return ServiceProviderResult::success(ServiceProviderOperation::EXTEND, ['response' => $data, 'expires_at' => now()->createFromTimestampMs($newExpiry)->toISOString()], $email, $context->idempotencyKey);
    }

    protected function doAddCapacity(Service $service, int $capacity, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $current = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client before capacity update')->requireSuccess('Read client before capacity update');
        $current = is_array($current) ? $current : [];
        $payload = [...$current, 'totalGB' => (int) ($current['totalGB'] ?? 0) + $capacity];
        unset($payload['inboundIds'], $payload['traffic']);

        $data = $this->client($context)->post('/panel/api/clients/update/'.rawurlencode($email), $payload, 'Increase client capacity')->requireSuccess('Increase client capacity');

        return ServiceProviderResult::success(ServiceProviderOperation::ADD_CAPACITY, ['response' => $data, 'capacity' => $payload['totalGB']], $email, $context->idempotencyKey);
    }

    protected function doDisable(Service $service, ServiceProviderContext $context): ServiceProviderResult
    {
        return $this->updateEnabled($service, $context, false, ServiceProviderOperation::DISABLE);
    }

    protected function doDelete(Service $service, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $data = $this->client($context)->post('/panel/api/clients/del/'.rawurlencode($email), [], 'Delete client')->requireSuccess('Delete client');

        return ServiceProviderResult::success(ServiceProviderOperation::DELETE, ['response' => $data, 'status' => 'deleted'], $email, $context->idempotencyKey);
    }

    protected function doStatus(Service $service, ServiceProviderContext $context): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $remote = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client status')->requireSuccess('Read client status');
        $remote = is_array($remote) ? $remote : [];

        return ServiceProviderResult::success(ServiceProviderOperation::STATUS, ['status' => (bool) ($remote['enable'] ?? false) ? 'active' : 'disabled', 'remote' => $remote], $email, $context->idempotencyKey);
    }

    public function healthCheck(ServiceProviderContext $context): ServiceProviderResult
    {
        $data = $this->client($context)->get('/panel/api/server/status', 'Health check')->requireSuccess('Health check');

        return ServiceProviderResult::success(ServiceProviderOperation::STATUS, ['healthy' => true, 'remote' => $data], 'health-check', $context->idempotencyKey);
    }

    private function updateEnabled(Service $service, ServiceProviderContext $context, bool $enabled, ServiceProviderOperation $operation): ServiceProviderResult
    {
        $email = $this->email($service, $context);
        $current = $this->client($context)->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client before status update')->requireSuccess('Read client before status update');
        $current = is_array($current) ? $current : [];
        $payload = [...$current, 'enable' => $enabled];
        unset($payload['inboundIds'], $payload['traffic']);
        $data = $this->client($context)->post('/panel/api/clients/update/'.rawurlencode($email), $payload, 'Update client status')->requireSuccess('Update client status');

        return ServiceProviderResult::success($operation, ['response' => $data, 'status' => $enabled ? 'active' : 'disabled'], $email, $context->idempotencyKey);
    }

    private function client(ServiceProviderContext $context): SanaeiHttpClient
    {
        if ($context->account === null) {
            throw new SanaeiApiException('Sanaei provider account is required.');
        }

        return SanaeiHttpClient::fromCredentials($context->account->secureCredentials());
    }

    private function email(Service $service, ServiceProviderContext $context): string
    {
        $email = $service->metadata['sanaei_email'] ?? $context->attributes['email'] ?? null;
        if (! is_string($email) || $email === '') {
            throw new SanaeiApiException('Service does not contain a Sanaei client email.');
        }
        return $email;
    }

    private function externalReference(mixed $remote, string $fallback): string
    {
        return is_array($remote) ? (string) ($remote['id'] ?? $remote['email'] ?? $fallback) : $fallback;
    }
}

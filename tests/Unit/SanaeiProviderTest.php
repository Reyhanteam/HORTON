<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Models\Plan;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\TelegramAccount;
use App\Services\Providers\Sanaei\SanaeiProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class SanaeiProviderTest extends TestCase
{
    public function test_create_uses_current_sanaei_clients_api_and_does_not_log_credentials(): void
    {
        Http::fake([
            'https://sanaei.test/panel/api/clients/add' => Http::response(['success' => true, 'msg' => 'Client added', 'obj' => null], 200),
            'https://sanaei.test/panel/api/clients/get/*' => Http::response(['success' => true, 'obj' => ['id' => 'uuid-1', 'email' => 'alice']], 200),
        ]);

        [$account, $plan, $context] = $this->fixtures();
        $result = (new SanaeiProvider)->create(
            $account,
            $plan,
            $context->with(['email' => 'alice']),
        );

        self::assertSame(ServiceProviderOperation::CREATE, $result->operation);
        self::assertSame('uuid-1', $result->externalReference);
        Http::assertSent(fn ($request) => $request->url() === 'https://sanaei.test/panel/api/clients/add'
            && $request->hasHeader('Authorization', 'Bearer secret-token')
            && $request['client']['email'] === 'alice'
            && $request['inboundIds'] === [3]);
    }

    public function test_renew_extend_capacity_disable_delete_and_status_use_client_endpoints(): void
    {
        Http::fake(function ($request) {
            if ($request->method() === 'GET') {
                return Http::response(['success' => true, 'obj' => [
                    'id' => 'uuid-1', 'email' => 'alice', 'totalGB' => 10_000_000_000,
                    'expiryTime' => now()->getTimestampMs(), 'enable' => true,
                ]], 200);
            }
            return Http::response(['success' => true, 'msg' => 'ok', 'obj' => null], 200);
        });

        [, $plan, $context] = $this->fixtures();
        $service = new Service(['metadata' => ['sanaei_email' => 'alice'], 'service_provider_id' => 1, 'provider_account_id' => 2, 'capacity' => 10]);
        $service->exists = true;

        $provider = new SanaeiProvider;
        self::assertSame(ServiceProviderOperation::RENEW, $provider->renew($service, $plan, $context)->operation);
        self::assertSame(ServiceProviderOperation::EXTEND, $provider->extend($service, 7, $context)->operation);
        self::assertSame(ServiceProviderOperation::ADD_CAPACITY, $provider->addCapacity($service, 100, $context)->operation);
        self::assertSame(ServiceProviderOperation::DISABLE, $provider->disable($service, $context)->operation);
        self::assertSame(ServiceProviderOperation::STATUS, $provider->status($service, $context)->operation);
        self::assertSame(ServiceProviderOperation::DELETE, $provider->delete($service, $context)->operation);
    }

    private function fixtures(): array
    {
        $provider = new ServiceProvider(['id' => 1, 'driver' => 'sanaei']);
        $provider->exists = true;

        $account = new TelegramAccount(['id' => 10, 'telegram_user_id' => 123456]);
        $account->exists = true;

        $plan = new Plan(['id' => 20, 'duration_value' => 30, 'capacity_value' => 50_000_000_000]);
        $plan->exists = true;

        $providerAccount = new ServiceProviderAccount(['id' => 2, 'service_provider_id' => 1]);
        $providerAccount->exists = true;
        $providerAccount->setSecureCredentials([
            'base_url' => 'https://sanaei.test',
            'token' => 'secret-token',
        ]);

        return [$account, $plan, new ServiceProviderContext($provider, $providerAccount, 'sanaei-key', [3])];
    }
}

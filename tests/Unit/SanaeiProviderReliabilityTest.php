<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\SanaeiApiException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\TelegramAccount;
use App\Services\Providers\Sanaei\SanaeiProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class SanaeiProviderReliabilityTest extends TestCase
{
    public function test_create_reads_back_remote_client_and_preserves_mapping(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/clients/add')) {
                return Http::response(['success' => true, 'obj' => ['created' => true]], 200);
            }
            return Http::response(['success' => true, 'obj' => ['id' => 'remote-77', 'email' => 'horton-10-abcd1234', 'enable' => true]], 200);
        });

        $result = $this->provider()->create($this->telegramAccount(), $this->plan(), $this->context());

        self::assertTrue($result->success);
        self::assertSame(ServiceProviderOperation::CREATE, $result->operation);
        self::assertSame('remote-77', $result->externalId);
        self::assertSame($result->externalReference, $result->data['email']);
        self::assertSame([11, 12], $result->data['inbound_ids']);
        self::assertCount(2, Http::recorded());
    }

    public function test_get_and_status_read_remote_state(): void
    {
        Http::fake(fn ($request) => Http::response(['success' => true, 'obj' => ['id' => 'remote-77', 'email' => 'alice@example.test', 'enable' => true, 'totalGB' => 50]], 200));
        $service = $this->service();
        $service->metadata = ['sanaei_email' => 'alice@example.test'];

        $get = $this->provider()->get($service, $this->context());
        $status = $this->provider()->status($service, $this->context());

        self::assertSame('remote-77', $get->externalId);
        self::assertSame('active', $status->data['status']);
        self::assertCount(2, Http::recorded());
    }

    public function test_disable_updates_remote_client_and_reports_disabled_state(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/clients/get/alice%40example.test')) {
                return Http::response(['success' => true, 'obj' => ['email' => 'alice@example.test', 'enable' => true]], 200);
            }
            return Http::response(['success' => true, 'obj' => ['updated' => true]], 200);
        });

        $result = $this->provider()->disable($this->service(), $this->context());
        self::assertSame('disabled', $result->data['status']);
        self::assertCount(2, Http::recorded());
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/clients/update/alice%40example.test') && $request['enable'] === false);
    }

    public function test_extend_and_capacity_use_remote_state_before_write(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/clients/get/')) {
                return Http::response(['success' => true, 'obj' => ['email' => 'alice@example.test', 'expiryTime' => now()->getTimestampMs() + 86400000, 'totalGB' => 20]], 200);
            }
            return Http::response(['success' => true, 'obj' => ['updated' => true]], 200);
        });

        $provider = $this->provider();
        $service = $this->service();
        $service->metadata = ['sanaei_email' => 'alice@example.test'];
        $provider->extend($service, 7, $this->context());
        $provider->addCapacity($service, 30, $this->context());

        self::assertCount(4, Http::recorded());
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/clients/update/alice%40example.test') && $request['totalGB'] === 50);
    }

    public function test_remote_api_failure_is_exposed_as_provider_exception_without_secret(): void
    {
        Http::fake(['https://sanaei.test/panel/api/clients/get/*' => Http::response(['success' => false, 'msg' => 'client not found'], 200)]);

        try {
            $this->provider()->get($this->service(), $this->context());
            self::fail('Expected SanaeiApiException.');
        } catch (SanaeiApiException $e) {
            self::assertStringContainsString('client not found', $e->getMessage());
            self::assertStringNotContainsString('secret-token', $e->getMessage());
        }
    }

    public function test_missing_sanaei_email_is_rejected_before_network_call(): void
    {
        $service = $this->service();
        $service->metadata = [];

        try {
            $this->provider()->get($service, $this->context());
            self::fail('Expected missing email exception.');
        } catch (SanaeiApiException $e) {
            self::assertStringContainsString('does not contain a Sanaei client email', $e->getMessage());
        }
        self::assertCount(0, Http::recorded());
    }

    private function provider(): SanaeiProvider { return app(SanaeiProvider::class); }

    private function context(): ServiceProviderContext
    {
        $provider = new ServiceProvider(['driver' => 'sanaei']);
        $provider->id = 30; $provider->exists = true;
        $account = new ServiceProviderAccount(['service_provider_id' => 30]);
        $account->id = 40; $account->exists = true;
        $account->setSecureCredentials(['base_url' => 'https://sanaei.test', 'token' => 'secret-token', 'timeout' => 5, 'connect_timeout' => 2, 'retry_times' => 0]);
        $account->setRelation('provider', $provider);
        return new ServiceProviderContext($provider, $account, 'test-key', [11, 12]);
    }

    private function telegramAccount(): TelegramAccount
    {
        $account = new TelegramAccount(['telegram_user_id' => 1001]);
        $account->id = 10; $account->exists = true;
        return $account;
    }

    private function plan(): Plan
    {
        $plan = new Plan(['duration_value' => 30, 'capacity_value' => 50, 'is_trial' => false]);
        $plan->id = 20; $plan->exists = true;
        return $plan;
    }

    private function service(): Service
    {
        $service = new Service(['service_provider_id' => 30, 'provider_account_id' => 40, 'status' => 'active', 'capacity' => 20]);
        $service->id = 50; $service->exists = true;
        $service->metadata = ['sanaei_email' => 'alice@example.test'];
        return $service;
    }
}

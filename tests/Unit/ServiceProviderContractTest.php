<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\TelegramAccount;
use App\Services\Providers\FakeServiceProvider;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ServiceProviderContractTest extends TestCase
{
    private function account(): TelegramAccount
    {
        $model = new TelegramAccount;
        $model->exists = true;
        $model->id = 10;
        return $model;
    }

    private function plan(): Plan
    {
        $model = new Plan(['duration_value' => 30, 'capacity_value' => 5, 'is_trial' => false]);
        $model->exists = true;
        $model->id = 20;
        return $model;
    }

    private function provider(): ServiceProvider
    {
        $model = new ServiceProvider(['driver' => 'fake']);
        $model->exists = true;
        $model->id = 30;
        return $model;
    }

    private function providerAccount(): ServiceProviderAccount
    {
        $model = new ServiceProviderAccount(['service_provider_id' => 30]);
        $model->exists = true;
        $model->id = 40;
        return $model;
    }

    private function service(): Service
    {
        $model = new Service(['service_provider_id' => 30, 'provider_account_id' => 40, 'status' => 'active', 'capacity' => 5]);
        $model->exists = true;
        $model->id = 50;
        return $model;
    }

    public function test_contract_exposes_all_lifecycle_operations(): void
    {
        $provider = new FakeServiceProvider;
        $context = new ServiceProviderContext($this->provider(), $this->providerAccount(), 'key-1');

        $this->assertTrue($provider->create($this->account(), $this->plan(), $context)->success);
        $service = $this->service();
        $this->assertSame('active', $provider->get($service, $context)->data['status']);
        $this->assertArrayHasKey('expires_at', $provider->renew($service, $this->plan(), $context->with(['renew' => true]))->data);
        $this->assertArrayHasKey('expires_at', $provider->extend($service, 7, $context)->data);
        $this->assertSame(7, $provider->addCapacity($service, 2, $context)->data['capacity']);
        $this->assertSame('disabled', $provider->disable($service, $context)->data['status']);
        $this->assertSame('deleted', $provider->delete($service, $context)->data['status']);
        $this->assertSame('active', $provider->status($service, $context)->data['status']);
    }

    public function test_create_is_idempotent_for_same_key(): void
    {
        $provider = new FakeServiceProvider;
        $context = new ServiceProviderContext($this->provider(), null, 'same-key');

        $first = $provider->create($this->account(), $this->plan(), $context);
        $second = $provider->create($this->account(), $this->plan(), $context);

        $this->assertSame($first->externalId, $second->externalId);
        $this->assertCount(2, $provider->calls());
    }

    public function test_different_keys_create_different_external_ids(): void
    {
        $provider = new FakeServiceProvider;
        $first = $provider->create($this->account(), $this->plan(), new ServiceProviderContext(null, null, 'a'));
        $second = $provider->create($this->account(), $this->plan(), new ServiceProviderContext(null, null, 'b'));
        $this->assertNotSame($first->externalId, $second->externalId);
    }

    public function test_failure_is_simulated_and_can_recover(): void
    {
        $provider = (new FakeServiceProvider)->fail(ServiceProviderOperation::CREATE, 'temporary failure');
        $context = new ServiceProviderContext(null, null, 'recover-key');

        $this->expectException(ServiceProviderException::class);
        $provider->create($this->account(), $this->plan(), $context);
    }

    public function test_failure_simulation_is_one_shot(): void
    {
        $provider = (new FakeServiceProvider)->fail(ServiceProviderOperation::CREATE);
        $context = new ServiceProviderContext(null, null, 'recover-key');
        try { $provider->create($this->account(), $this->plan(), $context); } catch (ServiceProviderException) {}
        $this->assertTrue($provider->create($this->account(), $this->plan(), $context)->success);
    }

    public function test_unpersisted_account_is_rejected(): void
    {
        $account = new TelegramAccount;
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->create($account, $this->plan());
    }

    public function test_unpersisted_plan_is_rejected(): void
    {
        $plan = new Plan(['duration_value' => 30]);
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->create($this->account(), $plan);
    }

    public function test_mismatched_provider_account_is_rejected(): void
    {
        $account = $this->providerAccount();
        $account->service_provider_id = 999;
        $context = new ServiceProviderContext($this->provider(), $account, 'mismatch');
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->create($this->account(), $this->plan(), $context);
    }

    public function test_invalid_provider_account_is_rejected(): void
    {
        $account = new ServiceProviderAccount(['service_provider_id' => 30]);
        $context = new ServiceProviderContext($this->provider(), $account);
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->create($this->account(), $this->plan(), $context);
    }

    public function test_service_must_be_persisted(): void
    {
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->status(new Service, new ServiceProviderContext($this->provider()));
    }

    public function test_service_provider_mismatch_is_rejected(): void
    {
        $service = $this->service();
        $service->service_provider_id = 999;
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->status($service, new ServiceProviderContext($this->provider()));
    }

    public function test_service_account_mismatch_is_rejected(): void
    {
        $service = $this->service();
        $service->provider_account_id = 999;
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->status($service, new ServiceProviderContext($this->provider(), $this->providerAccount()));
    }

    #[DataProvider('positiveValues')]
    public function test_extend_accepts_positive_days(int $days): void
    {
        $result = (new FakeServiceProvider)->extend($this->service(), $days);
        $this->assertNotNull($result->data['expires_at']);
    }

    public static function positiveValues(): array
    {
        return [[1], [7], [30], [365]];
    }

    #[DataProvider('nonPositiveValues')]
    public function test_extend_rejects_non_positive_days(int $days): void
    {
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->extend($this->service(), $days);
    }

    public static function nonPositiveValues(): array
    {
        return [[0], [-1], [-30]];
    }

    #[DataProvider('nonPositiveValues')]
    public function test_add_capacity_rejects_non_positive_values(int $capacity): void
    {
        $this->expectException(ServiceProviderException::class);
        (new FakeServiceProvider)->addCapacity($this->service(), $capacity);
    }

    public function test_context_with_preserves_original_context(): void
    {
        $context = new ServiceProviderContext($this->provider(), $this->providerAccount(), 'key', ['inbound'], ['a' => 1]);
        $next = $context->with(['b' => 2]);
        $this->assertSame('key', $next->idempotencyKey);
        $this->assertSame(['a' => 1, 'b' => 2], $next->attributes);
        $this->assertSame(['inbound'], $next->inbounds);
    }

    public function test_result_contains_operation_and_external_reference(): void
    {
        $result = (new FakeServiceProvider)->create($this->account(), $this->plan(), new ServiceProviderContext(null, null, 'ref'));
        $this->assertSame(ServiceProviderOperation::CREATE, $result->operation);
        $this->assertSame('ref', $result->externalReference);
        $this->assertTrue($result->success);
    }
}

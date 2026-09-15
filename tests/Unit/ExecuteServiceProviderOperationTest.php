<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\ExecuteServiceProviderOperation;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Jobs\ExecuteServiceProviderOperationJob;
use App\Models\Service;
use App\Services\Providers\ServiceProviderFactory;
use Tests\TestCase;

final class ExecuteServiceProviderOperationTest extends TestCase
{
    public function test_create_is_rejected_by_lifecycle_action(): void
    {
        $service = new Service;
        $service->exists = true;
        $service->id = 100;

        $this->expectException(ServiceProviderException::class);
        $this->expectExceptionMessage('Service has no provider instance.');

        app(ExecuteServiceProviderOperation::class)->execute($service, ServiceProviderOperation::GET);
    }

    public function test_job_carries_operation_and_retry_policy(): void
    {
        $job = new ExecuteServiceProviderOperationJob(
            100,
            ServiceProviderOperation::RENEW,
            200,
            30,
            'service:100:renew:200:30',
        );

        $this->assertSame(100, $job->serviceId);
        $this->assertSame(ServiceProviderOperation::RENEW, $job->operation);
        $this->assertSame(200, $job->planId);
        $this->assertSame(30, $job->value);
        $this->assertSame('service:100:renew:200:30', $job->idempotencyKey);
        $this->assertSame(3, $job->tries);
        $this->assertSame(120, $job->timeout);
        $this->assertSame([10, 60, 180], $job->backoff);
        $this->assertSame('providers', $job->queue);
    }

    public function test_action_is_container_resolvable(): void
    {
        $action = app(ExecuteServiceProviderOperation::class);
        $this->assertInstanceOf(ExecuteServiceProviderOperation::class, $action);
    }

    public function test_factory_is_injected_instead_of_provider_specific_business_logic(): void
    {
        $action = app(ExecuteServiceProviderOperation::class);
        $reflection = new \ReflectionClass($action);
        $property = $reflection->getProperty('factory');

        $this->assertSame(ServiceProviderFactory::class, $property->getType()?->getName());
    }
}

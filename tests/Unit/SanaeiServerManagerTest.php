<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Services\Providers\Sanaei\SanaeiServerManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class SanaeiServerManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        foreach (['service_operations', 'services', 'service_provider_accounts', 'service_providers'] as $table) Schema::dropIfExists($table);

        Schema::create('service_providers', function (Blueprint $t): void {
            $t->id(); $t->string('name'); $t->string('slug'); $t->string('driver'); $t->string('status')->default('active'); $t->timestamps();
        });
        Schema::create('service_provider_accounts', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('service_provider_id'); $t->string('name'); $t->text('credentials')->nullable(); $t->string('status')->default('active');
            $t->unsignedInteger('priority')->default(100); $t->json('metadata')->nullable(); $t->timestamps();
        });
        Schema::create('services', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('provider_account_id')->nullable(); $t->timestamps();
        });
        Schema::create('service_operations', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('provider_account_id')->nullable(); $t->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    protected function tearDown(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['service_operations', 'services', 'service_provider_accounts', 'service_providers'] as $table) Schema::dropIfExists($table);
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }

    public function test_create_encrypts_credentials_and_stores_server_metadata(): void
    {
        $provider = ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);

        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://sanaei.test/', 'token' => 'super-secret',
            'inbound_ids' => [1, 2, 2], 'region' => 'de', 'priority' => 10,
        ]);

        $this->assertSame($provider->id, $server->service_provider_id);
        $this->assertSame([1, 2], $server->metadata['inbound_ids']);
        $this->assertSame('de', $server->metadata['region']);
        $this->assertStringNotContainsString('super-secret', (string) $server->credentials);
        $this->assertSame('super-secret', $server->secureCredentials()['token']);
        $this->assertSame('https://sanaei.test', $server->secureCredentials()['base_url']);
    }

    public function test_update_preserves_existing_secret_when_token_is_omitted(): void
    {
        $provider = ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);
        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://old.test', 'token' => 'secret', 'inbound_ids' => [1],
        ]);

        app(SanaeiServerManager::class)->update($server, [
            'name' => 'Germany 02', 'base_url' => 'https://new.test', 'inbound_ids' => [3], 'status' => 'active', 'priority' => 20,
        ]);

        $fresh = $server->fresh();
        $this->assertSame('Germany 02', $fresh->name);
        $this->assertSame('https://new.test', $fresh->secureCredentials()['base_url']);
        $this->assertSame('secret', $fresh->secureCredentials()['token']);
        $this->assertSame([3], $fresh->metadata['inbound_ids']);
    }

    public function test_connection_test_and_health_check_are_recorded_without_logging_secret(): void
    {
        Http::fake(['https://sanaei.test/panel/api/server/status' => Http::response(['success' => true, 'obj' => ['cpu' => 1]], 200)]);
        ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);
        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://sanaei.test', 'token' => 'secret-token', 'inbound_ids' => [1],
        ]);

        $result = app(SanaeiServerManager::class)->healthCheck($server);
        $fresh = $server->fresh();

        $this->assertTrue($result['healthy']);
        $this->assertSame('healthy', $fresh->metadata['health']['status']);
        $this->assertNotEmpty($fresh->metadata['health']['checked_at']);
        $this->assertStringNotContainsString('secret-token', json_encode($fresh->metadata['health']));
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer secret-token'));
    }

    public function test_disable_and_activate_change_only_server_status(): void
    {
        ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);
        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://sanaei.test', 'token' => 'secret', 'inbound_ids' => [1],
        ]);

        app(SanaeiServerManager::class)->disable($server);
        $this->assertSame('disabled', $server->fresh()->status);
        app(SanaeiServerManager::class)->activate($server);
        $this->assertSame('active', $server->fresh()->status);
    }

    public function test_delete_is_blocked_when_server_has_dependencies(): void
    {
        $provider = ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);
        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://sanaei.test', 'token' => 'secret', 'inbound_ids' => [1],
        ]);
        $server->services()->create([]);

        $this->expectException(ValidationException::class);
        app(SanaeiServerManager::class)->delete($server);
        $this->assertDatabaseHas('service_provider_accounts', ['id' => $server->id]);
    }

    public function test_delete_succeeds_without_dependencies(): void
    {
        ServiceProvider::create(['name' => 'Sanaei', 'slug' => 'sanaei', 'driver' => 'sanaei']);
        $server = app(SanaeiServerManager::class)->create([
            'name' => 'Germany 01', 'base_url' => 'https://sanaei.test', 'token' => 'secret', 'inbound_ids' => [1],
        ]);

        app(SanaeiServerManager::class)->delete($server);
        $this->assertDatabaseMissing('service_provider_accounts', ['id' => $server->id]);
    }
}

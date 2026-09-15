<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

final class ProviderSchemaContractTest extends TestCase
{
    public function test_provider_architecture_migration_contains_required_tables(): void
    {
        $files = glob(base_path('database/migrations/*provider*php')) ?: [];
        $migration = implode("\n", array_map(static fn (string $file): string => (string) file_get_contents($file), $files));
        foreach (['provider_policies','provider_capabilities','provider_pools','provider_pool_items','provider_inbounds','provider_health_checks'] as $table) {
            $this->assertStringContainsString("Schema::create('{$table}'", $migration);
        }
    }

    public function test_provider_schema_preserves_priority_and_capacity_controls(): void
    {
        $files = glob(base_path('database/migrations/*provider*php')) ?: [];
        $migration = implode("\n", array_map(static fn (string $file): string => (string) file_get_contents($file), $files));
        foreach (['priority','max_users','max_services','max_traffic_bytes','health_status'] as $column) $this->assertStringContainsString("'{$column}'", $migration);
    }

    public function test_service_provisioning_key_is_unique(): void
    {
        $files = glob(base_path('database/migrations/*provider*php')) ?: [];
        $migration = implode("\n", array_map(static fn (string $file): string => (string) file_get_contents($file), $files));
        $this->assertStringContainsString("'provisioning_key'", $migration);
        $this->assertStringContainsString("->unique()", $migration);
    }
}

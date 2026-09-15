<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_providers')) {
            $this->ensureServiceProviderReferenceKey();

            Schema::table('service_providers', function (Blueprint $table): void {
                if (! Schema::hasColumn('service_providers', 'priority')) {
                    $table->unsignedInteger('priority')->default(100)->index();
                }
                if (! Schema::hasColumn('service_providers', 'region')) {
                    $table->string('region', 64)->nullable()->index();
                }
                if (! Schema::hasColumn('service_providers', 'max_users')) {
                    $table->unsignedInteger('max_users')->nullable();
                }
                if (! Schema::hasColumn('service_providers', 'max_services')) {
                    $table->unsignedInteger('max_services')->nullable();
                }
                if (! Schema::hasColumn('service_providers', 'max_traffic_bytes')) {
                    $table->unsignedBigInteger('max_traffic_bytes')->nullable();
                }
                if (! Schema::hasColumn('service_providers', 'health_status')) {
                    $table->string('health_status', 32)->default('unknown')->index();
                }
                if (! Schema::hasColumn('service_providers', 'last_error')) {
                    $table->text('last_error')->nullable();
                }
            });
        }

        if (! Schema::hasTable('provider_policies')) {
            Schema::create('provider_policies', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('service_provider_id')->constrained('service_providers')->cascadeOnDelete();
                $table->boolean('allow_create')->default(true);
                $table->boolean('allow_trial')->default(true);
                $table->boolean('allow_renew')->default(true);
                $table->boolean('allow_extend')->default(true);
                $table->boolean('allow_add_capacity')->default(true);
                $table->boolean('allow_disable')->default(true);
                $table->boolean('allow_delete')->default(true);
                $table->unsignedInteger('max_services_per_user')->nullable();
                $table->unsignedInteger('max_capacity_per_service')->nullable();
                $table->unsignedInteger('max_duration_days')->nullable();
                $table->json('rules')->nullable();
                $table->timestamps();
                $table->unique('service_provider_id');
            });
        }

        if (! Schema::hasTable('provider_capabilities')) {
            Schema::create('provider_capabilities', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('service_provider_id')->constrained('service_providers')->cascadeOnDelete();
                $table->string('operation', 32);
                $table->boolean('supported')->default(true);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->unique(['service_provider_id', 'operation'], 'provider_capability_unique');
                $table->index(['operation', 'supported']);
            });
        }

        if (! Schema::hasTable('provider_pools')) {
            Schema::create('provider_pools', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 255);
                $table->string('slug', 255)->unique();
                $table->string('selection_strategy', 32)->default('priority');
                $table->string('status', 32)->default('active')->index();
                $table->string('region', 64)->nullable()->index();
                $table->json('rules')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('provider_pool_items')) {
            Schema::create('provider_pool_items', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('provider_pool_id')->constrained('provider_pools')->cascadeOnDelete();
                $table->foreignId('service_provider_id')->constrained('service_providers')->cascadeOnDelete();
                $table->unsignedInteger('priority')->default(100);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['provider_pool_id', 'service_provider_id'], 'provider_pool_item_unique');
                $table->index(['provider_pool_id', 'is_active', 'priority']);
            });
        }

        if (! Schema::hasTable('provider_inbounds')) {
            Schema::create('provider_inbounds', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('service_provider_id')->constrained('service_providers')->cascadeOnDelete();
                $table->string('name', 255);
                $table->string('protocol', 64)->nullable();
                $table->string('tag', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('configuration')->nullable();
                $table->timestamps();
                $table->unique(['service_provider_id', 'name'], 'provider_inbound_unique');
                $table->index(['service_provider_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('provider_health_checks')) {
            Schema::create('provider_health_checks', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('service_provider_id')->constrained('service_providers')->cascadeOnDelete();
                $table->string('status', 32);
                $table->unsignedInteger('latency_ms')->nullable();
                $table->string('error_code', 64)->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('checked_at');
                $table->json('metadata')->nullable();
                $table->index(['service_provider_id', 'checked_at']);
                $table->index(['status', 'checked_at']);
            });
        }

        if (Schema::hasTable('services') && ! Schema::hasColumn('services', 'provisioning_key')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->string('provisioning_key', 128)->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'provisioning_key')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropUnique(['provisioning_key']);
                $table->dropColumn('provisioning_key');
            });
        }

        foreach (['provider_health_checks', 'provider_inbounds', 'provider_pool_items', 'provider_pools', 'provider_capabilities', 'provider_policies'] as $table) {
            Schema::dropIfExists($table);
        }

        if (Schema::hasTable('service_providers')) {
            Schema::table('service_providers', function (Blueprint $table): void {
                foreach (['priority', 'region', 'max_users', 'max_services', 'max_traffic_bytes', 'health_status', 'last_error'] as $column) {
                    if (Schema::hasColumn('service_providers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    private function ensureServiceProviderReferenceKey(): void
    {
        $uniqueIndexes = DB::select(
            "SHOW INDEX FROM `service_providers` WHERE `Column_name` = 'id' AND `Non_unique` = 0"
        );

        if ($uniqueIndexes !== []) {
            return;
        }

        $duplicate = DB::selectOne(
            "SELECT `id`, COUNT(*) AS `aggregate` FROM `service_providers` GROUP BY `id` HAVING COUNT(*) > 1 LIMIT 1"
        );

        if ($duplicate !== null) {
            throw new RuntimeException(
                'Cannot add the service_providers.id primary key because duplicate ids exist: '.$duplicate->id
            );
        }

        DB::statement('ALTER TABLE `service_providers` ADD PRIMARY KEY (`id`)');
    }
};

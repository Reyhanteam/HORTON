<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_operations')) {
            return;
        }

        Schema::table('service_operations', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_operations', 'service_provider_id')) {
                $table->unsignedBigInteger('service_provider_id')->nullable()->after('service_id');
                $table->index('service_provider_id');
                $table->foreign('service_provider_id')->references('id')->on('service_providers')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_operations', 'provider_account_id')) {
                $table->unsignedBigInteger('provider_account_id')->nullable()->after('service_provider_id');
                $table->index('provider_account_id');
                $table->foreign('provider_account_id')->references('id')->on('service_provider_accounts')->nullOnDelete();
            }
        });

        // A nullable UNIQUE key still permits multiple NULLs, while making every
        // actual provisioning operation idempotent across workers/processes.
        try {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->unique('idempotency_key', 'service_operations_idempotency_unique');
            });
        } catch (\Throwable) {
            // The source schema may already contain the unique index on an upgraded installation.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('service_operations')) {
            return;
        }

        try {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->dropUnique('service_operations_idempotency_unique');
            });
        } catch (\Throwable) {
        }

        Schema::table('service_operations', function (Blueprint $table): void {
            foreach (['provider_account_id', 'service_provider_id'] as $column) {
                if (Schema::hasColumn('service_operations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

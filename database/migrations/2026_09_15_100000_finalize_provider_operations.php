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
        if (! Schema::hasTable('service_operations')) {
            return;
        }

        if (Schema::hasTable('service_provider_accounts')) {
            $this->ensureServiceProviderAccountReferenceKey();
        }

        if (! Schema::hasColumn('service_operations', 'service_provider_id')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->unsignedBigInteger('service_provider_id')->nullable()->after('service_id');
                $table->index('service_provider_id');
            });
        } elseif (! $this->hasIndexForColumn('service_operations', 'service_provider_id')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->index('service_provider_id');
            });
        }

        if (! Schema::hasColumn('service_operations', 'provider_account_id')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->unsignedBigInteger('provider_account_id')->nullable()->after('service_provider_id');
                $table->index('provider_account_id');
            });
        } elseif (! $this->hasIndexForColumn('service_operations', 'provider_account_id')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->index('provider_account_id');
            });
        }

        if (Schema::hasTable('service_providers') && ! $this->hasForeignKey('service_operations', 'service_operations_service_provider_id_foreign')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->foreign('service_provider_id', 'service_operations_service_provider_id_foreign')
                    ->references('id')->on('service_providers')->nullOnDelete();
            });
        }

        if (Schema::hasTable('service_provider_accounts') && ! $this->hasForeignKey('service_operations', 'service_operations_provider_account_id_foreign')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->foreign('provider_account_id', 'service_operations_provider_account_id_foreign')
                    ->references('id')->on('service_provider_accounts')->nullOnDelete();
            });
        }

        // A nullable UNIQUE key still permits multiple NULLs, while making every
        // actual provisioning operation idempotent across workers/processes.
        if (! $this->hasIndex('service_operations', 'service_operations_idempotency_unique')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->unique('idempotency_key', 'service_operations_idempotency_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('service_operations')) {
            return;
        }

        if ($this->hasForeignKey('service_operations', 'service_operations_provider_account_id_foreign')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->dropForeign('service_operations_provider_account_id_foreign');
            });
        }

        if ($this->hasForeignKey('service_operations', 'service_operations_service_provider_id_foreign')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->dropForeign('service_operations_service_provider_id_foreign');
            });
        }

        if ($this->hasIndex('service_operations', 'service_operations_idempotency_unique')) {
            Schema::table('service_operations', function (Blueprint $table): void {
                $table->dropUnique('service_operations_idempotency_unique');
            });
        }

        Schema::table('service_operations', function (Blueprint $table): void {
            foreach (['provider_account_id', 'service_provider_id'] as $column) {
                if (Schema::hasColumn('service_operations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function ensureServiceProviderAccountReferenceKey(): void
    {
        $uniqueIndexes = DB::select(
            "SHOW INDEX FROM `service_provider_accounts` WHERE `Column_name` = 'id' AND `Non_unique` = 0"
        );

        if ($uniqueIndexes !== []) {
            return;
        }

        $duplicate = DB::selectOne(
            "SELECT `id`, COUNT(*) AS `aggregate` FROM `service_provider_accounts` GROUP BY `id` HAVING COUNT(*) > 1 LIMIT 1"
        );

        if ($duplicate !== null) {
            throw new RuntimeException(
                'Cannot add the service_provider_accounts.id primary key because duplicate ids exist: '.$duplicate->id
            );
        }

        DB::statement('ALTER TABLE `service_provider_accounts` ADD PRIMARY KEY (`id`)');
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        return DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $constraint]
        ) !== null;
    }

    private function hasIndex(string $table, string $index): bool
    {
        return DB::selectOne(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        ) !== null;
    }

    private function hasIndexForColumn(string $table, string $column): bool
    {
        return DB::selectOne(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND INDEX_NAME <> ? LIMIT 1',
            [$table, $column, 'PRIMARY']
        ) !== null;
    }
};

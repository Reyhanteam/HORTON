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
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'id')) {
            return;
        }

        $this->normalizeUsersPrimaryKey();
        $this->normalizeRoleUserPivot();
        $this->normalizeAuditLogsOwner();
    }

    public function down(): void
    {
        // This migration repairs compatibility with the canonical HORTON identity
        // schema and must not destructively restore the legacy admin_users links.
    }

    private function normalizeUsersPrimaryKey(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            'ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
        );
    }

    private function normalizeRoleUserPivot(): void
    {
        if (! Schema::hasTable('role_user')) {
            return;
        }

        $hasLegacyColumn = Schema::hasColumn('role_user', 'admin_user_id');
        $hasCanonicalColumn = Schema::hasColumn('role_user', 'user_id');

        if ($hasLegacyColumn && ! $hasCanonicalColumn) {
            $this->dropForeignKeyIfExists('role_user', 'role_user_admin_user_id_foreign');

            if (DB::getDriverName() === 'mysql') {
                DB::statement(
                    'ALTER TABLE `role_user` CHANGE `admin_user_id` `user_id` BIGINT UNSIGNED NOT NULL'
                );
            } else {
                Schema::table('role_user', function (Blueprint $table): void {
                    $table->renameColumn('admin_user_id', 'user_id');
                });
            }
        }

        if (Schema::hasColumn('role_user', 'user_id')) {
            $this->addForeignKeyIfMissing(
                table: 'role_user',
                column: 'user_id',
                referencedTable: 'users',
                referencedColumn: 'id',
                constraint: 'role_user_user_id_foreign',
                onDelete: 'cascade',
            );
        }
    }

    private function normalizeAuditLogsOwner(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        $hasLegacyColumn = Schema::hasColumn('audit_logs', 'admin_user_id');
        $hasCanonicalColumn = Schema::hasColumn('audit_logs', 'user_id');

        if ($hasLegacyColumn && ! $hasCanonicalColumn) {
            $this->dropForeignKeyIfExists('audit_logs', 'audit_logs_admin_user_id_foreign');

            if (DB::getDriverName() === 'mysql') {
                DB::statement(
                    'ALTER TABLE `audit_logs` CHANGE `admin_user_id` `user_id` BIGINT UNSIGNED NULL'
                );
            } else {
                Schema::table('audit_logs', function (Blueprint $table): void {
                    $table->renameColumn('admin_user_id', 'user_id');
                });
            }
        }

        if (Schema::hasColumn('audit_logs', 'user_id')) {
            $this->addForeignKeyIfMissing(
                table: 'audit_logs',
                column: 'user_id',
                referencedTable: 'users',
                referencedColumn: 'id',
                constraint: 'audit_logs_user_id_foreign',
                onDelete: 'set null',
            );
        }
    }

    private function dropForeignKeyIfExists(string $table, string $constraint): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                $table,
                $constraint,
            ));
        }
    }

    private function addForeignKeyIfMissing(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $constraint,
        string $onDelete,
    ): void {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s',
            $table,
            $constraint,
            $column,
            $referencedTable,
            $referencedColumn,
            strtoupper($onDelete),
        ));
    }
};

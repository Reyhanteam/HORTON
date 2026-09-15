<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            $this->moveRoleOwnershipToUsers();
            $this->moveAuditLogOwnershipToUsers();
            Schema::dropIfExists('admin_users');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        Schema::create('admin_users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        if (Schema::hasTable('role_user') && Schema::hasColumn('role_user', 'user_id')) {
            $this->dropForeignKeys('role_user', 'user_id', 'users');
            DB::statement('ALTER TABLE `role_user` CHANGE `user_id` `admin_user_id` BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'user_id')) {
            $this->dropForeignKeys('audit_logs', 'user_id', 'users');
            DB::statement('ALTER TABLE `audit_logs` CHANGE `user_id` `admin_user_id` BIGINT UNSIGNED NULL');
        }
    }

    private function moveRoleOwnershipToUsers(): void
    {
        if (! Schema::hasTable('role_user') || ! Schema::hasColumn('role_user', 'admin_user_id')) {
            return;
        }

        $this->dropForeignKeys('role_user', 'admin_user_id', 'admin_users');
        DB::statement('ALTER TABLE `role_user` CHANGE `admin_user_id` `user_id` BIGINT UNSIGNED NOT NULL');
    }

    private function moveAuditLogOwnershipToUsers(): void
    {
        if (! Schema::hasTable('audit_logs') || ! Schema::hasColumn('audit_logs', 'admin_user_id')) {
            return;
        }

        $this->dropForeignKeys('audit_logs', 'admin_user_id', 'admin_users');
        DB::statement('ALTER TABLE `audit_logs` CHANGE `admin_user_id` `user_id` BIGINT UNSIGNED NULL');

        if (Schema::hasTable('users')) {
            DB::statement(
                'UPDATE `audit_logs` AS `logs` LEFT JOIN `users` ON `users`.`id` = `logs`.`user_id` SET `logs`.`user_id` = `users`.`id`'
            );
        }
    }

    private function dropForeignKeys(string $table, string $column, string $referencedTable): void
    {
        $constraints = DB::select(
            <<<'SQL'
            SELECT DISTINCT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME = ?
            SQL,
            [$table, $column, $referencedTable]
        );

        foreach ($constraints as $constraint) {
            DB::statement('ALTER TABLE `'.$table.'` DROP FOREIGN KEY `'.$constraint->CONSTRAINT_NAME.'`');
        }
    }
};

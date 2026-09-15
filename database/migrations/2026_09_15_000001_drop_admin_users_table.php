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

        if (! Schema::hasTable('audit_logs') || ! Schema::hasColumn('audit_logs', 'user_id')) {
            return;
        }

        $constraints = DB::select(
            <<<'SQL'
            SELECT DISTINCT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'audit_logs'
              AND COLUMN_NAME = 'user_id'
            SQL
        );

        foreach ($constraints as $constraint) {
            DB::statement('ALTER TABLE `audit_logs` DROP FOREIGN KEY `'.$constraint->CONSTRAINT_NAME.'`');
        }

        DB::statement('ALTER TABLE `audit_logs` CHANGE `user_id` `admin_user_id` BIGINT UNSIGNED NULL');
    }

    private function moveAuditLogOwnershipToUsers(): void
    {
        if (! Schema::hasTable('audit_logs') || ! Schema::hasColumn('audit_logs', 'admin_user_id')) {
            return;
        }

        $constraints = DB::select(
            <<<'SQL'
            SELECT DISTINCT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'audit_logs'
              AND COLUMN_NAME = 'admin_user_id'
              AND REFERENCED_TABLE_NAME = 'admin_users'
            SQL
        );

        foreach ($constraints as $constraint) {
            DB::statement('ALTER TABLE `audit_logs` DROP FOREIGN KEY `'.$constraint->CONSTRAINT_NAME.'`');
        }

        DB::statement('ALTER TABLE `audit_logs` CHANGE `admin_user_id` `user_id` BIGINT UNSIGNED NULL');

        if (Schema::hasTable('users')) {
            DB::statement(
                'UPDATE `audit_logs` AS `logs` LEFT JOIN `users` ON `users`.`id` = `logs`.`user_id` SET `logs`.`user_id` = `users`.`id`'
            );
        }
    }
};

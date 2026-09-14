<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'status')) {
                    $table->string('status', 32)->default('active')->index();
                }

                if (! Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->index();
                }
            });
        }

        if (Schema::hasTable('telegram_accounts')) {
            Schema::table('telegram_accounts', function (Blueprint $table): void {
                if (! Schema::hasColumn('telegram_accounts', 'registration_status')) {
                    $table->string('registration_status', 32)->default('pending')->index();
                }

                if (! Schema::hasColumn('telegram_accounts', 'registered_at')) {
                    $table->timestamp('registered_at')->nullable();
                }

                if (! Schema::hasColumn('telegram_accounts', 'activated_at')) {
                    $table->timestamp('activated_at')->nullable();
                }

                if (! Schema::hasColumn('telegram_accounts', 'deactivated_at')) {
                    $table->timestamp('deactivated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('telegram_accounts')) {
            Schema::table('telegram_accounts', function (Blueprint $table): void {
                foreach (['registration_status', 'registered_at', 'activated_at', 'deactivated_at'] as $column) {
                    if (Schema::hasColumn('telegram_accounts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                foreach (['status', 'last_login_at'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

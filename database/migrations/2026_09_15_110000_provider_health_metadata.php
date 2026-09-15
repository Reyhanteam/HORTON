<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_providers')) return;
        Schema::table('service_providers', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_providers', 'last_health_check_at')) $table->timestamp('last_health_check_at')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('service_providers')) return;
        Schema::table('service_providers', function (Blueprint $table): void {
            if (Schema::hasColumn('service_providers', 'last_health_check_at')) $table->dropColumn('last_health_check_at');
        });
    }
};

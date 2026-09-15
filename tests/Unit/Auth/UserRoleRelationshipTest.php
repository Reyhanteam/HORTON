<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class UserRoleRelationshipTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (['audit_logs', 'role_user', 'roles', 'users'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_user_roles_relation_uses_the_canonical_user_id_pivot_key(): void
    {
        $relation = (new User())->roles();

        self::assertSame('role_user', $relation->getTable());
        self::assertSame('user_id', $relation->getForeignPivotKeyName());
        self::assertSame('role_id', $relation->getRelatedPivotKeyName());
    }

    public function test_role_users_relation_uses_the_canonical_user_id_pivot_key(): void
    {
        $relation = (new Role())->users();

        self::assertSame('role_user', $relation->getTable());
        self::assertSame('role_id', $relation->getForeignPivotKeyName());
        self::assertSame('user_id', $relation->getRelatedPivotKeyName());
    }

    public function test_audit_log_relation_uses_the_canonical_user_id_column(): void
    {
        $relation = (new AuditLog())->user();

        self::assertSame('user_id', $relation->getForeignKeyName());
    }

    public function test_identity_migration_converts_legacy_admin_user_pivots(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('role_user', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('admin_user_id');
            $table->primary(['role_id', 'admin_user_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->string('action');
        });

        DB::table('users')->insert(['id' => 1, 'name' => 'Horton Admin']);
        DB::table('roles')->insert(['id' => 1, 'name' => 'super-admin']);
        DB::table('role_user')->insert(['role_id' => 1, 'admin_user_id' => 1]);
        DB::table('audit_logs')->insert(['id' => 1, 'admin_user_id' => 1, 'action' => 'test']);

        $migration = require base_path(
            'database/migrations/2026_09_15_120000_normalize_dashboard_identity_schema.php'
        );

        $migration->up();

        self::assertTrue(Schema::hasColumn('role_user', 'user_id'));
        self::assertFalse(Schema::hasColumn('role_user', 'admin_user_id'));
        self::assertTrue(Schema::hasColumn('audit_logs', 'user_id'));
        self::assertFalse(Schema::hasColumn('audit_logs', 'admin_user_id'));

        self::assertDatabaseHas('role_user', [
            'role_id' => 1,
            'user_id' => 1,
        ]);

        self::assertDatabaseHas('audit_logs', [
            'id' => 1,
            'user_id' => 1,
        ]);
    }
}

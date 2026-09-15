<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

final class UserRoleRelationshipTest extends TestCase
{
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
}

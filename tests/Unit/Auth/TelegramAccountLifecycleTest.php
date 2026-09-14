<?php

namespace Tests\Unit\Auth;

use App\Models\TelegramAccount;
use Tests\TestCase;

final class TelegramAccountLifecycleTest extends TestCase
{
    public function test_pending_account_cannot_use_bot(): void
    {
        $account = new TelegramAccount([
            'registration_status' => 'pending',
            'is_active' => false,
        ]);

        self::assertFalse($account->isRegistered());
        self::assertFalse($account->isActive());
        self::assertFalse($account->canUseBot());
    }

    public function test_registered_but_inactive_account_cannot_use_bot(): void
    {
        $account = new TelegramAccount([
            'registration_status' => 'registered',
            'is_active' => false,
        ]);

        self::assertTrue($account->isRegistered());
        self::assertFalse($account->isActive());
        self::assertFalse($account->canUseBot());
    }

    public function test_active_account_can_use_bot(): void
    {
        $account = new TelegramAccount([
            'registration_status' => 'active',
            'is_active' => true,
        ]);

        self::assertTrue($account->isRegistered());
        self::assertTrue($account->isActive());
        self::assertTrue($account->canUseBot());
    }
}

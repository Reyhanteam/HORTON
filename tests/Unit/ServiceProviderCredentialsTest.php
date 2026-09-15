<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ServiceProviderAccount;
use Tests\TestCase;

final class ServiceProviderCredentialsTest extends TestCase
{
    public function test_credentials_are_encrypted_at_rest(): void
    {
        $account = new ServiceProviderAccount;
        $account->setSecureCredentials(['url' => 'https://panel.example', 'username' => 'admin', 'password' => 'secret-value']);
        $this->assertIsString($account->credentials);
        $this->assertStringNotContainsString('secret-value', $account->credentials);
        $this->assertSame('secret-value', $account->secureCredentials()['password']);
    }

    public function test_empty_credentials_are_safe(): void
    {
        $account = new ServiceProviderAccount;
        $account->credentials = null;
        $this->assertSame([], $account->secureCredentials());
    }
}

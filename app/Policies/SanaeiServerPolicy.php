<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceProviderAccount;
use App\Models\User;

final class SanaeiServerPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'provider-servers.view');
    }

    public function view(User $user, ServiceProviderAccount $server): bool
    {
        return $this->isSanaei($server) && $this->allows($user, 'provider-servers.view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'provider-servers.create');
    }

    public function update(User $user, ServiceProviderAccount $server): bool
    {
        return $this->isSanaei($server) && $this->allows($user, 'provider-servers.update');
    }

    public function delete(User $user, ServiceProviderAccount $server): bool
    {
        return $this->isSanaei($server) && $this->allows($user, 'provider-servers.delete');
    }

    public function connectionTest(User $user, ServiceProviderAccount $server): bool
    {
        return $this->isSanaei($server) && $this->allows($user, 'provider-servers.test');
    }

    public function healthCheck(User $user, ServiceProviderAccount $server): bool
    {
        return $this->isSanaei($server) && $this->allows($user, 'provider-servers.health');
    }

    private function isSanaei(ServiceProviderAccount $server): bool
    {
        return $server->provider?->driver === 'sanaei';
    }

    private function allows(User $user, string $permission): bool
    {
        return $user->canAccessDashboard()
            && ($user->hasPermission($permission) || $user->hasRole('super-admin'));
    }
}

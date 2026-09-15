<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        if (! $user) {
            $email = env('ADMIN_EMAIL');
            $password = env('ADMIN_PASSWORD');

            if (! $email || ! $password) {
                return;
            }

            $user = User::create([
                'name' => env('ADMIN_NAME', 'Horton Admin'),
                'email' => $email,
                'password' => $password,
                'status' => 'active',
            ]);
        }

        $role = Role::query()->firstOrCreate([
            'name' => 'super-admin',
        ]);

        $permissions = [
            'provider-servers.view',
            'provider-servers.create',
            'provider-servers.update',
            'provider-servers.delete',
            'provider-servers.test',
            'provider-servers.health',
        ];

        $permissionIds = collect($permissions)
            ->map(fn (string $name): int => Permission::query()->firstOrCreate(['name' => $name])->getKey())
            ->all();

        $role->permissions()->syncWithoutDetaching($permissionIds);
        $user->roles()->syncWithoutDetaching([$role->getKey()]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = AdminUser::query()->first();

        if (! $admin) {
            $email = env('ADMIN_EMAIL');
            $password = env('ADMIN_PASSWORD');

            if (! $email || ! $password) {
                return;
            }

            $admin = AdminUser::create([
                'name' => env('ADMIN_NAME', 'Horton Admin'),
                'email' => $email,
                'password' => $password,
                'status' => 'active',
            ]);
        }

        $role = Role::query()->firstOrCreate([
            'name' => 'super-admin',
        ]);

        $admin->roles()->syncWithoutDetaching([$role->getKey()]);
    }
}

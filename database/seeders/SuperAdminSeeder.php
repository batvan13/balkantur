<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed a single platform super admin (idempotent by email).
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );
    }
}

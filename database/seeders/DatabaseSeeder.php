<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Super Admin Account
        User::firstOrCreate(
            ['email' => 'superadmin@mon-activite.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SUPER_ADMIN,
                'is_active' => true,
            ]
        );

        // Default Admin / Owner Account
        User::firstOrCreate(
            ['email' => 'admin@mon-activite.com'],
            [
                'name' => 'Propriétaire Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
            ]
        );

        // Default Cashier Account
        User::firstOrCreate(
            ['email' => 'cashier@mon-activite.com'],
            [
                'name' => 'Caissier Principal',
                'password' => Hash::make('password'),
                'role' => UserRole::CASHIER,
                'is_active' => true,
            ]
        );
    }
}

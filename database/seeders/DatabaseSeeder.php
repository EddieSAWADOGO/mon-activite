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

        // Demo Suppliers
        \App\Domain\Fournisseurs\Models\Supplier::firstOrCreate(
            ['name' => 'SODEFA Agro-Chimie'],
            [
                'type' => 'company',
                'phone' => '+229 97 00 11 22',
                'whatsapp' => '+229 97 00 11 22',
                'address' => 'Cotonou, Zone Industrielle',
                'contact_person' => 'M. Dossou',
                'email' => 'contact@sodefa.com',
                'notes' => 'Fournisseur principal d\'engrais et d\'intrants agricoles',
                'is_active' => true,
            ]
        );

        \App\Domain\Fournisseurs\Models\Supplier::firstOrCreate(
            ['name' => 'Etablissements BioPhyto'],
            [
                'type' => 'company',
                'phone' => '+229 95 33 44 55',
                'address' => 'Parakou, Quartier Guéma',
                'contact_person' => 'Mme Bio',
                'is_active' => true,
            ]
        );
    }
}

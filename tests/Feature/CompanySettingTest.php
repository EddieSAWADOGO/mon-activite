<?php

namespace Tests\Feature;

use App\Domain\Settings\Models\CompanySetting;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_company_settings_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->get(route('settings.company.edit'));

        $response->assertStatus(200);
        $response->assertSee('Paramètres de l\'Entreprise Émettrice');
    }

    public function test_cashier_cannot_access_company_settings_page(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $response = $this->actingAs($cashier)->get(route('settings.company.edit'));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_company_settings(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->put(route('settings.company.update'), [
            'name' => 'ENTREPRISE TEST SARL',
            'tagline' => 'Négoce & Intrants',
            'ifu' => '1234567890',
            'rccm' => 'BF OUA 2026 B 9999',
            'address' => 'Avenue Babanguida, Ouagadougou',
            'phone' => '+226 25 00 00 00',
            'email' => 'contact@test.bf',
            'bank_details' => 'BOA: 01001 0000000 12',
        ]);

        $response->assertRedirect(route('settings.company.edit'));
        $response->assertSessionHas('success');

        $setting = CompanySetting::first();
        $this->assertEquals('ENTREPRISE TEST SARL', $setting->name);
        $this->assertEquals('1234567890', $setting->ifu);
    }
}

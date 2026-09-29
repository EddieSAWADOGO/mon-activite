<?php

namespace Tests\Feature;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Paiements\Models\Payment;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasePaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Supplier $supplier;
    private Purchase $purchase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->cashier = User::factory()->create([
            'role' => UserRole::CASHIER,
        ]);

        $this->supplier = Supplier::factory()->create();

        $this->purchase = Purchase::create([
            'purchase_number' => 'ACH-20260101-0001',
            'supplier_id' => $this->supplier->id,
            'total_amount' => 500000,
            'paid_amount' => 200000,
            'remaining_amount' => 300000,
            'purchase_date' => now()->toDateString(),
            'created_by_user_id' => $this->admin->id,
            'notes' => 'Achat test avec acompte',
        ]);
    }

    public function test_admin_can_access_purchase_payment_creation_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('paiements.create-purchase', $this->purchase));

        $response->assertStatus(200);
        $response->assertSee('ACH-20260101-0001');
        $response->assertSee('Enregistrer un règlement Fournisseur');
    }

    public function test_cannot_access_purchase_payment_page_if_already_fully_paid(): void
    {
        $this->purchase->update([
            'paid_amount' => 500000,
            'remaining_amount' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('paiements.create-purchase', $this->purchase));

        $response->assertRedirect(route('achats.show', $this->purchase));
        $response->assertSessionHas('error');
    }

    public function test_partial_purchase_payment_updates_paid_and_remaining_amounts(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store-purchase', $this->purchase), [
                'purchase_id' => $this->purchase->id,
                'amount' => 150000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Virement Bancaire',
                'reference' => 'VIR-REG-88',
                'notes' => 'Règlement partiel de la dette supplier',
            ]);

        $response->assertRedirect(route('achats.show', $this->purchase));

        $this->assertDatabaseHas('payments', [
            'purchase_id' => $this->purchase->id,
            'amount' => 150000,
            'payment_method' => 'Virement Bancaire',
            'reference' => 'VIR-REG-88',
        ]);

        $this->purchase->refresh();

        $this->assertEquals(350000, $this->purchase->paid_amount);
        $this->assertEquals(150000, $this->purchase->remaining_amount);
    }

    public function test_full_purchase_payment_clears_remaining_amount(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store-purchase', $this->purchase), [
                'purchase_id' => $this->purchase->id,
                'amount' => 300000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $response->assertRedirect(route('achats.show', $this->purchase));

        $this->purchase->refresh();

        $this->assertEquals(500000, $this->purchase->paid_amount);
        $this->assertEquals(0, $this->purchase->remaining_amount);
    }

    public function test_purchase_payment_exceeding_remaining_amount_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('paiements.store-purchase', $this->purchase), [
                'purchase_id' => $this->purchase->id,
                'amount' => 400000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, Payment::where('purchase_id', $this->purchase->id)->count());
    }

    public function test_cashier_cannot_record_purchase_payment(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('paiements.store-purchase', $this->purchase), [
                'purchase_id' => $this->purchase->id,
                'amount' => 100000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $response->assertStatus(403);
    }
}

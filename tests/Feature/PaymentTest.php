<?php

namespace Tests\Feature;

use App\Domain\Clients\Models\Customer;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Paiements\Models\Payment;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Ventes\Models\Sale;
use App\Models\User;
use App\Support\Enums\InvoiceStatus;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private User $admin;
    private Invoice $invoice;
    private Sale $sale;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->create([
            'role' => UserRole::CASHIER,
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $product = Product::factory()->create();
        $unit = StockUnit::factory()->create([
            'product_id' => $product->id,
            'is_base_unit' => true,
            'current_stock' => 100,
            'default_selling_price' => 5000,
        ]);

        $customer = Customer::factory()->create();

        $this->sale = Sale::create([
            'sale_number' => 'VNT-20260101-0001',
            'customer_id' => $customer->id,
            'total_amount' => 10000,
            'paid_amount' => 0,
            'remaining_amount' => 10000,
            'sale_date' => now(),
            'created_by_user_id' => $this->cashier->id,
        ]);

        $this->invoice = Invoice::create([
            'invoice_number' => 'FAC-20260101-0001',
            'sale_id' => $this->sale->id,
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'subtotal_amount' => 10000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 10000,
            'paid_amount' => 0,
            'remaining_amount' => 10000,
            'status' => InvoiceStatus::UNPAID,
            'created_by_user_id' => $this->cashier->id,
        ]);
    }

    public function test_cashier_can_access_payment_creation_page(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('paiements.create', $this->invoice));

        $response->assertStatus(200);
        $response->assertSee('FAC-20260101-0001');
    }

    public function test_partial_payment_updates_invoice_and_sale_status(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('paiements.store'), [
                'invoice_id' => $this->invoice->id,
                'amount' => 4000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Mobile Money',
                'reference' => 'MM-123456',
            ]);

        $response->assertRedirect(route('factures.show', $this->invoice));

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 4000,
            'payment_method' => 'Mobile Money',
            'reference' => 'MM-123456',
        ]);

        $this->invoice->refresh();
        $this->sale->refresh();

        $this->assertEquals(4000, $this->invoice->paid_amount);
        $this->assertEquals(6000, $this->invoice->remaining_amount);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID, $this->invoice->status);

        $this->assertEquals(4000, $this->sale->paid_amount);
        $this->assertEquals(6000, $this->sale->remaining_amount);
    }

    public function test_full_payment_marks_invoice_as_paid(): void
    {
        $this->actingAs($this->cashier)
            ->post(route('paiements.store'), [
                'invoice_id' => $this->invoice->id,
                'amount' => 10000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $this->invoice->refresh();

        $this->assertEquals(10000, $this->invoice->paid_amount);
        $this->assertEquals(0, $this->invoice->remaining_amount);
        $this->assertEquals(InvoiceStatus::PAID, $this->invoice->status);
    }

    public function test_payment_exceeding_remaining_amount_is_rejected(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('paiements.store'), [
                'invoice_id' => $this->invoice->id,
                'amount' => 15000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, Payment::count());
    }

    public function test_multiple_payments_accumulate_correctly(): void
    {
        $this->actingAs($this->cashier)
            ->post(route('paiements.store'), [
                'invoice_id' => $this->invoice->id,
                'amount' => 3000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Espèces',
            ]);

        $this->actingAs($this->cashier)
            ->post(route('paiements.store'), [
                'invoice_id' => $this->invoice->id,
                'amount' => 7000,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Virement Bancaire',
            ]);

        $this->invoice->refresh();

        $this->assertEquals(2, Payment::count());
        $this->assertEquals(10000, $this->invoice->paid_amount);
        $this->assertEquals(0, $this->invoice->remaining_amount);
        $this->assertEquals(InvoiceStatus::PAID, $this->invoice->status);
    }
}

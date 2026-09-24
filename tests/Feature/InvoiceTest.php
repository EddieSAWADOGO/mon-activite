<?php

namespace Tests\Feature;

use App\Domain\Facturation\Models\Invoice;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Ventes\Services\SaleService;
use App\Models\User;
use App\Support\Enums\UserRole;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $product = Product::factory()->create();
        $unit = StockUnit::create([
            'product_id' => $product->id,
            'name' => 'Bidon 1L',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'default_selling_price' => 5000,
            'low_stock_threshold' => 5,
            'current_stock' => 20,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'sale_date' => now()->toDateTimeString(),
            'paid_amount' => 10000,
            'lines' => [
                [
                    'product_id' => $product->id,
                    'stock_unit_id' => $unit->id,
                    'quantity' => 2,
                    'unit_price' => 5000,
                    'discount_reason' => '',
                ]
            ]
        ], $this->cashier);

        $this->invoice = $sale->invoice;
    }

    public function test_invoice_view_reconstructs_from_db(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('factures.show', $this->invoice));

        $response->assertStatus(200);
        $response->assertSee($this->invoice->invoice_number);
        $response->assertSee('10 000 FCFA');
    }

    public function test_invoice_is_immutable_and_throws_exception_on_protected_update(): void
    {
        $this->expectException(DomainException::class);

        // Trying to change total_amount must throw DomainException
        $this->invoice->update([
            'total_amount' => 5000,
        ]);
    }

    public function test_invoice_is_immutable_and_throws_exception_on_delete(): void
    {
        $this->expectException(DomainException::class);

        $this->invoice->delete();
    }

    public function test_pdf_generation_route(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('factures.pdf', $this->invoice));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_whatsapp_link_generation(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('factures.whatsapp', $this->invoice));

        $response->assertRedirect();
        $this->assertStringContainsString('https://wa.me/', $response->headers->get('Location'));
    }
}

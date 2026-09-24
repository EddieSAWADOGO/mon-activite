<?php

namespace App\Providers;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Achats\Policies\PurchasePolicy;
use App\Domain\Clients\Models\Customer;
use App\Domain\Clients\Policies\CustomerPolicy;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Facturation\Policies\InvoicePolicy;
use App\Domain\Historique\Policies\HistoryPolicy;
use App\Domain\Paiements\Models\Payment;
use App\Domain\Paiements\Policies\PaymentPolicy;
use App\Domain\Pertes\Models\Loss;
use App\Domain\Pertes\Policies\LossPolicy;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Policies\ProductPolicy;
use App\Domain\Reconditionnement\Models\Repackaging;
use App\Domain\Reconditionnement\Policies\RepackagingPolicy;
use App\Domain\Retours\Models\CustomerReturn;
use App\Domain\Retours\Policies\CustomerReturnPolicy;
use App\Domain\Utilisateurs\Policies\UserPolicy;
use App\Domain\Ventes\Models\Sale;
use App\Domain\Ventes\Policies\SalePolicy;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Purchase::class, PurchasePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(CustomerReturn::class, CustomerReturnPolicy::class);
        Gate::policy(Loss::class, LossPolicy::class);
        Gate::policy(Repackaging::class, RepackagingPolicy::class);
        Gate::policy('history', HistoryPolicy::class);
    }
}

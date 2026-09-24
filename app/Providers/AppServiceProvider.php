<?php

namespace App\Providers;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Achats\Policies\PurchasePolicy;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Policies\ProductPolicy;
use App\Domain\Utilisateurs\Policies\UserPolicy;
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
    }
}

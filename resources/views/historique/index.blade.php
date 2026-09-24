<x-layouts.app title="Historique & Suivi d'Activité">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-clock class="w-6 h-6 text-emerald-600" />
            Module Historique & Suivi d'Activité
        </h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card Achats -->
        <a href="{{ route('historique.purchases') }}" class="group">
            <x-ui.card class="p-6 h-full hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base group-hover:text-emerald-600 transition-colors">Historique des Achats</h2>
                        <p class="text-xs text-slate-500">Par produit, fournisseur et période</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600">Consultez tous les achats effectués, les prix d'achat et les montants totaux sur n'importe quelle période.</p>
            </x-ui.card>
        </a>

        <!-- Card Ventes -->
        <a href="{{ route('historique.sales') }}" class="group">
            <x-ui.card class="p-6 h-full hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base group-hover:text-emerald-600 transition-colors">Historique des Ventes</h2>
                        <p class="text-xs text-slate-500">Par produit, client, prix et remises</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600">Consultez les ventes réalisées, les motifs de remise/écart de prix et le volume vendu sur la période.</p>
            </x-ui.card>
        </a>

        <!-- Card Stock à une date passée -->
        <a href="{{ route('historique.stock-at-date') }}" class="group">
            <x-ui.card class="p-6 h-full hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:bg-sky-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-archive-box class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base group-hover:text-sky-600 transition-colors">Stock à une Date Passée</h2>
                        <p class="text-xs text-slate-500">Reconstitution d'état de stock par unité</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600">Reconstituez l'état exact des compteurs de stock d'un produit à n'importe quelle date passée (snapshots + rejeu).</p>
            </x-ui.card>
        </a>

        <!-- Card Classement Top Produits -->
        <a href="{{ route('historique.top-products') }}" class="group">
            <x-ui.card class="p-6 h-full hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors">Produits les plus Vendus</h2>
                        <p class="text-xs text-slate-500">Palmarès par volume ou par montant</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600">Découvrez le classement des meilleures ventes sur une période donnée en quantité ou en chiffre d'affaires.</p>
            </x-ui.card>
        </a>

        <!-- Card Synthèse Créances & Dettes -->
        <a href="{{ route('historique.financial-overview') }}" class="group">
            <x-ui.card class="p-6 h-full hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base group-hover:text-purple-600 transition-colors">Créances & Dettes</h2>
                        <p class="text-xs text-slate-500">Aperçu consolidé des encours financiers</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600">Suivez le montant global et détaillé des créances dues par les clients et des dettes dues aux fournisseurs.</p>
            </x-ui.card>
        </a>
    </div>
</x-layouts.app>

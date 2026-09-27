<x-layouts.app>
    <x-slot:title>Catalogue des produits</x-slot:title>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-cube class="w-6 h-6 text-emerald-600" />
                    <span>Produits & Stock</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Catalogue des produits et stock par unité.</p>
            </div>

            @can('create', \App\Domain\Produits\Models\Product::class)
                <a href="{{ route('produits.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                    <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                        Nouveau produit
                    </x-ui.button>
                </a>
            @endcan
        </div>

        <!-- Filters & Search Bar -->
        <x-ui.card class="p-3.5 sm:p-4">
            <form method="GET" action="{{ route('produits.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher un produit..."
                       class="flex-1 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 w-full sm:w-auto">

                <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 w-full sm:w-auto">
                    <option value="">Tous les statuts</option>
                    <option value="active" @selected(request('status') === 'active')>Actifs uniquement</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactifs uniquement</option>
                </select>

                <label for="low_stock" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm font-semibold cursor-pointer transition-all select-none active:scale-98 w-full sm:w-auto shrink-0
                    {{ request('low_stock') ? 'bg-amber-500/10 border-amber-400 text-amber-900 shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                    <input type="checkbox"
                           id="low_stock"
                           name="low_stock"
                           value="1"
                           @checked(request('low_stock'))
                           onchange="this.form.submit()"
                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500/20">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                    <span>Stock bas uniquement</span>
                </label>

                <div class="flex items-center justify-center gap-2 shrink-0 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="md" class="w-full sm:w-auto">
                        Filtrer
                    </x-ui.button>
                    @if(request()->anyFilled(['search', 'status', 'low_stock']))
                        <a href="{{ route('produits.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Catalogue des Produits ({{ $products->total() }})</span>
            </h2>
        </div>

        <!-- Products List -->
        @if($products->isEmpty())
            <x-ui.empty-state
                title="Aucun produit trouvé"
                description="Aucun produit ne correspond à vos critères."
                icon="cube"
            >
                @can('create', \App\Domain\Produits\Models\Product::class)
                    <a href="{{ route('produits.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                        <x-ui.button variant="primary" size="sm" icon="plus" class="w-full sm:w-auto">
                            Créer un produit
                        </x-ui.button>
                    </a>
                @endcan
            </x-ui.empty-state>
        @else
                <!-- Table Unifiée Scrollable Horizon -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
                <table class="w-full text-left text-xs sm:text-sm min-w-[650px]">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="py-3.5 px-4 whitespace-nowrap">Produit</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Unité de base</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Stock par unité</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Statut</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        <a href="{{ route('produits.show', $product) }}" class="hover:text-emerald-600 transition">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                    @if($product->description)
                                        <div class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">{{ $product->description }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-800">{{ $product->baseUnit?->name }}</span>
                                    <div class="text-xs text-slate-400">{{ number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') }} FCFA</div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product->units as $unit)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $unit->isLowStock() ? 'bg-red-50 text-red-700 border-red-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                <strong class="font-extrabold">{{ number_format($unit->current_stock, 0, ',', ' ') }}</strong> {{ $unit->name }}
                                                @if($unit->isLowStock())
                                                    <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5 text-red-600" />
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($product->is_active)
                                        <x-ui.badge color="emerald">Actif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="slate">Inactif</x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('produits.show', $product) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Fiche produit">
                                            <x-heroicon-o-cube class="w-4 h-4" />
                                        </a>

                                        @can('update', $product)
                                            <a href="{{ route('produits.edit', $product) }}" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Modifier">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

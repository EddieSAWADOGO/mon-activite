<x-layouts.app>
    <x-slot:title>Catalogue des produits</x-slot:title>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Produits & Stock</h1>
                <p class="text-sm text-slate-500 mt-1">Catalogue des produits et consultation du stock par unité déclarée.</p>
            </div>

            @can('create', \App\Domain\Produits\Models\Product::class)
                <a href="{{ route('produits.create') }}" class="inline-flex">
                    <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                        Nouveau produit
                    </x-ui.button>
                </a>
            @endcan
        </div>

        <!-- Filters & Search Bar -->
        <x-ui.card class="mb-6 p-4">
            <form method="GET" action="{{ route('produits.index') }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                <!-- Search input -->
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher un produit..."
                       class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

                <!-- Status Filter -->
                <select name="status" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                    <option value="">Tous les statuts</option>
                    <option value="active" @selected(request('status') === 'active')>Actifs uniquement</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactifs uniquement</option>
                </select>

                <!-- Low Stock Checkbox -->
                <label for="low_stock" class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer py-2 sm:py-0 shrink-0">
                    <input type="checkbox"
                           id="low_stock"
                           name="low_stock"
                           value="1"
                           @checked(request('low_stock'))
                           onchange="this.form.submit()"
                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-500" />
                    <span>Stock bas</span>
                </label>

                <!-- Submit / Reset Buttons -->
                <div class="flex items-center gap-2 shrink-0">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                        Filtrer
                    </x-ui.button>
                    @if(request()->anyFilled(['search', 'status', 'low_stock']))
                        <a href="{{ route('produits.index') }}" class="text-slate-400 hover:text-slate-600 text-xs font-medium whitespace-nowrap">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Products List -->
        @if($products->isEmpty())
            <x-ui.empty-state
                title="Aucun produit trouvé"
                description="Aucun produit ne correspond à vos critères de recherche ou aucun produit n'a encore été créé."
                icon="cube"
            >
                @can('create', \App\Domain\Produits\Models\Product::class)
                    <a href="{{ route('produits.create') }}">
                        <x-ui.button variant="primary" size="sm" icon="plus">
                            Créer un produit
                        </x-ui.button>
                    </a>
                @endcan
            </x-ui.empty-state>
        @else
            <!-- Mobile View: Cards -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h2 class="text-base font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('produits.show', $product) }}" class="hover:text-emerald-600 transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </h2>
                                @if($product->description)
                                    <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $product->description }}</p>
                                @endif
                            </div>

                            @if($product->is_active)
                                <x-ui.badge color="emerald">Actif</x-ui.badge>
                            @else
                                <x-ui.badge color="slate">Inactif</x-ui.badge>
                            @endif
                        </div>

                        <!-- Stock Counters per unit -->
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1.5">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">
                                Stock par unité
                            </span>

                            <div class="flex flex-wrap gap-2">
                                @foreach($product->units as $unit)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border {{ $unit->isLowStock() ? 'bg-red-50 text-red-800 border-red-200' : 'bg-white text-slate-700 border-slate-200' }}">
                                        <span class="font-bold">{{ number_format($unit->current_stock, 0, ',', ' ') }}</span>
                                        <span>{{ $unit->name }}</span>

                                        @if($unit->is_base_unit)
                                            <span class="text-[10px] bg-slate-100 text-slate-500 px-1 rounded">Base</span>
                                        @endif

                                        @if($unit->isLowStock())
                                            <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5 text-red-600" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                            <span class="text-slate-500">
                                Prix base: <strong class="text-slate-800">{{ number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') }} FCFA</strong>
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('produits.show', $product) }}" class="p-2 text-slate-600 hover:text-emerald-600 font-medium">
                                    Détails
                                </a>

                                @can('update', $product)
                                    <a href="{{ route('produits.edit', $product) }}" class="p-2 text-slate-600 hover:text-emerald-600" title="Modifier">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop / Tablet View: Table -->
            <div class="hidden md:block bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Produit</th>
                            <th class="px-6 py-3.5">Unité de base</th>
                            <th class="px-6 py-3.5">Stock par unité</th>
                            <th class="px-6 py-3.5">Statut</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">
                                        <a href="{{ route('produits.show', $product) }}" class="hover:text-emerald-600 transition-colors">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                    @if($product->description)
                                        <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $product->description }}</div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-800">{{ $product->baseUnit?->name }}</span>
                                    <div class="text-xs text-slate-500">{{ number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') }} FCFA</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product->units as $unit)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium border {{ $unit->isLowStock() ? 'bg-red-50 text-red-700 border-red-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                <strong class="font-bold">{{ number_format($unit->current_stock, 0, ',', ' ') }}</strong> {{ $unit->name }}
                                                @if($unit->isLowStock())
                                                    <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5 text-red-600" />
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($product->is_active)
                                        <x-ui.badge color="emerald">Actif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="slate">Inactif</x-ui.badge>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('produits.show', $product) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100" title="Voir les détails">
                                            <x-heroicon-o-cube class="w-5 h-5" />
                                        </a>

                                        @can('update', $product)
                                            <a href="{{ route('produits.edit', $product) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100" title="Modifier">
                                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

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
            <form method="GET" action="{{ route('produits.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center sm:items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher un produit..."
                       class="flex-1 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full sm:w-auto">

                <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">
                    <option value="">Tous les statuts</option>
                    <option value="active" @selected(request('status') === 'active')>Actifs uniquement</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactifs uniquement</option>
                </select>

                <label for="low_stock" class="flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer py-1.5 sm:py-0 shrink-0">
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

                <div class="flex items-center justify-center gap-2 shrink-0 w-full sm:w-auto">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto">
                        Filtrer
                    </x-ui.button>
                    @if(request()->anyFilled(['search', 'status', 'low_stock']))
                        <a href="{{ route('produits.index') }}" class="text-slate-400 hover:text-slate-600 text-xs font-medium whitespace-nowrap px-2">
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
            <!-- Mobile View: Cards -->
            <div class="grid grid-cols-1 gap-3 md:hidden">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    <a href="{{ route('produits.show', $product) }}" class="hover:text-emerald-600 transition">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                @if($product->description)
                                    <p class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $product->description }}</p>
                                @endif
                            </div>

                            @if($product->is_active)
                                <x-ui.badge color="emerald">Actif</x-ui.badge>
                            @else
                                <x-ui.badge color="slate">Inactif</x-ui.badge>
                            @endif
                        </div>

                        <!-- Stock Counters per unit -->
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">
                                Stock par unité
                            </span>

                            <div class="flex flex-wrap gap-1.5">
                                @foreach($product->units as $unit)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $unit->isLowStock() ? 'bg-red-50 text-red-800 border-red-200' : 'bg-white text-slate-700 border-slate-200' }}">
                                        <strong class="font-extrabold">{{ number_format($unit->current_stock, 0, ',', ' ') }}</strong>
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
                            <span class="text-slate-500 text-xs">
                                Prix base: <strong class="text-slate-800">{{ number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') }} FCFA</strong>
                            </span>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('produits.show', $product) }}"
                                   class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                   title="Voir la fiche produit">
                                    <x-heroicon-o-cube class="w-4 h-4" />
                                </a>

                                @can('update', $product)
                                    <a href="{{ route('produits.edit', $product) }}"
                                       class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                       title="Modifier le produit">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block bg-white rounded-2xl border border-slate-200/80 overflow-hidden">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 text-slate-400 font-bold uppercase text-[10px] sm:text-xs tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5">Produit</th>
                            <th class="py-3 px-5">Unité de base</th>
                            <th class="py-3 px-5">Stock par unité</th>
                            <th class="py-3 px-5">Statut</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900">
                                        <a href="{{ route('produits.show', $product) }}" class="hover:text-emerald-600 transition">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                    @if($product->description)
                                        <div class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $product->description }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-5">
                                    <span class="font-bold text-slate-800">{{ $product->baseUnit?->name }}</span>
                                    <div class="text-xs text-slate-400">{{ number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') }} FCFA</div>
                                </td>

                                <td class="py-3.5 px-5">
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

                                <td class="py-3.5 px-5">
                                    @if($product->is_active)
                                        <x-ui.badge color="emerald">Actif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="slate">Inactif</x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
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

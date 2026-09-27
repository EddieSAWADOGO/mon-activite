<x-layouts.app title="État des Stocks">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-archive-box class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>État Général des Stocks</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Consultation des niveaux de stock réels détenus par unité de conditionnement
                </p>
            </div>

            <a href="{{ route('stock.movements') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="secondary" icon="clock" size="sm" class="w-full sm:w-auto">
                    Historique des Mouvements
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('stock.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
            <div class="w-full sm:flex-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Recherche Produit</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du produit..."
                       class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 w-full sm:w-auto">
                <label class="w-full sm:w-auto inline-flex items-center justify-center gap-2 cursor-pointer bg-slate-100 px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))
                           class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span>Stock bas uniquement</span>
                </label>

                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="w-full sm:w-auto">
                    Filtrer
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Niveaux de Stock par Produit ({{ $products->total() }})</span>
        </h2>
    </div>

    @if($products->isEmpty())
        <x-ui.empty-state
            title="Aucun produit trouvé dans le stock"
            description="Essayez de modifier vos critères de recherche ou réinitialisez les filtres."
            icon="archive-box" />
    @else
        <!-- Products List -->
        <div class="space-y-4">
            @foreach($products as $product)
                <x-ui.card class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('produits.show', $product) }}" class="text-base font-bold text-slate-900 hover:text-emerald-600 transition-colors">
                                    {{ $product->name }}
                                </a>
                                @if($product->isLowStock())
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">
                                        <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5" />
                                        <span>Alerte Stock Bas</span>
                                    </span>
                                @endif
                            </div>
                            @if($product->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $product->description }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <x-ui.button href="{{ route('produits.show', $product) }}" variant="outline" size="sm">
                                Fiche Produit
                            </x-ui.button>
                        </div>
                    </div>

                    <!-- Stock Units List Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($product->activeUnits as $unit)
                            @php
                                $isLow = $unit->isLowStock();
                                $stockVal = (float) $unit->current_stock;
                                $thresholdVal = (float) $unit->low_stock_threshold;
                            @endphp
                            <div class="p-3 rounded-xl border {{ $isLow ? 'bg-red-50/50 border-red-200' : 'bg-slate-50 border-slate-200' }} space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-1">
                                        {{ $unit->name }}
                                        @if($unit->is_base_unit)
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded">Base</span>
                                        @endif
                                    </span>

                                    @if($isLow)
                                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-600" />
                                    @endif
                                </div>

                                <div class="flex items-baseline justify-between pt-1">
                                    <span class="text-xs text-slate-500">Stock actuel :</span>
                                    <span class="text-sm font-extrabold font-mono {{ $isLow ? 'text-red-700' : 'text-slate-900' }}">
                                        {{ rtrim(rtrim(number_format($stockVal, 4, ',', ' '), '0'), ',') }}
                                    </span>
                                </div>

                                <div class="flex items-baseline justify-between text-xs text-slate-500 border-t border-slate-200/60 pt-1">
                                    <span>Seuil alerte :</span>
                                    <span>{{ rtrim(rtrim(number_format($thresholdVal, 4, ',', ' '), '0'), ',') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</x-layouts.app>

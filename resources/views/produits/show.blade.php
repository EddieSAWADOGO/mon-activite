<x-layouts.app>
    <x-slot:title>Fiche Produit - {{ $product->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header & Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('produits.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition-colors">
                    <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $product->name }}</h1>
                        @if($product->is_active)
                            <x-ui.badge color="emerald">Actif</x-ui.badge>
                        @else
                            <x-ui.badge color="slate">Inactif</x-ui.badge>
                        @endif
                    </div>
                    @if($product->description)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $product->description }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $product)
                    <a href="{{ route('produits.edit', $product) }}">
                        <x-ui.button variant="outline" icon="pencil-square" size="sm">
                            Modifier
                        </x-ui.button>
                    </a>
                @endcan

                @can('delete', $product)
                    <form method="POST" action="{{ route('produits.destroy', $product) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" icon="trash" size="sm">
                            Supprimer
                        </x-ui.button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-ui.stat-card
                title="Unité de base"
                :value="$product->baseUnit?->name ?? '-'"
                :subtitle="'Prix ref: ' . number_format($product->baseUnit?->default_selling_price ?? 0, 0, ',', ' ') . ' FCFA'"
                icon="cube"
                color="emerald"
            />

            <x-ui.stat-card
                title="Unités déclarées"
                :value="(string) $product->units->count()"
                :subtitle="$product->activeUnits->count() . ' unités actives'"
                icon="archive-box"
                color="sky"
            />

            <x-ui.stat-card
                title="État du stock"
                :value="$product->isLowStock() ? 'Alerte Stock Bas' : 'Stock Normal'"
                :subtitle="$product->isLowStock() ? 'Au moins une unité sous le seuil' : 'Toutes les unités supérieures au seuil'"
                icon="exclamation-triangle"
                :color="$product->isLowStock() ? 'red' : 'emerald'"
            />
        </div>

        <!-- Stock Details per Unit -->
        <x-ui.card title="Compteurs de stock par unité déclarée" subtitle="Le stock est géré séparément pour chaque unité physique réellement détenue.">
            <!-- Mobile Cards View -->
            <div class="grid grid-cols-1 gap-3 sm:hidden">
                @foreach($product->units as $unit)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                {{ $unit->name }}
                                @if($unit->is_base_unit)
                                    <x-ui.badge color="sky">Base</x-ui.badge>
                                @endif
                            </span>

                            @if($unit->is_active)
                                <x-ui.badge color="emerald">Active</x-ui.badge>
                            @else
                                <x-ui.badge color="slate">Archivée</x-ui.badge>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 border-t border-b border-slate-200/60 py-2">
                            <div>
                                <span class="text-slate-400 block">Équivalence:</span>
                                <strong>{{ number_format($unit->base_unit_equivalent, 2, ',', ' ') }} {{ $product->baseUnit?->name }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Prix de vente:</span>
                                <strong>{{ number_format($unit->default_selling_price, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Stock actuel:</span>
                                <strong class="{{ $unit->isLowStock() ? 'text-red-600 font-bold' : 'text-slate-900' }}">
                                    {{ number_format($unit->current_stock, 2, ',', ' ') }}
                                </strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Seuil d'alerte:</span>
                                <span>{{ number_format($unit->low_stock_threshold, 2, ',', ' ') }}</span>
                            </div>
                        </div>

                        @if(!$unit->is_base_unit)
                            @can('update', $product)
                                <form method="POST" action="{{ route('produits.unites.toggle-status', [$product, $unit]) }}" class="pt-1 text-right">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs text-slate-500 hover:text-slate-800 underline">
                                        {{ $unit->is_active ? 'Archiver cette unité' : 'Réactiver cette unité' }}
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Unité</th>
                            <th class="px-4 py-3">Équivalence unité base</th>
                            <th class="px-4 py-3">Prix de vente par défaut</th>
                            <th class="px-4 py-3">Stock actuel</th>
                            <th class="px-4 py-3">Seuil d'alerte</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($product->units as $unit)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $unit->name }}</span>
                                        @if($unit->is_base_unit)
                                            <x-ui.badge color="sky">Unité de base</x-ui.badge>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    1 {{ $unit->name }} = <strong>{{ number_format($unit->base_unit_equivalent, 2, ',', ' ') }}</strong> {{ $product->baseUnit?->name }}
                                </td>

                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ number_format($unit->default_selling_price, 0, ',', ' ') }} FCFA
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-sm font-bold border {{ $unit->isLowStock() ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }}">
                                        {{ number_format($unit->current_stock, 2, ',', ' ') }}
                                        @if($unit->isLowStock())
                                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-600" />
                                        @endif
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ number_format($unit->low_stock_threshold, 2, ',', ' ') }}
                                </td>

                                <td class="px-4 py-3">
                                    @if($unit->is_active)
                                        <x-ui.badge color="emerald">Active</x-ui.badge>
                                    @else
                                        <x-ui.badge color="slate">Archivée</x-ui.badge>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right">
                                    @if(!$unit->is_base_unit)
                                        @can('update', $product)
                                            <form method="POST" action="{{ route('produits.unites.toggle-status', [$product, $unit]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs text-slate-500 hover:text-slate-800 hover:underline">
                                                    {{ $unit->is_active ? 'Archiver' : 'Réactiver' }}
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        <span class="text-xs text-slate-400">Non désactivable</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Movement History Placeholder -->
        <x-ui.card title="Historique des mouvements de stock" subtitle="Achats, ventes, retours, pertes et reconditionnements liés à ce produit.">
            <x-ui.empty-state
                title="Aucun mouvement enregistré"
                description="Les entrées et sorties de stock enregistrées lors des opérations d'achat, de vente ou de régularisation apparaîtront ici."
                icon="clock"
            />
        </x-ui.card>
    </div>
</x-layouts.app>

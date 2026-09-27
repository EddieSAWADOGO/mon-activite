<x-layouts.app>
    <x-slot:title>Fiche Produit - {{ $product->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header & Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2.5 min-w-0">
                <x-ui.back-button href="{{ route('produits.index') }}" label="Retour" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $product->name }}</h1>
                        @if($product->is_active)
                            <x-ui.badge color="emerald">Actif</x-ui.badge>
                        @else
                            <x-ui.badge color="slate">Inactif</x-ui.badge>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto" x-data="{ showDeleteModal: false, submittingDelete: false }">
                @can('update', $product)
                    <a href="{{ route('produits.edit', $product) }}" class="w-full sm:w-auto">
                        <x-ui.button variant="outline" icon="pencil-square" size="sm" title="Modifier le produit" class="w-full sm:w-auto">
                            Modifier
                        </x-ui.button>
                    </a>
                @endcan

                @can('delete', $product)
                    <x-ui.button type="button" @click="showDeleteModal = true" variant="danger" icon="trash" size="sm" title="Supprimer le produit" class="w-full sm:w-auto">
                        Supprimer
                    </x-ui.button>

                    <x-ui.confirm-modal
                        name="showDeleteModal"
                        title="Supprimer ce produit ?"
                        :message="'Êtes-vous sûr de vouloir supprimer le produit ' . $product->name . ' ? Cette action est irréversible.'"
                        confirmText="Supprimer"
                        variant="danger"
                        icon="trash">
                        <form method="POST" action="{{ route('produits.destroy', $product) }}" @submit="submittingDelete = true" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" size="sm" class="w-full" ::loading="submittingDelete">
                                Supprimer
                            </x-ui.button>
                        </form>
                    </x-ui.confirm-modal>
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
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
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

        <!-- Movement History -->
        <x-ui.card title="Historique des mouvements de stock" subtitle="Les 20 derniers mouvements (achats, ventes, cassures, retours, pertes, reconditionnements).">
            @if($product->movements->isEmpty())
                <x-ui.empty-state
                    title="Aucun mouvement enregistré"
                    description="Les entrées et sorties de stock enregistrées lors des opérations d'achat, de vente ou de régularisation apparaîtront ici."
                    icon="clock"
                />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Type</th>
                                <th class="py-2.5 px-4">Unité</th>
                                <th class="py-2.5 px-4">Quantité</th>
                                <th class="py-2.5 px-4">Sens</th>
                                <th class="py-2.5 px-4">Auteur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($product->movements as $movement)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-2.5 px-4 font-medium text-slate-700 whitespace-nowrap">
                                        {{ $movement->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <x-ui.badge :color="match($movement->movement_type?->value ?? $movement->movement_type) {
                                            'purchase' => 'emerald',
                                            'sale' => 'sky',
                                            'return' => 'purple',
                                            'loss' => 'red',
                                            'repackaging' => 'amber',
                                            default => 'slate'
                                        }">
                                            {{ match($movement->movement_type?->value ?? $movement->movement_type) {
                                                'purchase' => 'Achat',
                                                'sale' => 'Vente',
                                                'return' => 'Retour',
                                                'loss' => 'Perte',
                                                'repackaging' => 'Recond.',
                                                default => $movement->movement_type?->value ?? $movement->movement_type
                                            } }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">
                                        {{ $movement->stockUnit?->name ?? '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-slate-900">
                                        {{ number_format($movement->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        @if(($movement->direction?->value ?? $movement->direction) === 'in')
                                            <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                                                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" /> Entrée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-red-600">
                                                <x-heroicon-o-arrow-up-tray class="w-3.5 h-3.5" /> Sortie
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-500">
                                        {{ $movement->createdBy?->name ?? 'Système' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>

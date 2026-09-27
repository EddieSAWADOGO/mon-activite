<x-layouts.app title="Historique des Mouvements de Stock">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-clock class="w-6 h-6 text-emerald-600" />
                    <span>Historique Général des Mouvements</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Journal unifié de toutes les entrées et sorties de stock (achats, ventes, cassures, retours, pertes, reconditionnements)
                </p>
            </div>

            <x-ui.button href="{{ route('stock.index') }}" variant="secondary" icon="archive-box" size="sm" class="w-full sm:w-auto">
                État du Stock
            </x-ui.button>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('stock.movements') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Produit</label>
                <select name="product_id" class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Tous les produits</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Type de Mouvement</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Tous les types</option>
                    @foreach(App\Support\Enums\MovementType::cases() as $case)
                        <option value="{{ $case->value }}" @selected(request('type') == $case->value)>
                            {{ $case->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Date Début</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date Fin</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                    Filtrer
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Journal des Mouvements ({{ $movements->total() }})</span>
        </h2>
    </div>

    @if($movements->isEmpty())
        <x-ui.empty-state
            title="Aucun mouvement enregistré"
            description="L'historique des mouvements se remplira au fur et à mesure des achats, ventes et opérations de stock."
            icon="clock" />
    @else
        <!-- Desktop Table View -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden">
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date & Heure</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Format / Unité</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Quantité</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Auteur</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Notes</th>
                    </tr>
                </x-slot>

                @foreach($movements as $mvt)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-slate-600 font-mono whitespace-nowrap">
                            {{ $mvt->movement_date ? $mvt->movement_date->format('d/m/Y H:i') : $mvt->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-xs font-bold text-slate-900">
                            {{ $mvt->product->name }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $mvt->stockUnit->name }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge :color="$mvt->type->badgeColor()">
                                {{ $mvt->type->label() }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-xs font-mono font-bold text-right whitespace-nowrap {{ $mvt->direction === 'in' ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $mvt->direction === 'in' ? '+' : '-' }}{{ rtrim(rtrim(number_format($mvt->quantity, 4, ',', ' '), '0'), ',') }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $mvt->createdBy->name ?? 'Système' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 max-w-xs truncate">
                            {{ $mvt->notes ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        </div>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    @endif
</x-layouts.app>

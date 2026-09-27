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
    <x-ui.card class="mb-6 p-3 sm:p-4">
        <form method="GET" action="{{ route('stock.movements') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Produit</label>
                <select name="product_id" class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
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
                <select name="type" class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Tous les types</option>
                    @foreach(App\Support\Enums\MovementType::cases() as $case)
                        <option value="{{ $case->value }}" @selected(request('type') == $case->value)>
                            {{ $case->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:col-span-2 lg:col-span-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Du</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Au</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
            </div>

            <div class="flex gap-2 w-full">
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full justify-center">
                    Filtrer
                </x-ui.button>
                @if(request()->hasAny(['product_id', 'type', 'start_date', 'end_date']))
                    <a href="{{ route('stock.movements') }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3 text-slate-600 hover:text-slate-900 flex items-center justify-center whitespace-nowrap bg-white">
                        Effacer
                    </a>
                @endif
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
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Date & Heure</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Produit</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Format / Unité</th>
                        <th class="px-4 py-3.5 text-center text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Type</th>
                        <th class="px-4 py-3.5 text-right text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Quantité</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Auteur</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Notes</th>
                    </tr>
                </x-slot>

                @foreach($movements as $mvt)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-slate-600 font-mono whitespace-nowrap">
                            {{ $mvt->movement_date ? $mvt->movement_date->format('d/m/Y H:i') : $mvt->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-xs font-bold text-slate-900 whitespace-nowrap">
                            {{ $mvt->product->name }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">
                            {{ $mvt->stockUnit->name }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <x-ui.badge :color="$mvt->type->badgeColor()">
                                {{ $mvt->type->label() }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-xs font-mono font-bold text-right whitespace-nowrap {{ $mvt->direction === 'in' ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $mvt->direction === 'in' ? '+' : '-' }}{{ rtrim(rtrim(number_format($mvt->quantity, 4, ',', ' '), '0'), ',') }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">
                            {{ $mvt->createdBy->name ?? 'Système' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap max-w-xs truncate">
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

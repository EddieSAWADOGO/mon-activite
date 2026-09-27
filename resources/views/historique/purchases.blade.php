<x-layouts.app title="Historique des Achats">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 shrink-0" />
                    <span class="truncate">Historique des Achats</span>
                </h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Consultez et filtrez les bons d'achats fournisseurs enregistrés
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Barre de filtres -->
        <x-ui.card class="p-3.5 sm:p-4" x-data="{ period: '{{ $period }}' }">
            <form action="{{ route('historique.purchases') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label for="product_id" class="block text-xs font-semibold text-slate-700 mb-1">Produit</label>
                    <select name="product_id" id="product_id" class="w-full py-2.5 px-3 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Tous les produits</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="period" class="block text-xs font-semibold text-slate-700 mb-1">Période</label>
                    <select name="period" id="period" x-model="period" class="w-full py-2.5 px-3 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois-ci</option>
                        <option value="custom">Dates personnalisées</option>
                    </select>
                </div>

                <div x-show="period === 'custom'" class="col-span-1 sm:col-span-2 grid grid-cols-2 gap-2" x-transition>
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Du</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full py-2.5 px-2.5 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">Au</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full py-2.5 px-2.5 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="col-span-1 sm:col-span-2 lg:col-span-4 flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2 border-t border-slate-100 [&>button]:w-full [&>button]:sm:w-auto">
                    <x-ui.button type="submit" variant="primary" icon="magnifying-glass" class="w-full sm:w-auto justify-center">
                        Appliquer les filtres
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <!-- Métrique du Total -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-ui.stat-card
                title="Montant Total des Achats (Période)"
                value="{{ number_format($totalAmount, 0, ',', ' ') }} FCFA"
                icon="banknotes"
                color="emerald" />

            <x-ui.stat-card
                title="Période Analysée"
                value="{{ $startDate->format('d/m/Y') }} au {{ $endDate->format('d/m/Y') }}"
                icon="clock"
                color="sky" />
        </div>

        <!-- Tableau des Lignes d'Achats -->
        @if($lines->isEmpty())
            <x-ui.empty-state
                title="Aucun achat trouvé"
                description="Aucun achat ne correspond aux critères sélectionnés sur cette période."
                icon="arrow-down-tray">
            </x-ui.empty-state>
        @else
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[650px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">N° Achat</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Fournisseur</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Produit</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Unité</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Qté</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">P.U.</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Total FCFA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lines as $line)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $line->purchase->purchase_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <a href="{{ route('achats.show', $line->purchase) }}" class="text-emerald-600 hover:underline">
                                            {{ $line->purchase->purchase_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-800 whitespace-nowrap font-medium">
                                        {{ $line->purchase->supplier->name }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $line->product->name }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $line->stockUnit->name }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                        {{ number_format($line->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium text-slate-700 whitespace-nowrap">
                                        {{ number_format($line->unit_price, 0, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600 whitespace-nowrap">
                                        {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($lines->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $lines->links() }}
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

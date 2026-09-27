<x-layouts.app title="Historique des Ventes">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
                Historique des Ventes
            </h1>
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour à l'historique" />
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Barre de filtres -->
        <x-ui.card class="p-4" x-data="{ period: '{{ $period }}' }">
            <form action="{{ route('historique.sales') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label for="product_id" class="block text-xs font-medium text-slate-700 mb-1">Produit</label>
                    <select name="product_id" id="product_id" class="w-full py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">Tous les produits</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="period" class="block text-xs font-medium text-slate-700 mb-1">Période</label>
                    <select name="period" id="period" x-model="period" class="w-full py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois-ci</option>
                        <option value="custom">Dates personnalisées</option>
                    </select>
                </div>

                <div x-show="period === 'custom'" class="col-span-2 grid grid-cols-2 gap-2">
                    <div>
                        <label for="start_date" class="block text-xs font-medium text-slate-700 mb-1">Du</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium text-slate-700 mb-1">Au</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>

                <div class="sm:col-span-4 flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <x-ui.button type="submit" variant="primary" icon="magnifying-glass">
                        Appliquer les filtres
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <!-- Métrique du Total -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-ui.stat-card
                title="Montant Total des Ventes (Période)"
                value="{{ number_format($totalAmount, 0, ',', ' ') }} FCFA"
                icon="banknotes"
                color="emerald" />

            <x-ui.stat-card
                title="Période Analysée"
                value="{{ $startDate->format('d/m/Y') }} au {{ $endDate->format('d/m/Y') }}"
                icon="clock"
                color="sky" />
        </div>

        <!-- Tableau des Lignes de Ventes -->
        @if($lines->isEmpty())
            <x-ui.empty-state
                title="Aucune vente trouvée"
                description="Aucune vente ne correspond aux critères sélectionnés sur cette période."
                icon="arrow-up-tray">
            </x-ui.empty-state>
        @else
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">N° Vente</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Produit</th>
                                <th class="py-3 px-4">Unité</th>
                                <th class="py-3 px-4 text-right">Qté</th>
                                <th class="py-3 px-4 text-right">P.U.</th>
                                <th class="py-3 px-4 text-right">Total FCFA</th>
                                <th class="py-3 px-4">Motif Remise</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lines as $line)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $line->sale->sale_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <a href="{{ route('ventes.show', $line->sale) }}" class="text-emerald-600 hover:underline">
                                            {{ $line->sale->sale_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-800">
                                        {{ $line->sale->customer ? $line->sale->customer->name : 'Client de passage' }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $line->product->name }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        {{ $line->stockUnit->name }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900">
                                        {{ number_format($line->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium text-slate-700">
                                        {{ number_format($line->unit_price, 0, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600 whitespace-nowrap">
                                        {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 px-4 text-xs">
                                        @if($line->discount_reason)
                                            <span class="text-amber-700 font-semibold italic">{{ $line->discount_reason }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
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

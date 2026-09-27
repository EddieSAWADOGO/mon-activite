<x-layouts.app title="Classement des Produits les plus vendus">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-squares-2x2 class="w-6 h-6 text-amber-600" />
                Classement des Produits les plus vendus
            </h1>
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour à l'historique" />
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="p-4" x-data="{ period: '{{ $period }}' }">
            <form action="{{ route('historique.top-products') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label for="sort_by" class="block text-xs font-medium text-slate-700 mb-1">Classer par</label>
                    <select name="sort_by" id="sort_by" class="w-full py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="amount" {{ $sortBy === 'amount' ? 'selected' : '' }}>Montant total vendu (Chiffre d'affaires)</option>
                        <option value="quantity" {{ $sortBy === 'quantity' ? 'selected' : '' }}>Quantité totale vendue</option>
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
                        Actualiser le classement
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if($topProducts->isEmpty())
            <x-ui.empty-state
                title="Aucune donnée de vente"
                description="Aucun produit n'a été vendu sur la période sélectionnée."
                icon="squares-2x2">
            </x-ui.empty-state>
        @else
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4 w-16 text-center">Rang</th>
                                <th class="py-3 px-4">Produit</th>
                                <th class="py-3 px-4 text-right">Quantité Totale Vendue</th>
                                <th class="py-3 px-4 text-right">Montant Total Vendu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($topProducts as $index => $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($index === 1 ? 'bg-slate-200 text-slate-800' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-600')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $item->name }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-800">
                                        {{ number_format($item->total_quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600 whitespace-nowrap">
                                        {{ number_format($item->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

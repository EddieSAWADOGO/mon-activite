<x-layouts.app title="Classement des Produits les plus vendus">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600 shrink-0" />
                    <span class="truncate">Classement des Produits les plus vendus</span>
                </h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Top des articles par chiffre d'affaires généré et quantités écoulées
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="p-3.5 sm:p-4" x-data="{ period: '{{ $period }}' }">
            <form action="{{ route('historique.top-products') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label for="sort_by" class="block text-xs font-semibold text-slate-700 mb-1">Classer par</label>
                    <select name="sort_by" id="sort_by" class="w-full py-2.5 px-3 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="amount" {{ $sortBy === 'amount' ? 'selected' : '' }}>Montant total vendu (CA)</option>
                        <option value="quantity" {{ $sortBy === 'quantity' ? 'selected' : '' }}>Quantité totale vendue</option>
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
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[550px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 w-16 text-center whitespace-nowrap">Rang</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Produit</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Quantité Totale Vendue</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Montant Total Vendu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($topProducts as $index => $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($index === 1 ? 'bg-slate-200 text-slate-800' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-600')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $item->name }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-800 whitespace-nowrap">
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

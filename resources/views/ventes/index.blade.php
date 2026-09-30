<x-layouts.app title="Historique des Ventes">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Ventes</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Historique et enregistrement des transactions de vente.</p>
            </div>

            <a href="{{ route('ventes.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouvelle Vente
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6 p-3 sm:p-4">
        <form method="GET" action="{{ route('ventes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 items-center">
            <div class="sm:col-span-2 lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       x-on:input.debounce.400ms="$el.form.submit()"
                       placeholder="Rechercher par N° vente, client..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       onchange="this.form.submit()"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500"
                       title="Date début">

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       onchange="this.form.submit()"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500"
                       title="Date fin">
            </div>

            <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto justify-center">
                    Filtrer
                </x-ui.button>
                @if(request()->hasAny(['search', 'start_date', 'end_date']))
                    <a href="{{ route('ventes.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Liste des Ventes ({{ $sales->total() }})</span>
        </h2>
    </div>

    @if($sales->isEmpty())
        <x-ui.empty-state
            title="Aucune vente enregistrée"
            description="Enregistrez une vente pour générer une facture et décrémenter le stock."
            icon="arrow-up-tray">
            <x-ui.button href="{{ route('ventes.create') }}" variant="primary" icon="plus" class="mt-2 w-full sm:w-auto">
                Nouvelle Vente
            </x-ui.button>
        </x-ui.empty-state>
    @else
        <!-- Table Unifiée Scrollable Horizon -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
            <table class="w-full text-left text-xs sm:text-sm min-w-[640px]">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">N° Vente & Date</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Client</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Montant Total</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Montant Payé</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Reste à Payer</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Vendeur</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sales as $sale)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $sale->sale_number }}</div>
                                <div class="text-[11px] text-slate-400 font-medium">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-medium text-slate-800">
                                {{ $sale->customer?->name ?? 'Client de passage' }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-bold text-slate-900">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-emerald-600 font-semibold">
                                {{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap">
                                @if($sale->remaining_amount > 0)
                                    <x-ui.badge color="amber">
                                        {{ number_format($sale->remaining_amount, 0, ',', ' ') }} FCFA
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge color="emerald">Soldé</x-ui.badge>
                                @endif
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-slate-500 text-xs">
                                {{ $sale->createdBy->name }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('ventes.show', $sale) }}"
                                   class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                   title="Détails de la vente">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                                @if($sale->invoice)
                                    <a href="{{ route('factures.show', $sale->invoice) }}"
                                       class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                       title="Voir la facture">
                                        <x-heroicon-o-document-text class="w-4 h-4" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    @endif
</x-layouts.app>

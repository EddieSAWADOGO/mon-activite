<x-layouts.app title="Historique des Ventes">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
                    <span>Ventes</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Historique des ventes et enregistrement de nouvelles transactions
                </p>
            </div>

            <x-ui.button href="{{ route('ventes.create') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
                Nouvelle Vente
            </x-ui.button>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('ventes.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par N° vente, nom client..."
                   class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">

            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">

            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                Filtrer
            </x-ui.button>
        </form>
    </x-ui.card>

    @if($sales->isEmpty())
        <x-ui.empty-state
            title="Aucune vente enregistrée"
            description="Enregistrez votre première vente pour générer des factures et décrémenter automatiquement le stock."
            icon="arrow-up-tray">
            <x-ui.button href="{{ route('ventes.create') }}" variant="primary" icon="plus" class="mt-4">
                Nouvelle Vente
            </x-ui.button>
        </x-ui.empty-state>
    @else
        <x-ui.card class="p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">N° Vente & Date</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Montant Total</th>
                            <th class="py-3 px-4">Montant Payé</th>
                            <th class="py-3 px-4">Reste à Payer</th>
                            <th class="py-3 px-4">Vendeur</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sales as $sale)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $sale->sale_number }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-800">
                                    {{ $sale->customer?->name ?? 'Client de passage' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4 text-emerald-600 font-semibold">
                                    {{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4">
                                    @if($sale->remaining_amount > 0)
                                        <x-ui.badge color="amber">
                                            {{ number_format($sale->remaining_amount, 0, ',', ' ') }} FCFA
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge color="emerald">Soldé</x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-500 text-[11px]">
                                    {{ $sale->createdBy->name }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-1">
                                    <x-ui.button href="{{ route('ventes.show', $sale) }}" variant="outline" size="sm">
                                        Détails
                                    </x-ui.button>
                                    @if($sale->invoice)
                                        <x-ui.button href="{{ route('factures.show', $sale->invoice) }}" variant="secondary" size="sm" icon="document-text">
                                            Facture
                                        </x-ui.button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    @endif
</x-layouts.app>

<x-layouts.app title="Détail Vente — {{ $sale->sale_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2.5 min-w-0">
                <x-ui.back-button href="{{ route('ventes.index') }}" label="Retour" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $sale->sale_number }}</h2>
                        <x-ui.badge :color="$sale->remaining_amount > 0 ? 'amber' : 'emerald'">
                            {{ $sale->remaining_amount > 0 ? 'Partiellement payée' : 'Soldée' }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                @if($sale->invoice)
                    <x-ui.button href="{{ route('factures.show', $sale->invoice) }}" variant="primary" icon="document-text" size="sm" class="w-full sm:w-auto">
                        Consulter la Facture ({{ $sale->invoice->invoice_number }})
                    </x-ui.button>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-ui.stat-card
            title="Montant Total"
            value="{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA"
            icon="banknotes"
            color="emerald" />

        <x-ui.stat-card
            title="Montant Réglé"
            value="{{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA"
            icon="check-circle"
            color="emerald" />

        <x-ui.stat-card
            title="Reste à Payer"
            value="{{ number_format($sale->remaining_amount, 0, ',', ' ') }} FCFA"
            icon="exclamation-triangle"
            :color="$sale->remaining_amount > 0 ? 'amber' : 'slate'" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lines table -->
        <div class="lg:col-span-2">
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-3 mb-3">Lignes de Vente</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[600px]">
                        <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4 whitespace-nowrap">Produit & Unité</th>
                                <th class="py-3 px-4 whitespace-nowrap">Quantité</th>
                                <th class="py-3 px-4 whitespace-nowrap">Prix Unitaire</th>
                                <th class="py-3 px-4 whitespace-nowrap">Sous-total</th>
                                <th class="py-3 px-4 whitespace-nowrap">Cassure / Remise</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sale->lines as $line)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">{{ $line->product->name }}</div>
                                        <div class="text-xs text-slate-500 font-medium">Unité : {{ $line->stockUnit->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-800">
                                        {{ number_format($line->quantity, 2, ',', ' ') }} {{ $line->stockUnit->name }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-900">
                                        {{ number_format($line->unit_price, 0, ',', ' ') }} FCFA
                                        @if($line->hasDiscount())
                                            <div class="text-xs text-slate-400 line-through">
                                                {{ number_format($line->default_unit_price, 0, ',', ' ') }} FCFA
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-extrabold text-slate-900 text-sm sm:text-base">
                                        {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap space-y-1">
                                        @if($line->isCassure())
                                            <x-ui.badge color="amber">
                                                Cassure (Ouverture {{ $line->sourceStockUnit->name }})
                                            </x-ui.badge>
                                        @endif

                                        @if($line->discount_reason)
                                            <div class="text-xs text-amber-800 font-semibold italic">
                                                Motif : {{ $line->discount_reason }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        <!-- Meta side -->
        <div class="lg:col-span-1 space-y-4">
            <x-ui.card class="p-4 sm:p-5 space-y-3.5 text-xs sm:text-sm">
                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-2.5">Informations Transaction</h3>
                <div>
                    <span class="text-slate-400 block font-medium">Client</span>
                    <span class="font-bold text-slate-800 text-sm sm:text-base">
                        @if($sale->customer)
                            <a href="{{ route('clients.show', $sale->customer) }}" class="text-emerald-600 hover:underline">
                                {{ $sale->customer->name }}
                            </a>
                        @else
                            Client de passage (Anonyme)
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Vendeur / Caissier</span>
                    <span class="font-semibold text-slate-800">{{ $sale->createdBy->name }} ({{ $sale->createdBy->email }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Date d'enregistrement</span>
                    <span class="font-semibold text-slate-800">{{ $sale->sale_date->format('d/m/Y à H:i') }}</span>
                </div>
                @if($sale->notes)
                    <div class="pt-2.5 border-t border-slate-100">
                        <span class="text-slate-400 block font-medium">Notes internes</span>
                        <span class="italic text-slate-700">{{ $sale->notes }}</span>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app title="Détail Vente — {{ $sale->sale_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4">
            <div>
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $sale->sale_number }}</h2>
                    <x-ui.badge :color="$sale->remaining_amount > 0 ? 'amber' : 'emerald'">
                        {{ $sale->remaining_amount > 0 ? 'Partiellement payée' : 'Soldée' }}
                    </x-ui.badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Vente réalisée le {{ $sale->sale_date->format('d/m/Y à H:i') }} par {{ $sale->createdBy->name }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                @if($sale->invoice)
                    <x-ui.button href="{{ route('factures.show', $sale->invoice) }}" variant="primary" icon="document-text" size="sm" class="w-full sm:w-auto">
                        Consulter la Facture ({{ $sale->invoice->invoice_number }})
                    </x-ui.button>
                @endif
                <x-ui.back-button href="{{ route('ventes.index') }}" label="Retour à la liste" class="w-full sm:w-auto justify-center" />
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
            <x-ui.card class="p-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 mb-3">Lignes de Vente</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="py-2.5 px-3">Produit & Unité</th>
                                <th class="py-2.5 px-3">Quantité</th>
                                <th class="py-2.5 px-3">Prix Unitaire</th>
                                <th class="py-2.5 px-3">Sous-total</th>
                                <th class="py-2.5 px-3">Cassure / Remise</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sale->lines as $line)
                                <tr>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-900">{{ $line->product->name }}</div>
                                        <div class="text-[11px] text-slate-500">Unité : {{ $line->stockUnit->name }}</div>
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-slate-800">
                                        {{ number_format($line->quantity, 2, ',', ' ') }} {{ $line->stockUnit->name }}
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-slate-900">
                                        {{ number_format($line->unit_price, 0, ',', ' ') }} FCFA
                                        @if($line->hasDiscount())
                                            <div class="text-[10px] text-slate-400 line-through">
                                                {{ number_format($line->default_unit_price, 0, ',', ' ') }} FCFA
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 font-extrabold text-slate-900">
                                        {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 px-3 space-y-1">
                                        @if($line->isCassure())
                                            <x-ui.badge color="amber">
                                                Cassure (Ouverture {{ $line->sourceStockUnit->name }})
                                            </x-ui.badge>
                                        @endif

                                        @if($line->discount_reason)
                                            <div class="text-[11px] text-amber-800 italic">
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
            <x-ui.card class="p-4 space-y-3 text-xs">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Informations Transaction</h3>
                <div>
                    <span class="text-slate-400 block">Client</span>
                    <span class="font-semibold text-slate-800">
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
                    <span class="text-slate-400 block">Vendeur / Caissier</span>
                    <span class="font-semibold text-slate-800">{{ $sale->createdBy->name }} ({{ $sale->createdBy->email }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Date d'enregistrement</span>
                    <span class="font-semibold text-slate-800">{{ $sale->sale_date->format('d/m/Y à H:i') }}</span>
                </div>
                @if($sale->notes)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 block">Notes internes</span>
                        <span class="italic text-slate-700">{{ $sale->notes }}</span>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app title="Gestion des Factures">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
                    <span>Factures</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Consultation des factures immuables générées à partir des transactions
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Search filter -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('factures.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par N° facture, client..."
                   class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

            <select name="status" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                <option value="">Tous les statuts</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partiellement payée</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Impayée</option>
            </select>

            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                Filtrer
            </x-ui.button>
        </form>
    </x-ui.card>

    @if($invoices->isEmpty())
        <x-ui.empty-state
            title="Aucune facture"
            description="Toute vente enregistrée génère automatiquement sa facture correspondante."
            icon="document-text" />
    @else
        <x-ui.card class="p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">N° Facture & Date</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Montant Total</th>
                            <th class="py-3 px-4">Montant Payé</th>
                            <th class="py-3 px-4">Reste à Payer</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $invoice->invoice_number }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $invoice->invoice_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-800">
                                    {{ $invoice->customer?->name ?? 'Client de passage' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4 text-emerald-600 font-semibold">
                                    {{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4 font-semibold">
                                    {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4">
                                    <x-ui.badge :color="$invoice->status->badgeColor()">
                                        {{ $invoice->status->label() }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1">
                                    <x-ui.button href="{{ route('factures.show', $invoice) }}" variant="outline" size="sm">
                                        Consulter
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    @endif
</x-layouts.app>

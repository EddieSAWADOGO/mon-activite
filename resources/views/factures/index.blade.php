<x-layouts.app title="Gestion des Factures">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Factures</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Consultation et gestion des factures immuables.</p>
            </div>
        </div>
    </x-slot>

    <!-- Search filter -->
    <x-ui.card class="mb-6 p-3 sm:p-4">
        <form method="GET" action="{{ route('factures.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par N° facture, client..."
                   class="w-full sm:flex-1 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

            <select name="status" class="w-full sm:w-auto rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Tous les statuts</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partiellement payée</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Impayée</option>
            </select>

            <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto justify-center">
                    Filtrer
                </x-ui.button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('factures.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Registre des Factures Émises ({{ $invoices->total() }})</span>
        </h2>
    </div>

    @if($invoices->isEmpty())
        <x-ui.empty-state
            title="Aucune facture"
            description="Toute vente enregistrée génère automatiquement sa facture."
            icon="document-text" />
    @else
        <!-- Table Unifiée Scrollable Horizon -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
            <table class="w-full text-left text-xs sm:text-sm min-w-[640px]">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">N° Facture & Date</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Client</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Montant Total</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Montant Payé</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Reste à Payer</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Statut</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $invoice->invoice_number }}</div>
                                <div class="text-[11px] text-slate-400 font-medium">{{ $invoice->invoice_date->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-medium text-slate-800">
                                {{ $invoice->customer?->name ?? 'Client de passage' }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-bold text-slate-900">
                                {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-emerald-600 font-semibold">
                                {{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-semibold text-slate-900">
                                {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap">
                                <x-ui.badge :color="$invoice->status->badgeColor()">
                                    {{ $invoice->status->label() }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('factures.show', $invoice) }}"
                                   class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                   title="Consulter la facture">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $invoices->links() }}
        </div>
    @endif
</x-layouts.app>

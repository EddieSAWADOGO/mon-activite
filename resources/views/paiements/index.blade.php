<x-layouts.app title="Historique des Règlements">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
                Historique des Règlements
            </h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="mb-6 p-3.5 sm:p-4">
            <form action="{{ route('paiements.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 items-center">
                <div class="sm:col-span-2 lg:col-span-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           x-on:input.debounce.400ms="$el.form.submit()"
                           placeholder="N° document, client, fournisseur..."
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Tous les types</option>
                        <option value="customer" @selected(request('type') === 'customer' || request('type') === 'client')>Clients (Factures)</option>
                        <option value="supplier" @selected(request('type') === 'supplier' || request('type') === 'fournisseur')>Fournisseurs (Achats)</option>
                    </select>
                </div>

                <div>
                    <select name="payment_method" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Tous les modes</option>
                        <option value="Espèces" @selected(request('payment_method') === 'Espèces')>Espèces</option>
                        <option value="Orange Money" @selected(request('payment_method') === 'Orange Money')>Orange Money</option>
                        <option value="Moov Money" @selected(request('payment_method') === 'Moov Money')>Moov Money</option>
                        <option value="Wave" @selected(request('payment_method') === 'Wave')>Wave</option>
                        <option value="Virement bancaire" @selected(request('payment_method') === 'Virement bancaire')>Virement bancaire</option>
                        <option value="Chèque" @selected(request('payment_method') === 'Chèque')>Chèque</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" title="Date début">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" title="Date fin">
                </div>

                <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto justify-center">
                        Filtrer
                    </x-ui.button>

                    @if(request()->hasAny(['search', 'type', 'payment_method', 'start_date', 'end_date']))
                        <a href="{{ route('paiements.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Tous les Règlements Enregistrés ({{ $payments->total() }})</span>
            </h2>
        </div>

        @if($payments->isEmpty())
            <x-ui.empty-state
                title="Aucun règlement trouvé"
                description="Aucun règlement de facture ou d'achat n'a été trouvé."
                icon="banknotes">
            </x-ui.empty-state>
        @else
            <!-- Tableau Unifié Scrollable Horizon -->
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Type</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Document</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Tiers</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Mode</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Référence</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Montant</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Enregistré par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $payment->payment_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($payment->purchase_id)
                                            <x-ui.badge color="purple">Fournisseur</x-ui.badge>
                                        @else
                                            <x-ui.badge color="emerald">Client</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        @if($payment->purchase_id)
                                            <a href="{{ route('achats.show', $payment->purchase_id) }}" class="text-emerald-600 hover:underline">
                                                {{ $payment->purchase ? $payment->purchase->purchase_number : 'Achat N°' . $payment->purchase_id }}
                                            </a>
                                        @else
                                            <a href="{{ route('factures.show', $payment->invoice_id) }}" class="text-emerald-600 hover:underline">
                                                {{ $payment->invoice ? $payment->invoice->invoice_number : 'Facture N°' . $payment->invoice_id }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-800 whitespace-nowrap font-medium">
                                        @if($payment->purchase_id)
                                            {{ $payment->purchase && $payment->purchase->supplier ? $payment->purchase->supplier->name : '-' }}
                                        @else
                                            {{ $payment->invoice && $payment->invoice->customer ? $payment->invoice->customer->name : 'Client de passage' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                        {{ $payment->reference ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold {{ $payment->purchase_id ? 'text-slate-900' : 'text-emerald-600' }} whitespace-nowrap">
                                        {{ $payment->purchase_id ? '-' : '+' }} {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">
                                        {{ $payment->createdBy ? $payment->createdBy->name : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

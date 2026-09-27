<x-layouts.app>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
                Règlements Client
            </h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Barre de recherche -->
        <x-ui.card class="mb-6 p-4">
            <form action="{{ route('paiements.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher par N° de facture ou nom du client..."
                       class="flex-1 rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500">

                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                    Rechercher
                </x-ui.button>

                @if(request('search'))
                    <a href="{{ route('paiements.index') }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3 text-slate-600 hover:text-slate-800 flex items-center justify-center whitespace-nowrap bg-white">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Historique des Paiements Reçus ({{ $payments->total() }})</span>
            </h2>
        </div>

        @if($payments->isEmpty())
            <x-ui.empty-state
                title="Aucun règlement trouvé"
                description="Aucun règlement de facture n'a encore été enregistré."
                icon="banknotes">
            </x-ui.empty-state>
        @else
            <!-- Tableau / Cartes Mobile -->
            <x-ui.card class="p-0 overflow-hidden">
                <div class="block sm:hidden divide-y divide-slate-100">
                    @foreach($payments as $payment)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('factures.show', $payment->invoice_id) }}" class="font-bold text-emerald-600 hover:underline">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                                <span class="text-xs text-slate-500">
                                    {{ $payment->payment_date->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-xs sm:text-sm">
                                <span class="text-slate-600">Client:</span>
                                <span class="font-medium text-slate-900">
                                    {{ $payment->invoice->customer ? $payment->invoice->customer->name : 'Client de passage' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-xs sm:text-sm">
                                <span class="text-slate-600">Mode:</span>
                                <x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-slate-50">
                                <span class="text-xs text-slate-400">Par {{ $payment->createdBy->name }}</span>
                                <span class="text-base font-extrabold text-emerald-700">
                                    + {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Facture</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Mode</th>
                                <th class="py-3 px-4">Référence</th>
                                <th class="py-3 px-4 text-right">Montant</th>
                                <th class="py-3 px-4">Enregistré par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $payment->payment_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <a href="{{ route('factures.show', $payment->invoice_id) }}" class="text-emerald-600 hover:underline">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-800">
                                        {{ $payment->invoice->customer ? $payment->invoice->customer->name : 'Client de passage' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 font-mono text-xs">
                                        {{ $payment->reference ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600 whitespace-nowrap">
                                        + {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs">
                                        {{ $payment->createdBy->name }}
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

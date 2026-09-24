<x-layouts.app title="Facture {{ $invoice->invoice_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Facture {{ $invoice->invoice_number }}</h2>
                    <x-ui.badge :color="$invoice->status->badgeColor()">
                        {{ $invoice->status->label() }}
                    </x-ui.badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Émise le {{ $invoice->invoice_date->format('d/m/Y à H:i') }} par {{ $invoice->createdBy->name }}
                </p>
            </div>

            <div class="flex items-center flex-wrap gap-2">
                @if($invoice->remaining_amount > 0)
                    <x-ui.button href="{{ route('paiements.create', $invoice) }}" variant="primary" icon="banknotes" size="sm">
                        Enregistrer un règlement
                    </x-ui.button>
                @endif

                <button onclick="window.print()" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50">
                    <x-heroicon-o-document-text class="w-4 h-4 text-slate-500" />
                    <span>Imprimer</span>
                </button>

                <x-ui.button href="{{ route('factures.pdf', $invoice) }}" variant="secondary" icon="arrow-down-tray" size="sm">
                    Télécharger PDF
                </x-ui.button>

                <x-ui.button href="{{ route('factures.whatsapp', $invoice) }}" variant="outline" icon="chat-bubble-left-right" size="sm" target="_blank">
                    Partager WhatsApp
                </x-ui.button>

                <x-ui.button href="{{ route('factures.index') }}" variant="outline" size="sm">
                    Retour
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Printable Invoice Container -->
    <div class="max-w-3xl mx-auto">
        <x-ui.card class="p-6 sm:p-8 bg-white shadow-sm border border-slate-200" id="printable-area">
            <!-- Header Brand & Invoice Title -->
            <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-emerald-600 tracking-tight">Mon-Activité</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Gestion commerciale & négoce</p>
                </div>

                <div class="sm:text-right">
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wide">FACTURE</h2>
                    <p class="text-xs font-mono font-bold text-emerald-700 mt-0.5">N° {{ $invoice->invoice_number }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Date : {{ $invoice->invoice_date->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Customer & Seller Infos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-200 pb-6 mb-6 text-xs">
                <div>
                    <h3 class="font-bold text-slate-400 uppercase text-[10px] tracking-wider mb-1">Émetteur</h3>
                    <p class="font-bold text-slate-800 text-sm">Mon-Activité Sarl</p>
                    <p class="text-slate-600">Vendeur / Caissier : {{ $invoice->createdBy->name }}</p>
                </div>

                <div class="sm:text-right">
                    <h3 class="font-bold text-slate-400 uppercase text-[10px] tracking-wider mb-1">Facturé à</h3>
                    <p class="font-bold text-slate-900 text-sm">
                        {{ $invoice->customer?->name ?? 'Client de passage (Anonyme)' }}
                    </p>
                    @if($invoice->customer)
                        @if($invoice->customer->phone)
                            <p class="text-slate-600">Tél : {{ $invoice->customer->phone }}</p>
                        @endif
                        @if($invoice->customer->address)
                            <p class="text-slate-600">Adresse : {{ $invoice->customer->address }}</p>
                        @endif
                        @if($invoice->customer->type === 'entreprise' && $invoice->customer->ifu)
                            <p class="text-slate-600 font-mono">IFU : {{ $invoice->customer->ifu }}</p>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Lines Table -->
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Désignation</th>
                            <th class="py-2.5 px-3">Unité</th>
                            <th class="py-2.5 px-3 text-right">Qté</th>
                            <th class="py-2.5 px-3 text-right">P.U.</th>
                            <th class="py-2.5 px-3 text-right">Total FCFA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 border-b border-slate-200">
                        @foreach($invoice->lines as $line)
                            <tr>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-900">{{ $line->product->name }}</div>
                                    @if($line->discount_reason)
                                        <div class="text-[10px] text-amber-700 italic">
                                            Remise / Écart : {{ $line->discount_reason }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $line->stockUnit->name }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-slate-900">
                                    {{ number_format($line->quantity, 2, ',', ' ') }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-slate-900">
                                    {{ number_format($line->unit_price, 0, ',', ' ') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-slate-900">
                                    {{ number_format($line->subtotal, 0, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Totals -->
            <div class="flex justify-end mb-8 text-xs">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Sous-total</span>
                        <span class="font-bold text-slate-900">{{ number_format($invoice->subtotal_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-amber-700">
                            <span>Remise globale</span>
                            <span class="font-bold">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-200">
                        <span>Total Facture</span>
                        <span class="text-emerald-700">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Montant Réglé</span>
                        <span class="font-semibold text-emerald-600">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-100"
                         class="{{ $invoice->remaining_amount > 0 ? 'text-amber-800' : 'text-slate-500' }}">
                        <span>Reste à Payer</span>
                        <span>{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Footer Message -->
            <div class="border-t border-slate-200 pt-4 text-center text-[11px] text-slate-400">
                Merci de votre confiance ! — Document reconstruit à la demande à partir de la base de données.
            </div>
        </x-ui.card>

        @if($invoice->payments->count() > 0)
            <x-ui.card class="mt-6 p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-emerald-600" />
                    Historique des Règlements de cette Facture
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Mode</th>
                                <th class="py-2.5 px-3">Référence</th>
                                <th class="py-2.5 px-3 text-right">Montant</th>
                                <th class="py-2.5 px-3">Enregistré par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="py-2.5 px-3 text-slate-600">{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-3"><x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge></td>
                                    <td class="py-2.5 px-3 text-slate-500 font-mono text-[11px]">{{ $payment->reference ?: '-' }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-emerald-600">+ {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $payment->createdBy->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

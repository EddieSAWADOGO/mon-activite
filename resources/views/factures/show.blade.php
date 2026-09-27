<x-layouts.app title="Facture {{ $invoice->invoice_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <x-ui.back-button href="{{ route('factures.index') }}" label="Retour" />
                <div>
                    <div class="flex items-center justify-center sm:justify-start gap-2">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Facture {{ $invoice->invoice_number }}</h1>
                        <x-ui.badge :color="$invoice->status->badgeColor()">
                            {{ $invoice->status->label() }}
                        </x-ui.badge>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Émise le {{ $invoice->invoice_date->format('d/m/Y à H:i') }} par <strong class="text-slate-700">{{ $invoice->createdBy->name }}</strong>
                    </p>
                </div>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                @if($invoice->remaining_amount > 0)
                    <x-ui.button href="{{ route('paiements.create', $invoice) }}" variant="primary" icon="banknotes" size="sm" title="Saisir un règlement pour cette facture" class="w-full sm:w-auto">
                        Régler ({{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA)
                    </x-ui.button>
                @endif

                <button onclick="window.print()" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs cursor-pointer" title="Imprimer la facture">
                    <x-heroicon-o-printer class="w-4 h-4 text-slate-600" />
                    <span>Imprimer</span>
                </button>

                <x-ui.button href="{{ route('factures.pdf', $invoice) }}" variant="secondary" icon="arrow-down-tray" size="sm" title="Télécharger le fichier PDF" class="w-full sm:w-auto">
                    PDF
                </x-ui.button>

                <x-ui.button href="{{ route('factures.whatsapp', $invoice) }}" variant="outline" icon="chat-bubble-left-right" size="sm" target="_blank" title="Envoyer par WhatsApp" class="w-full sm:w-auto">
                    WhatsApp
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Printable Corporate Invoice Container -->
    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card class="p-6 sm:p-10 bg-white shadow-sm border border-slate-200 rounded-3xl" id="printable-area">

            <!-- Corporate Header & Brand -->
            <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 mb-6 gap-6">
                <div class="flex items-start gap-4">
                    <img src="/logo.webp" alt="Logo Mon-Activité" class="w-12 h-12 object-contain shrink-0 mt-1">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">MON-ACTIVITÉ SARL</h1>
                        <p class="text-xs font-semibold text-emerald-700 mt-1 uppercase tracking-wider">Commerce Général & Négoce d'Intrants</p>
                        <p class="text-xs text-slate-500 mt-1">
                            Cotonou, République du Bénin &bull; Tél: +229 97 00 11 22<br>
                            IFU: 3202112345678 &bull; RCCM: RB/COT/21 B 12345
                        </p>
                    </div>
                </div>

                <div class="sm:text-right bg-slate-50 p-4 rounded-2xl border border-slate-200/80 shrink-0 w-full sm:w-auto">
                    <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full mb-1">
                        FACTURE OFFICIELLE
                    </span>
                    <h2 class="text-lg font-mono font-extrabold text-slate-900 leading-tight">N° {{ $invoice->invoice_number }}</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Date: <strong class="text-slate-800">{{ $invoice->invoice_date->format('d/m/Y H:i') }}</strong>
                    </p>
                </div>
            </div>

            <!-- Customer & Issuer Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-200 pb-6 mb-6 text-xs sm:text-sm">
                <!-- Seller Card -->
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Émetteur / Vendeur</p>
                    <p class="font-extrabold text-slate-900">MON-ACTIVITÉ SARL</p>
                    <p class="text-slate-600 text-xs">Vendeur / Caissier : <strong class="text-slate-800">{{ $invoice->createdBy->name }}</strong></p>
                    <p class="text-slate-500 text-xs">Email : {{ $invoice->createdBy->email }}</p>
                </div>

                <!-- Customer Card -->
                <div class="p-4 bg-emerald-50/40 rounded-2xl border border-emerald-100/80 space-y-1 sm:text-right">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-700 mb-1">Facturé à (Client)</p>
                    <p class="font-extrabold text-slate-900 text-base">
                        {{ $invoice->customer?->name ?? 'Client de passage (Comptoir)' }}
                    </p>
                    @if($invoice->customer)
                        <div class="text-xs text-slate-600 space-y-0.5">
                            @if($invoice->customer->type === 'entreprise')
                                <p><span class="font-semibold text-emerald-800">Entreprise</span> &bull; Contact: {{ $invoice->customer->contact_person ?? '-' }}</p>
                            @else
                                <p><span class="font-semibold text-sky-800">Particulier</span></p>
                            @endif

                            @if($invoice->customer->phone)
                                <p>Tél : <strong class="text-slate-800">{{ $invoice->customer->phone }}</strong></p>
                            @endif
                            @if($invoice->customer->address)
                                <p>Adresse : {{ $invoice->customer->address }}</p>
                            @endif
                            @if($invoice->customer->type === 'entreprise' && $invoice->customer->ifu)
                                <p class="font-mono text-xs">IFU : {{ $invoice->customer->ifu }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Vente anonyme au comptoir</p>
                    @endif
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-900 text-white font-bold uppercase text-[10px] sm:text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 rounded-l-xl">#</th>
                            <th class="py-3 px-4">Désignation du Produit</th>
                            <th class="py-3 px-4">Conditionnement</th>
                            <th class="py-3 px-4 text-right">Qté</th>
                            <th class="py-3 px-4 text-right">P.U. (FCFA)</th>
                            <th class="py-3 px-4 text-right rounded-r-xl">Total (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 border-b border-slate-200">
                        @foreach($invoice->lines as $index => $line)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono text-slate-400 text-xs">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $line->product->name }}</div>
                                    @if($line->discount_reason)
                                        <div class="text-xs text-amber-800 bg-amber-50 inline-block px-2 py-0.5 rounded border border-amber-200 mt-1">
                                            Motif remise/écart : <strong>{{ $line->discount_reason }}</strong>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $line->stockUnit->name }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                    {{ number_format($line->quantity, 2, ',', ' ') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-800 whitespace-nowrap">
                                    {{ number_format($line->unit_price, 0, ',', ' ') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                    {{ number_format($line->subtotal, 0, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary Box -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-6 mb-8 pt-2">
                <div class="text-xs text-slate-500 space-y-2 max-w-sm">
                    <p class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Conditions de règlement</p>
                    <p>Paiement cash à la livraison ou selon échéances convenues. Aucun escompte accordé pour paiement anticipé.</p>
                    <p class="text-[11px] text-slate-400 italic">Document officiel reconstruit en temps réel à partir du registre immuable des factures.</p>
                </div>

                <div class="w-full sm:w-80 bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-2.5 text-xs sm:text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Sous-total HT</span>
                        <span class="font-bold text-slate-900">{{ number_format($invoice->subtotal_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-amber-700">
                            <span>Remise accordée</span>
                            <span class="font-bold">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>NET À PAYER</span>
                        <span class="text-emerald-700">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Montant Réglé</span>
                        <span class="font-bold text-emerald-600">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between text-sm font-extrabold pt-2 border-t border-slate-200/80 p-2 rounded-xl {{ $invoice->remaining_amount > 0 ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }}">
                        <span>SOLDE RESTANT</span>
                        <span>{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Footer Message -->
            <div class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400 space-y-1">
                <p class="font-bold text-slate-600">Merci de votre confiance et à bientôt !</p>
                <p class="text-[11px]">Mon-Activité SARL — Système de Gestion Commerciale & Traçabilité</p>
            </div>
        </x-ui.card>

        <!-- Payments History Block -->
        @if($invoice->payments->count() > 0)
            <x-ui.card class="p-6 rounded-3xl">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-5 h-5 text-emerald-600" />
                    <span>Historique des règlements enregistrés pour cette facture</span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Mode</th>
                                <th class="py-2.5 px-3">Référence</th>
                                <th class="py-2.5 px-3 text-right">Montant Réglé</th>
                                <th class="py-2.5 px-3">Opérateur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="py-2.5 px-3 text-slate-700 font-medium">{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-3"><x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge></td>
                                    <td class="py-2.5 px-3 text-slate-500 font-mono text-xs">{{ $payment->reference ?: '-' }}</td>
                                    <td class="py-2.5 px-3 text-right font-extrabold text-emerald-600">+ {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-2.5 px-3 text-slate-600">{{ $payment->createdBy->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

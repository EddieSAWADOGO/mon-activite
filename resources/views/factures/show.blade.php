<x-layouts.app title="Facture {{ $invoice->invoice_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2.5 min-w-0">
                <x-ui.back-button href="{{ route('factures.index') }}" label="Retour" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight truncate">
                            Facture {{ $invoice->invoice_number }}
                        </h1>
                        <x-ui.badge :color="$invoice->status->badgeColor()">
                            {{ $invoice->status->label() }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                @if($invoice->remaining_amount > 0)
                    <x-ui.button href="{{ route('paiements.create', $invoice) }}" variant="primary" icon="banknotes" size="sm" title="Saisir un règlement pour cette facture" class="w-full sm:w-auto">
                        Régler ({{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA)
                    </x-ui.button>
                @endif


                <div x-data="{ downloading: false }">
                    <x-ui.button href="{{ route('factures.pdf', $invoice) }}" @click="downloading = true; setTimeout(() => downloading = false, 4000)" variant="secondary" icon="arrow-down-tray" size="sm" title="Télécharger le fichier PDF" class="w-full sm:w-auto">
                        <span x-text="downloading ? 'Génération PDF...' : 'PDF'">PDF</span>
                    </x-ui.button>
                </div>

                <a href="{{ route('factures.whatsapp', $invoice) }}" target="_blank" title="Envoyer le récapitulatif par WhatsApp" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs sm:text-sm font-bold hover:bg-emerald-100 focus:ring-2 focus:ring-emerald-500/20 transition shadow-2xs cursor-pointer">
                    <x-ui.whatsapp-icon class="w-4 h-4 text-[#25D366] fill-[#25D366] shrink-0" />
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-area, #printable-area * {
                visibility: visible;
            }
            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Printable Paper Invoice Container -->
        <div class="p-4 sm:p-10 lg:p-12 bg-white shadow-xs border border-slate-200 rounded-2xl relative overflow-hidden space-y-5 sm:space-y-6" id="printable-area">

            <!-- Exponentially Huge & Rotated Centered Background Logo / Watermark -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.08] select-none z-0 overflow-hidden p-4 transform -rotate-12">
                @if($company->logo_path)
                    <img src="{{ $company->logo_path }}" alt="Logo {{ $company->name }}" class="w-[450px] sm:w-[600px] h-auto max-w-full object-contain">
                @else
                    <div class="text-center">
                        <div class="text-6xl sm:text-[110px] font-black uppercase tracking-widest text-slate-900 border-[8px] sm:border-[12px] border-slate-900 p-6 sm:p-8 rounded-3xl">
                            {{ strtoupper(substr($company->name, 0, 4)) }}
                        </div>
                        <p class="text-xl sm:text-3xl font-black uppercase tracking-widest text-slate-900 mt-4">
                            {{ $company->name }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Invoice Content Body -->
            <div class="relative z-10 space-y-5 sm:space-y-6">

                <!-- 1. En-tête : FACTURE N° [ref] centré, Date d'émission dessous -->
                <div class="text-center pb-4 sm:pb-6 border-b-2 border-slate-900 space-y-1">
                    <h1 class="text-xl sm:text-3xl font-black text-slate-900 tracking-wider uppercase">
                        FACTURE <span class="font-mono ml-2">N° {{ $invoice->invoice_number }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm font-bold text-slate-700 italic">
                        Date d'émission : <strong class="text-slate-900 not-italic">{{ $invoice->invoice_date->format('d/m/Y à H:i') }}</strong>
                    </p>
                </div>

                <!-- 2. Bloc Émetteur (Entreprise) -->
                <div class="border-b border-slate-300 pb-5 sm:pb-6 text-center sm:text-left">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-5">

                        <div class="space-y-0.5 text-xs sm:text-sm text-slate-800 font-semibold">
                            <h2 class="text-lg sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                                {{ $company->name }}
                            </h2>
                            @if($company->tagline)
                                <p class="text-2xs sm:text-xs font-black text-emerald-800 uppercase tracking-wider pb-1">
                                    {{ $company->tagline }}
                                </p>
                            @endif
                            @if($company->address)
                                <p>Adresse : <strong class="text-slate-900">{{ $company->address }}</strong></p>
                            @endif
                            @if($company->phone)
                                <p>Téléphone : <strong class="text-slate-900 font-extrabold">{{ $company->phone }}</strong></p>
                            @endif
                            @if($company->email)
                                <p>E-mail : <strong class="text-slate-900">{{ $company->email }}</strong></p>
                            @endif
                            @if($company->ifu)
                                <p class="font-mono">N° IFU : <strong class="text-slate-900 font-black">{{ $company->ifu }}</strong></p>
                            @endif
                            @if($company->rccm)
                                <p class="font-mono">N° RCCM : <strong class="text-slate-900 font-black">{{ $company->rccm }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. Bloc Client (FACTURÉ À) -->
                <div class="pb-5 sm:pb-6 border-b border-slate-300 text-center sm:text-left text-xs sm:text-sm space-y-1">
                    <p class="text-2xs sm:text-xs font-black uppercase tracking-widest text-slate-500">
                        FACTURÉ À :
                    </p>
                    <p class="text-base sm:text-xl font-black text-slate-900">
                        {{ $invoice->customer?->name ?? 'Client au comptoir' }}
                    </p>
                    @if($invoice->customer)
                        <div class="text-xs sm:text-sm text-slate-800 font-semibold space-y-0.5 pt-0.5">
                            @if($invoice->customer->phone)
                                <p>Téléphone : <strong class="font-mono text-slate-900">{{ $invoice->customer->phone }}</strong></p>
                            @endif
                            @if($invoice->customer->address)
                                <p>Adresse : {{ $invoice->customer->address }}</p>
                            @endif
                            @if($invoice->customer->type === 'entreprise')
                                @if($invoice->customer->contact_person)
                                    <p>Représentant : {{ $invoice->customer->contact_person }}</p>
                                @endif
                                @if($invoice->customer->ifu)
                                    <p class="font-mono font-black text-slate-900 text-xs sm:text-sm">N° IFU Client : {{ $invoice->customer->ifu }}</p>
                                @endif
                                @if($invoice->customer->rccm)
                                    <p class="font-mono text-slate-900">N° RCCM Client : {{ $invoice->customer->rccm }}</p>
                                @endif
                            @endif
                        </div>
                    @else
                        <p class="text-xs italic text-slate-400">Vente directe au comptoir</p>
                    @endif
                </div>

                <!-- 4. Tableau des lignes d'articles -->
                <div class="overflow-x-auto mb-5 sm:mb-6">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[500px] whitespace-nowrap">
                        <thead class="bg-slate-900 text-white font-extrabold uppercase text-xs tracking-wider">
                            <tr>
                                <th class="py-2.5 px-3.5 w-10 text-center">#</th>
                                <th class="py-2.5 px-3.5">Désignation</th>
                                <th class="py-2.5 px-3.5">Unité</th>
                                <th class="py-2.5 px-3.5 text-right">Qté</th>
                                <th class="py-2.5 px-3.5 text-right">P.U. (FCFA)</th>
                                <th class="py-2.5 px-3.5 text-right">Total FCFA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white font-semibold">
                            @foreach($invoice->lines as $index => $line)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3.5 text-center font-mono text-slate-500 text-xs">{{ $index + 1 }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 text-xs sm:text-sm">
                                        {{ $line->product->name }}
                                    </td>
                                    <td class="py-3 px-3.5 text-slate-900 font-bold text-xs sm:text-sm">{{ $line->stockUnit->name }}</td>
                                    <td class="py-3 px-3.5 text-right font-black text-slate-900 text-xs sm:text-sm">{{ number_format($line->quantity, 2, ',', ' ') }}</td>
                                    <td class="py-3 px-3.5 text-right text-slate-900 text-xs sm:text-sm">{{ number_format($line->unit_price, 0, ',', ' ') }}</td>
                                    <td class="py-3 px-3.5 text-right font-black text-slate-900 text-xs sm:text-sm">{{ number_format($line->subtotal, 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- 5. Totaux & Point de Crédit du Client -->
                <div class="space-y-4 pt-2 mb-6">
                    <div class="flex justify-center sm:justify-end">
                        <div class="w-full sm:w-96 space-y-2 text-xs sm:text-sm text-center sm:text-right font-bold">
                            @if($invoice->discount_amount > 0)
                                <div class="flex justify-between text-amber-800 font-bold">
                                    <span>Remise :</span>
                                    <span>- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-base sm:text-xl font-black text-slate-900 pt-2.5 border-t-2 border-slate-900">
                                <span>Montant Total Facture :</span>
                                <span class="text-slate-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Point de Crédit / Situation Financière du Client -->
                    <div class="rounded-xl border-2 border-slate-900 bg-white text-xs sm:text-sm text-slate-800 overflow-hidden shadow-2xs">
                        <div class="bg-slate-900 text-white px-3.5 py-2.5 flex flex-col sm:flex-row items-center justify-between gap-2">
                            <span class="font-extrabold uppercase tracking-wide text-xs sm:text-sm flex items-center gap-1.5">
                                <x-heroicon-o-calculator class="w-4 h-4 text-emerald-400 shrink-0" />
                                <span>SITUATION DE CRÉDIT & COMPTE CLIENT</span>
                            </span>
                            @if($invoice->customer)
                                <a href="{{ route('clients.statement-pdf', $invoice->customer) }}"
                                   title="Télécharger le relevé de compte complet du client au format PDF"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-2xs sm:text-xs transition">
                                    <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 shrink-0" />
                                    <span>Télécharger le Relevé de Compte (PDF)</span>
                                </a>
                            @endif
                        </div>

                        <div class="p-3.5 sm:p-4 space-y-3 font-medium bg-slate-50/50">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center sm:text-left">
                                <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                    <span class="text-slate-500 block text-2xs font-bold uppercase">Reliquat cette facture (N° {{ $invoice->invoice_number }})</span>
                                    <strong class="text-slate-900 text-sm sm:text-base font-black">
                                        {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA
                                    </strong>
                                </div>
                                <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                    <span class="text-slate-500 block text-2xs font-bold uppercase">Anciens crédits (Factures précédentes)</span>
                                    <strong class="{{ $previousCustomerDebt > 0 ? 'text-amber-800' : 'text-slate-900' }} text-sm sm:text-base font-black">
                                        {{ number_format($previousCustomerDebt, 0, ',', ' ') }} FCFA
                                    </strong>
                                </div>
                                <div class="p-2.5 bg-slate-900 text-white rounded-xl border border-slate-900">
                                    <span class="text-slate-300 block text-2xs font-bold uppercase">TOTAL DÛ GLOBAL PAR LE CLIENT</span>
                                    <strong class="text-emerald-400 text-base sm:text-lg font-black">
                                        {{ number_format($invoice->remaining_amount + $previousCustomerDebt, 0, ',', ' ') }} FCFA
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Pied de page -->
                <div class="border-t-2 border-slate-900 pt-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-2xs sm:text-xs text-slate-800 font-bold text-center sm:text-left">
                    <p class="font-bold text-slate-900">
                        {{ $company->name }} @if($company->ifu) &bull; IFU : {{ $company->ifu }} @endif @if($company->rccm) &bull; RCCM : {{ $company->rccm }} @endif
                    </p>
                    <p class="text-slate-800">
                        Facturé par : <strong class="text-slate-900 font-black">{{ $invoice->createdBy->name }}</strong>
                    </p>
                </div>

            </div>
        </div>

        <!-- Payments History Block (Internal Caisse tracking) -->
        @if($invoice->payments->count() > 0)
            <x-ui.card class="p-4 sm:p-6 rounded-2xl">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" />
                    <span>Suivi interne des règlements (Gestion de caisse)</span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[500px] whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-2xs sm:text-xs border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Mode</th>
                                <th class="py-2.5 px-3">Référence</th>
                                <th class="py-2.5 px-3 text-right">Montant Réglé</th>
                                <th class="py-2.5 px-3">Agent</th>
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

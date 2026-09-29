<x-layouts.app title="Détail du Bon d'Achat">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Bon d'Achat N° {{ $purchase->purchase_number }}
                    </h2>
                    <x-ui.badge :color="$purchase->remaining_amount == 0 ? 'emerald' : ($purchase->paid_amount > 0 ? 'amber' : 'red')">
                        {{ $purchase->remaining_amount == 0 ? 'Réglé' : ($purchase->paid_amount > 0 ? 'Partiel' : 'Impayé') }}
                    </x-ui.badge>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Enregistré le {{ $purchase->created_at->format('d/m/Y à H:i') }} par {{ $purchase->createdBy->name }}
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                @if($purchase->remaining_amount > 0)
                    @can('create', App\Domain\Paiements\Models\Payment::class)
                        <x-ui.button href="{{ route('paiements.create-purchase', $purchase) }}" variant="primary" icon="banknotes" size="sm">
                            Enregistrer un règlement
                        </x-ui.button>
                    @endcan
                @endif
                <x-ui.back-button href="{{ route('achats.index') }}" label="Retour" />
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Lines Table -->
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-emerald-600" />
                    <span>Produits Achetés</span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[600px]">
                        <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4 whitespace-nowrap">Produit</th>
                                <th class="py-3 px-4 whitespace-nowrap">Unité / Format</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap">Quantité</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap">P.U. Achat</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($purchase->lines as $line)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $line->product->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $line->stockUnit->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                        {{ rtrim(rtrim(number_format($line->quantity, 4, ',', ' '), '0'), ',') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-700 font-medium whitespace-nowrap">
                                        {{ number_format($line->unit_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 text-sm sm:text-base whitespace-nowrap">
                                        {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <!-- Payments History Block -->
            @if($purchase->payments->count() > 0)
                <x-ui.card class="p-4 sm:p-6">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-5 h-5 text-emerald-600" />
                        <span>Historique des Règlements au Fournisseur ({{ $purchase->payments->count() }})</span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm min-w-[500px]">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-3 whitespace-nowrap">Date</th>
                                    <th class="py-2.5 px-3 whitespace-nowrap">Mode</th>
                                    <th class="py-2.5 px-3 whitespace-nowrap">Référence</th>
                                    <th class="py-2.5 px-3 text-right whitespace-nowrap">Montant</th>
                                    <th class="py-2.5 px-3 whitespace-nowrap">Auteur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($purchase->payments as $payment)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-2.5 px-3 text-slate-700 font-medium whitespace-nowrap">{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                        <td class="py-2.5 px-3 whitespace-nowrap"><x-ui.badge color="sky">{{ $payment->payment_method }}</x-ui.badge></td>
                                        <td class="py-2.5 px-3 text-slate-500 font-mono text-xs whitespace-nowrap">{{ $payment->reference ?: '-' }}</td>
                                        <td class="py-2.5 px-3 text-right font-extrabold text-emerald-600 whitespace-nowrap">+ {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">{{ $payment->createdBy ? $payment->createdBy->name : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endif

            @if($purchase->notes)
                <x-ui.card class="p-4 sm:p-6">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notes & Observations</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200">
                        {{ $purchase->notes }}
                    </p>
                </x-ui.card>
            @endif
        </div>

        <!-- Summary & Supplier Info Sidebar -->
        <div class="space-y-6">
            <x-ui.card class="p-4 sm:p-6 bg-slate-900 text-white">
                <h3 class="text-sm sm:text-base font-bold text-emerald-400 border-b border-slate-800 pb-3 mb-4">
                    Récapitulatif Financier
                </h3>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center text-slate-300">
                        <span>Montant Total Achat :</span>
                        <span class="font-extrabold text-lg text-white">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-300">
                        <span>Montant Payé :</span>
                        <span class="font-bold text-emerald-400">{{ number_format($purchase->paid_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex justify-between items-center text-slate-300">
                        <span>Reste à Payer :</span>
                        <span class="font-black text-base text-red-400">{{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if($purchase->remaining_amount > 0)
                        @can('create', App\Domain\Paiements\Models\Payment::class)
                            <div class="pt-3 border-t border-slate-800">
                                <x-ui.button href="{{ route('paiements.create-purchase', $purchase) }}" variant="primary" size="sm" icon="banknotes" class="w-full justify-center">
                                    Régler ce solde
                                </x-ui.button>
                            </div>
                        @endcan
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card class="p-4 sm:p-6">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-3 flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 text-emerald-600" />
                    <span>Fournisseur</span>
                </h3>

                <div class="space-y-2.5 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-500 font-medium">Nom :</span>
                        <span class="font-extrabold text-slate-900 block text-sm sm:text-base">{{ $purchase->supplier->name }}</span>
                    </div>

                    @if($purchase->supplier->phone)
                        <div>
                            <span class="text-slate-500 font-medium">Téléphone :</span>
                            <span class="font-semibold text-slate-800 block">{{ $purchase->supplier->phone }}</span>
                        </div>
                    @endif

                    @if($purchase->supplier->address)
                        <div>
                            <span class="text-slate-500 font-medium">Adresse :</span>
                            <span class="text-slate-700 block">{{ $purchase->supplier->address }}</span>
                        </div>
                    @endif

                    <div class="pt-2">
                        <x-ui.button href="{{ route('fournisseurs.show', $purchase->supplier) }}" variant="outline" size="sm" class="w-full">
                            Fiche Fournisseur
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>

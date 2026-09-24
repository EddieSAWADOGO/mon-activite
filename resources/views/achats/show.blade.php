<x-layouts.app title="Détail du Bon d'Achat">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                        Bon d'Achat N° {{ $purchase->purchase_number }}
                    </h2>
                    <x-ui.badge :color="$purchase->remaining_amount == 0 ? 'emerald' : ($purchase->paid_amount > 0 ? 'amber' : 'red')">
                        {{ $purchase->remaining_amount == 0 ? 'Réglé' : ($purchase->paid_amount > 0 ? 'Partiel' : 'Impayé') }}
                    </x-ui.badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Enregistré le {{ $purchase->created_at->format('d/m/Y à H:i') }} par {{ $purchase->createdBy->name }}
                </p>
            </div>

            <x-ui.button href="{{ route('achats.index') }}" variant="secondary" size="sm">
                Retour à la liste
            </x-ui.button>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Lines Table -->
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-emerald-600" />
                    <span>Produits Achetés</span>
                </h3>

                <div class="overflow-x-auto">
                    <x-ui.table>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Produit</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Unité / Format</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">P.U. Achat</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Sous-total</th>
                            </tr>
                        </x-slot>

                        @foreach($purchase->lines as $line)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-xs font-bold text-slate-900">
                                    {{ $line->product->name }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $line->stockUnit->name }}
                                </td>
                                <td class="px-4 py-3 text-xs text-right font-mono text-slate-900">
                                    {{ rtrim(rtrim(number_format($line->quantity, 4, ',', ' '), '0'), ',') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-right text-slate-700">
                                    {{ number_format($line->unit_price, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 text-xs text-right font-bold text-slate-900">
                                    {{ number_format($line->subtotal, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </div>
            </x-ui.card>

            @if($purchase->notes)
                <x-ui.card class="p-4 sm:p-6">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notes & Observations</h3>
                    <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                        {{ $purchase->notes }}
                    </p>
                </x-ui.card>
            @endif
        </div>

        <!-- Summary & Supplier Info Sidebar -->
        <div class="space-y-6">
            <x-ui.card class="p-4 sm:p-6 bg-slate-900 text-white">
                <h3 class="text-sm font-bold text-emerald-400 border-b border-slate-800 pb-3 mb-4">
                    Récapitulatif Financier
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-300">
                        <span>Montant Total Achat :</span>
                        <span class="font-bold text-lg text-white">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="flex justify-between text-slate-300">
                        <span>Montant Payé :</span>
                        <span class="font-semibold text-emerald-400">{{ number_format($purchase->paid_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex justify-between text-slate-300">
                        <span>Reste à Payer :</span>
                        <span class="font-bold text-base text-red-400">{{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-4 sm:p-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-3 flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 text-emerald-600" />
                    <span>Fournisseur</span>
                </h3>

                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-slate-500">Nom :</span>
                        <span class="font-bold text-slate-900 block text-sm">{{ $purchase->supplier->name }}</span>
                    </div>

                    @if($purchase->supplier->phone)
                        <div>
                            <span class="text-slate-500">Téléphone :</span>
                            <span class="font-medium text-slate-800 block">{{ $purchase->supplier->phone }}</span>
                        </div>
                    @endif

                    @if($purchase->supplier->address)
                        <div>
                            <span class="text-slate-500">Adresse :</span>
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

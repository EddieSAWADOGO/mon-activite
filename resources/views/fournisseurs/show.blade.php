<x-layouts.app title="Fiche Fournisseur">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ $supplier->name }}
                    </h2>
                    <x-ui.badge :color="$supplier->is_active ? 'emerald' : 'slate'">
                        {{ $supplier->is_active ? 'Actif' : 'Archivé' }}
                    </x-ui.badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Fiche détaillée et suivi des engagements financiers
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button href="{{ route('fournisseurs.edit', $supplier) }}" variant="secondary" size="sm" icon="pencil-square">
                    Éditer
                </x-ui.button>
                <x-ui.button href="{{ route('fournisseurs.index') }}" variant="outline" size="sm">
                    Retour
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-ui.stat-card
            title="Total Achats"
            :value="number_format($totalPurchased, 0, ',', ' ') . ' FCFA'"
            icon="arrow-down-tray"
            color="emerald" />

        <x-ui.stat-card
            title="Total Payé"
            :value="number_format($totalPaid, 0, ',', ' ') . ' FCFA'"
            icon="check-circle"
            color="sky" />

        <x-ui.stat-card
            title="Dette / Reste Dû"
            :value="number_format($totalRemaining, 0, ',', ' ') . ' FCFA'"
            icon="exclamation-triangle"
            :color="$totalRemaining > 0 ? 'red' : 'emerald'" />
    </div>

    <!-- Supplier Details & Recent Purchases -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card class="p-4 sm:p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3">
                Coordonnées & Informations
            </h3>

            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-500">Type :</span>
                    <span class="font-medium text-slate-900 block">{{ $supplier->type === 'company' ? 'Entreprise / Société' : 'Particulier' }}</span>
                </div>

                @if($supplier->contact_person)
                    <div>
                        <span class="text-slate-500">Personne de contact :</span>
                        <span class="font-medium text-slate-900 block">{{ $supplier->contact_person }}</span>
                    </div>
                @endif

                @if($supplier->phone)
                    <div>
                        <span class="text-slate-500">Téléphone :</span>
                        <span class="font-medium text-slate-900 block">{{ $supplier->phone }}</span>
                    </div>
                @endif

                @if($supplier->whatsapp)
                    <div>
                        <span class="text-slate-500">WhatsApp :</span>
                        <span class="font-medium text-slate-900 block">{{ $supplier->whatsapp }}</span>
                    </div>
                @endif

                @if($supplier->email)
                    <div>
                        <span class="text-slate-500">Email :</span>
                        <span class="font-medium text-slate-900 block">{{ $supplier->email }}</span>
                    </div>
                @endif

                @if($supplier->address)
                    <div>
                        <span class="text-slate-500">Adresse :</span>
                        <span class="text-slate-800 block">{{ $supplier->address }}</span>
                    </div>
                @endif

                @if($supplier->notes)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-500">Notes :</span>
                        <p class="text-slate-700 bg-slate-50 p-2 rounded border border-slate-200 mt-1">{{ $supplier->notes }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        <x-ui.card class="lg:col-span-2 p-4 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Derniers Achats chez ce Fournisseur</h3>
                @can('create', App\Domain\Achats\Models\Purchase::class)
                    <x-ui.button href="{{ route('achats.create') }}" variant="primary" size="sm" icon="plus">
                        Nouvel Achat
                    </x-ui.button>
                @endcan
            </div>

            @if($supplier->purchases->isEmpty())
                <x-ui.empty-state
                    title="Aucun achat enregistré"
                    description="Aucun achat n'a encore été réalisé auprès de ce fournisseur."
                    icon="arrow-down-tray" />
            @else
                <x-ui.table>
                    <x-slot name="header">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-bold text-slate-700 uppercase">N° Achat</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-slate-700 uppercase">Montant</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-slate-700 uppercase">Reste</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-slate-700 uppercase">Action</th>
                        </tr>
                    </x-slot>

                    @foreach($supplier->purchases as $purchase)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 font-mono text-xs font-bold text-slate-900">
                                {{ $purchase->purchase_number }}
                            </td>
                            <td class="px-3 py-2 text-xs text-slate-600">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-bold text-slate-900">
                                {{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-medium text-red-600">
                                {{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-3 py-2 text-center">
                                <x-ui.button href="{{ route('achats.show', $purchase) }}" variant="outline" size="sm">
                                    Voir
                                </x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>

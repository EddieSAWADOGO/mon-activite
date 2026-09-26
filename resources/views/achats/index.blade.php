<x-layouts.app title="Gestion des Achats">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-emerald-600" />
                    <span>Achats / Approvisionnements</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Enregistrement et suivi des commandes effectuées auprès des fournisseurs
                </p>
            </div>

            @can('create', App\Domain\Achats\Models\Purchase::class)
                <div>
                    <x-ui.button href="{{ route('achats.create') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
                        Nouveau Bon d'Achat
                    </x-ui.button>
                </div>
            @endcan
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('achats.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="N° d'achat ou fournisseur..."
                   class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

            <select name="supplier_id" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                <option value="">Tous les fournisseurs</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">

            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">

            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                Filtrer
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Content -->
    @if($purchases->isEmpty())
        <x-ui.empty-state
            title="Aucun bon d'achat enregistré"
            description="Enregistrez un premier approvisionnement pour augmenter les stocks de vos produits."
            icon="arrow-down-tray">
            @can('create', App\Domain\Achats\Models\Purchase::class)
                <x-ui.button href="{{ route('achats.create') }}" variant="primary" icon="plus" class="mt-4">
                    Enregistrer un achat
                </x-ui.button>
            @endcan
        </x-ui.empty-state>
    @else
        <!-- Mobile Cards View -->
        <div class="space-y-3 sm:hidden">
            @foreach($purchases as $purchase)
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div>
                            <span class="font-mono text-xs font-bold text-slate-900">{{ $purchase->purchase_number }}</span>
                            <p class="text-xs text-slate-500">{{ $purchase->purchase_date->format('d/m/Y') }}</p>
                        </div>
                        <x-ui.badge :color="$purchase->remaining_amount == 0 ? 'emerald' : ($purchase->paid_amount > 0 ? 'amber' : 'red')">
                            {{ $purchase->remaining_amount == 0 ? 'Réglé' : ($purchase->paid_amount > 0 ? 'Partiel' : 'Impayé') }}
                        </x-ui.badge>
                    </div>

                    <div class="text-xs space-y-1">
                        <div class="flex justify-between text-slate-600">
                            <span>Fournisseur :</span>
                            <span class="font-semibold text-slate-900">{{ $purchase->supplier->name }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Montant Total :</span>
                            <span class="font-bold text-slate-900">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Reste à payer :</span>
                            <span class="font-semibold text-red-600">{{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex justify-end">
                        <x-ui.button href="{{ route('achats.show', $purchase) }}" variant="outline" size="sm" icon="clock">
                            Consulter le détail
                        </x-ui.button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden sm:block bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">N° Achat</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Fournisseur</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Montant Total</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Payé</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Reste</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Action</th>
                    </tr>
                </x-slot>

                @foreach($purchases as $purchase)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-slate-900">
                            {{ $purchase->purchase_number }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $purchase->purchase_date->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-900">
                            {{ $purchase->supplier->name }}
                        </td>
                        <td class="px-4 py-3 text-xs font-bold text-right text-slate-900">
                            {{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 py-3 text-xs text-right text-emerald-700 font-medium">
                            {{ number_format($purchase->paid_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 py-3 text-xs text-right text-red-600 font-medium">
                            {{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge :color="$purchase->remaining_amount == 0 ? 'emerald' : ($purchase->paid_amount > 0 ? 'amber' : 'red')">
                                {{ $purchase->remaining_amount == 0 ? 'Réglé' : ($purchase->paid_amount > 0 ? 'Partiel' : 'Impayé') }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <x-ui.button href="{{ route('achats.show', $purchase) }}" variant="outline" size="sm">
                                Voir
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    @endif
</x-layouts.app>

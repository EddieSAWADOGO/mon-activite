<x-layouts.app title="Gestion des Achats">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Achats / Approvisionnements</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Enregistrement et suivi des commandes effectuées auprès des fournisseurs
                </p>
            </div>

            @can('create', App\Domain\Achats\Models\Purchase::class)
                <a href="{{ route('achats.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                    <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                        Nouveau Bon d'Achat
                    </x-ui.button>
                </a>
            @endcan
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6 p-3 sm:p-4">
        <form method="GET" action="{{ route('achats.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 items-center">
            <div class="sm:col-span-2 lg:col-span-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="N° d'achat ou fournisseur..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="supplier_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Tous les fournisseurs</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:col-span-2 lg:col-span-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 sm:px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500"
                       title="Date début">

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-2.5 sm:px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500"
                       title="Date fin">
            </div>

            <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto justify-center">
                    Filtrer
                </x-ui.button>
                @if(request()->hasAny(['search', 'supplier_id', 'start_date', 'end_date']))
                    <a href="{{ route('achats.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Registre des Achats ({{ $purchases->total() }})</span>
        </h2>
    </div>

    <!-- Content -->
    @if($purchases->isEmpty())
        <x-ui.empty-state
            title="Aucun bon d'achat enregistré"
            description="Enregistrez un premier approvisionnement pour augmenter les stocks de vos produits."
            icon="arrow-down-tray">
            @can('create', App\Domain\Achats\Models\Purchase::class)
                <a href="{{ route('achats.create') }}" class="inline-flex w-full sm:w-auto justify-center mt-4">
                    <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                        Enregistrer un achat
                    </x-ui.button>
                </a>
            @endcan
        </x-ui.empty-state>
    @else
        <!-- Table Unifiée Scrollable Horizon -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
            <table class="w-full text-left text-xs sm:text-sm min-w-[650px]">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-xs tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">N° Achat</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Date</th>
                        <th class="py-3.5 px-3.5 sm:px-4 whitespace-nowrap">Fournisseur</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Montant Total</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Payé</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Reste</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-center whitespace-nowrap">Statut</th>
                        <th class="py-3.5 px-3.5 sm:px-4 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchases as $purchase)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap font-mono text-xs font-bold text-slate-900">
                                {{ $purchase->purchase_number }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-xs font-medium text-slate-900">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-xs font-bold text-right text-slate-900">
                                {{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-xs text-right text-emerald-700 font-medium">
                                {{ number_format($purchase->paid_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-xs text-right text-red-600 font-medium">
                                {{ number_format($purchase->remaining_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-center">
                                <x-ui.badge :color="$purchase->remaining_amount == 0 ? 'emerald' : ($purchase->paid_amount > 0 ? 'amber' : 'red')">
                                    {{ $purchase->remaining_amount == 0 ? 'Réglé' : ($purchase->paid_amount > 0 ? 'Partiel' : 'Impayé') }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($purchase->remaining_amount > 0)
                                        @can('create', App\Domain\Paiements\Models\Payment::class)
                                            <a href="{{ route('paiements.create-purchase', $purchase) }}"
                                               class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                               title="Enregistrer un règlement">
                                                <x-heroicon-o-banknotes class="w-4 h-4" />
                                            </a>
                                        @endcan
                                    @endif
                                    <a href="{{ route('achats.show', $purchase) }}"
                                       class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                       title="Consulter le bon d'achat">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    @endif
</x-layouts.app>

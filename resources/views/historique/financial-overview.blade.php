<x-layouts.app title="Créances & Dettes Consolidées">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-banknotes class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 shrink-0" />
                    <span class="truncate">Situation des Créances & Dettes</span>
                </h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Bilan consolidé des impayés clients et dettes envers les fournisseurs
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Cartes récapitulatives globales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-ui.stat-card
                title="Total Créances Clients (Ce qu'on doit me payer)"
                value="{{ number_format($totalCustomerReceivables, 0, ',', ' ') }} FCFA"
                icon="banknotes"
                color="amber" />

            <x-ui.stat-card
                title="Total Dettes Fournisseurs (Ce que je dois payer)"
                value="{{ number_format($totalSupplierDebt, 0, ',', ' ') }} FCFA"
                icon="exclamation-triangle"
                color="red" />
        </div>

        <!-- Section Créances Clients -->
        <x-ui.card class="p-4 sm:p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-user class="w-5 h-5 text-emerald-600" />
                    Créances Clients par Client
                </h2>
            </div>

            @if($customers->isEmpty() || $totalCustomerReceivables == 0)
                <x-ui.empty-state
                    title="Aucune créance client"
                    description="Aucun client n'a de solde restant à payer actuellement."
                    icon="check-circle">
                </x-ui.empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Client</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Total Acheté</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Total Réglé</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Créance / Reste Dû</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customers as $c)
                                @if(($c->total_remaining ?? 0) > 0)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                            {{ $c->name }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-slate-700 whitespace-nowrap">
                                            {{ number_format($c->total_purchased ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-emerald-600 whitespace-nowrap">
                                            {{ number_format($c->total_paid ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-amber-700 whitespace-nowrap">
                                            {{ number_format($c->total_remaining, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap">
                                            <x-ui.button href="{{ route('clients.show', $c) }}" variant="outline" size="sm">
                                                Fiche Client
                                            </x-ui.button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>

        <!-- Section Dettes Fournisseurs -->
        <x-ui.card class="p-4 sm:p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 text-red-600" />
                    Dettes Fournisseurs par Fournisseur
                </h2>
            </div>

            @if($suppliers->isEmpty() || $totalSupplierDebt == 0)
                <x-ui.empty-state
                    title="Aucune dette fournisseur"
                    description="Vous n'avez aucun impayé envers vos fournisseurs actuellement."
                    icon="check-circle">
                </x-ui.empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Fournisseur</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Total Achats</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Total Payé</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Dette / Reste Dû</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($suppliers as $s)
                                @if(($s->total_remaining ?? 0) > 0)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                            {{ $s->name }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-slate-700 whitespace-nowrap">
                                            {{ number_format($s->total_purchased ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-emerald-600 whitespace-nowrap">
                                            {{ number_format($s->total_paid ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-red-600 whitespace-nowrap">
                                            {{ number_format($s->total_remaining, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap">
                                            <x-ui.button href="{{ route('fournisseurs.show', $s) }}" variant="outline" size="sm">
                                                Fiche Fournisseur
                                            </x-ui.button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>

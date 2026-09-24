<x-layouts.app title="Créances & Dettes Consolides">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-banknotes class="w-6 h-6 text-purple-600" />
                Situation globale des Créances & Dettes
            </h1>
            <a href="{{ route('historique.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
                &larr; Retour à l'historique
            </a>
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
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4 text-right">Total Acheté</th>
                                <th class="py-3 px-4 text-right">Total Réglé</th>
                                <th class="py-3 px-4 text-right">Créance / Reste Dû</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customers as $c)
                                @if(($c->total_remaining ?? 0) > 0)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4 font-bold text-slate-900">
                                            {{ $c->name }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-slate-700">
                                            {{ number_format($c->total_purchased ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-emerald-600">
                                            {{ number_format($c->total_paid ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-amber-700 whitespace-nowrap">
                                            {{ number_format($c->total_remaining, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right">
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
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Fournisseur</th>
                                <th class="py-3 px-4 text-right">Total Achats</th>
                                <th class="py-3 px-4 text-right">Total Payé</th>
                                <th class="py-3 px-4 text-right">Dette / Reste Dû</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($suppliers as $s)
                                @if(($s->total_remaining ?? 0) > 0)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4 font-bold text-slate-900">
                                            {{ $s->name }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-slate-700">
                                            {{ number_format($s->total_purchased ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-emerald-600">
                                            {{ number_format($s->total_paid ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-red-600 whitespace-nowrap">
                                            {{ number_format($s->total_remaining, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-4 text-right">
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

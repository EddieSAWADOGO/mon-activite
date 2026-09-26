<x-layouts.app title="Retours Clients">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-arrow-uturn-left class="w-6 h-6 text-emerald-600" />
                Gestion des Retours Clients
            </h1>
            <x-ui.button href="{{ route('retours.create') }}" variant="primary" icon="plus">
                Enregistrer un retour
            </x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="mb-6 p-4">
            <form action="{{ route('retours.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher par N° de retour, produit, client..."
                       class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="restocked" {{ request('status') === 'restocked' ? 'selected' : '' }}>Réintégré</option>
                    <option value="discarded" {{ request('status') === 'discarded' ? 'selected' : '' }}>Déclaré en perte</option>
                </select>

                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                    Filtrer
                </x-ui.button>
            </form>
        </x-ui.card>

        @if($returns->isEmpty())
            <x-ui.empty-state 
                title="Aucun retour enregisté" 
                description="Aucun retour client n'a été enregistré pour le moment." 
                icon="arrow-uturn-left">
            </x-ui.empty-state>
        @else
            <x-ui.card class="p-0 overflow-hidden">
                <div class="block sm:hidden divide-y divide-slate-100">
                    @foreach($returns as $return)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ $return->return_number }}</span>
                                @if($return->isPending())
                                    <x-ui.badge color="amber">En attente validation</x-ui.badge>
                                @elseif($return->isRestocked())
                                    <x-ui.badge color="emerald">Réintégré en stock</x-ui.badge>
                                @else
                                    <x-ui.badge color="red">Déclaré en perte</x-ui.badge>
                                @endif
                            </div>
                            <div class="text-sm font-semibold text-slate-800">{{ $return->product->name }}</div>
                            <div class="flex justify-between text-xs text-slate-600">
                                <span>Quantité: <strong>{{ number_format($return->quantity, 2, ',', ' ') }} {{ $return->stockUnit->name }}</strong></span>
                                <span>Client: {{ $return->customer ? $return->customer->name : 'Passage' }}</span>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <x-ui.button href="{{ route('retours.show', $return) }}" variant="outline" size="sm">
                                    Détails / Valider
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">N° Retour</th>
                                <th class="py-3 px-4">Produit & Unité</th>
                                <th class="py-3 px-4 text-right">Qté</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($returns as $return)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $return->return_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $return->return_number }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $return->product->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $return->stockUnit->name }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                        {{ number_format($return->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700">
                                        {{ $return->customer ? $return->customer->name : 'Client de passage' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($return->isPending())
                                            <x-ui.badge color="amber">En attente</x-ui.badge>
                                        @elseif($return->isRestocked())
                                            <x-ui.badge color="emerald">Réintégré</x-ui.badge>
                                        @else
                                            <x-ui.badge color="red">Perte</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <x-ui.button href="{{ route('retours.show', $return) }}" variant="outline" size="sm">
                                            Détails
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($returns->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $returns->links() }}
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

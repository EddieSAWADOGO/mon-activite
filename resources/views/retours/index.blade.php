<x-layouts.app title="Retours Clients">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-arrow-uturn-left class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Retours Clients</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Registre et décision sur les retours produits.</p>
            </div>

            <a href="{{ route('retours.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Enregistrer un retour
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="mb-6 p-3.5 sm:p-4">
            <form action="{{ route('retours.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5 items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       x-on:input.debounce.400ms="$el.form.submit()"
                       placeholder="Rechercher par N° de retour, produit, client..."
                       class="w-full sm:flex-1 rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500">

                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="restocked" {{ request('status') === 'restocked' ? 'selected' : '' }}>Réintégré</option>
                    <option value="discarded" {{ request('status') === 'discarded' ? 'selected' : '' }}>Déclaré en perte</option>
                </select>

                <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto">
                        Filtrer
                    </x-ui.button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('retours.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Registre des Retours Clients ({{ $returns->total() }})</span>
            </h2>
        </div>

        @if($returns->isEmpty())
            <x-ui.empty-state
                title="Aucun retour enregistré"
                description="Aucun retour client n'a été enregistré pour le moment."
                icon="arrow-uturn-left">
            </x-ui.empty-state>
        @else
            <!-- Table Unifiée Scrollable Horizon -->
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[650px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">N° Retour</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Produit & Unité</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Qté</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Client</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Statut</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($returns as $return)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $return->return_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $return->return_number }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-900 block">{{ $return->product->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $return->stockUnit->name }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                        {{ number_format($return->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 whitespace-nowrap">
                                        {{ $return->customer ? $return->customer->name : 'Client de passage' }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($return->isPending())
                                            <x-ui.badge color="amber">En attente</x-ui.badge>
                                        @elseif($return->isRestocked())
                                            <x-ui.badge color="emerald">Réintégré</x-ui.badge>
                                        @else
                                            <x-ui.badge color="red">Perte</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('retours.show', $return) }}"
                                           class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                           title="Consulter le retour">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
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

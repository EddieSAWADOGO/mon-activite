<x-layouts.app title="Reconditionnement de stock">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-arrows-right-left class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Reconditionnement d'Unités</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Regroupement et conversion d'unités de stock.</p>
            </div>

            <a href="{{ route('reconditionnement.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouveau reconditionnement
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="p-4">
            <form action="{{ route('reconditionnement.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="relative w-full sm:flex-1">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Rechercher par N° de reconditionnement ou produit..."
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-xs sm:text-sm">
                </div>
                <x-ui.button type="submit" variant="primary" icon="magnifying-glass" class="w-full sm:w-auto">
                    Rechercher
                </x-ui.button>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Registre des Reconditionnements ({{ $repackagings->total() }})</span>
            </h2>
        </div>

        @if($repackagings->isEmpty())
            <x-ui.empty-state
                title="Aucun reconditionnement"
                description="Aucune opération de reconditionnement / regroupement d'unités n'a été réalisée."
                icon="arrows-right-left">
            </x-ui.empty-state>
        @else
            <!-- Tableau Unifié Scrollable Horizon -->
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[650px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">N° Reconditionnement</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Produit</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Unité Source (Prélevée)</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Unité Cible (Obtenue)</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Opérateur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($repackagings as $r)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $r->repackaging_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <a href="{{ route('reconditionnement.show', $r) }}" class="text-emerald-600 hover:underline">
                                            {{ $r->repackaging_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $r->product->name }}
                                    </td>
                                    <td class="py-3 px-4 text-red-600 font-semibold whitespace-nowrap">
                                        - {{ number_format($r->source_quantity, 2, ',', ' ') }} {{ $r->sourceStockUnit->name }}
                                    </td>
                                    <td class="py-3 px-4 text-emerald-600 font-bold whitespace-nowrap">
                                        + {{ number_format($r->target_quantity, 2, ',', ' ') }} {{ $r->targetStockUnit->name }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">
                                        {{ $r->createdBy->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($repackagings->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $repackagings->links() }}
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

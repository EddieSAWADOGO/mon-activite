<x-layouts.app title="Gestion des Pertes">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-x-circle class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" />
                    <span>Déclarations de Pertes</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Suivi des pertes et avaries de stock.</p>
            </div>

            <a href="{{ route('pertes.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="danger" icon="plus" class="w-full sm:w-auto">
                    Déclarer une perte
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="mb-6 p-3.5 sm:p-4">
            <form action="{{ route('pertes.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5 items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       x-on:input.debounce.400ms="$el.form.submit()"
                       placeholder="Rechercher par N° de perte, motif, produit..."
                       class="w-full sm:flex-1 rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500">

                <div class="flex gap-2 w-full sm:w-auto [&>a]:flex-1 [&>button]:flex-1 sm:[&>a]:flex-none sm:[&>button]:flex-none">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto">
                        Rechercher
                    </x-ui.button>
                    @if(request('search'))
                        <a href="{{ route('pertes.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-center whitespace-nowrap bg-white min-h-[42px] transition active:scale-98">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-red-600" />
                <span>Registre des Pertes de Stock ({{ $losses->total() }})</span>
            </h2>
        </div>

        @if($losses->isEmpty())
            <x-ui.empty-state
                title="Aucune perte déclarée"
                description="Aucune perte de stock n'a été déclarée."
                icon="x-circle">
            </x-ui.empty-state>
        @else
            <!-- Tableau Unified Scrollable Horizon -->
            <x-ui.card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[650px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">N° Perte</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Produit & Unité</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Qté Perdue</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Motif</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Déclaré par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($losses as $loss)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $loss->loss_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <a href="{{ route('pertes.show', $loss) }}" class="hover:underline text-red-600">
                                            {{ $loss->loss_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-900 block">{{ $loss->product->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $loss->stockUnit->name }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-red-600 whitespace-nowrap">
                                        - {{ number_format($loss->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <x-ui.badge color="red">{{ $loss->reason }}</x-ui.badge>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">
                                        {{ $loss->createdBy->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($losses->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $losses->links() }}
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>

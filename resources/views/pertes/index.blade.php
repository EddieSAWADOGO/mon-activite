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
        <x-ui.card class="mb-6 p-4">
            <form action="{{ route('pertes.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher par N° de perte, motif, produit..."
                       class="w-full sm:flex-1 rounded-xl border border-slate-200 text-xs sm:text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-500">

                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="w-full sm:w-auto">
                    Rechercher
                </x-ui.button>
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
            <x-ui.card class="p-0 overflow-hidden">
                <div class="block sm:hidden divide-y divide-slate-100">
                    @foreach($losses as $loss)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">{{ $loss->loss_number }}</span>
                                <span class="text-xs text-slate-500">{{ $loss->loss_date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-sm font-semibold text-slate-800">{{ $loss->product->name }}</div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-600">Quantité: <strong class="text-red-600">- {{ number_format($loss->quantity, 2, ',', ' ') }} {{ $loss->stockUnit->name }}</strong></span>
                                <x-ui.badge color="red">{{ $loss->reason }}</x-ui.badge>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">N° Perte</th>
                                <th class="py-3 px-4">Produit & Unité</th>
                                <th class="py-3 px-4 text-right">Qté Perdue</th>
                                <th class="py-3 px-4">Motif</th>
                                <th class="py-3 px-4">Déclaré par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($losses as $loss)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $loss->loss_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <a href="{{ route('pertes.show', $loss) }}" class="hover:underline text-red-600">
                                            {{ $loss->loss_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $loss->product->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $loss->stockUnit->name }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-red-600 whitespace-nowrap">
                                        - {{ number_format($loss->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-ui.badge color="red">{{ $loss->reason }}</x-ui.badge>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 text-xs">
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

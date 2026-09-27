<x-layouts.app title="Reconstitution du stock à une date passée">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('historique.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-archive-box class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600 shrink-0" />
                    <span class="truncate">Reconstitution du Stock à une Date Passée</span>
                </h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Calcul récursif et rejouement de l'historique des mouvements de stock
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card class="p-3.5 sm:p-4">
            <form action="{{ route('historique.stock-at-date') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label for="product_id" class="block text-xs font-semibold text-slate-700 mb-1">
                        Sélectionner le produit <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" id="product_id" required class="w-full py-2.5 px-3.5 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Choisir un produit --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-xs font-semibold text-slate-700 mb-1">
                        Date cible de consultation <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" id="date" value="{{ request('date', now()->format('Y-m-d')) }}" required class="w-full py-2.5 px-3.5 border border-slate-200 rounded-xl bg-slate-50 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <x-ui.button type="submit" variant="primary" icon="magnifying-glass" class="w-full justify-center">
                        Reconstituer l'état du stock
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if($selectedProduct && $calculatedStock)
            <x-ui.card class="p-6">
                <div class="border-b border-slate-100 pb-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs uppercase font-semibold text-slate-400">Résultat de la reconstitution</span>
                        <h2 class="text-xl font-extrabold text-slate-900">{{ $selectedProduct->name }}</h2>
                    </div>
                    <div>
                        <x-ui.badge color="sky">
                            État au {{ \Carbon\Carbon::parse($targetDateInput)->format('d/m/Y') }}
                        </x-ui.badge>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse min-w-[500px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 whitespace-nowrap">Unité déclarée</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Équivalence unité de base</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Quantité en stock à cette date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($calculatedStock as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $item['unit']->name }}
                                        @if($item['unit']->is_base_unit)
                                            <span class="text-xs font-normal text-emerald-600 ml-1">(Unité de base)</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center text-slate-600 font-mono whitespace-nowrap">
                                        {{ $item['unit']->base_unit_equivalent }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-slate-900 text-base whitespace-nowrap">
                                        {{ number_format($item['calculated_stock'], 2, ',', ' ') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @else
            <x-ui.empty-state
                title="Sélectionnez un produit et une date"
                description="Veuillez choisir un produit et la date souhaitée dans le formulaire ci-dessus pour afficher l'état reconstitué de ses compteurs de stock."
                icon="archive-box">
            </x-ui.empty-state>
        @endif
    </div>
</x-layouts.app>

<x-layouts.app title="Reconditionnement {{ $repackaging->repackaging_number }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Reconditionnement N° {{ $repackaging->repackaging_number }}</h1>
            <x-ui.back-button href="{{ route('reconditionnement.index') }}" label="Retour à la liste" />
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <x-ui.card>
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-4">
                    <span class="text-slate-400 text-xs block">Produit concerné</span>
                    <span class="text-lg font-bold text-slate-900">{{ $repackaging->product->name }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                        <span class="text-red-700 font-bold block mb-1">Source (Sortie de stock)</span>
                        <span class="text-base font-extrabold text-red-600 block">
                            - {{ number_format($repackaging->source_quantity, 2, ',', ' ') }} {{ $repackaging->sourceStockUnit->name }}
                        </span>
                        <span class="text-xs text-slate-500">Équivalence : {{ $repackaging->sourceStockUnit->base_unit_equivalent }} unité(s) de base</span>
                    </div>

                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                        <span class="text-emerald-700 font-bold block mb-1">Cible (Entrée en stock)</span>
                        <span class="text-base font-extrabold text-emerald-600 block">
                            + {{ number_format($repackaging->target_quantity, 2, ',', ' ') }} {{ $repackaging->targetStockUnit->name }}
                        </span>
                        <span class="text-xs text-slate-500">Équivalence : {{ $repackaging->targetStockUnit->base_unit_equivalent }} unité(s) de base</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block">Date de l'opération</span>
                        <span class="font-semibold text-slate-800">{{ $repackaging->repackaging_date->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Exécuté par</span>
                        <span class="font-semibold text-slate-800">{{ $repackaging->createdBy->name }}</span>
                    </div>
                </div>

                @if($repackaging->notes)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block">Notes / Remarques</span>
                        <p class="text-slate-700 bg-slate-50 p-3 rounded border border-slate-200 mt-1">{{ $repackaging->notes }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>

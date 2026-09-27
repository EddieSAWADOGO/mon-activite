<x-layouts.app title="Perte N° {{ $loss->loss_number }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Déclaration de Perte {{ $loss->loss_number }}</h1>
            <x-ui.back-button href="{{ route('pertes.index') }}" label="Retour à la liste" />
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <x-ui.card>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-slate-400 text-xs block">Produit</span>
                        <span class="text-lg font-bold text-slate-900">{{ $loss->product->name }}</span>
                    </div>
                    <div>
                        <x-ui.badge color="red">{{ $loss->reason }}</x-ui.badge>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Unité & Quantité Perdue</span>
                        <span class="text-base font-extrabold text-red-600">
                            - {{ number_format($loss->quantity, 2, ',', ' ') }} {{ $loss->stockUnit->name }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Date de constatation</span>
                        <span class="font-semibold text-slate-800">{{ $loss->loss_date->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 text-xs">
                    <span class="text-slate-400 block">Déclaré par</span>
                    <span class="text-slate-700">{{ $loss->createdBy->name }}</span>
                </div>

                @if($loss->customerReturn)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block">Issu du retour client N°</span>
                        <a href="{{ route('retours.show', $loss->customerReturn) }}" class="text-emerald-600 font-bold hover:underline">
                            {{ $loss->customerReturn->return_number }}
                        </a>
                    </div>
                @endif

                @if($loss->notes)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block">Notes / Explications</span>
                        <p class="text-slate-700 bg-slate-50 p-3 rounded border border-slate-200 mt-1">{{ $loss->notes }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>

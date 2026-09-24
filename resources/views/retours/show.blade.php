<x-layouts.app title="Fiche Retour N° {{ $customerReturn->return_number }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Retour Client {{ $customerReturn->return_number }}</h1>
                @if($customerReturn->isPending())
                    <x-ui.badge color="amber">En attente de validation</x-ui.badge>
                @elseif($customerReturn->isRestocked())
                    <x-ui.badge color="emerald">Réintégré en stock</x-ui.badge>
                @else
                    <x-ui.badge color="red">Déclaré en perte</x-ui.badge>
                @endif
            </div>
            <a href="{{ route('retours.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
                &larr; Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <x-ui.card>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-slate-400 text-xs block">Produit concerné</span>
                        <span class="text-base font-bold text-slate-900">{{ $customerReturn->product->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs block">Quantité & Unité</span>
                        <span class="text-base font-bold text-slate-900">
                            {{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Motif du retour</span>
                        <span class="font-semibold text-slate-800">{{ $customerReturn->reason }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Client</span>
                        <span class="font-semibold text-slate-800">
                            {{ $customerReturn->customer ? $customerReturn->customer->name : 'Client de passage' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Facture associée</span>
                        <span class="font-semibold text-slate-800">
                            {{ $customerReturn->invoice ? $customerReturn->invoice->invoice_number : 'Aucune' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block">Enregistré par</span>
                        <span class="text-slate-700">{{ $customerReturn->createdBy->name }} le {{ $customerReturn->return_date->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($customerReturn->validatedBy)
                        <div>
                            <span class="text-slate-400 block">Validé par</span>
                            <span class="text-slate-700">{{ $customerReturn->validatedBy->name }} le {{ $customerReturn->validated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    @endif
                </div>

                @if($customerReturn->notes)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block">Notes / Remarques</span>
                        <p class="text-slate-700 bg-slate-50 p-2 rounded border border-slate-200 mt-1">{{ $customerReturn->notes }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        @if($customerReturn->isPending())
            @can('validate', $customerReturn)
                <x-ui.card class="border-amber-200 bg-amber-50/50">
                    <h3 class="text-sm font-bold text-amber-900 mb-2 flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
                        Décision et Validation du Retour Client
                    </h3>
                    <p class="text-xs text-amber-800 mb-4">
                        En tant qu'administrateur, veuillez choisir l'action à appliquer sur ce retour. 
                        Cette décision est définitive et mettra à jour les compteurs de stock.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Option 1: Réintégrer en Stock -->
                        <form action="{{ route('retours.restock', $customerReturn) }}" method="POST" onsubmit="return confirm('Confirmer la réintégration du produit en stock ?')">
                            @csrf
                            <x-ui.button type="submit" variant="primary" icon="check-circle" class="w-full justify-center">
                                Réintégrer au stock (+{{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }})
                            </x-ui.button>
                        </form>

                        <!-- Option 2: Déclarer en Perte -->
                        <form action="{{ route('retours.discard', $customerReturn) }}" method="POST" onsubmit="return confirm('Confirmer la déclaration de ce retour en perte (non récupérable) ?')">
                            @csrf
                            <x-ui.button type="submit" variant="danger" icon="x-circle" class="w-full justify-center">
                                Déclarer comme perte définitive
                            </x-ui.button>
                        </form>
                    </div>
                </x-ui.card>
            @endcan
        @endif
    </div>
</x-layouts.app>

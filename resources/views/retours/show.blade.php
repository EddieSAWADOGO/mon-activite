<x-layouts.app title="Fiche Retour N° {{ $customerReturn->return_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2.5 min-w-0">
                <x-ui.back-button href="{{ route('retours.index') }}" label="Retour" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">
                            Retour Client {{ $customerReturn->return_number }}
                        </h2>
                        @if($customerReturn->isPending())
                            <x-ui.badge color="amber">En attente de validation</x-ui.badge>
                        @elseif($customerReturn->isRestocked())
                            <x-ui.badge color="emerald">Réintégré en stock</x-ui.badge>
                        @else
                            <x-ui.badge color="red">Déclaré en perte</x-ui.badge>
                        @endif
                    </div>
                </div>
            </div>

            @if($customerReturn->invoice)
                <div class="w-full sm:w-auto">
                    <x-ui.button href="{{ route('factures.show', $customerReturn->invoice) }}" variant="outline" icon="document-text" size="sm" class="w-full sm:w-auto">
                        Facture {{ $customerReturn->invoice->invoice_number }}
                    </x-ui.button>
                </div>
            @endif
        </div>
    </x-slot>

    <!-- Metrics Header -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card
            title="Quantité Retournée"
            value="{{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }}"
            icon="arrow-uturn-left"
            :color="$customerReturn->isPending() ? 'amber' : ($customerReturn->isRestocked() ? 'emerald' : 'red')" />

        <x-ui.stat-card
            title="Produit Concerné"
            value="{{ $customerReturn->product->name }}"
            icon="cube"
            color="sky" />

        <x-ui.stat-card
            title="Client"
            value="{{ $customerReturn->customer ? $customerReturn->customer->name : 'Client de passage' }}"
            icon="user"
            color="purple" />

        <x-ui.stat-card
            title="Décision / Statut"
            value="{{ $customerReturn->isPending() ? 'En attente' : ($customerReturn->isRestocked() ? 'Réintégré' : 'Perte') }}"
            icon="clipboard-document-check"
            :color="$customerReturn->isPending() ? 'amber' : ($customerReturn->isRestocked() ? 'emerald' : 'red')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main details (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Article & Motif Card -->
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-emerald-600" />
                    <span>Détails du Produit & Motif du Retour</span>
                </h3>

                <div class="space-y-4">
                    <!-- Produit banner -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Produit Retourné</span>
                            <a href="{{ route('produits.show', $customerReturn->product) }}" class="text-base sm:text-lg font-bold text-slate-900 hover:text-emerald-600 transition flex items-center gap-1.5">
                                <span>{{ $customerReturn->product->name }}</span>
                                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 text-slate-400" />
                            </a>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Quantité & Conditionnement</span>
                            <span class="text-base sm:text-lg font-extrabold text-emerald-700">
                                {{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Motif du retour -->
                    <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 space-y-1">
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-4 h-4 text-amber-600" />
                            <span>Motif Déclaré pour le Retour</span>
                        </span>
                        <p class="text-sm font-bold text-amber-950 pt-1">
                            {{ $customerReturn->reason }}
                        </p>
                    </div>

                    <!-- Notes complémentaires -->
                    @if($customerReturn->notes)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1 text-xs sm:text-sm">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Notes & Remarques</span>
                            <p class="text-slate-700 whitespace-pre-line font-medium">
                                {{ $customerReturn->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- Card Validation / Decision -->
            @if($customerReturn->isPending())
                @can('validate', $customerReturn)
                    <x-ui.card class="p-4 sm:p-6 border-2 border-amber-300 bg-amber-50/50">
                        <div class="space-y-4" x-data="{ showRestockModal: false, showDiscardModal: false, submittingRestock: false, submittingDiscard: false }">
                            <div class="flex items-start gap-3">
                                <div class="p-2.5 rounded-2xl bg-amber-100 text-amber-700 shrink-0">
                                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-amber-950">
                                        Décision d'Administration & Impact Stock
                                    </h3>
                                    <p class="text-xs sm:text-sm text-amber-800 mt-0.5">
                                        En tant qu'administrateur, choisissez l'action à appliquer sur ces produits retournés.
                                        Cette décision est définitive et mettra immédiatement à jour la comptabilité de stock.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                                <!-- Option 1: Réintégrer au Stock -->
                                <div>
                                    <x-ui.button type="button" @click="showRestockModal = true" variant="primary" icon="check-circle" class="w-full justify-center">
                                        Réintégrer au stock (+{{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }})
                                    </x-ui.button>

                                    <x-ui.confirm-modal
                                        name="showRestockModal"
                                        title="Réintégrer ce retour au stock ?"
                                        message="Confirmez-vous la réintégration des {{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }} dans les compteurs de stock disponible ?"
                                        confirmText="Réintégrer au stock"
                                        variant="primary"
                                        icon="check-circle">
                                        <form action="{{ route('retours.restock', $customerReturn) }}" method="POST" @submit="submittingRestock = true" class="flex-1">
                                            @csrf
                                            <x-ui.button type="submit" variant="primary" size="sm" class="w-full" ::loading="submittingRestock">
                                                Confirmer la réintégration
                                            </x-ui.button>
                                        </form>
                                    </x-ui.confirm-modal>
                                </div>

                                <!-- Option 2: Déclarer en Perte -->
                                <div>
                                    <x-ui.button type="button" @click="showDiscardModal = true" variant="danger" icon="x-circle" class="w-full justify-center">
                                        Déclarer comme perte définitive
                                    </x-ui.button>

                                    <x-ui.confirm-modal
                                        name="showDiscardModal"
                                        title="Déclarer ce retour en perte ?"
                                        message="Confirmez-vous la déclaration de ce retour comme perte non récupérable ? (Les produits ne seront pas remis en stock)."
                                        confirmText="Déclarer en perte"
                                        variant="danger"
                                        icon="x-circle">
                                        <form action="{{ route('retours.discard', $customerReturn) }}" method="POST" @submit="submittingDiscard = true" class="flex-1">
                                            @csrf
                                            <x-ui.button type="submit" variant="danger" size="sm" class="w-full" ::loading="submittingDiscard">
                                                Confirmer la perte
                                            </x-ui.button>
                                        </form>
                                    </x-ui.confirm-modal>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                @endcan
            @elseif($customerReturn->isRestocked())
                <x-ui.card class="p-4 sm:p-6 bg-emerald-50/70 border border-emerald-200">
                    <div class="flex items-start gap-3.5">
                        <div class="p-2.5 rounded-2xl bg-emerald-100 text-emerald-700 shrink-0">
                            <x-heroicon-o-check-badge class="w-6 h-6" />
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-base font-extrabold text-emerald-950">Retour Réintégré en Stock</h4>
                            <p class="text-xs sm:text-sm text-emerald-800">
                                Les {{ number_format($customerReturn->quantity, 2, ',', ' ') }} {{ $customerReturn->stockUnit->name }} ont été réintégrés avec succès dans le stock disponible.
                            </p>
                            @if($customerReturn->validatedBy)
                                <span class="text-xs text-emerald-700 block font-semibold pt-1">
                                    Validé par {{ $customerReturn->validatedBy->name }} le {{ $customerReturn->validated_at ? $customerReturn->validated_at->format('d/m/Y à H:i') : '' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @elseif($customerReturn->isDiscarded())
                @php
                    $associatedLoss = \App\Domain\Pertes\Models\Loss::where('customer_return_id', $customerReturn->id)->first();
                @endphp
                <x-ui.card class="p-4 sm:p-6 bg-red-50/70 border border-red-200">
                    <div class="flex items-start gap-3.5">
                        <div class="p-2.5 rounded-2xl bg-red-100 text-red-700 shrink-0">
                            <x-heroicon-o-x-circle class="w-6 h-6" />
                        </div>
                        <div class="space-y-1.5">
                            <h4 class="text-base font-extrabold text-red-950">Retour Déclaré en Perte Définitive</h4>
                            <p class="text-xs sm:text-sm text-red-800">
                                Ce retour a été validé comme non récupérable. Les quantités n'ont pas été réintégrées au stock.
                            </p>
                            @if($customerReturn->validatedBy)
                                <span class="text-xs text-red-700 block font-semibold">
                                    Validé par {{ $customerReturn->validatedBy->name }} le {{ $customerReturn->validated_at ? $customerReturn->validated_at->format('d/m/Y à H:i') : '' }}
                                </span>
                            @endif
                            @if($associatedLoss)
                                <div class="pt-1">
                                    <x-ui.button href="{{ route('pertes.show', $associatedLoss) }}" variant="outline" size="xs" icon="document-magnifying-glass">
                                        Consulter la Fiche de Perte ({{ $associatedLoss->loss_number }})
                                    </x-ui.button>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>

        <!-- Right sidebar (1 column) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Client & Facture Card -->
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-user class="w-5 h-5 text-emerald-600" />
                    <span>Client & Origine</span>
                </h3>

                <dl class="space-y-3 text-xs sm:text-sm">
                    <div>
                        <dt class="text-slate-400 font-medium">Client</dt>
                        <dd class="font-bold text-slate-900 pt-0.5">
                            @if($customerReturn->customer)
                                <a href="{{ route('clients.show', $customerReturn->customer) }}" class="hover:text-emerald-600 transition flex items-center gap-1">
                                    <span>{{ $customerReturn->customer->name }}</span>
                                    <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5 text-slate-400" />
                                </a>
                            @else
                                <span class="text-slate-600">Client de passage (Anonyme)</span>
                            @endif
                        </dd>
                    </div>

                    @if($customerReturn->customer && $customerReturn->customer->phone)
                        <div>
                            <dt class="text-slate-400 font-medium">Téléphone Client</dt>
                            <dd class="font-bold text-slate-800 pt-0.5">{{ $customerReturn->customer->phone }}</dd>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100">
                        <dt class="text-slate-400 font-medium">Facture d'Origine</dt>
                        <dd class="font-bold text-slate-900 pt-0.5">
                            @if($customerReturn->invoice)
                                <a href="{{ route('factures.show', $customerReturn->invoice) }}" class="text-emerald-600 font-bold hover:underline flex items-center gap-1">
                                    <x-heroicon-o-document-text class="w-4 h-4" />
                                    <span>N° {{ $customerReturn->invoice->invoice_number }}</span>
                                </a>
                            @else
                                <span class="text-slate-500 font-normal">Aucune facture associée</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            <!-- Traçabilité Card -->
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-emerald-600" />
                    <span>Traçabilité</span>
                </h3>

                <dl class="space-y-3 text-xs sm:text-sm">
                    <div>
                        <dt class="text-slate-400 font-medium">Date de Réception</dt>
                        <dd class="font-bold text-slate-900 pt-0.5">
                            {{ $customerReturn->return_date->format('d/m/Y à H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-400 font-medium">Enregistré par</dt>
                        <dd class="font-bold text-slate-800 pt-0.5 flex items-center gap-1.5">
                            <x-heroicon-o-user-circle class="w-4 h-4 text-slate-400" />
                            <span>{{ $customerReturn->createdBy->name }}</span>
                        </dd>
                    </div>

                    @if($customerReturn->validatedBy)
                        <div class="pt-2 border-t border-slate-100">
                            <dt class="text-slate-400 font-medium">Validé / Traité par</dt>
                            <dd class="font-bold text-slate-800 pt-0.5 flex items-center gap-1.5">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600" />
                                <span>{{ $customerReturn->validatedBy->name }}</span>
                            </dd>
                            <span class="text-xs text-slate-400 block mt-0.5">
                                Le {{ $customerReturn->validated_at ? $customerReturn->validated_at->format('d/m/Y à H:i') : '' }}
                            </span>
                        </div>
                    @endif
                </dl>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>

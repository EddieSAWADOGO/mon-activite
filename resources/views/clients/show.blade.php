<x-layouts.app title="Fiche Client — {{ $customer->name }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2.5 min-w-0">
                <x-ui.back-button href="{{ route('clients.index') }}" label="Retour" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $customer->name }}</h2>
                        <x-ui.badge :color="$customer->is_active ? 'emerald' : 'slate'">
                            {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                        </x-ui.badge>
                        <x-ui.badge :color="$customer->type === 'entreprise' ? 'sky' : 'slate'" class="capitalize">
                            {{ $customer->type }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                <a href="{{ route('clients.statement-pdf', $customer) }}" title="Télécharger le Relevé de Compte complet du client en PDF"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-emerald-400 shrink-0" />
                    <span>Relevé de Compte (PDF)</span>
                </a>

                @can('update', $customer)
                    <x-ui.button href="{{ route('clients.edit', $customer) }}" variant="secondary" icon="pencil-square" size="sm" class="w-full sm:w-auto">
                        Éditer
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <!-- Metrics Header -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card
            title="Total Acheté"
            value="{{ number_format($totalPurchased, 0, ',', ' ') }} FCFA"
            icon="banknotes"
            color="emerald" />

        <x-ui.stat-card
            title="Total Reglé"
            value="{{ number_format($totalPaid, 0, ',', ' ') }} FCFA"
            icon="check-circle"
            color="emerald" />

        <x-ui.stat-card
            title="Reste à Payer"
            value="{{ number_format($remainingDue, 0, ',', ' ') }} FCFA"
            icon="exclamation-triangle"
            :color="$remainingDue > 0 ? 'amber' : 'slate'" />

        <x-ui.stat-card
            title="Dernière Commande"
            value="{{ $lastOrderDate ? $lastOrderDate->format('d/m/Y') : 'Aucune' }}"
            icon="clock"
            color="sky" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations générales -->
        <div class="lg:col-span-1 space-y-4">
            <x-ui.card class="p-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 mb-3">Coordonnées</h3>
                <dl class="space-y-2 text-xs">
                    <div>
                        <dt class="text-slate-400">Téléphone</dt>
                        <dd class="font-medium text-slate-800">{{ $customer->phone ?? 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">WhatsApp</dt>
                        <dd class="font-medium text-slate-800">
                            @if($customer->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->whatsapp) }}" target="_blank" class="text-emerald-600 font-semibold hover:underline flex items-center gap-1">
                                    <x-heroicon-o-chat-bubble-left-right class="w-3.5 h-3.5" />
                                    <span>{{ $customer->whatsapp }}</span>
                                </a>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Email</dt>
                        <dd class="font-medium text-slate-800">{{ $customer->email ?? 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Adresse</dt>
                        <dd class="font-medium text-slate-800">{{ $customer->address ?? 'Non renseignée' }}</dd>
                    </div>

                    @if($customer->type === 'entreprise')
                        <div class="pt-2 border-t border-slate-100">
                            <dt class="text-slate-400">Personne de contact</dt>
                            <dd class="font-medium text-slate-800">{{ $customer->contact_person ?? 'Non renseigné' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">N° IFU</dt>
                            <dd class="font-medium text-slate-800">{{ $customer->ifu ?? 'Non renseigné' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">N° RCCM</dt>
                            <dd class="font-medium text-slate-800">{{ $customer->rccm ?? 'Non renseigné' }}</dd>
                        </div>
                    @endif

                    @if($customer->notes)
                        <div class="pt-2 border-t border-slate-100">
                            <dt class="text-slate-400">Notes</dt>
                            <dd class="font-medium text-slate-800 italic">{{ $customer->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </x-ui.card>
        </div>

        <!-- Historique des Achats & Factures -->
        <div class="lg:col-span-2">
            <x-ui.card class="p-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 mb-3 flex items-center justify-between">
                    <span>Historique des Ventes & Factures</span>
                    <span class="text-xs font-normal text-slate-500">{{ $customer->sales->count() }} vente(s)</span>
                </h3>

                @if($customer->sales->isEmpty())
                    <x-ui.empty-state
                        title="Aucune transaction"
                        description="Ce client n'a effectué aucun achat pour le moment."
                        icon="document-text" />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm min-w-[550px]">
                            <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-xs">
                                <tr>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Date & N° Vente</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Montant Total</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Reste à payer</th>
                                    <th class="py-3 px-3.5 text-right whitespace-nowrap">Facture</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($customer->sales as $sale)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <div class="font-bold text-slate-800">{{ $sale->sale_number }}</div>
                                            <div class="text-xs text-slate-400 font-medium">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="py-3 px-3.5 font-bold text-slate-900 whitespace-nowrap">
                                            {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            @if($sale->remaining_amount > 0)
                                                <x-ui.badge color="amber">
                                                    {{ number_format($sale->remaining_amount, 0, ',', ' ') }} FCFA
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge color="emerald">Payé</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                            @if($sale->invoice)
                                                <x-ui.button href="{{ route('factures.show', $sale->invoice) }}" variant="outline" size="sm">
                                                    {{ $sale->invoice->invoice_number }}
                                                </x-ui.button>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>

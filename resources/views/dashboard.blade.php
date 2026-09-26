<x-layouts.app>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Tableau de bord</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Bonjour <span class="font-semibold text-slate-800">{{ $user->name }}</span> &bull; <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $user->role?->label() }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('ventes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow-sm shadow-emerald-600/20 transition active:scale-[0.99]">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    <span>Nouvelle vente</span>
                </a>
                @if ($user->isAdmin())
                    <a href="{{ route('achats.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold shadow-sm transition active:scale-[0.99]">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        <span>Nouvel achat</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Today's Sales Card -->
            <x-ui.stat-card
                title="Ventes du jour"
                value="{{ number_format($todaySalesTotal, 0, ',', ' ') }} FCFA"
                subtitle="{{ $todaySalesCount }} transaction(s) effectuée(s)"
                icon="banknotes"
                iconColor="emerald"
            />

            <!-- Low Stock Count Card -->
            <x-ui.stat-card
                title="Alertes Stock Bas"
                value="{{ $lowStockUnits->count() }} unité(s)"
                subtitle="{{ $lowStockUnits->count() > 0 ? 'Réapprovisionnement suggéré' : 'Stock optimal' }}"
                icon="exclamation-triangle"
                iconColor="{{ $lowStockUnits->count() > 0 ? 'amber' : 'emerald' }}"
            />

            @if ($user->isAdmin())
                <!-- Customer Receivables Card -->
                <x-ui.stat-card
                    title="Créances Clients"
                    value="{{ number_format($customerTotalReceivables, 0, ',', ' ') }} FCFA"
                    subtitle="En attente de règlement"
                    icon="arrow-trending-up"
                    iconColor="blue"
                />

                <!-- Supplier Debts Card -->
                <x-ui.stat-card
                    title="Dettes Fournisseurs"
                    value="{{ number_format($supplierTotalDebt, 0, ',', ' ') }} FCFA"
                    subtitle="Engagements à payer"
                    icon="arrow-trending-down"
                    iconColor="red"
                />
            @endif
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 columns: Low Stock & Recent Invoices -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Low Stock Alerts List -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-base">Alertes de Stock Bas</h2>
                                <p class="text-xs text-slate-500">Produits sous le seuil d'alerte configuré</p>
                            </div>
                        </div>
                        <a href="{{ route('stock.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                            <span>Voir le stock</span>
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    @if ($lowStockUnits->isEmpty())
                        <div class="py-8 text-center text-slate-400 text-sm space-y-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                                <x-heroicon-o-check-circle class="w-6 h-6" />
                            </div>
                            <p class="font-medium text-slate-600">Stock en parfait état</p>
                            <p class="text-xs text-slate-400">Aucun produit ne nécessite de réapprovisionnement immédiat.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($lowStockUnits as $unit)
                                <div class="py-3 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                                    <div>
                                        <a href="{{ route('produits.show', $unit->product_id) }}" class="font-bold text-slate-800 text-sm hover:text-emerald-600 transition-colors">
                                            {{ $unit->product?->name }}
                                        </a>
                                        <p class="text-xs text-slate-500">Unité concernée : <span class="font-medium text-slate-700">{{ $unit->name }}</span></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span>Restant : {{ number_format($unit->current_stock, 0, ',', ' ') }}</span>
                                            <span class="text-amber-400">&bull;</span>
                                            <span class="font-normal text-amber-700">Seuil : {{ number_format($unit->low_stock_threshold, 0, ',', ' ') }}</span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent Invoices List -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <x-heroicon-o-document-text class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-base">Dernières Factures</h2>
                                <p class="text-xs text-slate-500">Aperçu des factures émanant des ventes</p>
                            </div>
                        </div>
                        <a href="{{ route('factures.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                            <span>Toutes les factures</span>
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    @if ($recentInvoices->isEmpty())
                        <div class="py-8 text-center text-slate-400 text-sm">
                            Aucune facture enregistrée pour le moment.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[11px] font-bold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                                        <th class="py-2.5 px-2">N° Facture</th>
                                        <th class="py-2.5 px-2">Client</th>
                                        <th class="py-2.5 px-2">Montant Total</th>
                                        <th class="py-2.5 px-2">Statut</th>
                                        <th class="py-2.5 px-2 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($recentInvoices as $invoice)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3 px-2 font-bold text-slate-900">{{ $invoice->invoice_number }}</td>
                                            <td class="py-3 px-2 text-slate-600 font-medium">{{ $invoice->customer?->name ?? 'Client comptant' }}</td>
                                            <td class="py-3 px-2 font-extrabold text-slate-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="py-3 px-2">
                                                @if ($invoice->status?->value === 'paid')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Payée
                                                    </span>
                                                @elseif ($invoice->status?->value === 'partially_paid')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Partielle
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Impayée
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-2 text-right">
                                                <a href="{{ route('factures.show', $invoice->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">
                                                    Consulter
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right 1 column: Recent Operations Log & Access -->
            <div class="space-y-6">

                <!-- Recent Operations Log -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                                <x-heroicon-o-clock class="w-5 h-5" />
                            </div>
                            <h2 class="font-bold text-slate-900 text-base">Activité Récente</h2>
                        </div>
                        <a href="{{ route('stock.movements') }}" class="text-xs text-emerald-600 hover:underline font-semibold">Voir tout</a>
                    </div>

                    @if ($recentMovements->isEmpty())
                        <div class="py-8 text-center text-slate-400 text-sm">
                            Aucune activité récente.
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach ($recentMovements as $mv)
                                <div class="p-3 bg-slate-50/80 hover:bg-slate-100/80 rounded-xl flex items-start justify-between gap-2 border border-slate-100 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                                {{ $mv->direction === 'in' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $mv->direction === 'in' ? 'Entrée' : 'Sortie' }}
                                            </span>
                                            <span class="text-[11px] font-medium text-slate-500">{{ $mv->type?->label() ?? $mv->type?->value }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800">{{ $mv->product?->name }}</p>
                                        <p class="text-[11px] text-slate-500">Quantité : <strong class="text-slate-700">{{ number_format($mv->quantity, 0, ',', ' ') }} {{ $mv->stockUnit?->name }}</strong></p>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-400 shrink-0">{{ $mv->movement_date?->format('H:i') ?? $mv->created_at->format('H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Navigation Shortcuts Card -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl p-5 space-y-3.5 shadow-lg">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="font-bold text-sm text-slate-200">Accès Rapide</h3>
                        <span class="text-[10px] text-emerald-400 font-semibold">Raccourcis</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('ventes.create') }}" class="p-2.5 bg-slate-800/80 hover:bg-emerald-600 rounded-xl font-semibold text-slate-200 hover:text-white flex items-center gap-2 transition text-xs border border-slate-700/50">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-emerald-400 group-hover:text-white" />
                            <span>Vente</span>
                        </a>
                        <a href="{{ route('produits.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs border border-slate-700/50">
                            <x-heroicon-o-cube class="w-4 h-4 text-emerald-400" />
                            <span>Produits</span>
                        </a>
                        <a href="{{ route('clients.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs border border-slate-700/50">
                            <x-heroicon-o-user class="w-4 h-4 text-emerald-400" />
                            <span>Clients</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs border border-slate-700/50">
                            <x-heroicon-o-archive-box class="w-4 h-4 text-emerald-400" />
                            <span>Stock</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>


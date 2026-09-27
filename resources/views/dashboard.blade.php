<x-layouts.app>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Tableau de bord</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5 flex items-center gap-2">
                    <span>Bonjour <strong class="text-slate-800">{{ $user->name }}</strong></span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $user->role?->label() }}
                    </span>
                </p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('ventes.create') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs sm:text-sm font-semibold transition active:scale-[0.99]">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    <span>Nouvelle vente</span>
                </a>
                @if ($user->isAdmin())
                    <a href="{{ route('achats.create') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs sm:text-sm font-semibold transition active:scale-[0.99]">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        <span>Nouvel achat</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Stat Cards Grid (2 cols on mobile, 4 on desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <x-ui.stat-card
                title="Ventes du jour"
                value="{{ number_format($todaySalesTotal, 0, ',', ' ') }} FCFA"
                subtitle="{{ $todaySalesCount }} vente(s)"
                icon="banknotes"
                iconColor="emerald"
            />

            <x-ui.stat-card
                title="Alertes Stock"
                value="{{ $lowStockUnits->count() }} unité(s)"
                subtitle="{{ $lowStockUnits->count() > 0 ? 'Réapprovisionner' : 'Stock OK' }}"
                icon="exclamation-triangle"
                iconColor="{{ $lowStockUnits->count() > 0 ? 'amber' : 'emerald' }}"
            />

            @if ($user->isAdmin())
                <x-ui.stat-card
                    title="Créances Clients"
                    value="{{ number_format($customerTotalReceivables, 0, ',', ' ') }} FCFA"
                    subtitle="À encaisser"
                    icon="arrow-trending-up"
                    iconColor="blue"
                />

                <x-ui.stat-card
                    title="Dettes Fournisseurs"
                    value="{{ number_format($supplierTotalDebt, 0, ',', ' ') }} FCFA"
                    subtitle="À régler"
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
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-sm sm:text-base">Alertes de Stock</h2>
                                <p class="text-xs text-slate-400">Seuil d'alerte atteint</p>
                            </div>
                        </div>
                        <a href="{{ route('stock.index') }}" class="text-xs sm:text-sm text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                            <span>Stock</span>
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    @if ($lowStockUnits->isEmpty())
                        <div class="py-6 text-center text-slate-400 text-xs sm:text-sm space-y-1.5">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            </div>
                            <p class="font-semibold text-slate-700">Stock optimal</p>
                            <p class="text-xs text-slate-400">Aucun produit sous le seuil d'alerte.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($lowStockUnits as $unit)
                                <div class="py-2.5 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('produits.show', $unit->product_id) }}" class="font-bold text-slate-800 text-xs sm:text-sm hover:text-emerald-600 transition truncate block">
                                            {{ $unit->product?->name }}
                                        </a>
                                        <p class="text-xs text-slate-400 truncate">Unité : <span class="font-medium text-slate-600">{{ $unit->name }}</span></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                            <span>Restant : {{ number_format($unit->current_stock, 0, ',', ' ') }}</span>
                                            <span class="text-amber-400">&bull;</span>
                                            <span class="font-medium text-amber-700">Seuil : {{ number_format($unit->low_stock_threshold, 0, ',', ' ') }}</span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent Invoices List -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <x-heroicon-o-document-text class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-sm sm:text-base">Dernières Factures</h2>
                                <p class="text-xs text-slate-400">Récemment émises</p>
                            </div>
                        </div>
                        <a href="{{ route('factures.index') }}" class="text-xs sm:text-sm text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                            <span>Toutes</span>
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    @if ($recentInvoices->isEmpty())
                        <div class="py-6 text-center text-slate-400 text-xs sm:text-sm">
                            Aucune facture récente.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs sm:text-sm">
                                <thead>
                                    <tr class="text-xs font-bold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                                        <th class="py-2 px-2 whitespace-nowrap">Facture</th>
                                        <th class="py-2 px-2 whitespace-nowrap">Client</th>
                                        <th class="py-2 px-2 whitespace-nowrap">Montant</th>
                                        <th class="py-2 px-2 whitespace-nowrap">Statut</th>
                                        <th class="py-2 px-2 text-right whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($recentInvoices as $invoice)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-2.5 px-2 font-bold text-slate-900 text-xs sm:text-sm whitespace-nowrap">{{ $invoice->invoice_number }}</td>
                                            <td class="py-2.5 px-2 text-slate-600 font-medium text-xs sm:text-sm whitespace-nowrap">{{ $invoice->customer?->name ?? 'Comptant' }}</td>
                                            <td class="py-2.5 px-2 font-extrabold text-slate-900 text-xs sm:text-sm whitespace-nowrap">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="py-2.5 px-2 whitespace-nowrap">
                                                @if ($invoice->status?->value === 'paid')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Payée
                                                    </span>
                                                @elseif ($invoice->status?->value === 'partially_paid')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Partielle
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Impayée
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-2 text-right whitespace-nowrap">
                                                <a href="{{ route('factures.show', $invoice->id) }}"
                                                   class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center justify-center"
                                                   title="Consulter la facture">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
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

            <!-- Right 1 column: Activity Log & Quick Access -->
            <div class="space-y-6">

                <!-- Recent Operations Log -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                <x-heroicon-o-clock class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <h2 class="font-bold text-slate-900 text-sm sm:text-base">Activité Récente</h2>
                        </div>
                        <a href="{{ route('stock.movements') }}" class="text-xs sm:text-sm text-emerald-600 hover:underline font-semibold">Historique</a>
                    </div>

                    @if ($recentMovements->isEmpty())
                        <div class="py-6 text-center text-slate-400 text-xs sm:text-sm">
                            Aucun mouvement récent.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($recentMovements as $mv)
                                <div class="p-2.5 bg-slate-50/80 rounded-xl flex items-start justify-between gap-2 border border-slate-100 transition">
                                    <div class="space-y-0.5 min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-block text-xs font-bold px-1.5 py-0.2 rounded uppercase tracking-wider
                                                {{ $mv->direction === 'in' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $mv->direction === 'in' ? '+' : '-' }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-500 truncate">{{ $mv->type?->label() ?? $mv->type?->value }}</span>
                                        </div>
                                        <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $mv->product?->name }}</p>
                                        <p class="text-xs text-slate-500 truncate"><strong class="text-slate-700">{{ number_format($mv->quantity, 0, ',', ' ') }} {{ $mv->stockUnit?->name }}</strong></p>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400 shrink-0 mt-0.5">{{ $mv->movement_date?->format('H:i') ?? $mv->created_at->format('H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Navigation Shortcuts Card -->
                <div class="bg-slate-900 text-white rounded-2xl p-4 sm:p-5 space-y-3 border border-slate-800">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                        <h3 class="font-bold text-xs sm:text-sm text-slate-200">Raccourcis</h3>
                        <span class="text-xs text-emerald-400 font-semibold">Accès Rapide</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('ventes.create') }}" class="p-2.5 bg-slate-800/80 hover:bg-emerald-600 rounded-xl font-semibold text-slate-200 hover:text-white flex items-center gap-2 transition text-xs sm:text-sm border border-slate-700/50">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-emerald-400" />
                            <span>Vente</span>
                        </a>
                        <a href="{{ route('produits.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs sm:text-sm border border-slate-700/50">
                            <x-heroicon-o-cube class="w-4 h-4 text-emerald-400" />
                            <span>Produits</span>
                        </a>
                        <a href="{{ route('clients.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs sm:text-sm border border-slate-700/50">
                            <x-heroicon-o-user class="w-4 h-4 text-emerald-400" />
                            <span>Clients</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="p-2.5 bg-slate-800/80 hover:bg-slate-800 rounded-xl font-semibold text-slate-200 flex items-center gap-2 transition text-xs sm:text-sm border border-slate-700/50">
                            <x-heroicon-o-archive-box class="w-4 h-4 text-emerald-400" />
                            <span>Stock</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>

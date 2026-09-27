<x-layouts.app title="Gestion des Fournisseurs">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Fournisseurs</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Répertoire des partenaires fournisseurs.</p>
            </div>

            <a href="{{ route('fournisseurs.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouveau Fournisseur
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <!-- Search filter -->
    <x-ui.card class="mb-6 p-3.5 sm:p-4">
        <form method="GET" action="{{ route('fournisseurs.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par nom, téléphone..."
                   class="w-full sm:flex-1 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto">
                Filtrer
            </x-ui.button>
        </form>
    </x-ui.card>

    <div class="mb-3 flex items-center justify-between px-1">
        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
            <span>Répertoire des Fournisseurs ({{ $suppliers->total() }})</span>
        </h2>
    </div>

    @if($suppliers->isEmpty())
        <x-ui.empty-state
            title="Aucun fournisseur trouvé"
            description="Ajoutez vos fournisseurs pour enregistrer les achats."
            icon="truck">
            <a href="{{ route('fournisseurs.create') }}" class="mt-2 inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouveau Fournisseur
                </x-ui.button>
            </a>
        </x-ui.empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($suppliers as $supplier)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between border-b border-slate-100 pb-2.5 mb-2.5 gap-2">
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 text-sm truncate">{{ $supplier->name }}</h3>
                                <p class="text-xs text-slate-400 uppercase font-semibold mt-0.5">{{ $supplier->type === 'company' ? 'Entreprise' : 'Particulier' }}</p>
                            </div>
                            <x-ui.badge :color="$supplier->is_active ? 'emerald' : 'slate'">
                                {{ $supplier->is_active ? 'Actif' : 'Archivé' }}
                            </x-ui.badge>
                        </div>

                        <div class="space-y-1 text-xs sm:text-sm text-slate-600">
                            @if($supplier->phone)
                                <div class="text-xs text-slate-600 truncate">
                                    Tél : <strong class="text-slate-800 font-medium">{{ $supplier->phone }}</strong>
                                </div>
                            @endif

                            @if($supplier->contact_person)
                                <div class="text-slate-500 text-xs truncate">
                                    Contact : {{ $supplier->contact_person }}
                                </div>
                            @endif

                            @if($supplier->address)
                                <div class="text-slate-500 text-xs truncate">
                                    {{ $supplier->address }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('fournisseurs.show', $supplier) }}"
                           class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold"
                           title="Consulter la fiche fournisseur">
                            <x-heroicon-o-truck class="w-4 h-4" />
                            <span>Fiche</span>
                        </a>

                        <a href="{{ route('fournisseurs.edit', $supplier) }}"
                           class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold"
                           title="Modifier le fournisseur">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            <span>Éditer</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $suppliers->links() }}
        </div>
    @endif
</x-layouts.app>

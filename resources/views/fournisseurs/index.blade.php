<x-layouts.app title="Gestion des Fournisseurs">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-truck class="w-6 h-6 text-emerald-600" />
                    <span>Fournisseurs</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Répertoire et fiche de suivi des partenaires fournisseurs
                </p>
            </div>

            <x-ui.button href="{{ route('fournisseurs.create') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
                Nouveau Fournisseur
            </x-ui.button>
        </div>
    </x-slot>

    <!-- Search filter -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('fournisseurs.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par nom, téléphone, contact..."
                   class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                Rechercher
            </x-ui.button>
        </form>
    </x-ui.card>

    @if($suppliers->isEmpty())
        <x-ui.empty-state
            title="Aucun fournisseur enregistré"
            description="Ajoutez vos partenaires fournisseurs pour pouvoir enregistrer des bons d'achat."
            icon="truck">
            <x-ui.button href="{{ route('fournisseurs.create') }}" variant="primary" icon="plus" class="mt-4">
                Nouveau Fournisseur
            </x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($suppliers as $supplier)
                <x-ui.card class="p-4 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between border-b border-slate-100 pb-2 mb-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $supplier->name }}</h3>
                                <p class="text-[11px] text-slate-500">{{ $supplier->type === 'company' ? 'Entreprise' : 'Particulier' }}</p>
                            </div>
                            <x-ui.badge :color="$supplier->is_active ? 'emerald' : 'slate'">
                                {{ $supplier->is_active ? 'Actif' : 'Archivé' }}
                            </x-ui.badge>
                        </div>

                        <div class="space-y-1 text-xs text-slate-600">
                            @if($supplier->phone)
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-user class="w-3.5 h-3.5 text-slate-400" />
                                    <span>Tél : {{ $supplier->phone }}</span>
                                </div>
                            @endif

                            @if($supplier->contact_person)
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-user class="w-3.5 h-3.5 text-slate-400" />
                                    <span>Contact : {{ $supplier->contact_person }}</span>
                                </div>
                            @endif

                            @if($supplier->address)
                                <div class="text-slate-500 text-[11px]">
                                    Adresse : {{ $supplier->address }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <x-ui.button href="{{ route('fournisseurs.show', $supplier) }}" variant="outline" size="sm">
                            Fiche détaillée
                        </x-ui.button>

                        <x-ui.button href="{{ route('fournisseurs.edit', $supplier) }}" variant="secondary" size="sm" icon="pencil-square">
                            Éditer
                        </x-ui.button>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    @endif
</x-layouts.app>

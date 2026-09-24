<x-layouts.app title="Gestion des Clients">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-user class="w-6 h-6 text-emerald-600" />
                    <span>Clients</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Répertoire des clients particuliers et entreprises
                </p>
            </div>

            <x-ui.button href="{{ route('clients.create') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
                Nouveau Client
            </x-ui.button>
        </div>
    </x-slot>

    <!-- Search filter -->
    <x-ui.card class="mb-6 p-4">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par nom, téléphone, email..."
                   class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

            <select name="type" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                <option value="">Tous les types</option>
                <option value="particulier" {{ request('type') === 'particulier' ? 'selected' : '' }}>Particuliers</option>
                <option value="entreprise" {{ request('type') === 'entreprise' ? 'selected' : '' }}>Entreprises</option>
            </select>

            <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                Rechercher
            </x-ui.button>
        </form>
    </x-ui.card>

    @if($customers->isEmpty())
        <x-ui.empty-state
            title="Aucun client enregistré"
            description="Enregistrez vos clients pour faciliter l'émission de leurs factures et le suivi de leurs créances."
            icon="user">
            <x-ui.button href="{{ route('clients.create') }}" variant="primary" icon="plus" class="mt-4">
                Nouveau Client
            </x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($customers as $customer)
                <x-ui.card class="p-4 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between border-b border-slate-100 pb-2 mb-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    @if($customer->type === 'entreprise')
                                        <x-heroicon-o-building-office class="w-4 h-4 text-emerald-600" />
                                    @else
                                        <x-heroicon-o-user class="w-4 h-4 text-sky-600" />
                                    @endif
                                    <span>{{ $customer->name }}</span>
                                </h3>
                                <p class="text-[11px] text-slate-500 capitalize">{{ $customer->type }}</p>
                            </div>
                            <x-ui.badge :color="$customer->is_active ? 'emerald' : 'slate'">
                                {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                            </x-ui.badge>
                        </div>

                        <div class="space-y-1 text-xs text-slate-600">
                            @if($customer->phone)
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-user class="w-3.5 h-3.5 text-slate-400" />
                                    <span>Tél : {{ $customer->phone }}</span>
                                </div>
                            @endif

                            @if($customer->type === 'entreprise' && $customer->contact_person)
                                <div class="text-slate-500 text-[11px]">
                                    Contact : {{ $customer->contact_person }}
                                </div>
                            @endif

                            @if($customer->address)
                                <div class="text-slate-500 text-[11px] truncate">
                                    Adresse : {{ $customer->address }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <x-ui.button href="{{ route('clients.show', $customer) }}" variant="outline" size="sm">
                            Fiche détaillée
                        </x-ui.button>

                        @can('update', $customer)
                            <x-ui.button href="{{ route('clients.edit', $customer) }}" variant="secondary" size="sm" icon="pencil-square">
                                Éditer
                            </x-ui.button>
                        @endcan
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    @endif
</x-layouts.app>

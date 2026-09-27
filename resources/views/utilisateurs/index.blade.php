<x-layouts.app>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left gap-3 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <x-heroicon-o-users class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                    <span>Gestion des Utilisateurs</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Gestion des accès, rôles et réinitialisation de mot de passe.</p>
            </div>
            <a href="{{ route('utilisateurs.create') }}" class="inline-flex w-full sm:w-auto justify-center">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouvel utilisateur
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ showResetModal: false, showDeleteUserModal: false, deleteAction: '', selectedUser: null, resetAction: '', submittingReset: false, submittingDelete: false }">

        <!-- Search & Filter Bar -->
        <x-ui.card class="p-3.5 sm:p-4">
            <form method="GET" action="{{ route('utilisateurs.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email..."
                       class="w-full sm:flex-1 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                <select name="role" class="rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm py-2.5 px-3.5 focus:bg-white focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">
                    <option value="">Tous les rôles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" {{ request('role') === $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2 w-full sm:w-auto">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" size="sm" class="w-full sm:w-auto">
                        Filtrer
                    </x-ui.button>
                    @if (request()->hasAny(['search', 'role']))
                        <a href="{{ route('utilisateurs.index') }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3 text-slate-600 hover:text-slate-900 flex items-center justify-center whitespace-nowrap bg-white">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-4 h-4 text-emerald-600" />
                <span>Liste des Comptes Utilisateurs ({{ $users->total() }})</span>
            </h2>
        </div>

        @if ($users->isEmpty())
            <x-ui.empty-state
                title="Aucun utilisateur trouvé"
                description="Aucun compte ne correspond aux filtres de recherche."
                icon="users"
            />
        @else
            <!-- Table Unifiée Scrollable Horizon -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-x-auto shadow-xs">
                <table class="w-full text-left text-xs sm:text-sm min-w-[650px]">
                    <thead class="bg-slate-50/80 text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4 whitespace-nowrap">Utilisateur</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Rôle</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Statut</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Créé le</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $u)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $u->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $u->isSuperAdmin() ? 'bg-purple-50 text-purple-700 border border-purple-200' : ($u->isAdmin() ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                        {{ $u->role?->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($u->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 font-medium whitespace-nowrap">
                                    {{ $u->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                    @can('update', $u)
                                        <button type="button"
                                                @click="selectedUser = {{ json_encode(['id' => $u->id, 'name' => $u->name]) }}; resetAction = '{{ route('utilisateurs.reset-password', $u->id) }}'; showResetModal = true"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition inline-flex items-center"
                                                title="Réinitialiser le mot de passe">
                                            <x-heroicon-o-key class="w-4 h-4" />
                                        </button>
                                        <a href="{{ route('utilisateurs.edit', $u->id) }}"
                                           class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center"
                                           title="Modifier l'utilisateur">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                    @endcan

                                    @can('delete', $u)
                                        <button type="button"
                                                @click="selectedUser = {{ json_encode(['id' => $u->id, 'name' => $u->name]) }}; deleteAction = '{{ route('utilisateurs.destroy', $u->id) }}'; showDeleteUserModal = true"
                                                class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition inline-flex items-center"
                                                title="Supprimer l'utilisateur">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pt-2">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Modal Password Reset -->
            <div x-cloak
                 x-show="showResetModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="showResetModal = false"
                 class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div x-show="showResetModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     @click.away="showResetModal = false"
                     class="bg-white rounded-3xl max-w-md w-[calc(100%-2rem)] sm:w-full p-6 shadow-2xl space-y-4 border border-slate-100 relative my-auto mx-auto transform text-center">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900">Réinitialiser le mot de passe</h3>
                        <button type="button" @click="showResetModal = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <p class="text-xs text-slate-500">
                        Saisissez le nouveau mot de passe pour <strong class="text-slate-900 font-bold" x-text="selectedUser?.name"></strong> :
                    </p>

                    <form :action="resetAction" method="POST" @submit="submittingReset = true" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5 text-left">Nouveau mot de passe <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full bg-white border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 transition"
                                   placeholder="Minimum 8 caractères">
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row items-center justify-center sm:justify-end gap-2 pt-2 border-t border-slate-100 [&>button]:w-full [&>button]:sm:w-auto">
                            <button type="button" @click="showResetModal = false" class="w-full sm:w-auto px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-xs font-semibold transition active:scale-98">
                                Annuler
                            </button>
                            <x-ui.button type="submit" variant="primary" size="sm" ::loading="submittingReset" class="w-full sm:w-auto">
                                Enregistrer
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Delete User Confirmation -->
            <x-ui.confirm-modal
                name="showDeleteUserModal"
                title="Supprimer cet utilisateur ?"
                message="Confirmez-vous la suppression définitive du compte utilisateur ?"
                confirmText="Supprimer"
                variant="danger"
                icon="trash">
                <form :action="deleteAction" method="POST" @submit="submittingDelete = true" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm" class="w-full" ::loading="submittingDelete">
                        Supprimer
                    </x-ui.button>
                </form>
            </x-ui.confirm-modal>
        @endif

    </div>
</x-layouts.app>

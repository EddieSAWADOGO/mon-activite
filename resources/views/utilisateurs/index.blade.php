<x-layouts.app>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Gestion des Utilisateurs</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Gérez les accès, rôles et réinitialisations de mot de passe du personnel.</p>
            </div>
            <a href="{{ route('utilisateurs.create') }}" class="inline-flex">
                <x-ui.button variant="primary" icon="plus" class="w-full sm:w-auto">
                    Nouvel utilisateur
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Search & Filter Bar -->
        <x-ui.card class="mb-6 p-4">
            <form method="GET" action="{{ route('utilisateurs.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email..."
                       class="flex-1 rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">

                <select name="role" class="rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500 bg-white w-full sm:w-auto">
                    <option value="">Tous les rôles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" {{ request('role') === $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <x-ui.button type="submit" variant="secondary" icon="magnifying-glass" class="h-9">
                        Filtrer
                    </x-ui.button>
                    @if (request()->hasAny(['search', 'role']))
                        <a href="{{ route('utilisateurs.index') }}" class="rounded-lg border border-slate-300 text-xs py-2 px-3 text-slate-600 hover:text-slate-800 flex items-center whitespace-nowrap">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden"
             x-data="{ showResetModal: false, selectedUser: null, resetAction: '' }">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5">Utilisateur</th>
                            <th class="py-3 px-5">Rôle</th>
                            <th class="py-3 px-5">Statut</th>
                            <th class="py-3 px-5">Créé le</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $u->email }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $u->isSuperAdmin() ? 'bg-purple-50 text-purple-700 border border-purple-200' : ($u->isAdmin() ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                        {{ $u->role?->label() }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
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
                                <td class="py-4 px-5 text-xs text-slate-500 font-medium">
                                    {{ $u->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5 text-right space-x-2">
                                    @can('update', $u)
                                        <button type="button" @click="selectedUser = {{ json_encode(['id' => $u->id, 'name' => $u->name]) }}; resetAction = '{{ route('utilisateurs.reset-password', $u->id) }}'; showResetModal = true"
                                                class="text-xs text-amber-600 hover:text-amber-700 font-semibold" title="Réinitialiser le mot de passe">
                                            Mot de passe
                                        </button>
                                        <a href="{{ route('utilisateurs.edit', $u->id) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Modifier</a>
                                    @endcan

                                    @can('delete', $u)
                                        <form method="POST" action="{{ route('utilisateurs.destroy', $u->id) }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold">Supprimer</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Modal Password Reset -->
            <div x-cloak x-show="showResetModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-4 border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Réinitialiser le mot de passe</h3>
                    <p class="text-xs text-slate-500">
                        Saisissez un nouveau mot de passe pour <span class="font-bold text-slate-800" x-text="selectedUser?.name"></span> :
                    </p>

                    <form :action="resetAction" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nouveau mot de passe</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900"
                                   placeholder="Minimum 8 caractères">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showResetModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-xs font-semibold transition">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2.5 bg-amber-600 text-white hover:bg-amber-500 rounded-xl text-xs font-semibold shadow-sm transition">
                                Valider la réinitialisation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>


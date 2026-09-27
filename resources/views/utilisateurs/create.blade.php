<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvel Utilisateur</h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-1">Créez un nouveau compte d'accès pour l'application.</p>
            </div>
            <x-ui.back-button href="{{ route('utilisateurs.index') }}" label="Retour à la liste" />
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card class="p-4 sm:p-6 space-y-6">

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" /> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('utilisateurs.store') }}"
                  x-data="{ submitting: false }"
                  @submit="submitting = true"
                  class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nom complet *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="Ex: Jean Dupont">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Adresse Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="jean.dupont@entreprise.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Mot de passe *</label>
                    <input type="password" name="password" id="password" required minlength="8"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="Minimum 8 caractères">
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">Rôle d'accès *</label>
                    <select name="role" id="role" required
                            class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500/20">
                        <span class="text-xs font-semibold text-slate-700">Compte actif (autorisé à se connecter)</span>
                    </label>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-100 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                    <x-ui.button href="{{ route('utilisateurs.index') }}" variant="outline">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Enregistrer l'utilisateur
                    </x-ui.button>
                </div>
            </form>

        </x-ui.card>
    </div>
</x-layouts.app>

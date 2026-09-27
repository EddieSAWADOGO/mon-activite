<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('utilisateurs.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">Nouvel Utilisateur</h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">Créez un nouveau compte d'accès pour l'application.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl sm:max-w-5xl mx-auto">
        <x-ui.card class="p-3 sm:p-6 space-y-4">

            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" /> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('utilisateurs.store') }}"
                  x-data="{ submitting: false }"
                  @submit="submitting = true"
                  class="space-y-3.5 sm:space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="Ex: Jean Dupont">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Adresse Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="jean.dupont@entreprise.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required minlength="8"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           placeholder="Minimum 8 caractères">
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">Rôle d'accès <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required
                            class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500/20">
                        <span class="text-xs font-semibold text-slate-700">Compte actif (autorisé à se connecter)</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
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

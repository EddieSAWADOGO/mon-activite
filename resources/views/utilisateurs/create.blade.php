<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvel Utilisateur</h1>
                <p class="text-xs text-slate-500 mt-1">Créez un nouveau compte d'accès pour l'application.</p>
            </div>
            <a href="{{ route('utilisateurs.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                &larr; Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>&bull; {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('utilisateurs.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nom complet *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-sm text-slate-900"
                           placeholder="Ex: Jean Dupont">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Adresse Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-sm text-slate-900"
                           placeholder="jean.dupont@entreprise.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Mot de passe *</label>
                    <input type="password" name="password" id="password" required minlength="8"
                           class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-sm text-slate-900"
                           placeholder="Minimum 8 caractères">
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">Rôle d'accès *</label>
                    <select name="role" id="role" required
                            class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl px-3 py-2.5 text-sm text-slate-900">
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
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span class="text-xs font-medium text-slate-700">Compte actif (autorisé à se connecter)</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('utilisateurs.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-semibold transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-sm font-semibold transition shadow-sm">
                        Enregistrer l'utilisateur
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-layouts.app>

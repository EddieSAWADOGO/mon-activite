<x-layouts.app title="Créer un Fournisseur">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-truck class="w-6 h-6 text-emerald-600" />
                    <span>Nouveau Fournisseur</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Ajoutez un nouveau partenaire fournisseur dans votre base de données
                </p>
            </div>
            <x-ui.button href="{{ route('fournisseurs.index') }}" variant="secondary" size="sm">
                Retour
            </x-ui.button>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('fournisseurs.store') }}" class="max-w-2xl mx-auto space-y-6">
        @csrf

        <x-ui.card class="p-4 sm:p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nom / Raison Sociale <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="company" @selected(old('type') == 'company')>Entreprise / Société</option>
                        <option value="individual" @selected(old('type') == 'individual')>Particulier</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Personne de contact</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone principal</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse physique / Siège</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Remarques libres</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <x-ui.button href="{{ route('fournisseurs.index') }}" variant="secondary" size="md">
                    Annuler
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle">
                    Enregistrer le fournisseur
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-layouts.app>

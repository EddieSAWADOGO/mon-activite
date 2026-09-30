<x-layouts.app title="Modifier le Fournisseur">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('fournisseurs.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Éditer : {{ $supplier->name }}</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('fournisseurs.update', $supplier) }}"
          x-data="{ submitting: false }"
          @submit="submitting = true"
          class="max-w-6xl mx-auto space-y-5">
        @csrf
        @method('PUT')

        <x-ui.card class="p-4 sm:p-6 lg:p-7 space-y-4 sm:space-y-5">
            <div>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nom / Raison Sociale <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                @error('name')<p class="text-xs text-red-600 mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        <option value="company" @selected(old('type', $supplier->type) == 'company')>Entreprise / Société</option>
                        <option value="individual" @selected(old('type', $supplier->type) == 'individual')>Particulier</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Statut <span class="text-red-500">*</span></label>
                    <select name="is_active" class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        <option value="1" @selected(old('is_active', $supplier->is_active) == true)>Actif</option>
                        <option value="0" @selected(old('is_active', $supplier->is_active) == false)>Inactif / Archivé</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Personne de contact</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Téléphone principal</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Numéro WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $supplier->whatsapp) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Adresse physique / Siège</label>
                <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Notes / Remarques libres</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                <x-ui.button href="{{ route('fournisseurs.index') }}" variant="secondary" size="md">
                    Annuler
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle" ::loading="submitting">
                    Mettre à jour
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-layouts.app>

<x-layouts.app title="Paramètres de l'Entreprise Émettrice">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60 min-w-0">
            <x-ui.back-button href="{{ route('dashboard') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">
                    Paramètres de l'Entreprise Émettrice
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-5" x-data="{ submitting: false }">
        <x-ui.card class="p-4 sm:p-6 lg:p-7 bg-white shadow-xs border border-slate-200/80 rounded-3xl">
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Coordonnées & Identifiants Légaux</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Ces informations figurent sur toutes vos factures imprimées, PDF et reçus transmis à vos clients.
                    </p>
                </div>

                @if($setting->logo_path)
                    <div class="shrink-0 flex items-center gap-3 p-2 bg-slate-50 rounded-2xl border border-slate-200">
                        <img src="{{ $setting->logo_path }}" alt="Logo actuel" class="h-12 w-auto max-w-[120px] object-contain rounded-lg">
                        <span class="text-xs font-semibold text-slate-600 pr-1">Logo actuel</span>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" @submit="submitting = true" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nom de l'entreprise -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Nom de l'entreprise émettrice <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $setting->name) }}" required
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: AGRO-SERVICES FASO SARL">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sigle / Activité -->
                    <div class="sm:col-span-2">
                        <label for="tagline" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Domaine d'activité / Sous-titre commercial
                        </label>
                        <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $setting->tagline) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: Commerce Général & Négoce d'Intrants Agricoles">
                        @error('tagline')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- N° IFU -->
                    <div>
                        <label for="ifu" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            N° IFU (Identifiant Fiscal Unique)
                        </label>
                        <input type="text" name="ifu" id="ifu" value="{{ old('ifu', $setting->ifu) }}"
                            class="w-full font-mono rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: 00012345A ou 3202415987612">
                        @error('ifu')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- N° RCCM -->
                    <div>
                        <label for="rccm" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            N° RCCM (Registre du Commerce)
                        </label>
                        <input type="text" name="rccm" id="rccm" value="{{ old('rccm', $setting->rccm) }}"
                            class="w-full font-mono rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: BF OUA 2024 B 4589">
                        @error('rccm')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphones -->
                    <div>
                        <label for="phone" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Téléphone(s) de contact
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $setting->phone) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: +226 25 30 00 00 / +226 70 00 11 22">
                        @error('phone')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Adresse E-mail officielle
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $setting->email) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: contact@entreprise.bf">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse physique -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Adresse géographique / Siège
                        </label>
                        <input type="text" name="address" id="address" value="{{ old('address', $setting->address) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: Avenue Kadiogo, Zone Industrielle, Ouagadougou, Burkina Faso">
                        @error('address')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coordonnées bancaires -->
                    <div class="sm:col-span-2">
                        <label for="bank_details" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Coordonnées bancaires (RIB / Compte)
                        </label>
                        <input type="text" name="bank_details" id="bank_details" value="{{ old('bank_details', $setting->bank_details) }}"
                            class="w-full font-mono rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            placeholder="ex: BOA Burkina: BF084 01001 012345678901 45">
                        @error('bank_details')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo File Upload -->
                    <div class="sm:col-span-2 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <label for="logo" class="block text-xs sm:text-sm font-semibold text-slate-800 mb-1">
                            Téléverser / Modifier le logo de l'entreprise
                        </label>
                        <p class="text-xs text-slate-500 mb-2">
                            Formats acceptés: PNG, JPEG, WEBP, SVG (Max 2 Mo).
                        </p>
                        <input type="file" name="logo" id="logo" accept="image/*"
                            class="block w-full text-xs sm:text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
                        @error('logo')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Footer Actions -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <x-ui.button href="{{ route('dashboard') }}" variant="outline" class="w-full sm:w-auto">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check" ::loading="submitting" class="w-full sm:w-auto">
                        Enregistrer la configuration
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

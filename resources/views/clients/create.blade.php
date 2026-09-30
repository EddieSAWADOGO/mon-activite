<x-layouts.app title="Nouveau Client">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('clients.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-user class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Nouveau Client</span>
                </h2>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Enregistrer un nouveau client particulier ou entreprise
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-5" x-data="{ submitting: false, type: '{{ old('type', 'particulier') }}' }">
        <x-ui.card class="p-4 sm:p-6 lg:p-7">
            <form method="POST" action="{{ route('clients.store') }}" @submit="submitting = true" class="space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Type de client <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center p-3 rounded-xl border text-xs sm:text-sm font-medium cursor-pointer transition active:scale-98"
                               :class="type === 'particulier' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="type" value="particulier" x-model="type" class="sr-only">
                            <x-heroicon-o-user class="w-4 h-4 mr-1.5 text-emerald-600 shrink-0" />
                            <span>Particulier</span>
                        </label>
                        <label class="flex items-center justify-center p-3 rounded-xl border text-xs sm:text-sm font-medium cursor-pointer transition active:scale-98"
                               :class="type === 'entreprise' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="type" value="entreprise" x-model="type" class="sr-only">
                            <x-heroicon-o-building-office class="w-4 h-4 mr-1.5 text-emerald-600 shrink-0" />
                            <span>Entreprise</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5"><span x-text="type === 'entreprise' ? 'Raison Sociale' : 'Nom et Prénom'"></span> <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('name') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div x-show="type === 'entreprise'" x-transition class="p-4 sm:p-5 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-4">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200/60 pb-2">
                        <x-heroicon-o-building-office class="w-4 h-4 text-emerald-600" />
                        <span>Informations Entreprise (Spécifiques)</span>
                    </h3>

                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nom du responsable / Contact</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('contact_person') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Numéro IFU</label>
                            <input type="text" name="ifu" value="{{ old('ifu') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            @error('ifu') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Numéro RCCM</label>
                            <input type="text" name="rccm" value="{{ old('rccm') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            @error('rccm') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('phone') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('whatsapp') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('email') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Adresse physique</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('address') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Notes / Remarques</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                    <x-ui.button href="{{ route('clients.index') }}" variant="outline">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Enregistrer le Client
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

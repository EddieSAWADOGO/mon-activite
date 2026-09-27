<x-layouts.app title="Éditer le Client">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('clients.show', $customer) }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Modifier {{ $customer->name }}</span>
                </h2>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Mise à jour des informations du client
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl sm:max-w-5xl mx-auto" x-data="{ submitting: false, type: '{{ old('type', $customer->type) }}' }">
        <x-ui.card class="p-3 sm:p-6">
            <form method="POST" action="{{ route('clients.update', $customer) }}" @submit="submitting = true" class="space-y-3.5 sm:space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-semibold text-slate-700">Statut du compte</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="rounded-md text-emerald-600 focus:ring-emerald-500/20 border-slate-300">
                        <span class="text-xs text-slate-700 font-medium">Compte actif</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de client <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-medium cursor-pointer transition active:scale-98"
                               :class="type === 'particulier' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="type" value="particulier" x-model="type" class="sr-only">
                            <x-heroicon-o-user class="w-4 h-4 mr-1.5 text-emerald-600" />
                            <span>Particulier</span>
                        </label>
                        <label class="flex items-center justify-center p-2.5 rounded-xl border text-xs font-medium cursor-pointer transition active:scale-98"
                               :class="type === 'entreprise' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="type" value="entreprise" x-model="type" class="sr-only">
                            <x-heroicon-o-building-office class="w-4 h-4 mr-1.5 text-emerald-600" />
                            <span>Entreprise</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1"><span x-text="type === 'entreprise' ? 'Raison Sociale' : 'Nom et Prénom'"></span> <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('name') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div x-show="type === 'entreprise'">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom du responsable / Contact</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $customer->contact_person) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('contact_person') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('phone') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('whatsapp') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('email') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse physique</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('address') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div x-show="type === 'entreprise'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro IFU</label>
                        <input type="text" name="ifu" value="{{ old('ifu', $customer->ifu) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('ifu') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro RCCM</label>
                        <input type="text" name="rccm" value="{{ old('rccm', $customer->rccm) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('rccm') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Remarques</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-white text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                    <x-ui.button href="{{ route('clients.show', $customer) }}" variant="outline">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Mettre à jour
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

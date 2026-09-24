<x-layouts.app title="Éditer le Client">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-pencil-square class="w-6 h-6 text-emerald-600" />
                    <span>Modifier {{ $customer->name }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Mise à jour des informations du client
                </p>
            </div>
            <x-ui.button href="{{ route('clients.show', $customer) }}" variant="outline" size="sm">
                Retour à la fiche
            </x-ui.button>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto" x-data="{ type: '{{ old('type', $customer->type) }}' }">
        <x-ui.card class="p-6">
            <form method="POST" action="{{ route('clients.update', $customer) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <span class="text-xs font-semibold text-slate-700">Statut du compte</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700">Compte actif</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de client *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center p-3 rounded-lg border text-xs font-medium cursor-pointer transition"
                               :class="type === 'particulier' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                            <input type="radio" name="type" value="particulier" x-model="type" class="sr-only">
                            <x-heroicon-o-user class="w-4 h-4 mr-2" />
                            <span>Particulier</span>
                        </label>
                        <label class="flex items-center justify-center p-3 rounded-lg border text-xs font-medium cursor-pointer transition"
                               :class="type === 'entreprise' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                            <input type="radio" name="type" value="entreprise" x-model="type" class="sr-only">
                            <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                            <span>Entreprise</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1" x-text="type === 'entreprise' ? 'Raison Sociale *' : 'Nom et Prénom *'"></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                    @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="type === 'entreprise'">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom du responsable / Contact</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $customer->contact_person) }}"
                           class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                    @error('contact_person') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                               class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                        @error('phone') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}"
                               class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                        @error('whatsapp') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                    @error('email') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse physique</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address) }}"
                           class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                    @error('address') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="type === 'entreprise'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro IFU</label>
                        <input type="text" name="ifu" value="{{ old('ifu', $customer->ifu) }}"
                               class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                        @error('ifu') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro RCCM</label>
                        <input type="text" name="rccm" value="{{ old('rccm', $customer->rccm) }}"
                               class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                        @error('rccm') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Remarques</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border border-slate-300 text-xs py-2 px-3 focus:ring-2 focus:ring-emerald-500">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <x-ui.button href="{{ route('clients.show', $customer) }}" variant="outline">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check-circle">
                        Mettre à jour
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

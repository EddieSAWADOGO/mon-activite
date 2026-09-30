<x-layouts.app title="Enregistrer un retour client">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('retours.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-arrow-uturn-left class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Enregistrer un retour client</span>
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-5">
        <x-ui.card class="p-4 sm:p-6 lg:p-7" x-data="{
            submitting: false,
            products: {{ json_encode($products) }},
            selectedProductId: '{{ old('product_id') }}',
            units: [],
            updateUnits() {
                const prod = this.products.find(p => p.id == this.selectedProductId);
                this.units = prod ? (prod.active_units || prod.units || []) : [];
            }
        }" x-init="if(selectedProductId) updateUnits()">
            <form action="{{ route('retours.store') }}" method="POST" @submit="submitting = true" class="space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label for="product_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Produit retourné <span class="text-red-500">*</span>
                    </label>
                    <x-ui.select-searchable
                        name="product_id"
                        id="product_id"
                        options="products"
                        placeholder="Commencer à taper le nom du produit..."
                        emptyLabel="-- Sélectionner un produit --"
                        :required="true"
                        model="selectedProductId"
                        onChange="updateUnits()"
                    />
                    @error('product_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label for="stock_unit_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Unité du produit <span class="text-red-500">*</span>
                        </label>
                        <select name="stock_unit_id"
                                id="stock_unit_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            <option value="">-- Sélectionner l'unité --</option>
                            <template x-for="u in units" :key="u.id">
                                <option :value="u.id" x-text="u.name"></option>
                            </template>
                        </select>
                        @error('stock_unit_id')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Quantité retournée <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="quantity"
                               id="quantity"
                               step="0.01"
                               min="0.01"
                               value="{{ old('quantity', 1) }}"
                               required
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-bold transition">
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label for="customer_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Client (Optionnel)
                        </label>
                        <x-ui.select-searchable
                            name="customer_id"
                            id="customer_id"
                            :options="$customers"
                            :value="old('customer_id', '')"
                            placeholder="Commencer à taper le nom du client..."
                            emptyLabel="Client anonyme / de passage"
                        />
                    </div>

                    <div>
                        <label for="invoice_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Facture d'origine (Optionnel)
                        </label>
                        <select name="invoice_id" id="invoice_id" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            <option value="">Aucune référence directe</option>
                            @foreach($recentInvoices as $inv)
                                <option value="{{ $inv->id }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                                    {{ $inv->invoice_number }} ({{ $inv->customer ? $inv->customer->name : 'Passage' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Motif du retour <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="reason"
                           id="reason"
                           placeholder="Ex: Produit défectueux, Erreur de commande, Client insatisfait..."
                           value="{{ old('reason') }}"
                           required
                           class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('reason')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="return_date" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Date de réception du retour <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="return_date"
                           id="return_date"
                           value="{{ old('return_date', now()->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('return_date')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Remarques supplémentaires (Optionnel)
                    </label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes') }}</textarea>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs sm:text-sm text-amber-800 flex items-start gap-2.5">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <span>
                        <strong>Rappel :</strong> L'enregistrement du retour ne réintègre <strong>pas automatiquement</strong> le produit en stock.
                    </span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                    <a href="{{ route('retours.index') }}">
                        <x-ui.button type="button" variant="outline">
                            Annuler
                        </x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Enregistrer le retour
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

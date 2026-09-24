<x-layouts.app title="Enregistrer un retour client">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-arrow-uturn-left class="w-6 h-6 text-emerald-600" />
                Enregistrer un retour client
            </h1>
            <a href="{{ route('retours.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
                &larr; Annuler
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card x-data="{
            products: {{ json_encode($products) }},
            selectedProductId: '{{ old('product_id') }}',
            units: [],
            updateUnits() {
                const prod = this.products.find(p => p.id == this.selectedProductId);
                this.units = prod ? (prod.active_units || prod.units || []) : [];
            }
        }" x-init="if(selectedProductId) updateUnits()">
            <form action="{{ route('retours.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="product_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Produit retourné <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" 
                            id="product_id" 
                            x-model="selectedProductId" 
                            @change="updateUnits()" 
                            required 
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">-- Sélectionner un produit --</option>
                        <template x-for="p in products" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stock_unit_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Unité du produit <span class="text-red-500">*</span>
                        </label>
                        <select name="stock_unit_id" 
                                id="stock_unit_id" 
                                required 
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Sélectionner l'unité --</option>
                            <template x-for="u in units" :key="u.id">
                                <option :value="u.id" x-text="u.name"></option>
                            </template>
                        </select>
                        @error('stock_unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">
                            Quantité retournée <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity" 
                               step="0.01" 
                               min="0.01" 
                               value="{{ old('quantity', 1) }}" 
                               required 
                               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Client (Optionnel)
                        </label>
                        <select name="customer_id" id="customer_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">Client anonyme / de passage</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="invoice_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Facture d'origine (Optionnel)
                        </label>
                        <select name="invoice_id" id="invoice_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
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
                    <label for="reason" class="block text-sm font-medium text-slate-700 mb-1">
                        Motif du retour <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="reason" 
                           id="reason" 
                           placeholder="Ex: Produit défectueux, Erreur de commande, Client insatisfait..." 
                           value="{{ old('reason') }}" 
                           required 
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="return_date" class="block text-sm font-medium text-slate-700 mb-1">
                        Date de réception du retour <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="return_date" 
                           id="return_date" 
                           value="{{ old('return_date', now()->format('Y-m-d\TH:i')) }}" 
                           required 
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('return_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">
                        Remarques supplémentaires (Optionnel)
                    </label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800 flex items-start gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <span>
                        <strong>Rappel :</strong> L'enregistrement du retour ne réintègre <strong>pas automatiquement</strong> le produit en stock. Une validation manuelle ultérieure permettra de décider s'il est réintégré ou déclaré en perte.
                    </span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('retours.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 border border-slate-300 rounded-lg">
                        Annuler
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle">
                        Enregistrer le retour
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

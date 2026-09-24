<x-layouts.app title="Déclarer une perte de stock">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-x-circle class="w-6 h-6 text-red-600" />
                Déclarer une perte de stock
            </h1>
            <a href="{{ route('pertes.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
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
            <form action="{{ route('pertes.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="product_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Produit concerné <span class="text-red-500">*</span>
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
                            Unité de constatation <span class="text-red-500">*</span>
                        </label>
                        <select name="stock_unit_id" 
                                id="stock_unit_id" 
                                required 
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Sélectionner l'unité --</option>
                            <template x-for="u in units" :key="u.id">
                                <option :value="u.id" x-text="`${u.name} (Stock actuel: ${u.current_stock})`"></option>
                            </template>
                        </select>
                        @error('stock_unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">
                            Quantité perdue <span class="text-red-500">*</span>
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

                <div>
                    <label for="reason" class="block text-sm font-medium text-slate-700 mb-1">
                        Motif de la perte <span class="text-red-500">*</span>
                    </label>
                    <select name="reason" id="reason" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="Produit périmé" {{ old('reason') == 'Produit périmé' ? 'selected' : '' }}>Produit périmé</option>
                        <option value="Produit cassé / abîmé" {{ old('reason') == 'Produit cassé / abîmé' ? 'selected' : '' }}>Produit cassé / abîmé</option>
                        <option value="Vol / Disparition" {{ old('reason') == 'Vol / Disparition' ? 'selected' : '' }}>Vol / Disparition</option>
                        <option value="Détérioration stock" {{ old('reason') == 'Détérioration stock' ? 'selected' : '' }}>Détérioration stock</option>
                        <option value="Erreur d'inventaire" {{ old('reason') == "Erreur d'inventaire" ? 'selected' : '' }}>Erreur d'inventaire</option>
                        <option value="Autre" {{ old('reason') == 'Autre' ? 'selected' : '' }}>Autre motif</option>
                    </select>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="loss_date" class="block text-sm font-medium text-slate-700 mb-1">
                        Date de la constatation <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="loss_date" 
                           id="loss_date" 
                           value="{{ old('loss_date', now()->format('Y-m-d\TH:i')) }}" 
                           required 
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('loss_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">
                        Commentaires / Explications (Optionnel)
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Détails complémentaires sur les circonstances..." class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-800 flex items-start gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                    <span>
                        <strong>Attention :</strong> La validation de cette déclaration va immédiatement <strong>décrémenter la quantité en stock</strong> de l'unité choisie.
                    </span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pertes.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 border border-slate-300 rounded-lg">
                        Annuler
                    </a>
                    <x-ui.button type="submit" variant="danger" icon="check-circle">
                        Valider la perte
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

<x-layouts.app title="Nouveau reconditionnement">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-arrows-right-left class="w-6 h-6 text-emerald-600" />
                Nouveau reconditionnement d'unités
            </h1>
            <a href="{{ route('reconditionnement.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
                &larr; Annuler
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card x-data="{
            products: {{ json_encode($products) }},
            selectedProductId: '{{ old('product_id') }}',
            units: [],
            sourceUnitId: '{{ old('source_stock_unit_id') }}',
            targetUnitId: '{{ old('target_stock_unit_id') }}',
            sourceQty: '{{ old('source_quantity', 1) }}',
            targetQty: '{{ old('target_quantity', 1) }}',
            updateUnits() {
                const prod = this.products.find(p => p.id == this.selectedProductId);
                this.units = prod ? (prod.active_units || prod.units || []) : [];
            },
            getSourceUnit() {
                return this.units.find(u => u.id == this.sourceUnitId);
            },
            getTargetUnit() {
                return this.units.find(u => u.id == this.targetUnitId);
            },
            getSourceBaseTotal() {
                const u = this.getSourceUnit();
                return u ? (parseFloat(this.sourceQty || 0) * parseFloat(u.base_unit_equivalent)) : 0;
            },
            getTargetBaseTotal() {
                const u = this.getTargetUnit();
                return u ? (parseFloat(this.targetQty || 0) * parseFloat(u.base_unit_equivalent)) : 0;
            }
        }" x-init="if(selectedProductId) updateUnits()">
            <form action="{{ route('reconditionnement.store') }}" method="POST" class="space-y-5">
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

                <!-- Unité Source (Prélèvement) -->
                <div class="bg-red-50/50 p-4 rounded-xl border border-red-100 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-red-700 flex items-center gap-1.5">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-red-600" />
                        Source (Unité prélevée / réduite)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="source_stock_unit_id" class="block text-xs font-medium text-slate-700 mb-1">
                                Unité de départ <span class="text-red-500">*</span>
                            </label>
                            <select name="source_stock_unit_id" 
                                    id="source_stock_unit_id" 
                                    x-model="sourceUnitId" 
                                    required 
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Unité source --</option>
                                <template x-for="u in units" :key="u.id">
                                    <option :value="u.id" x-text="`${u.name} (Stock: ${u.current_stock}, Eq: ${u.base_unit_equivalent})`"></option>
                                </template>
                            </select>
                            @error('source_stock_unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="source_quantity" class="block text-xs font-medium text-slate-700 mb-1">
                                Quantité à prélever <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="source_quantity" 
                                   id="source_quantity" 
                                   x-model="sourceQty" 
                                   step="0.01" 
                                   min="0.01" 
                                   required 
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-bold text-red-700">
                            @error('source_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Unité Cible (Destination) -->
                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-emerald-600" />
                        Cible (Unité créée / augmentée)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="target_stock_unit_id" class="block text-xs font-medium text-slate-700 mb-1">
                                Unité d'arrivée <span class="text-red-500">*</span>
                            </label>
                            <select name="target_stock_unit_id" 
                                    id="target_stock_unit_id" 
                                    x-model="targetUnitId" 
                                    required 
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Unité cible --</option>
                                <template x-for="u in units" :key="u.id">
                                    <option :value="u.id" x-text="`${u.name} (Stock: ${u.current_stock}, Eq: ${u.base_unit_equivalent})`"></option>
                                </template>
                            </select>
                            @error('target_stock_unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="target_quantity" class="block text-xs font-medium text-slate-700 mb-1">
                                Quantité à obtenir <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="target_quantity" 
                                   id="target_quantity" 
                                   x-model="targetQty" 
                                   step="0.01" 
                                   min="0.01" 
                                   required 
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-bold text-emerald-700">
                            @error('target_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Live Equivalence Verification Banner -->
                <div x-show="sourceUnitId && targetUnitId" class="p-3 rounded-lg text-xs"
                     :class="Math.abs(getSourceBaseTotal() - getTargetBaseTotal()) < 0.001 ? 'bg-sky-50 border border-sky-200 text-sky-900' : 'bg-amber-50 border border-amber-200 text-amber-900'">
                    <div class="flex items-center justify-between font-medium">
                        <span>Équivalence Source : <strong x-text="getSourceBaseTotal()"></strong> unités de base</span>
                        <span>Équivalence Cible : <strong x-text="getTargetBaseTotal()"></strong> unités de base</span>
                    </div>
                    <div class="mt-1 text-[11px]" x-show="Math.abs(getSourceBaseTotal() - getTargetBaseTotal()) >= 0.001">
                        <span class="font-bold text-amber-700">Attention : Le volume prélevé et le volume obtenu doivent être égaux.</span>
                    </div>
                </div>

                <div>
                    <label for="repackaging_date" class="block text-sm font-medium text-slate-700 mb-1">
                        Date de l'opération <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="repackaging_date" 
                           id="repackaging_date" 
                           value="{{ old('repackaging_date', now()->format('Y-m-d\TH:i')) }}" 
                           required 
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('repackaging_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">
                        Remarques (Optionnel)
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Ex: Regroupement de 12 vracs en 1 carton..." class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('reconditionnement.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 border border-slate-300 rounded-lg">
                        Annuler
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle">
                        Exécuter le reconditionnement
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

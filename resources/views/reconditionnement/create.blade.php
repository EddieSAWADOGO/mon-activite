<x-layouts.app title="Nouveau reconditionnement">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('reconditionnement.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-arrows-right-left class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Nouveau reconditionnement</span>
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl sm:max-w-5xl mx-auto">
        <x-ui.card class="p-4 sm:p-6 lg:p-7" x-data="{
            submitting: false,
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
            <form action="{{ route('reconditionnement.store') }}" method="POST" @submit="submitting = true" class="space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label for="product_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Produit concerné <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id"
                            id="product_id"
                            x-model="selectedProductId"
                            @change="updateUnits()"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium">
                        <option value="">-- Sélectionner un produit --</option>
                        <template x-for="p in products" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Unité Source (Prélèvement) -->
                <div class="bg-red-50/70 p-4 sm:p-5 lg:p-6 rounded-2xl border border-red-200/80 space-y-3.5 shadow-xs">
                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-red-700 flex items-center gap-1.5">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-red-600 shrink-0" />
                        <span>Source (Unité prélevée / réduite)</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <div>
                            <label for="source_stock_unit_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Unité de départ <span class="text-red-500">*</span>
                            </label>
                            <select name="source_stock_unit_id"
                                    id="source_stock_unit_id"
                                    x-model="sourceUnitId"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                                <option value="">-- Unité source --</option>
                                <template x-for="u in units" :key="u.id">
                                    <option :value="u.id" x-text="`${u.name} (Stock: ${u.current_stock}, Eq: ${u.base_unit_equivalent})`"></option>
                                </template>
                            </select>
                            @error('source_stock_unit_id')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="source_quantity" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Quantité à prélever <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="source_quantity"
                                   id="source_quantity"
                                   x-model="sourceQty"
                                   step="0.01"
                                   min="0.01"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            @error('source_quantity')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Unité Cible (Destination) -->
                <div class="bg-emerald-50/70 p-4 sm:p-5 lg:p-6 rounded-2xl border border-emerald-200/80 space-y-3.5 shadow-xs">
                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>Cible (Unité créée / augmentée)</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <div>
                            <label for="target_stock_unit_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Unité d'arrivée <span class="text-red-500">*</span>
                            </label>
                            <select name="target_stock_unit_id"
                                    id="target_stock_unit_id"
                                    x-model="targetUnitId"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                                <option value="">-- Unité cible --</option>
                                <template x-for="u in units" :key="u.id">
                                    <option :value="u.id" x-text="`${u.name} (Stock: ${u.current_stock}, Eq: ${u.base_unit_equivalent})`"></option>
                                </template>
                            </select>
                            @error('target_stock_unit_id')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="target_quantity" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Quantité à obtenir <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="target_quantity"
                                   id="target_quantity"
                                   x-model="targetQty"
                                   step="0.01"
                                   min="0.01"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            @error('target_quantity')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Live Equivalence Verification Banner -->
                <div x-show="sourceUnitId && targetUnitId" class="p-4 sm:p-5 rounded-2xl text-xs sm:text-sm shadow-xs"
                     :class="Math.abs(getSourceBaseTotal() - getTargetBaseTotal()) < 0.001 ? 'bg-sky-50 border border-sky-200 text-sky-900' : 'bg-amber-50 border border-amber-200 text-amber-900'">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-1.5 font-bold">
                        <span>Source : <strong class="text-sky-700" x-text="getSourceBaseTotal()"></strong> unités de base</span>
                        <span>Cible : <strong class="text-emerald-700" x-text="getTargetBaseTotal()"></strong> unités de base</span>
                    </div>
                    <div class="mt-1.5 text-xs font-semibold" x-show="Math.abs(getSourceBaseTotal() - getTargetBaseTotal()) >= 0.001">
                        <span class="text-amber-700 flex items-center gap-1"><x-heroicon-o-exclamation-triangle class="w-4 h-4 shrink-0" /> Le volume prélevé et le volume obtenu doivent être strictement égaux.</span>
                    </div>
                </div>

                <div>
                    <label for="repackaging_date" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Date de l'opération <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="repackaging_date"
                           id="repackaging_date"
                           value="{{ old('repackaging_date', now()->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('repackaging_date')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Remarques (Optionnel)
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Ex: Regroupement de 12 vracs en 1 carton..." class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                    <a href="{{ route('reconditionnement.index') }}">
                        <x-ui.button type="button" variant="outline">
                            Annuler
                        </x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Exécuter le reconditionnement
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

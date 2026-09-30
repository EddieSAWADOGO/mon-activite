<x-layouts.app title="Saisie d'Inventaire Physique & Ajustement">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('stock.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 shrink-0" />
                    <span class="truncate">Saisie d'Inventaire & Ajustement de Stock</span>
                </h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Ajuster le stock système sur la quantité physique réelle constatée lors du comptage en magasin.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-5">
        <x-ui.card class="p-4 sm:p-6 lg:p-7" x-data="{
            submitting: false,
            products: {{ json_encode($products) }},
            selectedProductId: '{{ old('product_id', $selectedProductId) }}',
            units: [],
            selectedUnitId: '{{ old('stock_unit_id', $selectedUnitId) }}',
            physicalQty: '{{ old('physical_quantity', '') }}',
            updateUnits() {
                const prod = this.products.find(p => p.id == this.selectedProductId);
                this.units = prod ? (prod.active_units || prod.units || []) : [];
                if (this.units.length > 0) {
                    if (!this.selectedUnitId || !this.units.find(u => u.id == this.selectedUnitId)) {
                        this.selectedUnitId = this.units[0].id;
                    }
                } else {
                    this.selectedUnitId = '';
                }
                this.syncPhysicalQtyWithSystem();
            },
            get selectedUnit() {
                return this.units.find(u => u.id == this.selectedUnitId);
            },
            get systemQty() {
                const u = this.selectedUnit;
                return u ? parseFloat(u.current_stock || 0) : 0;
            },
            get gap() {
                if (!this.selectedUnit || this.physicalQty === '' || this.physicalQty === null) return 0;
                return parseFloat(this.physicalQty || 0) - this.systemQty;
            },
            syncPhysicalQtyWithSystem() {
                if (this.selectedUnit) {
                    this.physicalQty = this.systemQty;
                } else {
                    this.physicalQty = '';
                }
            }
        }" x-init="if(selectedProductId) updateUnits()">
            <form action="{{ route('stock.inventory.store') }}" method="POST" @submit="submitting = true" class="space-y-5">
                @csrf

                <!-- Produit concerné -->
                <div>
                    <label for="product_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Produit concerné <span class="text-red-500">*</span>
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

                <!-- Unité & Quantités -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stock_unit_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Unité / Format de comptage <span class="text-red-500">*</span>
                        </label>
                        <select name="stock_unit_id"
                                id="stock_unit_id"
                                x-model="selectedUnitId"
                                @change="syncPhysicalQtyWithSystem()"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            <option value="">-- Sélectionner l'unité --</option>
                            <template x-for="u in units" :key="u.id">
                                <option :value="u.id" x-text="`${u.name} (Stock actuel : ${u.current_stock})`"></option>
                            </template>
                        </select>
                        @error('stock_unit_id')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock système actuel (lecture seule) -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Stock Théorique Système
                        </label>
                        <div class="py-2.5 px-3.5 sm:py-3 sm:px-4 rounded-xl bg-slate-100 border border-slate-200 text-xs sm:text-sm font-bold text-slate-900 flex items-center justify-between min-h-[42px]">
                            <span>Enregistré en système :</span>
                            <span class="text-base text-slate-900 font-extrabold" x-text="selectedUnit ? `${systemQty} ${selectedUnit.name}` : '0'"></span>
                        </div>
                    </div>
                </div>

                <!-- Quantité physique réelle comptée -->
                <div class="bg-purple-50/70 p-4 sm:p-5 lg:p-6 rounded-2xl border border-purple-200/80 space-y-4 shadow-xs">
                    <div>
                        <label for="physical_quantity" class="block text-xs sm:text-sm font-bold text-purple-950 mb-1.5">
                            Quantité Physique Réelle Comptée en Magasin <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="physical_quantity"
                               id="physical_quantity"
                               x-model="physicalQty"
                               step="0.01"
                               min="0"
                               required
                               placeholder="Entrez la quantité réelle dénombrée..."
                               class="w-full rounded-xl border border-purple-200 bg-white py-3 px-4 text-sm sm:text-base text-slate-900 font-extrabold focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition">
                        @error('physical_quantity')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dynamic Discrepancy Banner -->
                    <div x-show="selectedUnit && physicalQty !== '' && physicalQty !== null" class="p-4 rounded-2xl text-xs sm:text-sm font-bold shadow-2xs"
                         :class="{
                             'bg-emerald-100 border border-emerald-300 text-emerald-950': gap > 0,
                             'bg-red-100 border border-red-300 text-red-950': gap < 0,
                             'bg-sky-50 border border-sky-200 text-sky-950': Math.abs(gap) < 0.0001
                         }">
                        <div class="flex items-center justify-between gap-2">
                            <span class="flex items-center gap-1.5">
                                <template x-if="gap > 0">
                                    <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-emerald-600 shrink-0" />
                                </template>
                                <template x-if="gap < 0">
                                    <x-heroicon-o-arrow-trending-down class="w-5 h-5 text-red-600 shrink-0" />
                                </template>
                                <template x-if="Math.abs(gap) < 0.0001">
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-sky-600 shrink-0" />
                                </template>

                                <span x-text="gap > 0 ? 'Surplus d\'inventaire détecté (Ajustement positif)' : (gap < 0 ? 'Déficit / Manquant détecté (Ajustement négatif)' : 'Stock parfaitement conforme')"></span>
                            </span>

                            <span class="text-sm sm:text-base font-black font-mono"
                                  x-text="gap > 0 ? `+${gap} ${selectedUnit ? selectedUnit.name : ''}` : (gap < 0 ? `${gap} ${selectedUnit ? selectedUnit.name : ''}` : 'Écart 0')"></span>
                        </div>
                    </div>
                </div>

                <!-- Date & Notes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="inventory_date" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Date de l'inventaire <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local"
                               name="inventory_date"
                               id="inventory_date"
                               value="{{ old('inventory_date', now()->format('Y-m-d\TH:i')) }}"
                               required
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('inventory_date')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Observations / Explications (Optionnel)
                        </label>
                        <input type="text"
                               name="notes"
                               id="notes"
                               value="{{ old('notes') }}"
                               placeholder="Ex: Inventaire mensuel, comptage physique du magasin central..."
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 sm:py-3 sm:px-4 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto text-center">
                    <a href="{{ route('stock.index') }}">
                        <x-ui.button type="button" variant="outline">
                            Annuler
                        </x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Valider l'ajustement d'inventaire
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

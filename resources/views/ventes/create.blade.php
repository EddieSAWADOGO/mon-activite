<x-layouts.app title="Enregistrer une Vente">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
                    <span>Nouvelle Vente</span>
                </h2>
                <p class="hidden sm:block text-xs text-slate-500 mt-1">
                    Sélection des produits, unités, détection d'écart de prix et gestion des cassures
                </p>
            </div>
            <x-ui.back-button href="{{ route('ventes.index') }}" label="Retour à la liste" />
        </div>
    </x-slot>

    <!-- Raw JSON data for Alpine component -->
    <script>
        window.saleProducts = @json($products);
    </script>

    <div class="max-w-4xl mx-auto"
         x-data="{
            submitting: false,
            customerId: '{{ old('customer_id') }}',
            saleDate: '{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}',
            paidAmount: {{ old('paid_amount', 0) }},
            notes: '{{ old('notes') }}',
            lines: [],
            products: window.saleProducts || [],

            init() {
                this.addLine();
            },

            addLine() {
                this.lines.push({
                    product_id: '',
                    stock_unit_id: '',
                    quantity: 1,
                    unit_price: 0,
                    default_unit_price: 0,
                    discount_reason: '',
                    source_stock_unit_id: '',
                    availableUnits: [],
                    availableSourceUnits: [],
                    subtotal: 0
                });
            },

            removeLine(index) {
                if (this.lines.length > 1) {
                    this.lines.splice(index, 1);
                }
            },

            onProductChange(index) {
                const line = this.lines[index];
                const product = this.products.find(p => p.id == line.product_id);

                if (product && product.active_units.length > 0) {
                    line.availableUnits = product.active_units;
                    const baseUnit = product.active_units.find(u => u.is_base_unit) || product.active_units[0];
                    line.stock_unit_id = baseUnit.id;
                    this.onUnitChange(index);
                } else {
                    line.availableUnits = [];
                    line.stock_unit_id = '';
                    line.unit_price = 0;
                    line.default_unit_price = 0;
                    line.source_stock_unit_id = '';
                }
            },

            onUnitChange(index) {
                const line = this.lines[index];
                const unit = line.availableUnits.find(u => u.id == line.stock_unit_id);
                const product = this.products.find(p => p.id == line.product_id);

                if (unit) {
                    line.default_unit_price = parseFloat(unit.default_selling_price);
                    line.unit_price = line.default_unit_price;
                    line.source_stock_unit_id = '';

                    if (product) {
                        line.availableSourceUnits = product.active_units.filter(u => !u.is_base_unit && u.current_stock >= 1);
                    }
                }
                this.calculateLine(index);
            },

            calculateLine(index) {
                const line = this.lines[index];
                const qty = parseFloat(line.quantity) || 0;
                const price = parseFloat(line.unit_price) || 0;
                line.subtotal = qty * price;
            },

            hasDiscount(line) {
                return Math.abs(parseFloat(line.unit_price || 0) - parseFloat(line.default_unit_price || 0)) > 0.01;
            },

            needsCassure(line) {
                if (!line.stock_unit_id) return false;
                const unit = line.availableUnits.find(u => u.id == line.stock_unit_id);
                if (!unit) return false;
                return parseFloat(line.quantity || 0) > parseFloat(unit.current_stock || 0);
            },

            get grandTotal() {
                return this.lines.reduce((sum, line) => sum + (parseFloat(line.subtotal) || 0), 0);
            }
         }">

        <form method="POST" action="{{ route('ventes.store') }}" @submit="submitting = true" class="space-y-6">
            @csrf

            <!-- Sale Header Card -->
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 mb-4">Informations Générales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Client (Optionnel)</label>
                        <select name="customer_id" x-model="customerId" class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">Client de passage (Anonyme)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'Sans tél' }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Date et Heure *</label>
                        <input type="datetime-local" name="sale_date" x-model="saleDate" required
                               class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                        @error('sale_date') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Sale Lines Card -->
            <x-ui.card class="p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Produits à Vendre</h3>
                    <x-ui.button type="button" @click="addLine()" variant="outline" size="sm" icon="plus" class="w-full sm:w-auto">
                        Ajouter une ligne
                    </x-ui.button>
                </div>

                <div class="space-y-4">
                    <template x-for="(line, index) in lines" :key="index">
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3 relative">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-700" x-text="'Ligne #' + (index + 1)"></span>
                                <button type="button" @click="removeLine(index)" x-show="lines.length > 1" class="text-red-500 hover:text-red-700 text-xs font-semibold flex items-center gap-1">
                                    <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                    <span>Supprimer</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <!-- Product selection -->
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Produit *</label>
                                    <select :name="'lines[' + index + '][product_id]'"
                                            x-model="line.product_id"
                                            @change="onProductChange(index)" required
                                            class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                                        <option value="">-- Sélectionner un produit --</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Unit selection -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Unité *</label>
                                    <select :name="'lines[' + index + '][stock_unit_id]'"
                                            x-model="line.stock_unit_id"
                                            @change="onUnitChange(index)" required
                                            class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                                        <option value="">-- Unité --</option>
                                        <template x-for="u in line.availableUnits" :key="u.id">
                                            <option :value="u.id" x-text="u.name + ' (Stock: ' + u.current_stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Quantité *</label>
                                    <input type="number" step="0.01" min="0.01"
                                           :name="'lines[' + index + '][quantity]'"
                                           x-model="line.quantity"
                                           @input="calculateLine(index)" required
                                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                                </div>
                            </div>

                            <!-- Prices & Discounts -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Prix Unitaire Facturé (FCFA) *</label>
                                    <input type="number" step="1" min="0"
                                           :name="'lines[' + index + '][unit_price]'"
                                           x-model="line.unit_price"
                                           @input="calculateLine(index)" required
                                           class="w-full rounded-xl border text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white"
                                           :class="hasDiscount(line) ? 'border-amber-400 bg-amber-50' : 'border-slate-200'">
                                    <div x-show="line.default_unit_price > 0" class="text-[10px] text-slate-400 mt-1">
                                        Prix normal : <span x-text="line.default_unit_price"></span> FCFA
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sous-total Ligne</label>
                                    <div class="py-2.5 px-3.5 rounded-xl bg-slate-100 text-xs sm:text-sm font-bold text-slate-900">
                                        <span x-text="line.subtotal.toLocaleString('fr-FR')"></span> FCFA
                                    </div>
                                </div>

                                <!-- Mandatory Discount Reason if price deviates -->
                                <div class="sm:col-span-1" x-show="hasDiscount(line)">
                                    <label class="block text-[11px] font-semibold text-amber-800 mb-1">Motif de l'écart / remise *</label>
                                    <input type="text"
                                           :name="'lines[' + index + '][discount_reason]'"
                                           x-model="line.discount_reason"
                                           placeholder="ex: Remise fidélité, Prix de gros..."
                                           :required="hasDiscount(line)"
                                           class="w-full rounded-xl border border-amber-300 bg-amber-50 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-amber-500">
                                </div>
                            </div>

                            <!-- Cassure / Source Unit Selector if stock is insufficient -->
                            <div x-show="needsCassure(line)" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-2">
                                <div class="flex items-center gap-1.5 font-bold">
                                    <x-heroicon-o-arrows-right-left class="w-4 h-4 text-amber-600" />
                                    <span>Cassure de stock nécessaire (Stock en vrac insuffisant)</span>
                                </div>
                                <p class="text-[11px] text-amber-700">
                                    Le stock direct pour cette unité est inférieur à la quantité demandée. Sélectionnez le carton/boîte source à ouvrir.
                                </p>
                                <div>
                                    <label class="block text-[11px] font-semibold text-amber-900 mb-1">Unité source à ouvrir *</label>
                                    <select :name="'lines[' + index + '][source_stock_unit_id]'"
                                            x-model="line.source_stock_unit_id"
                                            :required="needsCassure(line)"
                                            class="w-full rounded-xl border border-amber-300 bg-white text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Choisir le carton source --</option>
                                        <template x-for="su in line.availableSourceUnits" :key="su.id">
                                            <option :value="su.id" x-text="su.name + ' (Équiv: ' + su.base_unit_equivalent + ' base, Stock actuel: ' + su.current_stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </x-ui.card>

            <!-- Summary & Payment Card -->
            <x-ui.card class="p-4 sm:p-6 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">Règlement et Finalisation</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Général de la Vente</label>
                        <div class="py-2.5 px-4 rounded-xl bg-slate-900 text-emerald-400 font-extrabold text-base sm:text-lg">
                            <span x-text="grandTotal.toLocaleString('fr-FR')"></span> FCFA
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Montant Payé immédiatement *</label>
                        <input type="number" step="1" min="0" name="paid_amount" x-model="paidAmount" required
                               class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                        <div class="text-[10px] text-slate-500 mt-1">
                            Laissez à 0 si vente entièrement à crédit.
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Reste à Payer (Créance)</label>
                        <div class="py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold"
                             :class="Math.max(0, grandTotal - paidAmount) > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200'">
                            <span x-text="Math.max(0, grandTotal - paidAmount).toLocaleString('fr-FR')"></span> FCFA
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Remarques</label>
                    <textarea name="notes" x-model="notes" rows="2"
                              class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white"
                              placeholder="Notes internes éventuelles sur cette vente..."></textarea>
                </div>

                <div class="pt-4 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 border-t border-slate-100 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                    <x-ui.button href="{{ route('ventes.index') }}" variant="outline">
                        Annuler
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Valider la Vente & Éditer la Facture
                    </x-ui.button>
                </div>
            </x-ui.card>
        </form>
    </div>
</x-layouts.app>

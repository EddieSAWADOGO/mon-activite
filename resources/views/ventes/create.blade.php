<x-layouts.app title="Enregistrer une Vente">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('ventes.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Nouvelle Vente</span>
                </h2>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Sélection des produits, unités, détection d'écart de prix et gestion des cassures
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Raw JSON data for Alpine component -->
    <script>
        window.saleProducts = @json($products);
    </script>

    <div class="max-w-6xl mx-auto"
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

        <form method="POST" action="{{ route('ventes.store') }}"
              @submit="if(parseFloat(paidAmount || 0) > parseFloat(grandTotal || 0)) { $event.preventDefault(); alert('Le montant payé immédiatement ne peut pas dépasser le total général de la vente (' + grandTotal.toLocaleString('fr-FR') + ' FCFA).'); return false; }; submitting = true"
              class="space-y-6">
            @csrf

            <!-- Sale Header Card -->
            <x-ui.card class="p-4 sm:p-6">
                <h3 class="font-bold text-slate-900 text-sm sm:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <x-heroicon-o-user class="w-5 h-5 text-emerald-600" />
                    <span>Informations Générales de la Vente</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Client (Optionnel)</label>
                        <x-ui.select-searchable
                            name="customer_id"
                            :options="$customers"
                            :value="old('customer_id', '')"
                            placeholder="Commencer à taper le nom ou tél du client..."
                            emptyLabel="Client de passage (Anonyme)"
                            model="customerId"
                        />
                        @error('customer_id') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Date et Heure <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="sale_date" x-model="saleDate" required
                               class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500 bg-white">
                        @error('sale_date') <p class="text-red-600 text-[11px] mt-1 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Sale Lines Card -->
            <x-ui.card class="p-4 sm:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                        <x-heroicon-o-cube class="w-5 h-5 text-emerald-600 shrink-0" />
                        <span>Produits à Vendre</span>
                    </h3>
                    <x-ui.button type="button" @click="addLine()" variant="outline" size="sm" icon="plus" class="w-full sm:w-auto justify-center">
                        Ajouter une ligne
                    </x-ui.button>
                </div>

                <div class="space-y-4 sm:space-y-5">
                    <template x-for="(line, index) in lines" :key="index">
                        <div class="p-4 sm:p-5 lg:p-6 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-4 relative shadow-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                                <span class="text-xs sm:text-sm font-extrabold text-emerald-800 uppercase tracking-wide flex items-center gap-1.5" x-text="'Ligne #' + (index + 1)"></span>
                                <button type="button" @click="removeLine(index)" x-show="lines.length > 1" class="text-red-600 hover:text-red-800 text-xs sm:text-sm font-bold flex items-center gap-1.5 p-1 rounded-lg hover:bg-red-50 transition">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                    <span>Supprimer</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-4">
                                <!-- Product selection -->
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Produit <span class="text-red-500">*</span></label>
                                    <x-ui.select-searchable
                                        name="'lines[' + index + '][product_id]'"
                                        options="products"
                                        placeholder="Commencer à taper le nom du produit..."
                                        emptyLabel="-- Sélectionner un produit --"
                                        :required="true"
                                        model="line.product_id"
                                        onChange="onProductChange(index)"
                                    />
                                </div>

                                <!-- Unit selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Unité <span class="text-red-500">*</span></label>
                                    <select :name="'lines[' + index + '][stock_unit_id]'"
                                            x-model="line.stock_unit_id"
                                            @change="onUnitChange(index)" required
                                            class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                                        <option value="">-- Unité --</option>
                                        <template x-for="u in line.availableUnits" :key="u.id">
                                            <option :value="u.id" x-text="u.name + ' (Stock: ' + u.current_stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Quantité <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" min="0.01"
                                           :name="'lines[' + index + '][quantity]'"
                                           x-model="line.quantity"
                                           @input="calculateLine(index)" required
                                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                                </div>
                            </div>

                            <!-- Prices & Discounts -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 pt-1">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Prix Unitaire Facturé (FCFA) <span class="text-red-500">*</span></label>
                                    <input type="number" step="1" min="0"
                                           :name="'lines[' + index + '][unit_price]'"
                                           x-model="line.unit_price"
                                           @input="calculateLine(index)" required
                                           class="w-full rounded-xl border text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white transition font-bold"
                                           :class="hasDiscount(line) ? 'border-amber-400 bg-amber-50 text-amber-900 ring-2 ring-amber-400/30' : 'border-slate-200'">
                                    <div x-show="line.unit_price > 0" class="text-xs font-bold text-emerald-700 mt-1">
                                        = <span x-text="formatNumberFR(line.unit_price)"></span> FCFA
                                    </div>
                                    <div x-show="line.default_unit_price > 0" class="text-xs text-slate-500 mt-0.5 font-medium">
                                        Prix normal : <span x-text="formatNumberFR(line.default_unit_price)"></span> FCFA
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sous-total Ligne</label>
                                    <div class="py-2.5 px-3.5 sm:py-3 sm:px-4 rounded-xl bg-slate-100 text-xs sm:text-sm font-extrabold text-slate-900 border border-slate-200">
                                        <span x-text="line.subtotal.toLocaleString('fr-FR')"></span> FCFA
                                    </div>
                                </div>
                            </div>

                            <!-- Mandatory Discount Reason if price deviates (Spacious Box) -->
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/90 border-2 border-amber-300 text-xs sm:text-sm text-amber-950 space-y-2.5 mt-2 w-full"
                                 x-show="hasDiscount(line)" x-transition>
                                <div class="flex items-center gap-2 font-bold text-amber-900">
                                    <x-heroicon-o-information-circle class="w-5 h-5 text-amber-600 shrink-0" />
                                    <span>Remise ou modification de prix détectée</span>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-amber-950">
                                        Motif de l'écart / remise <span class="text-red-500">*</span>
                                    </label>
                                    <!-- Options rapides en 1 clic -->
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <button type="button" @click="line.discount_reason = 'Prix de gros'"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                                :class="line.discount_reason === 'Prix de gros' ? 'bg-amber-600 text-white border-amber-700' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-100'">
                                            Prix de gros
                                        </button>
                                        <button type="button" @click="line.discount_reason = 'Remise commerciale'"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                                :class="line.discount_reason === 'Remise commerciale' ? 'bg-amber-600 text-white border-amber-700' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-100'">
                                            Remise commerciale
                                        </button>
                                        <button type="button" @click="line.discount_reason = 'Achat en quantité'"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                                :class="line.discount_reason === 'Achat en quantité' ? 'bg-amber-600 text-white border-amber-700' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-100'">
                                            Achat en quantité
                                        </button>
                                        <button type="button" @click="line.discount_reason = 'Client fidèle'"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                                :class="line.discount_reason === 'Client fidèle' ? 'bg-amber-600 text-white border-amber-700' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-100'">
                                            Client fidèle
                                        </button>
                                        <button type="button" @click="line.discount_reason = 'Promotion'"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                                :class="line.discount_reason === 'Promotion' ? 'bg-amber-600 text-white border-amber-700' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-100'">
                                            Promotion
                                        </button>
                                    </div>
                                    <input type="text"
                                           :name="'lines[' + index + '][discount_reason]'"
                                           x-model="line.discount_reason"
                                           list="discount-preset-reasons"
                                           placeholder="Sélectionner une option ci-dessus ou saisir..."
                                           :required="hasDiscount(line)"
                                           class="w-full rounded-xl border border-amber-300 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-amber-500 font-medium">
                                    <datalist id="discount-preset-reasons">
                                        <option value="Prix de gros"></option>
                                        <option value="Remise commerciale"></option>
                                        <option value="Achat en quantité"></option>
                                        <option value="Client fidèle"></option>
                                        <option value="Promotion"></option>
                                        <option value="Déstockage"></option>
                                    </datalist>
                                </div>
                            </div>

                            <!-- Cassure / Source Unit Selector if stock is insufficient -->
                            <div x-show="needsCassure(line)" x-transition class="p-4 sm:p-5 rounded-2xl bg-amber-50/90 border-2 border-amber-300 text-xs sm:text-sm text-amber-950 space-y-3 mt-3 w-full">
                                <div class="flex items-center gap-2 font-bold text-amber-900">
                                    <x-heroicon-o-arrows-right-left class="w-5 h-5 text-amber-600 shrink-0" />
                                    <span>Cassure de stock nécessaire (Stock en vrac insuffisant)</span>
                                </div>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Le stock direct pour cette unité est inférieur à la quantité demandée. Sélectionnez le carton/boîte source à ouvrir.
                                </p>
                                <div>
                                    <label class="block text-xs font-semibold text-amber-950 mb-1.5">Unité source à ouvrir <span class="text-red-500">*</span></label>
                                    <select :name="'lines[' + index + '][source_stock_unit_id]'"
                                            x-model="line.source_stock_unit_id"
                                            :required="needsCassure(line)"
                                            class="w-full rounded-xl border border-amber-300 bg-white text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-amber-500 font-medium">
                                        <option value="">-- Choisir le carton/conditionnement source --</option>
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
            <x-ui.card class="space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">Règlement et Finalisation</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Général de la Vente</label>
                        <div class="py-2.5 px-4 rounded-xl bg-slate-900 text-emerald-400 font-extrabold text-base sm:text-lg">
                            <span x-text="grandTotal.toLocaleString('fr-FR')"></span> FCFA
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Montant Payé immédiatement <span class="text-red-500">*</span></label>
                        <input type="number" step="1" min="0" :max="grandTotal" name="paid_amount" x-model.number="paidAmount" required
                               class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                        <div x-show="paidAmount > 0" class="text-xs font-bold text-emerald-700 mt-1">
                            = <span x-text="formatNumberFR(paidAmount)"></span> FCFA
                        </div>
                        <p x-show="parseFloat(paidAmount || 0) > parseFloat(grandTotal || 0)" class="mt-1 text-xs text-red-600 font-semibold flex items-center gap-1">
                            <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> Le montant payé ne peut pas dépasser le total de la vente.
                        </p>
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

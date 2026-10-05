<x-layouts.app title="Enregistrer un Achat">
    <x-slot name="header">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('achats.index') }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span class="truncate">Nouveau Bon d'Achat</span>
                </h2>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                    Enregistrez une entrée de stock multi-produits auprès d'un fournisseur
                </p>
            </div>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-700">
            <p class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('achats.store') }}"
          x-data="purchaseForm({{ Js::from($products) }})"
          @submit="if(parseFloat(paidAmount || 0) > parseFloat(totalAmount || 0)) { $event.preventDefault(); alert('Le montant payé ne peut pas dépasser le montant total de l\'achat (' + formatNumber(totalAmount) + ' FCFA).'); return false; }; submitting = true"
          class="space-y-6">
        @csrf

        <!-- General Info Card -->
        <x-ui.card class="p-4 sm:p-6 lg:p-7">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <x-heroicon-o-truck class="w-5 h-5 text-emerald-600" />
                <span>Informations Générales</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Fournisseur <span class="text-red-500">*</span></label>
                    <x-ui.select-searchable
                        name="supplier_id"
                        :options="$suppliers"
                        :value="old('supplier_id', '')"
                        placeholder="Commencer à taper le nom du fournisseur..."
                        emptyLabel="-- Sélectionner un fournisseur --"
                        :required="true"
                    />
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Date de l'achat <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>

                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Notes / Référence commande</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Ex: Livré selon bon de livraison N° 123"
                           class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 sm:px-4 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
            </div>
        </x-ui.card>

        <!-- Lines Card -->
        <x-ui.card class="p-4 sm:p-6 lg:p-7">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span>Lignes de Produits Achetés</span>
                </h3>

                <x-ui.button type="button" @click="addLine()" variant="secondary" size="sm" icon="plus" class="w-full sm:w-auto justify-center">
                    Ajouter une ligne
                </x-ui.button>
            </div>

            <div class="space-y-4 sm:space-y-5">
                <template x-for="(line, index) in lines" :key="index">
                    <div class="p-4 sm:p-5 lg:p-6 bg-slate-50/70 border border-slate-200/90 rounded-2xl space-y-4 relative shadow-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wide" x-text="'Ligne #' + (index + 1)"></span>
                            <button type="button" @click="removeLine(index)" x-show="lines.length > 1" class="text-red-600 hover:text-red-800 p-1 flex items-center gap-1 text-xs sm:text-sm font-bold">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                <span>Supprimer</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- Product Select -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Produit <span class="text-red-500">*</span></label>
                                <x-ui.select-searchable
                                    name="'lines[' + index + '][product_id]'"
                                    options="availableProducts"
                                    placeholder="Commencer à taper le nom du produit..."
                                    emptyLabel="-- Sélectionner un produit --"
                                    :required="true"
                                    model="line.product_id"
                                    onChange="onProductChange(index)"
                                />
                            </div>

                            <!-- Unit Select -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Format / Unité <span class="text-red-500">*</span></label>
                                <select :name="'lines['+index+'][stock_unit_id]'" x-model="line.stock_unit_id" required
                                        class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white">
                                    <option value="">-- Unité --</option>
                                    <template x-for="u in getUnitsForProduct(line.product_id)" :key="u.id">
                                        <option :value="u.id" x-text="u.name + (u.is_base_unit ? ' (Base)' : '')"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Quantité <span class="text-red-500">*</span></label>
                                <input type="number" step="0.0001" min="0.0001" :name="'lines['+index+'][quantity]'" x-model.number="line.quantity" required
                                       class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Prix Unitaire D'Achat (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" min="0" :name="'lines['+index+'][unit_price]'" x-model.number="line.unit_price" required
                                       class="w-full rounded-xl border border-slate-200 text-xs sm:text-sm py-2.5 px-3.5 sm:py-3 focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                                <div x-show="line.unit_price > 0" class="text-xs font-bold text-emerald-700 mt-1">
                                    = <span x-text="formatNumber(line.unit_price)"></span> FCFA
                                </div>
                            </div>
                        </div>

                        <div class="text-right text-xs sm:text-sm text-slate-600 pt-1 font-medium">
                            Sous-total ligne : <span class="font-extrabold text-slate-900" x-text="formatNumber(lineSubtotal(line)) + ' FCFA'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </x-ui.card>

        <!-- Payment & Total Summary Card -->
        <x-ui.card class="bg-slate-900 text-white">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                <div>
                    <span class="block text-xs text-slate-400">Montant Total de l'Achat</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-emerald-400" x-text="formatNumber(totalAmount) + ' FCFA'"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Montant Payé immédiatement (FCFA) <span class="text-red-400">*</span></label>
                    <input type="number" min="0" :max="totalAmount" name="paid_amount" x-model.number="paidAmount" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs sm:text-sm p-2.5 focus:ring-2 focus:ring-emerald-500 font-bold">
                    <div x-show="paidAmount > 0" class="text-xs font-bold text-emerald-400 mt-1">
                        = <span x-text="formatNumber(paidAmount)"></span> FCFA
                    </div>
                    <p x-show="parseFloat(paidAmount || 0) > parseFloat(totalAmount || 0)" class="mt-1 text-xs text-red-400 font-semibold flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> Le montant payé ne peut pas dépasser le total de l'achat.
                    </p>
                </div>

                <div>
                    <span class="block text-xs text-slate-400">Reste à Payer (Dette Fournisseur)</span>
                    <span class="text-lg sm:text-xl font-bold" :class="remainingAmount > 0 ? 'text-red-400' : 'text-slate-300'"
                          x-text="formatNumber(remainingAmount) + ' FCFA'"></span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                <x-ui.button href="{{ route('achats.index') }}" variant="secondary" size="md">
                    Annuler
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle" ::loading="submitting">
                    Enregistrer et Mettre à Jour les Stocks
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>

    <script>
        function purchaseForm(products) {
            return {
                submitting: false,
                availableProducts: products,
                lines: [
                    { product_id: '', stock_unit_id: '', quantity: 1, unit_price: 0 }
                ],
                paidAmount: 0,
                autoSetPaid: true,

                addLine() {
                    this.lines.push({ product_id: '', stock_unit_id: '', quantity: 1, unit_price: 0 });
                },

                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    }
                },

                getUnitsForProduct(productId) {
                    if (!productId) return [];
                    const prod = this.availableProducts.find(p => p.id == productId);
                    return prod ? (prod.active_units || []) : [];
                },

                onProductChange(index) {
                    const productId = this.lines[index].product_id;
                    const units = this.getUnitsForProduct(productId);
                    if (units.length > 0) {
                        this.lines[index].stock_unit_id = units[0].id;
                    } else {
                        this.lines[index].stock_unit_id = '';
                    }
                },

                lineSubtotal(line) {
                    const q = parseFloat(line.quantity) || 0;
                    const p = parseInt(line.unit_price) || 0;
                    return Math.round(q * p);
                },

                get totalAmount() {
                    const total = this.lines.reduce((sum, line) => sum + this.lineSubtotal(line), 0);
                    if (this.autoSetPaid) {
                        this.paidAmount = total;
                    }
                    return total;
                },

                get remainingAmount() {
                    return Math.max(0, this.totalAmount - (parseInt(this.paidAmount) || 0));
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('fr-FR').format(num || 0);
                }
            };
        }
    </script>
</x-layouts.app>

<x-layouts.app title="Enregistrer un Achat">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-emerald-600" />
                    <span>Nouveau Bon d'Achat</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Enregistrez une entrée de stock multi-produits auprès d'un fournisseur
                </p>
            </div>
            <x-ui.button href="{{ route('achats.index') }}" variant="secondary" size="sm">
                Retour
            </x-ui.button>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
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
          class="space-y-6">
        @csrf

        <!-- General Info Card -->
        <x-ui.card class="p-4 sm:p-6">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <x-heroicon-o-truck class="w-5 h-5 text-emerald-600" />
                <span>Informations Générales</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
                    <select name="supplier_id" required class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">-- Sélectionner un fournisseur --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date de l'achat <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>

                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Référence commande</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Ex: Livré selon bon de livraison N° 123"
                           class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
            </div>
        </x-ui.card>

        <!-- Lines Card -->
        <x-ui.card class="p-4 sm:p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-emerald-600" />
                    <span>Lignes de Produits Achetés</span>
                </h3>

                <x-ui.button type="button" @click="addLine()" variant="secondary" size="sm" icon="plus">
                    Ajouter une ligne
                </x-ui.button>
            </div>

            <div class="space-y-4">
                <template x-for="(line, index) in lines" :key="index">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3 relative">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700" x-text="'Ligne #' + (index + 1)"></span>
                            <button type="button" @click="removeLine(index)" x-show="lines.length > 1" class="text-red-500 hover:text-red-700 p-1">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Product Select -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Produit <span class="text-red-500">*</span></label>
                                <select :name="'lines['+index+'][product_id]'" x-model="line.product_id" @change="onProductChange(index)" required
                                        class="w-full rounded-lg border border-slate-300 text-xs p-2 focus:ring-2 focus:ring-emerald-500 bg-white">
                                    <option value="">-- Produit --</option>
                                    <template x-for="p in availableProducts" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Unit Select -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Format / Unité <span class="text-red-500">*</span></label>
                                <select :name="'lines['+index+'][stock_unit_id]'" x-model="line.stock_unit_id" required
                                        class="w-full rounded-lg border border-slate-300 text-xs p-2 focus:ring-2 focus:ring-emerald-500 bg-white">
                                    <option value="">-- Unité --</option>
                                    <template x-for="u in getUnitsForProduct(line.product_id)" :key="u.id">
                                        <option :value="u.id" x-text="u.name + (u.is_base_unit ? ' (Base)' : '')"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Quantité <span class="text-red-500">*</span></label>
                                <input type="number" step="0.0001" min="0.0001" :name="'lines['+index+'][quantity]'" x-model.number="line.quantity" required
                                       class="w-full rounded-lg border border-slate-300 text-xs p-2 focus:ring-2 focus:ring-emerald-500 bg-white">
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Prix Unitaire D'Achat (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" min="0" :name="'lines['+index+'][unit_price]'" x-model.number="line.unit_price" required
                                       class="w-full rounded-lg border border-slate-300 text-xs p-2 focus:ring-2 focus:ring-emerald-500 bg-white">
                            </div>
                        </div>

                        <div class="text-right text-xs text-slate-600 pt-1">
                            Sous-total ligne : <span class="font-bold text-slate-900" x-text="formatNumber(lineSubtotal(line)) + ' FCFA'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </x-ui.card>

        <!-- Payment & Total Summary Card -->
        <x-ui.card class="p-4 sm:p-6 bg-slate-900 text-white">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                <div>
                    <span class="block text-xs text-slate-400">Montant Total de l'Achat</span>
                    <span class="text-2xl font-extrabold text-emerald-400" x-text="formatNumber(totalAmount) + ' FCFA'"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Montant Payé immédiatement (FCFA) <span class="text-red-400">*</span></label>
                    <input type="number" min="0" name="paid_amount" x-model.number="paidAmount" required
                           class="w-full rounded-lg border border-slate-700 bg-slate-800 text-white text-sm p-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <span class="block text-xs text-slate-400">Reste à Payer (Dette Fournisseur)</span>
                    <span class="text-xl font-bold" :class="remainingAmount > 0 ? 'text-red-400' : 'text-slate-300'"
                          x-text="formatNumber(remainingAmount) + ' FCFA'"></span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex justify-end gap-3">
                <x-ui.button href="{{ route('achats.index') }}" variant="secondary" size="md">
                    Annuler
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle">
                    Enregistrer et Mettre à Jour les Stocks
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>

    <script>
        function purchaseForm(products) {
            return {
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

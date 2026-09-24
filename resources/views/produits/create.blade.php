<x-layouts.app>
    <x-slot:title>Nouveau produit</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('produits.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition-colors">
                <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouveau produit</h1>
                <p class="text-sm text-slate-500 mt-0.5">Définir les informations du produit, son unité de base et ses unités déclinaisons.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('produits.store') }}" x-data="{
            units: {{ json_encode(old('additional_units', [])) }},
            addUnit() {
                this.units.push({
                    name: '',
                    base_unit_equivalent: 1,
                    default_selling_price: 0,
                    low_stock_threshold: 0,
                    initial_stock: 0
                });
            },
            removeUnit(index) {
                this.units.splice(index, 1);
            }
        }" class="space-y-6">
            @csrf

            <!-- Informations Générales -->
            <x-ui.card title="1. Informations générales" subtitle="Identification du produit dans le catalogue.">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Nom du produit -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="ex: Pesticide Delta, Maïs Jaune, Huile Végétale..."
                               class="w-full py-2.5 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  placeholder="Notes ou spécifications sur le produit..."
                                  class="w-full py-2 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Unité de Base -->
            <x-ui.card title="2. Unité de base (Unité indivisible)" subtitle="C'est la plus petite unité de comptage pour ce produit (ex: Bidon, Kilogramme, Bouteille, Pièce).">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nom de l'unité de base -->
                    <div>
                        <label for="base_unit_name" class="block text-sm font-medium text-slate-700 mb-1">
                            Nom de l'unité <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="base_unit_name"
                               id="base_unit_name"
                               value="{{ old('base_unit_name') }}"
                               required
                               placeholder="ex: Bidon, KG, Pièce..."
                               class="w-full py-2 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('base_unit_name') border-red-500 @enderror">
                        @error('base_unit_name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix de vente par défaut -->
                    <div>
                        <label for="base_unit_price" class="block text-sm font-medium text-slate-700 mb-1">
                            Prix de vente (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               step="1"
                               min="0"
                               name="base_unit_price"
                               id="base_unit_price"
                               value="{{ old('base_unit_price', 0) }}"
                               required
                               class="w-full py-2 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('base_unit_price') border-red-500 @enderror">
                        @error('base_unit_price')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seuil d'alerte stock bas -->
                    <div>
                        <label for="base_unit_low_stock_threshold" class="block text-sm font-medium text-slate-700 mb-1">
                            Seuil alerte stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="base_unit_low_stock_threshold"
                               id="base_unit_low_stock_threshold"
                               value="{{ old('base_unit_low_stock_threshold', 10) }}"
                               required
                               class="w-full py-2 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('base_unit_low_stock_threshold') border-red-500 @enderror">
                        @error('base_unit_low_stock_threshold')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock initial -->
                    <div>
                        <label for="base_unit_initial_stock" class="block text-sm font-medium text-slate-700 mb-1">
                            Stock initial (vrac)
                        </label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="base_unit_initial_stock"
                               id="base_unit_initial_stock"
                               value="{{ old('base_unit_initial_stock', 0) }}"
                               class="w-full py-2 px-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500 @error('base_unit_initial_stock') border-red-500 @enderror">
                        @error('base_unit_initial_stock')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Unités Additionnelles Déclarées -->
            <x-ui.card>
                <x-slot:title>
                    <div class="flex items-center justify-between w-full">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">3. Autres unités déclarées (Optionnel)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Formats de conditionnement pour l'achat/vente (ex: Carton de 12, Sac de 25kg, Paquet de 6).</p>
                        </div>

                        <x-ui.button type="button" variant="outline" size="sm" icon="plus" @click="addUnit()">
                            Ajouter une unité
                        </x-ui.button>
                    </div>
                </x-slot:title>

                <div class="space-y-4">
                    <template x-if="units.length === 0">
                        <div class="text-center py-6 border border-dashed border-slate-200 rounded-lg text-xs text-slate-500">
                            Aucune unité additionnelle ajoutée. Vous pouvez vendre directement dans l'unité de base ou ajouter des formats groupés ci-dessus.
                        </div>
                    </template>

                    <template x-for="(unit, index) in units" :key="index">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3 relative">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="text-xs font-bold text-slate-700 uppercase" x-text="'Unité #' + (index + 1)"></span>
                                <button type="button" @click="removeUnit(index)" class="text-red-600 hover:text-red-800 text-xs font-medium inline-flex items-center gap-1">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                    <span>Supprimer</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                <!-- Nom -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Nom du format *</label>
                                    <input type="text"
                                           :name="'additional_units[' + index + '][name]'"
                                           x-model="unit.name"
                                           required
                                           placeholder="ex: Carton de 12"
                                           class="w-full py-1.5 px-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>

                                <!-- Équivalence unité de base -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Équivalence (en base) *</label>
                                    <input type="number"
                                           step="0.0001"
                                           min="0.0001"
                                           :name="'additional_units[' + index + '][base_unit_equivalent]'"
                                           x-model="unit.base_unit_equivalent"
                                           required
                                           class="w-full py-1.5 px-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>

                                <!-- Prix de vente -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Prix de vente (FCFA) *</label>
                                    <input type="number"
                                           step="1"
                                           min="0"
                                           :name="'additional_units[' + index + '][default_selling_price]'"
                                           x-model="unit.default_selling_price"
                                           required
                                           class="w-full py-1.5 px-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>

                                <!-- Seuil alerte -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Seuil alerte *</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :name="'additional_units[' + index + '][low_stock_threshold]'"
                                           x-model="unit.low_stock_threshold"
                                           required
                                           class="w-full py-1.5 px-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>

                                <!-- Stock initial -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Stock initial (unités)</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :name="'additional_units[' + index + '][initial_stock]'"
                                           x-model="unit.initial_stock"
                                           class="w-full py-1.5 px-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </x-ui.card>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('produits.index') }}">
                    <x-ui.button type="button" variant="secondary">
                        Annuler
                    </x-ui.button>
                </a>

                <x-ui.button type="submit" variant="primary" icon="check-circle">
                    Enregistrer le produit
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>

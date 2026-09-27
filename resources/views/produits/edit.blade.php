<x-layouts.app>
    <x-slot:title>Modifier le produit - {{ $product->name }}</x-slot:title>

    <div class="max-w-6xl mx-auto space-y-5">
        <!-- Page Header -->
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200/60">
            <x-ui.back-button href="{{ route('produits.show', $product) }}" label="Retour" />
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">Modifier : {{ $product->name }}</h1>
                <p class="hidden sm:block text-xs text-slate-500 mt-0.5">Mettre à jour les informations, prix, seuils d'alerte et ajouter de nouvelles unités.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('produits.update', $product) }}"
              x-data="{
                submitting: false,
                newUnits: {{ json_encode(old('new_units', [])) }},
                addNewUnit() {
                    this.newUnits.push({
                        name: '',
                        base_unit_equivalent: 1,
                        default_selling_price: 0,
                        low_stock_threshold: 0,
                        initial_stock: 0
                    });
                },
                removeNewUnit(index) {
                    this.newUnits.splice(index, 1);
                }
              }"
              @submit="submitting = true"
              class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations Générales -->
            <x-ui.card title="1. Informations générales" subtitle="Nom, description et statut d'activité du produit." class="p-4 sm:p-6 lg:p-7">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <!-- Nom du produit -->
                    <div class="sm:col-span-8">
                        <label for="name" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $product->name) }}"
                               required
                               class="w-full py-2.5 px-3.5 sm:py-3 sm:px-4 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Statut du produit -->
                    <div class="sm:col-span-4">
                        <label for="is_active" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Statut catalogue <span class="text-red-500">*</span>
                        </label>
                        <select name="is_active" id="is_active" class="w-full py-2.5 px-3.5 sm:py-3 sm:px-4 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white transition">
                            <option value="1" @selected(old('is_active', $product->is_active) == 1)>Actif (Proposé aux ventes)</option>
                            <option value="0" @selected(old('is_active', $product->is_active) == 0)>Inactif (Masqué des ventes)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-12">
                        <label for="description" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="w-full py-2.5 px-3.5 sm:py-3 sm:px-4 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Unités Existantes -->
            <x-ui.card title="2. Unités déclarées existantes" subtitle="L'équivalence en unité de base est immuable. Seuls le nom, le prix et le seuil sont modifiables.">
                <div class="space-y-4">
                    @foreach($product->units as $index => $unit)
                        <div class="p-3 sm:p-4 lg:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                            <input type="hidden" name="existing_units[{{ $index }}][id]" value="{{ $unit->id }}">

                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="text-xs font-bold text-slate-800 uppercase flex items-center gap-2">
                                    <span>{{ $unit->name }}</span>
                                    @if($unit->is_base_unit)
                                        <x-ui.badge color="sky">Unité de base</x-ui.badge>
                                    @endif
                                </span>

                                <span class="text-xs text-slate-500">
                                    Stock actuel: <strong class="text-slate-800">{{ number_format($unit->current_stock, 2, ',', ' ') }}</strong>
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- Nom de l'unité -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Nom de l'unité <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           name="existing_units[{{ $index }}][name]"
                                           value="{{ old("existing_units.{$index}.name", $unit->name) }}"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Équivalence unité de base (lecture seule) -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Équivalence (Immuable)</label>
                                    <input type="text"
                                           disabled
                                           value="{{ number_format($unit->base_unit_equivalent, 2, ',', ' ') }} {{ $product->baseUnit?->name }}"
                                           class="w-full py-2 px-3 border border-slate-200 bg-slate-100 text-slate-500 rounded-xl text-xs sm:text-sm cursor-not-allowed">
                                </div>

                                <!-- Prix de vente -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Prix de vente (FCFA) <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           step="1"
                                           min="0"
                                           name="existing_units[{{ $index }}][default_selling_price]"
                                           value="{{ old("existing_units.{$index}.default_selling_price", $unit->default_selling_price) }}"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Seuil alerte stock bas -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Seuil alerte stock <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="existing_units[{{ $index }}][low_stock_threshold]"
                                           value="{{ old("existing_units.{$index}.low_stock_threshold", $unit->low_stock_threshold) }}"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <!-- Nouvelles Unités Déclarées -->
            <x-ui.card>
                <x-slot:title>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 w-full">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">3. Ajouter de nouvelles unités</h3>
                            <p class="hidden sm:block text-xs text-slate-500 mt-0.5">Si le conditionnement d'un produit a changé, créez un nouveau format d'unité ici.</p>
                        </div>

                        <x-ui.button type="button" variant="outline" size="sm" icon="plus" @click="addNewUnit()" class="w-full sm:w-auto">
                            Ajouter un format
                        </x-ui.button>
                    </div>
                </x-slot:title>

                <div class="space-y-4">
                    <template x-if="newUnits.length === 0">
                        <div class="text-center py-6 border border-dashed border-slate-200 rounded-2xl text-xs text-slate-500">
                            Aucun nouveau format ajouté. Cliquez sur "Ajouter un format" si un nouveau conditionnement est disponible.
                        </div>
                    </template>

                    <template x-for="(unit, index) in newUnits" :key="index">
                        <div class="p-3 sm:p-4 lg:p-5 bg-emerald-50/50 rounded-2xl border border-emerald-200 space-y-3 relative">
                            <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                                <span class="text-xs font-bold text-emerald-800 uppercase" x-text="'Nouveau format #' + (index + 1)"></span>
                                <button type="button" @click="removeNewUnit(index)" class="text-red-600 hover:text-red-800 text-xs font-medium inline-flex items-center gap-1">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                    <span>Supprimer</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                <!-- Nom -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Nom du format <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           :name="'new_units[' + index + '][name]'"
                                           x-model="unit.name"
                                           required
                                           placeholder="ex: Carton de 10"
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Équivalence unité de base -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Équivalence (en base) <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           step="0.0001"
                                           min="0.0001"
                                           :name="'new_units[' + index + '][base_unit_equivalent]'"
                                           x-model="unit.base_unit_equivalent"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Prix de vente -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Prix de vente (FCFA) <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           step="1"
                                           min="0"
                                           :name="'new_units[' + index + '][default_selling_price]'"
                                           x-model="unit.default_selling_price"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Seuil alerte -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Seuil alerte <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :name="'new_units[' + index + '][low_stock_threshold]'"
                                           x-model="unit.low_stock_threshold"
                                           required
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>

                                <!-- Stock initial -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Stock initial (unités)</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :name="'new_units[' + index + '][initial_stock]'"
                                           x-model="unit.initial_stock"
                                           class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </x-ui.card>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-200 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                <a href="{{ route('produits.show', $product) }}">
                    <x-ui.button type="button" variant="secondary">
                        Annuler
                    </x-ui.button>
                </a>

                <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                    Enregistrer les modifications
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>

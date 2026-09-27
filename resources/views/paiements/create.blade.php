<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
                Enregistrer un règlement
            </h1>
            <x-ui.back-button href="{{ route('factures.show', $invoice) }}" label="Retour à la facture" />
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Récapitulatif Facture -->
        <x-ui.card>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Facture</span>
                        <p class="text-lg font-bold text-slate-900">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <x-ui.badge :color="$invoice->status->badgeColor()">
                            {{ $invoice->status->label() }}
                        </x-ui.badge>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-slate-500 text-xs block">Client</span>
                        <span class="font-medium text-slate-900">
                            {{ $invoice->customer ? $invoice->customer->name : 'Client de passage' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs block">Montant Total</span>
                        <span class="font-semibold text-slate-900">
                            {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs block">Déjà Payé</span>
                        <span class="font-semibold text-emerald-600">
                            {{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-amber-900 flex items-center gap-1.5">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
                        Reste à régler
                    </span>
                    <span class="text-lg font-extrabold text-amber-700">
                        {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            </div>
        </x-ui.card>

        <!-- Formulaire de Règlement -->
        <x-ui.card>
            <form action="{{ route('paiements.store') }}" method="POST"
                  x-data="{ submitting: false }"
                  @submit="submitting = true"
                  class="space-y-5">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                <div>
                    <label for="amount" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Montant du règlement (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           step="1"
                           min="1"
                           max="{{ $invoice->remaining_amount }}"
                           value="{{ old('amount', $invoice->remaining_amount) }}"
                           required
                           class="w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-lg font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_date" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Date du règlement <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local"
                               name="payment_date"
                               id="payment_date"
                               value="{{ old('payment_date', now()->format('Y-m-d\TH:i')) }}"
                               required
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        @error('payment_date')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Mode de paiement <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method"
                                id="payment_method"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            <option value="Espèces" {{ old('payment_method') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                            <option value="Mobile Money" {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money (MTN / Moov / Celtiis)</option>
                            <option value="Virement Bancaire" {{ old('payment_method') == 'Virement Bancaire' ? 'selected' : '' }}>Virement Bancaire</option>
                            <option value="Chèque" {{ old('payment_method') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                            <option value="Autre" {{ old('payment_method') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="reference" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Référence / N° de Transaction (Optionnel)
                    </label>
                    <input type="text"
                           name="reference"
                           id="reference"
                           placeholder="Ex: TRX-987654 ou N° de chèque"
                           value="{{ old('reference') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @error('reference')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Note / Remarque (Optionnel)
                    </label>
                    <textarea name="notes"
                              id="notes"
                              rows="2"
                              placeholder="Commentaire éventuel..."
                              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" /> {{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 [&>a]:w-full [&>a]:sm:w-auto [&>button]:w-full [&>button]:sm:w-auto">
                    <a href="{{ route('factures.show', $invoice) }}">
                        <x-ui.button type="button" variant="outline">
                            Annuler
                        </x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary" icon="check-circle" ::loading="submitting">
                        Valider le règlement
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>

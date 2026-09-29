@props([
    'name' => null,
    'options' => [],
    'value' => '',
    'placeholder' => 'Commencer à taper pour rechercher...',
    'emptyLabel' => '-- Sélectionner --',
    'allowEmpty' => true,
    'required' => false,
    'id' => null,
    'class' => '',
    'model' => null,
    'onChange' => null
])

@php
    if (is_iterable($options)) {
        $formattedOptions = [];
        foreach ($options as $item) {
            if (is_array($item)) {
                $formattedOptions[] = [
                    'id' => (string)($item['id'] ?? ''),
                    'name' => (string)($item['name'] ?? $item['label'] ?? ''),
                    'phone' => (string)($item['phone'] ?? ''),
                    'company_name' => (string)($item['company_name'] ?? ''),
                    'subtext' => (string)($item['subtext'] ?? '')
                ];
            } elseif (is_object($item)) {
                $formattedOptions[] = [
                    'id' => (string)($item->id ?? ''),
                    'name' => (string)($item->name ?? $item->label ?? ''),
                    'phone' => (string)($item->phone ?? ''),
                    'company_name' => (string)($item->company_name ?? ''),
                    'subtext' => (string)($item->subtext ?? '')
                ];
            }
        }
        $optionsExpr = json_encode($formattedOptions);
    } else {
        $optionsExpr = (string)$options;
    }

    $inputClass = "w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-10 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium placeholder:text-slate-400";
    $isDynamicName = str_contains((string)$name, '+') || str_contains((string)$name, 'index') || str_contains((string)$name, 'line');
@endphp

<script>
    if (typeof window.searchableCombobox !== 'function') {
        window.searchableCombobox = function(config = {}) {
            return {
                isOpen: false,
                searchQuery: '',
                selectedId: config.value !== undefined ? String(config.value) : '',
                options: config.options || [],
                placeholder: config.placeholder || 'Commencer à taper pour rechercher...',
                emptyLabel: config.emptyLabel || '',
                allowEmpty: config.allowEmpty !== false,

                init() {
                    this.$nextTick(() => {
                        if (this.$refs.hiddenInput && this.$refs.hiddenInput.value !== undefined) {
                            this.selectedId = String(this.$refs.hiddenInput.value);
                        }
                    });
                },

                get displayLabel() {
                    const list = Array.isArray(this.options) ? this.options : [];
                    const found = list.find(o => String(o.id) === String(this.selectedId));
                    if (found) {
                        let txt = found.name || found.label || '';
                        if (found.phone) {
                            txt += ' (' + found.phone + ')';
                        }
                        return txt;
                    }
                    if (this.allowEmpty && this.emptyLabel && (this.selectedId === '' || this.selectedId === null)) {
                        return this.emptyLabel;
                    }
                    return '';
                },

                get filteredOptions() {
                    const list = Array.isArray(this.options) ? this.options : [];
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    if (!q) return list;

                    return list.filter(o => {
                        const searchStr = (
                            (o.name || o.label || '') + ' ' +
                            (o.phone || '') + ' ' +
                            (o.company_name || '') + ' ' +
                            (o.code || '') + ' ' +
                            (o.category_name || '') + ' ' +
                            (o.subtext || '')
                        ).toLowerCase();
                        return searchStr.includes(q);
                    });
                },

                select(option) {
                    const newId = option ? String(option.id) : '';
                    if (this.$refs.hiddenInput) {
                        this.$refs.hiddenInput.value = newId;
                    }
                    this.selectedId = newId;
                    this.searchQuery = '';
                    this.isOpen = false;
                    if (this.$refs.hiddenInput) {
                        this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                },

                clear() {
                    if (this.$refs.hiddenInput) {
                        this.$refs.hiddenInput.value = '';
                    }
                    this.selectedId = '';
                    this.searchQuery = '';
                    this.isOpen = false;
                    if (this.$refs.hiddenInput) {
                        this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                },

                onFocus() {
                    this.isOpen = true;
                    this.searchQuery = '';
                },

                onBlur() {
                    setTimeout(() => {
                        this.isOpen = false;
                        this.searchQuery = '';
                    }, 200);
                }
            };
        };
    }
</script>

<div class="relative w-full {{ $class }}"
     x-bind:class="isOpen ? 'z-50 relative' : 'relative z-10'"
     x-data="searchableCombobox({
        value: '{{ $value }}',
        options: {{ $optionsExpr }},
        placeholder: @js($placeholder),
        emptyLabel: @js($emptyLabel),
        allowEmpty: {{ $allowEmpty ? 'true' : 'false' }}
     })"
     x-effect="if ($refs.hiddenInput && $refs.hiddenInput.value !== undefined && selectedId !== String($refs.hiddenInput.value)) { selectedId = String($refs.hiddenInput.value); }"
     @click.outside="isOpen = false; searchQuery = ''">

    <!-- Hidden Input for Form Submission & Alpine x-model -->
    @if($isDynamicName)
        <input type="hidden"
               x-ref="hiddenInput"
               x-bind:name="{{ $name }}"
               @if($model) x-model="{{ $model }}" @endif
               @if($onChange) @change="{{ $onChange }}" @endif
               @if($required) required @endif
               @if($id) id="{{ $id }}" @endif
               value="{{ $value }}">
    @else
        <input type="hidden"
               x-ref="hiddenInput"
               @if($name) name="{{ $name }}" @endif
               @if($model) x-model="{{ $model }}" @endif
               @if($onChange) @change="{{ $onChange }}" @endif
               @if($required) required @endif
               @if($id) id="{{ $id }}" @endif
               value="{{ $value }}">
    @endif

    <!-- Display / Search Input -->
    <div class="relative flex items-center">
        <!-- Search Icon -->
        <div class="absolute left-3 pointer-events-none text-slate-400 z-10">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400" />
        </div>

        <!-- Input Field -->
        <input type="text"
               x-ref="searchInput"
               x-bind:value="isOpen ? searchQuery : displayLabel"
               @focus="onFocus()"
               @input="searchQuery = $event.target.value; isOpen = true"
               @keydown.escape="isOpen = false; searchQuery = ''"
               @keydown.tab="isOpen = false; searchQuery = ''"
               x-bind:placeholder="isOpen ? 'Tapez pour filtrer...' : placeholder"
               class="{{ $inputClass }}"
               autocomplete="off">

        <!-- Action Icons (Clear 'X' & Chevron) -->
        <div class="absolute right-3 flex items-center gap-1 z-10">
            <button type="button"
                    x-show="selectedId !== '' && selectedId !== null && allowEmpty"
                    @click.stop="clear()"
                    class="text-slate-400 hover:text-red-500 p-0.5 rounded-md hover:bg-slate-100 transition"
                    title="Effacer la sélection">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </button>
            <button type="button"
                    @click.stop="isOpen = !isOpen; if(isOpen) { searchQuery = ''; $nextTick(() => $refs.searchInput.focus()); }"
                    class="text-slate-400 hover:text-slate-600 p-0.5 rounded-md hover:bg-slate-100 transition">
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform duration-200" x-bind:class="isOpen ? 'rotate-180 text-emerald-600' : ''" />
            </button>
        </div>
    </div>

    <!-- Dropdown Options Popup -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="absolute left-0 right-0 top-full mt-1.5 z-[100] bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden max-h-64 overflow-y-auto divide-y divide-slate-100 p-1"
         style="display: none; -webkit-overflow-scrolling: touch;">

        <!-- Empty / Anonyme Option -->
        <template x-if="allowEmpty && emptyLabel">
            <div @mousedown.prevent="select({ id: '', name: emptyLabel })"
                 @click="select({ id: '', name: emptyLabel })"
                 class="p-2.5 sm:p-3 hover:bg-slate-100 text-xs sm:text-sm font-semibold cursor-pointer rounded-xl flex items-center justify-between text-slate-600 transition"
                 x-bind:class="selectedId === '' || selectedId === null ? 'bg-slate-100/80 text-slate-900 font-bold' : ''">
                <span x-text="emptyLabel"></span>
                <x-heroicon-o-check x-show="selectedId === '' || selectedId === null" class="w-4 h-4 text-slate-600 shrink-0" />
            </div>
        </template>

        <!-- Filtered Options List -->
        <template x-for="opt in filteredOptions" :key="opt.id">
            <template x-if="opt.id !== '' && opt.id !== null">
                <div @mousedown.prevent="select(opt)"
                     @click="select(opt)"
                     class="p-2.5 sm:p-3 hover:bg-emerald-50 text-xs sm:text-sm font-medium cursor-pointer rounded-xl flex items-center justify-between text-slate-800 transition group"
                     x-bind:class="String(selectedId) === String(opt.id) ? 'bg-emerald-50/90 text-emerald-950 font-bold border-l-4 border-emerald-600 pl-2' : ''">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="truncate text-xs sm:text-sm font-semibold" x-text="opt.name || opt.label"></div>
                        <template x-if="opt.phone || opt.company_name || opt.subtext">
                            <div class="text-2xs text-slate-500 group-hover:text-emerald-700 font-normal truncate mt-0.5"
                                 x-text="(opt.company_name ? opt.company_name + ' • ' : '') + (opt.phone || opt.subtext || '')"></div>
                        </template>
                    </div>
                    <x-heroicon-o-check x-show="String(selectedId) === String(opt.id)" class="w-4 h-4 text-emerald-600 shrink-0" />
                </div>
            </template>
        </template>

        <!-- No Results Message -->
        <template x-if="filteredOptions.length === 0 || (filteredOptions.length === 1 && filteredOptions[0].id === '')">
            <div class="p-4 text-center text-xs text-slate-500 font-medium space-y-1">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 mx-auto text-slate-300" />
                <p>Aucun résultat correspondant à "<span x-text="searchQuery" class="font-bold text-slate-700"></span>"</p>
            </div>
        </template>
    </div>
</div>

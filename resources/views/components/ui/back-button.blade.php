@props([
    'href',
    'label' => 'Retour',
])

<a href="{{ $href }}"
   title="{{ $label }}"
   {{ $attributes->merge(['class' => 'group inline-flex items-center gap-1.5 px-3 py-1.5 text-xs sm:text-sm font-medium text-slate-600 bg-white hover:bg-slate-100 hover:text-slate-900 border border-slate-200/90 rounded-xl transition-all duration-150 active:scale-95 shadow-2xs shrink-0 select-none cursor-pointer']) }}>
    <x-heroicon-o-arrow-left class="w-4 h-4 text-slate-500 transition-transform group-hover:-translate-x-0.5" />
    <span>{{ $label }}</span>
</a>

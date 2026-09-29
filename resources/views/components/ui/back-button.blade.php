@props([
    'href' => null,
    'label' => 'Retour',
])

@php
    $fallbackHref = $href ?? url()->previous();
@endphp

<a href="{{ $fallbackHref }}"
   x-data
   @click="
       const ref = document.referrer;
       if (
           ref &&
           window.history.length > 1 &&
           ref.startsWith(window.location.origin) &&
           ref !== window.location.href &&
           !ref.includes('/login') &&
           !ref.includes('/logout') &&
           !(ref.includes('/create') && !window.location.pathname.includes('/create')) &&
           !(ref.includes('/edit') && !window.location.pathname.includes('/edit'))
       ) {
           $event.preventDefault();
           window.history.back();
       }
   "
   title="{{ $label }}"
   {{ $attributes->merge(['class' => 'group inline-flex items-center gap-1.5 px-3 py-1.5 text-xs sm:text-sm font-medium text-slate-600 bg-white hover:bg-slate-100 hover:text-slate-900 border border-slate-200/90 rounded-xl transition-all duration-150 active:scale-95 shadow-2xs shrink-0 select-none cursor-pointer']) }}>
    <x-heroicon-o-arrow-left class="w-4 h-4 text-slate-500 transition-transform group-hover:-translate-x-0.5" />
    <span>{{ $label }}</span>
</a>


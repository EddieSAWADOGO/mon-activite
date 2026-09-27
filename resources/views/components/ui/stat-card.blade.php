@props([
    'title' => '',
    'value' => '0',
    'icon' => 'squares-2x2',
    'iconColor' => 'emerald',
    'subtitle' => null,
])

@php
    $bgIconClasses = match ($iconColor) {
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'red' => 'bg-red-50 text-red-600 border-red-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
        'sky', 'blue' => 'bg-sky-50 text-sky-600 border-sky-100',
        'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
        default => 'bg-slate-50 text-slate-600 border-slate-100',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 p-3.5 sm:p-5 flex items-start justify-between gap-2.5 sm:gap-4 transition-all shadow-xs min-w-0']) }}>
    <div class="space-y-0.5 sm:space-y-1 min-w-0 flex-1">
        <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 line-clamp-1">{{ $title }}</p>
        <p class="text-sm sm:text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug break-words">{{ $value }}</p>
        @if ($subtitle)
            <p class="text-xs text-slate-500 font-medium truncate">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="p-2 sm:p-3 rounded-xl sm:rounded-2xl border shrink-0 {{ $bgIconClasses }}">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 sm:w-6 sm:h-6" />
    </div>
</div>

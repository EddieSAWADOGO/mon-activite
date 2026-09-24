@props([
    'title' => '',
    'value' => '0',
    'icon' => 'squares-2x2',
    'iconColor' => 'emerald',
    'subtitle' => null,
])

@php
    $bgIconClasses = match ($iconColor) {
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'red' => 'bg-red-50 text-red-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'sky' => 'bg-sky-50 text-sky-600',
        'purple' => 'bg-purple-50 text-purple-600',
        default => 'bg-slate-50 text-slate-600',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm flex items-start justify-between gap-4']) }}>
    <div>
        <p class="text-xs sm:text-sm font-medium text-slate-500">{{ $title }}</p>
        <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">{{ $value }}</p>
        @if ($subtitle)
            <p class="text-xs text-slate-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="p-2.5 sm:p-3 rounded-lg flex-shrink-0 {{ $bgIconClasses }}">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6 sm:w-7 sm:h-7" />
    </div>
</div>

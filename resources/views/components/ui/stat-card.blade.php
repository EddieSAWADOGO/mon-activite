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

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-start justify-between gap-4']) }}>
    <div class="space-y-1">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $title }}</p>
        <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</p>
        @if ($subtitle)
            <p class="text-xs text-slate-500 font-medium">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="p-3 rounded-2xl border flex-shrink-0 {{ $bgIconClasses }}">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6" />
    </div>
</div>


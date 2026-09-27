@props([
    'color' => 'slate',
    'icon' => null,
])

@php
    $colorClasses = match ($color) {
        'emerald', 'green' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'amber', 'yellow' => 'bg-amber-100 text-amber-800 border-amber-200',
        'sky', 'blue' => 'bg-sky-100 text-sky-800 border-sky-200',
        'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
        'slate', 'gray' => 'bg-slate-100 text-slate-800 border-slate-200',
        default => 'bg-slate-100 text-slate-800 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs sm:text-sm font-bold border {$colorClasses} shrink-0 whitespace-nowrap"]) }}>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 flex-shrink-0" />
    @endif
    <span>{{ $slot }}</span>
</span>

@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation min-h-[44px] min-w-[44px] cursor-pointer';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs gap-1.5 min-h-[38px]',
        'md' => 'px-4 py-2.5 text-sm gap-2 min-h-[44px]',
        'lg' => 'px-5 py-3 text-base gap-2.5 min-h-[48px]',
        default => 'px-4 py-2.5 text-sm gap-2 min-h-[44px]',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-emerald-600 hover:bg-emerald-500 text-white focus:ring-emerald-500/30 shadow-sm shadow-emerald-600/20 active:scale-[0.99]',
        'secondary' => 'bg-slate-900 hover:bg-slate-800 text-white focus:ring-slate-700 shadow-sm active:scale-[0.99]',
        'light' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 focus:ring-slate-400 active:scale-[0.99]',
        'danger' => 'bg-red-600 hover:bg-red-500 text-white focus:ring-red-500/30 shadow-sm shadow-red-600/20 active:scale-[0.99]',
        'outline' => 'border border-slate-300/80 hover:bg-slate-50 text-slate-700 focus:ring-emerald-500/30 bg-white active:scale-[0.99]',
        default => 'bg-emerald-600 hover:bg-emerald-500 text-white focus:ring-emerald-500/30 shadow-sm active:scale-[0.99]',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}
    @if ($disabled) disabled @endif
>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 flex-shrink-0" />
    @endif

    <span>{{ $slot }}</span>
</button>


@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
    'disabled' => false,
    'href' => null,
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed touch-manipulation cursor-pointer shrink-0 select-none min-h-[42px]';

    $sizeClasses = match ($size) {
        'xs' => 'px-2 py-1 text-xs gap-1 min-h-[32px]',
        'sm' => 'px-3 py-1.5 text-xs sm:text-sm gap-1.5 min-h-[36px]',
        'md' => 'px-4 py-2.5 text-xs sm:text-sm gap-2 min-h-[42px]',
        'lg' => 'px-5 py-3 text-sm sm:text-base gap-2.5 min-h-[48px]',
        'icon' => 'p-2 sm:p-2.5 text-xs sm:text-sm min-h-[38px] min-w-[38px]',
        default => 'px-4 py-2.5 text-xs sm:text-sm gap-2 min-h-[42px]',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs hover:shadow focus:ring-emerald-500/30 active:scale-[0.98]',
        'secondary' => 'bg-slate-900 hover:bg-slate-800 text-white shadow-xs hover:shadow focus:ring-slate-700 active:scale-[0.98]',
        'light' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 focus:ring-slate-300 active:scale-[0.98]',
        'danger' => 'bg-red-600 hover:bg-red-500 text-white shadow-xs hover:shadow focus:ring-red-500/30 active:scale-[0.98]',
        'outline' => 'border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 focus:ring-emerald-500/30 bg-white active:scale-[0.98]',
        'ghost' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:ring-slate-300 active:scale-[0.98]',
        default => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs hover:shadow focus:ring-emerald-500/30 active:scale-[0.98]',
    };

    $iconSizes = match ($size) {
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        default => 'w-4 h-4',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}>
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSizes }} flex-shrink-0" />
        @endif
        @if (trim($slot))
            <span>{{ $slot }}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }} @if ($disabled || $loading) disabled @endif>
        @if ($loading)
            <svg class="animate-spin {{ $iconSizes }} flex-shrink-0 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSizes }} flex-shrink-0" />
        @endif
        @if (trim($slot))
            <span>{{ $slot }}</span>
        @endif
    </button>
@endif

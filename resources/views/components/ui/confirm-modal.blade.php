@props([
    'name' => 'confirmModal',
    'title' => 'Confirmation requise',
    'message' => 'Êtes-vous sûr de vouloir effectuer cette action ?',
    'confirmText' => 'Confirmer',
    'cancelText' => 'Annuler',
    'variant' => 'danger',
    'icon' => 'exclamation-triangle',
])

@php
    $iconBgClasses = match($variant) {
        'danger' => 'bg-red-100 text-red-600',
        'primary' => 'bg-emerald-100 text-emerald-600',
        'warning' => 'bg-amber-100 text-amber-600',
        default => 'bg-emerald-100 text-emerald-600',
    };
@endphp

<div x-cloak
     x-show="{{ $name }}"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="{{ $name }} = false"
     class="fixed inset-0 z-[80] flex items-center justify-center p-4 sm:p-6 bg-slate-950/60 backdrop-blur-xs select-none overflow-y-auto">

    <div x-show="{{ $name }}"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="{{ $name }} = false"
         class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-5 sm:p-7 shadow-2xl space-y-4 border border-slate-100 text-center relative my-auto mx-auto transform">

        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl {{ $iconBgClasses }} flex items-center justify-center mx-auto shrink-0 shadow-xs">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6 sm:w-7 sm:h-7" />
        </div>

        <div class="space-y-1">
            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug tracking-tight break-words">{{ $title }}</h3>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed break-words">{{ $message }}</p>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-center gap-2.5 pt-2 text-center w-full">
            <button type="button"
                    @click="{{ $name }} = false"
                    class="w-full sm:flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition cursor-pointer active:scale-98 text-center">
                {{ $cancelText }}
            </button>

            <div class="w-full sm:flex-1 [&>form]:w-full [&>button]:w-full flex items-center justify-center">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

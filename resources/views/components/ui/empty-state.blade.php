@props([
    'title' => 'Aucune donnée',
    'description' => 'Aucun élément à afficher pour le moment.',
    'icon' => 'archive-box',
])

<div {{ $attributes->merge(['class' => 'text-center py-8 px-4 sm:py-12 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50']) }}>
    <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6" />
    </div>

    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
    <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mt-1">{{ $description }}</p>

    @if ($slot->isNotEmpty())
        <div class="mt-4 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>

@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 transition-all overflow-hidden min-w-0']) }}>
    @if ($title || $subtitle || $actions)
        <div class="px-4 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 bg-slate-50/50">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="text-sm sm:text-lg font-extrabold text-slate-900 tracking-tight truncate">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="hidden sm:block text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($actions)
                <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0 w-full sm:w-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-3.5 sm:p-5 lg:p-6 min-w-0">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-4 py-3 sm:px-6 sm:py-3 bg-slate-50/80 border-t border-slate-100 min-w-0">
            {{ $footer }}
        </div>
    @endif
</div>

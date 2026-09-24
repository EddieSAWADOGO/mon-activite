@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden']) }}>
    @if ($title || $subtitle || $actions)
        <div class="px-4 py-4 sm:px-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($actions)
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-4 py-3 sm:px-6 bg-slate-50 border-t border-slate-200">
            {{ $footer }}
        </div>
    @endif
</div>

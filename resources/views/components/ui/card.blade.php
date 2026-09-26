@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden']) }}>
    @if ($title || $subtitle || $actions)
        <div class="px-5 py-4 sm:px-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/50">
            <div>
                @if ($title)
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($actions)
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-5 sm:p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 py-3 sm:px-6 bg-slate-50/80 border-t border-slate-100">
            {{ $footer }}
        </div>
    @endif
</div>


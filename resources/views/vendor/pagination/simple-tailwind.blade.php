@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl cursor-default leading-5 select-none opacity-50">
                    &larr; Retour
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-emerald-600 transition leading-5 active:scale-95">
                    &larr; Retour
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-emerald-600 transition leading-5 active:scale-95">
                    Suivant &rarr;
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl cursor-default leading-5 select-none opacity-50">
                    Suivant &rarr;
                </span>
            @endif
        </div>
    </nav>
@endif

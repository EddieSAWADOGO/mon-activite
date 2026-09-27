@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigation" class="flex items-center justify-between w-full">
        <!-- Mobile view -->
        <div class="flex justify-between items-center flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl cursor-default leading-5 select-none opacity-50">
                    &larr; Retour
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-emerald-600 transition leading-5 active:scale-95">
                    &larr; Retour
                </a>
            @endif

            <span class="text-xs font-bold text-slate-600">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-emerald-600 transition leading-5 active:scale-95">
                    Suivant &rarr;
                </a>
            @else
                <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl cursor-default leading-5 select-none opacity-50">
                    Suivant &rarr;
                </span>
            @endif
        </div>

        <!-- Desktop view -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-slate-500 font-medium">
                    Affichage de
                    <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
                    à
                    <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
                    sur
                    <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
                    résultats
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-xs rounded-xl overflow-hidden border border-slate-200 bg-white">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Retour">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-400 bg-white border-r border-slate-200 cursor-default leading-5 opacity-50" aria-hidden="true">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Retour
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border-r border-slate-200 hover:bg-slate-50 hover:text-emerald-600 transition leading-5" aria-label="Retour">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Retour
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-400 bg-white border-r border-slate-200 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-white bg-emerald-600 border-r border-emerald-600 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border-r border-slate-200 hover:bg-slate-50 hover:text-emerald-600 transition leading-5" aria-label="Page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-emerald-600 transition leading-5" aria-label="Suivant">
                            Suivant
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Suivant">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-400 bg-white cursor-default leading-5 opacity-50" aria-hidden="true">
                                Suivant
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif

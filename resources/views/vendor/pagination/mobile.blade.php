@if ($paginator->hasPages())
    <div class="flex items-center justify-between gap-4 px-2 py-4 select-none">
        @if ($paginator->onFirstPage())
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-300 pointer-events-none opacity-50 shadow-sm">
                <span class="material-symbols-outlined text-2xl">chevron_right</span>
            </div>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-primary shadow-sm active:scale-90 transition-transform relative overflow-hidden group">
               <div class="absolute inset-0 bg-primary/5 opacity-0 group-active:opacity-100 transition-opacity"></div>
                <span class="material-symbols-outlined text-2xl">chevron_right</span>
            </a>
        @endif

        <div class="flex items-center gap-1.5 flex-1 justify-center overflow-x-auto scrollbar-hide no-scrollbar py-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-2 h-2 rounded-full bg-slate-200 mx-1"></span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <div class="min-w-[44px] h-[44px] flex items-center justify-center rounded-2xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/30 font-headline transform scale-110">
                                {{ $page }}
                            </div>
                        @else
                            @if ($page == 1 || $page == $paginator->lastPage() || abs($page - $paginator->currentPage()) <= 1)
                                <a href="{{ $url }}" 
                                   class="min-w-[40px] h-[40px] flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-500 font-medium text-sm hover:border-primary/30 transition-all active:scale-95 font-headline">
                                    {{ $page }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-primary shadow-sm active:scale-90 transition-transform relative overflow-hidden group">
               <div class="absolute inset-0 bg-primary/5 opacity-0 group-active:opacity-100 transition-opacity"></div>
                <span class="material-symbols-outlined text-2xl">chevron_left</span>
            </a>
        @else
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-300 pointer-events-none opacity-50 shadow-sm">
                <span class="material-symbols-outlined text-2xl">chevron_left</span>
            </div>
        @endif
    </div>
@endif

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

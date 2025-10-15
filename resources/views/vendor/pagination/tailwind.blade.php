@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center mt-4 sm:mt-6 overflow-x-auto">
        <div class="flex flex-wrap gap-1 sm:gap-2 items-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-primary text-white hover:text-primary hover:bg-white rounded-lg hover:bg-primary/90 hover:text-primary border-2 border-primary transition-all duration-300 ease-out dark:hover:bg-gray-800">
                    Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
                $showEllipsisStart = $start > 1;
                $showEllipsisEnd = $end < $paginator->lastPage();
            @endphp

            {{-- First Page --}}
            @if ($showEllipsisStart)
                <a href="{{ $paginator->url(1) }}" 
                   class="hidden sm:inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-primary/20 transition dark:hover:bg-gray-800 dark:text-gray-300">
                    1
                </a>
                <span class="hidden sm:inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm text-gray-500">...</span>
            @endif

            {{-- Page Links --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <span class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-primary text-white border-2 border-primary rounded-lg transition-all duration-300 ease-out">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" 
                       class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-white border-2 border-primary hover:bg-primary hover:text-white text-gray-700 rounded-lg hover:bg-primary/20 dark:bg-gray-800 dark:text-gray-300 duration-300 ease-in-out transition-all">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Last Page and Ellipsis --}}
            @if ($showEllipsisEnd)
                <span class="hidden sm:inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm text-gray-500">...</span>
                <a href="{{ $paginator->url($paginator->lastPage()) }}" 
                   class="hidden sm:inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-primary/20 transition dark:hover:bg-gray-800 dark:text-gray-300">
                    {{ $paginator->lastPage() }}
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm bg-primary hover:text-primary hover:bg-white text-white rounded-lg hover:bg-primary/90 hover:text-primary border-2 border-primary transition-all duration-300 ease-in-out dark:hover:bg-gray-800">
                    Next
                </a>
            @else
                <span class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
    </nav>
@endif
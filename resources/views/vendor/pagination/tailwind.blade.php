@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-2 mt-6">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all hover:bg-white hover:text-primary border-2 border-primary duration-300 ease-out">
                Prev
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500"> {{ $element }} </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 bg-primary text-white hover:text-primary hover:bg-white border-2 border-primary duration-300 transition-all ease-out cursor-pointer rounded-lg"> {{ $page }} </span>
                    @else
                        <a href="{{ $url }}" 
                           class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-primary-20 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-primary/90 hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 ease-out">
                Next
            </a>
        @else
            <span class="px-3 py-1.5 text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                Next
            </span>
        @endif
    </nav>
@endif

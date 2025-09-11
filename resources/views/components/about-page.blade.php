<div>
    <!-- No surplus words or unnecessary actions. - Marcus Aurelius -->
    <a href="{{ route('about') }}" class="relative group top-0">
        <img src="{{ asset('icons/information.png') }}" alt="About MDC PathFinder"
            class="w-8 h-8 sm:w-10 sm:h-10 hover:scale-115 transition-transform duration-300">
        <!-- Tooltip (desktop only) -->
        <span
            class="hidden lg:flex absolute right-full mr-3 px-3 py-1.5 rounded-md 
                 bg-gray-800 text-white text-sm shadow-md opacity-0 group-hover:opacity-100 
                 transition-opacity duration-300 whitespace-nowrap">
            About {{ config('app.name') }}
        </span>
    </a>
</div>

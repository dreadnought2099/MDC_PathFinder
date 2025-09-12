<!-- resources/views/components/about-page.blade.php -->
<div class="relative inline-block group">
    <!-- No surplus words or unnecessary actions. - Marcus Aurelius -->
    <a href="{{ route('about') }}" class="block">
        <img src="{{ asset('icons/information.png') }}" alt="About MDC PathFinder"
            class="w-8 h-8 sm:w-10 sm:h-10 hover:scale-115 transition-transform duration-300">
    </a>

    <!-- Tooltip -->
    <div
        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none">
        About {{ config('app.name') }}
        <!-- Arrow -->
        <div
            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                    border-t-4 border-t-transparent 
                    border-b-4 border-b-transparent">
        </div>
    </div>
</div>
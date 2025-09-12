<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->

    @props([
        'href' => '#',
        'icon' => null,
        'alt' => 'Icon',
        'title' => null,
    ])

    @php
        // Get the current route name to determine the context
        $currentRoute = Route::currentRouteName();

        // Build the scan URL with return parameter based on current route
        $scanUrl = route('scan.index');

        if (in_array($currentRoute, ['paths.select', 'paths.results'])) {
            $scanUrl = route('scan.index', ['return' => $currentRoute]);
        }
    @endphp

    <div class="fixed bottom-6 right-6 flex flex-col items-end space-y-2 z-50">
        <div class="relative inline-block group">
            <a href="{{ $scanUrl }}"
                class="inline-flex items-center shadow-primary-hover justify-center 
              p-4 sm:p-3 lg:p-2 xl:p-4 rounded-full bg-white dark:bg-gray-800 
              shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out 
              border-2 border-primary hover:border-primary">
                <!-- Icon -->
                <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                    class="w-12 h-12 sm:w-10 sm:h-10 lg:w-8 lg:h-8 xl:w-7 xl:h-7 
                    group-hover:scale-110 transition-all duration-300 ease-in-out 
                    filter group-hover:brightness-110">
            </a>

            <!-- Pure CSS Tooltip -->
            <div
                class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                        text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                        group-hover:opacity-100 group-hover:visible transition-all duration-300 
                        whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                Scan Office QR
                <!-- Arrow -->
                <div
                    class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                            border-l-4 border-l-gray-900 dark:border-l-gray-700
                            border-t-4 border-t-transparent 
                            border-b-4 border-b-transparent">
                </div>
            </div>
        </div>
    </div>
</div>

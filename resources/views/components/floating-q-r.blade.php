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

    <div
        class="fixed 
                bottom-3 right-3 
                sm:bottom-4 sm:right-4 
                md:bottom-5 md:right-5 
                lg:bottom-6 lg:right-6 
                flex flex-col items-end space-y-2 z-50">
        <div class="relative inline-block group">
            <a href="{{ $scanUrl }}"
                class="inline-flex items-center justify-center 
                      shadow-primary-hover transition-all duration-300 ease-in-out
                      bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl
                      border-2 border-primary hover:border-primary rounded-full
                      hover:scale-105 active:scale-95
                      
                      p-2 w-10 h-10
                      sm:p-3 sm:w-12 sm:h-12
                      md:p-3.5 md:w-14 md:h-14
                      lg:p-4 lg:w-16 lg:h-16
                      xl:p-4 xl:w-16 xl:h-16">

                <!-- RESPONSIVE: Icon with Tailwind sizing -->
                <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                    class="transition-all duration-300 ease-in-out 
                          group-hover:scale-110 group-hover:brightness-110
                          w-5 h-5
                          sm:w-7 sm:h-7
                          md:w-8 md:h-8
                          lg:w-10 lg:h-10
                          xl:w-10 xl:h-10">
            </a>

            <!-- RESPONSIVE: Tooltip using Tailwind -->
            <div
                class="absolute right-full mr-2 sm:mr-3 top-1/2 -translate-y-1/2 
                        px-2 py-1 sm:px-3 sm:py-2 
                        text-xs sm:text-sm font-medium text-white 
                        bg-gray-900 dark:bg-gray-700 rounded-md sm:rounded-lg 
                        shadow-sm opacity-0 invisible
                        group-hover:opacity-100 group-hover:visible 
                        transition-all duration-300 whitespace-nowrap 
                        pointer-events-none
                        hidden sm:block">

                Scan Office QR

                <!-- Tooltip arrow using Tailwind -->
                <div class="absolute left-full top-1/2 -translate-y-1/2">
                    <div
                        class="w-0 h-0 
                              border-l-4 border-l-gray-900 dark:border-l-gray-700
                              border-t-4 border-t-transparent 
                              border-b-4 border-b-transparent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
        <a href="{{ $scanUrl }}"
            class="group inline-flex items-center shadow-primary-hover justify-center 
          p-4 sm:p-3 lg:p-2 xl:p-4 rounded-full bg-white dark:bg-gray-800 
          shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out 
          border-2 border-primary hover:border-primary relative">
            <!-- Icon -->
            <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                class="w-12 h-12 sm:w-10 sm:h-10 lg:w-8 lg:h-8 xl:w-7 xl:h-7 
                group-hover:scale-110 transition-all duration-300 ease-in-out 
                filter group-hover:brightness-110">

            <!-- Tooltip (desktop only) -->
            <span
                class="hidden lg:flex absolute right-full mr-3 px-3 py-1.5 rounded-md 
                 bg-gray-800 text-white text-sm shadow-md opacity-0 group-hover:opacity-100 
                 transition-opacity duration-300 whitespace-nowrap">
                Scan QR
            </span>
        </a>
    </div>
</div>

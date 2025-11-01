@php
    // Default fallback
    $backUrl = route('index');
    $backText = 'Home';

    $currentRoute = Route::currentRouteName();
    $returnParam = request('return');

    // Define back button logic based on current route
    switch ($currentRoute) {
        case 'scan.room':
            $backUrl = route('scan.index');
            $backText = 'Scanner';
            if ($returnParam) {
                $backUrl = route('scan.index', ['return' => $returnParam]);
            }
            break;

        case 'staff.client-show':
            // Check if came from search
            if ($returnParam === 'search') {
                $backUrl = route('search');
                $backText = 'Search';
            } else {
                $backUrl = route('index');
                $backText = 'Home';
            }
            break;

        case 'paths.results':
            $backUrl = route('paths.select');
            $backText = 'Path Selection';
            break;

        case 'paths.select':
        case 'scan.index':
        case 'search':
            $backUrl = route('index');
            $backText = 'Home';
            break;

        case 'about':
            $backUrl = route('index');
            $backText = 'Home';
            break;

        default:
            // If return param exists and is valid route
            if ($returnParam && Route::has($returnParam)) {
                try {
                    $backUrl = route($returnParam);
                    $backText = 'Back';
                } catch (\Exception $e) {
                    $backUrl = route('index');
                    $backText = 'Home';
                }
            }
    }

    // Allow override via props
    $backUrl = $url ?? $backUrl;
    $backText = $text ?? $backText;
@endphp

<a href="{{ $backUrl }}"
    {{ $attributes->merge(['class' => 'flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300 text-sm sm:text-base']) }}>
    <svg class="h-5 w-5 sm:h-6 sm:w-6 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    <span class="font-medium hidden sm:inline">{{ $backText }}</span>
    <span class="font-medium sm:hidden">Back</span>
</a>

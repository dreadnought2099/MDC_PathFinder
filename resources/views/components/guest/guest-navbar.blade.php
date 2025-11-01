@props(['showBackButton' => false])

<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 dark:border-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Left: Logo or Back Button --}}
            <div class="flex-shrink-0">
                @if ($showBackButton)
                    <x-guest.guest-back-button />
                @else
                    <a href="{{ route('index') }}" class="text-xl font-bold text-primary">
                        {{ config('app.name') }}
                    </a>
                @endif
            </div>

            {{-- Right: Navigation Items --}}
            <div class="flex items-center space-x-4">
                <x-dark-mode-toggle />
                <x-guest.about-page />
                <x-guest.team-modal />
            </div>
        </div>
    </div>
</nav>

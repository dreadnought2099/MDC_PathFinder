<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo/Brand --}}
            <div class="flex-shrink-0">
                <a href="{{ route('index') }}" class="text-xl font-semibold hover-underline text-primary dark:text-gray-300">
                    {{ config('app.name') }}
                </a>
            </div>

            {{-- Navigation Items --}}
            <div class="flex items-center space-x-4">
                {{-- Dark Mode Toggle Button --}}
                <x-dark-mode-toggle />

                {{-- About Button --}}
                <x-about-page />

                {{-- Team Button --}}
                <x-team-modal />
            </div>
        </div>
    </div>
</nav>
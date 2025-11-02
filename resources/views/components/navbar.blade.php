<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 h-16 flex justify-between items-center">
        <!-- Left side (back button) -->
        <div class="flex items-center space-x-4 flex-shrink-0" x-data="{ currentTab: sessionStorage.getItem('activeTab') || 'rooms' }" x-init="window.addEventListener('tab-changed', e => currentTab = e.detail.tab)">
            @unless (Route::is('admin.dashboard') || Route::is('landing.page'))
                <x-back-button fallback="room.index" landing="admin.dashboard" x-bind:tab="currentTab" />
            @endunless
        </div>

        <!-- Right side (actions) -->
        <div class="flex items-center space-x-4">
            <x-dark-mode-toggle />
            <div class="relative" x-data="{ open: false }" x-cloak>
                <button @click="open = !open"
                    class="flex items-center space-x-2 focus:outline-none cursor-pointer dark:text-gray-300">
                    <img x-ref="navbarProfile"
                        src="{{ Auth::user()->profile_photo_path ? Storage::url(Auth::user()->profile_photo_path) : asset('images/mdc.png') }}"
                        alt="Profile" class="h-8 w-8 rounded-full">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" x-transition @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border-2 border-primary rounded shadow-lg z-[60]">
                    <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-2 text-sm text-primary hover:bg-gray-100 dark:hover:bg-gray-700">
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-secondary hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
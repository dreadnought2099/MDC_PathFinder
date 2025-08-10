<nav class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            @unless (Route::is('admin.dashboard') || Route::is('landing.page'))
                <x-back-button fallback="room.index" landing="admin.dashboard" />
            @endunless

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative ml-auto" x-cloak>
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none cursor-pointer">
                    <img x-ref="navbarProfile"
                        src="{{ Auth::user()->profile_photo_path ? Storage::url(Auth::user()->profile_photo_path) : asset('images/profile.jpeg') }}"
                        alt="Profile" class="h-8 w-8 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white border border-[#157ee1] rounded shadow-lg z-50 left-auto">
                    <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-2 text-sm text-[#157ee1] hover:bg-gray-100">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-secondary hover:bg-gray-100 cursor-pointer">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

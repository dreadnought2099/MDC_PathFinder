<nav class="bg-white border-b shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Back Button -->
            <div>
                <button onclick="history.back()" class="flex items-center text-gray-700 hover:text-black">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="ml-1 text-sm">Back</span>
                </button>
            </div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative" x-cloak>
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <img src="{{ Auth::user()->profile_photo_url ?? asset('images/profile.jpeg') }}"
                        alt="Profile" class="h-8 w-8 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition 
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                    <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

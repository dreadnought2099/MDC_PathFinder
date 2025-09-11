<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
    <div x-data="{ open: false }" @click.away="open = false"
        class="fixed bottom-6 right-6 flex flex-col items-end space-y-2 z-50">
        <!-- Buttons -->
        <template x-if="open">
            <div class="flex flex-col space-y-2 mb-2">

                <a href="{{ route('room.create') }}" class="group flex items-center space-x-2">
                    <div class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                        <img src="{{ asset('icons/offices.png') }}" alt="Add Room/Office" />
                    </div>
                    <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">Add Office</span>
                </a>

                @php
                    $firstPath = \App\Models\Path::first();
                @endphp

                <a href="{{ $firstPath ? route('path-image.create', $firstPath) : '#' }}"
                    class="group flex items-center space-x-2 @if (!$firstPath) opacity-50 cursor-not-allowed @endif">
                    <div class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                        <img src="{{ asset('icons/image.png') }}" alt="Add Path Images" />
                    </div>
                    <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">
                        Add Path Images
                    </span>
                </a>

                <a href="{{ route('staff.create') }}" class="group flex items-center space-x-2">
                    <div class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                        <img src="{{ asset('icons/user.png') }}" alt="Add Staff Member" />
                    </div>
                    <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">Add Staff Member</span>
                </a>
            </div>
        </template>

        <!-- Floating + Button -->
        <button @click="open = !open"
            class="w-12 h-12 rounded-full bg-primary text-white text-4xl flex items-center justify-center shadow-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
            <span x-text="open ? '-' : '+'"></span>
            <span
                class="hidden lg:flex absolute right-full mr-3 px-3 py-1.5 rounded-md 
                 bg-gray-800 text-white text-sm shadow-md opacity-0 group-hover:opacity-100 
                 transition-opacity duration-300 whitespace-nowrap">
                Click to Collapse
            </span>
        </button>
    </div>
</div>

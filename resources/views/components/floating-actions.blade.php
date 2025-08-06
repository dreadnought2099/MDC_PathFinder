<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
     <div x-data="{ open: false }" @click.away="open = false"
            class="fixed bottom-6 right-6 flex flex-col items-end space-y-2">
            <!-- Buttons -->
            <template x-if="open">
                <div class="flex flex-col space-y-2 mb-2">

                    <a href="{{ route('room.create') }}" class="group flex items-center space-x-2">
                        <div class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="{{ asset('icons/offices.png')}}" alt="Add Room/Office" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline">Add Room/Office</span>
                    </a>
                    <a href="{{ route('staff.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="{{ asset('icons/user.png')}}" alt="Add Staff Member" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline">Add Staff Member</span>
                    </a>
                </div>
            </template>

            <!-- Floating + Button -->
            <button @click="open = !open"
                class="w-12 h-12 rounded-full bg-primary text-white text-4xl flex items-center justify-center shadow-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer">
                <span x-text="open ? '-' : '+'"></span>
            </button>
        </div>
</div>
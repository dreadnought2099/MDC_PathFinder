<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
    <div x-data="{ open: false }" @click.away="open = false"
        class="fixed bottom-6 right-6 flex flex-col items-end space-y-2 z-50">
        <!-- Buttons -->
        @if (auth()->user()->hasRole('Admin'))
            <template x-if="open">
                <div
                    class="bg-slate-300 rounded-md p-4 border border-primary dark:bg-gray-800 flex flex-col space-y-2 mb-2">
                    <a href="{{ route('room.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/offices.png"
                                alt="Add Room/Office" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">Add Office</span>
                    </a>

                    @php
                        $firstPath = \App\Models\Path::first();
                    @endphp

                    <a id="floatingPathImageLink"
                        href="{{ $firstPath ? route('path-image.create', $firstPath->id) : '#' }}"
                        class="group flex items-center space-x-2 @if (!$firstPath) opacity-50 cursor-not-allowed @endif"
                        @if ($firstPath) onclick="return updatePathLinkBeforeNavigate(event, 'floatingPathImageLink')" @endif>
                        <div
                            class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Add Path Images" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">
                            Add Path Images
                        </span>
                    </a>

                    <a href="{{ route('staff.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@cac2411/public/icons/staff.png"
                                alt="Add Staff Member" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">Add Staff Member</span>
                    </a>

                    {{-- Create User (Admin only) --}}
                    <a href="{{ route('room-user.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-12 h-12 hover:scale-120 transition-all duration-300">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@cac2411/public/icons/user.png"
                                alt="Add Staff Member" />
                        </div>
                        <span class="text-sm text-gray-700 hover-underline dark:text-gray-300">Add Office User</span>
                    </a>
                </div>
            </template>

            <!-- Floating + Button -->
            <div class="relative inline-block group">
                <button @click="open = !open"
                    class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-primary text-white text-4xl shadow-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    <span x-text="open ? '-' : '+'"></span>
                </button>

                <!-- Tooltip -->
                <div
                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                    Add
                    <!-- Arrow -->
                    <div
                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                    border-t-4 border-t-transparent 
                    border-b-4 border-b-transparent">
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

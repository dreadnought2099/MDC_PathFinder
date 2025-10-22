<form action="{{ route('room.assign.update') }}" method="POST" id="assignForm">
    @csrf
    @method('PUT')
    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
    <input type="hidden" name="page" value="{{ request('page', 1) }}">

    <div class="mb-8">
        <!-- Pagination -->
        @if ($staff->hasPages())
            <div class="flex justify-center mb-8">
                <div class="w-full max-w-lg">
                    {{ $staff->appends(['roomId' => $selectedRoom->id])->links('pagination::tailwind') }}
                </div>
            </div>
        @endif

        <!-- Staff Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-12">
            @forelse ($staff as $member)
                @php
                    $assignedRoomId = $member->room_id;
                    $isSelectedRoom = $assignedRoomId == $selectedRoom->id;
                    $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                @endphp

                <div
                    class="staff-card {{ $isAssignedOtherRoom ? 'opacity-50' : '' }} bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-2 {{ $isSelectedRoom ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700 hover:border-primary/50' }}">
                    <div class="p-6">
                        <div class="flex flex-col items-center text-center">
                            <!-- Avatar -->
                            <div class="w-16 h-16 mb-4">
                                <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/pathfinder-bannerv2.png') }}"
                                    alt="{{ $member->first_name }} {{ $member->last_name }}"
                                    class="w-16 h-16 rounded-full object-cover">
                            </div>

                            <!-- Staff name -->
                            <h3
                                class="font-sofia text-gray-800 dark:text-gray-100 mb-2 {{ $isAssignedOtherRoom ? 'text-gray-400 dark:text-gray-500' : '' }}">
                                {{ $member->full_name }}
                            </h3>

                            <!-- Status badge -->
                            @if ($isSelectedRoom)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 mb-3 dark:text-green-200">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/success.png"
                                        class="w-4 h-4 mr-2" alt="Assigned">
                                    Assigned
                                </span>
                            @elseif ($isAssignedOtherRoom)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 mb-3 dark:text-yellow-200">
                                    <img src="{{ asset('icons/warning.png') }}" class="w-4 h-4 mr-2" alt="Other Room">
                                    @if ($member->room && $selectedRoom->name != $member->room->name)
                                        Assigned to {{ $member->room->name }}
                                    @else
                                        Assigned
                                    @endif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 mb-3 dark:text-gray-300">
                                    <img src="{{ asset('icons/error-gray.png') }}" class="w-4 h-4 mr-2"
                                        alt="Unassigned">
                                    Unassigned
                                </span>
                            @endif

                            <!-- Checkbox -->
                            <label class="flex items-center justify-center w-full cursor-pointer">
                                <input type="checkbox" data-staff-id="{{ $member->id }}" name="staff_ids[]"
                                    value="{{ $member->id }}"
                                    class="h-5 w-5 custom-time-input rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary focus:ring-2 transition-all duration-200"
                                    @if ($isSelectedRoom) checked @endif
                                    @if ($isAssignedOtherRoom) disabled @endif>
                                <span
                                    class="ml-2 text-sm {{ $isAssignedOtherRoom ? 'text-gray-400 dark:text-gray-500' : 'text-gray-600 dark:text-gray-300' }}">
                                    {{ $isSelectedRoom ? 'Assigned' : ($isAssignedOtherRoom ? 'Unavailable' : 'Available') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            @empty
                <!-- No Staff Found -->
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 text-center text-gray-500 dark:text-gray-400">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@cac2411/public/icons/staff.png"
                        alt="No staff" class="w-20 h-20 mb-4 opacity-80">
                    <p class="font-sofia text-lg">No staff found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Submit Button -->
    <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 py-4 z-40">
        <div class="max-w-7xl mx-auto flex justify-center">
            <button type="submit"
                class="bg-primary text-white px-6 py-3 rounded-md hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 transition-all duration-300 cursor-pointer font-medium shadow-lg hover:shadow-xl">
                Update Assignment
            </button>
        </div>
    </div>
</form>

<!-- Add padding to prevent content from being hidden behind fixed button -->
<div class="pb-20"></div>

<!-- Confirmation Modal -->
<div id="confirmModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
    onclick="closeModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border-2 border-secondary"
        onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-secondary">
            <div class="flex items-center justify-between">
                <h2 class="text-xl text-gray-900 dark:text-gray-300">
                    Confirm <span class="text-secondary">Unassignment</span>
                </h2>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-secondary transition-colors duration-200 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                            class="w-8 h-8" alt="Warning">
                    </div>
                </div>
                <div>
                    <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                        Are you sure you want to unassign <span id="modalMessage" class="text-secondary"></span>?
                        This will remove them from the current office.
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                    Cancel
                </button>
                <button type="button" onclick="confirmUnassign()"
                    class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                    Unassign Staff
                </button>
            </div>
        </div>
    </div>
</div>

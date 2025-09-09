@extends('layouts.app')

@section('content')
    <div id="scrollContainer" class="container max-w-6xl mx-auto overflow-y-auto h-[90vh]">
        <x-floating-actions />

        <div class="bg-white sticky top-0 z-48 pb-6 dark:bg-gray-900">
            <!-- Main Title with improved spacing -->
            <div class="text-center pt-8 pb-6">
                <h1 class="text-4xl font-bold text-gray-800 mb-2 dark:text-gray-100">
                    <span class="text-primary">Assign</span> Staff to Room
                </h1>
            </div>

            <!-- Room Selection Dropdown with better alignment -->
            <div class="flex justify-center px-4 pb-8">
                <form id="assign-staff-form" method="GET" action="{{ route('room.assign') }}" class="w-full max-w-md">
                    <select name="roomId"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}"
                                {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

            </div>
        </div>

        {{-- Staff assignment POST form --}}
        @if (isset($selectedRoom))
            <form action="{{ route('room.assign.update') }}" method="POST" id="assignForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="mb-8">
                    <!-- Pagination with proper alignment -->
                    <div class="flex justify-center mb-8">
                        <div class="w-full max-w-lg">
                            {{ $staff->appends(['roomId' => $selectedRoom->id ?? null])->links('pagination::tailwind') }}
                        </div>
                    </div>

                    <div id="staffCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($staff as $member)
                            @php
                                $assignedRoomId = $member->room_id;
                                $isSelectedRoom = isset($selectedRoom) && $assignedRoomId == $selectedRoom->id;
                                $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                            @endphp

                            <div
                                class="staff-card {{ $isAssignedOtherRoom ? 'opacity-50' : '' }} bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-2 border-primary {{ $isSelectedRoom ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700 hover:border-primary/50' }}">
                                <div class="p-6">
                                    <div class="flex flex-col items-center text-center">
                                        <!-- Avatar placeholder -->
                                        <div class="w-16 h-16 mb-4">
                                            <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc-logo.png') }}"
                                                alt="{{ $member->first_name }} {{ $member->last_name }}"
                                                class="w-16 h-16 rounded-full object-cover">
                                        </div>

                                        <!-- Staff name -->
                                        <h3
                                            class="text-gray-800 dark:text-gray-100 mb-2 {{ $isAssignedOtherRoom ? 'text-gray-400 dark:text-gray-500' : '' }}">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </h3>

                                        <!-- Status badge -->
                                        @if ($isSelectedRoom)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 mb-3 dark:text-green-200">
                                                <img src="{{ asset('icons/success.png') }}" class="w-4 h-4 mr-2"
                                                    alt="Assigned">
                                                Assigned
                                            </span>
                                        @elseif ($isAssignedOtherRoom)
                                            <span
                                                class="inline-flex items-center px-4 py-2 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 mb-3 dark:text-yellow-200">
                                                <img src="{{ asset('icons/warning.png') }}" class="w-4 h-4 mr-2"
                                                    alt="Other Room">
                                                Other Room
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
                                                class="h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary focus:ring-2 transition-all duration-200"
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
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button with proper spacing -->
                <div class="flex justify-center pt-8 pb-8 sticky bottom-0">
                    <button type="submit"
                        class="bg-primary text-white px-8 py-4 rounded-full hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 transition-all duration-300 cursor-pointer min-w-[200px] font-medium">
                        Update Assignment
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800"
            onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm <span
                            class="text-secondary">Unassignment</span></h2>
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
                            <img src="{{ asset('icons/warning-red.png') }}" class="w-8 h-8" alt="Warning">
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                            Are you sure you want to unassign <span id="modalMessage" class="text-secondary"></span>?
                            This will remove them from the current room.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-full transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300">
                        Cancel
                    </button>
                    <form id="unassignForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-red-600 rounded-full hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800">
                            Unassign Staff
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentStaffId = null;

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('scrollContainer');
            if (!container) return;

            // Restore scroll
            const savedPos = sessionStorage.getItem('scrollContainerPos');
            if (savedPos !== null) container.scrollTop = parseInt(savedPos, 10);

            // Save on scroll
            container.addEventListener('scroll', () => {
                sessionStorage.setItem('scrollContainerPos', container.scrollTop);
            });

            function openModal(staffId, staffName) {
                const modal = document.getElementById('confirmModal');
                const nameSpan = document.getElementById('modalMessage');
                const unassignForm = document.getElementById('unassignForm');
                const currentPage = new URLSearchParams(window.location.search).get('page') || '1';

                currentStaffId = staffId;
                nameSpan.textContent = staffName;
                unassignForm.action = `/admin/rooms/staff/${staffId}/remove?page=${currentPage}`;

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.bg-white').classList.remove('scale-95');
                    modal.querySelector('.bg-white').classList.add('scale-100');
                }, 10);

                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('confirmModal');
                const modalContent = modal.querySelector('.bg-white');

                modal.classList.add('opacity-0');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    currentStaffId = null;
                }, 300);
            }

            // Make functions globally available
            window.openModal = openModal;
            window.closeModal = closeModal;

            // Checkbox handlers
            document.querySelectorAll('#staffCards input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('click', function(e) {
                    if (!this.checked) {
                        e.preventDefault();
                        this.checked = true;
                        const staffCard = this.closest('.staff-card');
                        const staffName = staffCard.querySelector('h3').textContent.trim();
                        openModal(this.dataset.staffId, staffName);
                    } else {
                        const staffCard = this.closest('.staff-card');
                        staffCard.classList.add('border-primary', 'bg-primary/5');
                        staffCard.classList.remove('border-gray-200');
                    }
                });
            });

            // Escape key closes modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        });
    </script>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container max-w-6xl mx-auto overflow-y-auto h-[80vh]">
        <x-floating-actions />

        <div class="bg-white sticky top-0 z-48">
            <h1 class="text-3xl text-center font-bold mb-8 text-gray-800">
                <span class="text-primary">Assign</span> Staff to Room
            </h1>

            <div class="mb-8">
                <form method="GET" action="{{ route('room.assign') }}">
                    <select name="roomId" onchange="this.form.submit()"
                        class="w-full max-w-md mx-auto block border border-gray-300 rounded-lg px-4 py-3 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
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

                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Select Staff Members</h2>

                    <div id="staffCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($staff as $member)
                            @php
                                $assignedRoomId = $member->room_id;
                                $isSelectedRoom = isset($selectedRoom) && $assignedRoomId == $selectedRoom->id;
                                $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                            @endphp

                            <div
                                class="staff-card {{ $isAssignedOtherRoom ? 'opacity-50' : '' }} bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-2 border-primary {{ $isSelectedRoom ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary/50' }}">
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
                                            class="font-semibold text-gray-800 mb-2 {{ $isAssignedOtherRoom ? 'text-gray-400' : '' }}">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </h3>

                                        <!-- Status badge -->
                                        @if ($isSelectedRoom)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-3">
                                                <img src="{{ asset('icons/success.png') }}" class="w-4 h-4 mr-2"
                                                    alt="Assigned">
                                                Assigned
                                            </span>
                                        @elseif ($isAssignedOtherRoom)
                                            <span
                                                class="inline-flex items-center px-4 py-2 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-3">
                                                <img src="{{ asset('icons/warning.png') }}" class="w-4 h-4 mr-2"
                                                    alt="Other Room">
                                                Other Room
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mb-3">
                                                <img src="{{ asset('icons/error-gray.png') }}" class="w-4 h-4 mr-2"
                                                    alt="Unassigned">
                                                Unassigned
                                            </span>
                                        @endif

                                        <!-- Checkbox -->
                                        <label class="flex items-center justify-center w-full cursor-pointer">
                                            <input type="checkbox" data-staff-id="{{ $member->id }}" name="staff_ids[]"
                                                value="{{ $member->id }}"
                                                class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary focus:ring-2 transition-all duration-200"
                                                @if ($isSelectedRoom) checked @endif
                                                @if ($isAssignedOtherRoom) disabled @endif>
                                            <span
                                                class="ml-2 text-sm {{ $isAssignedOtherRoom ? 'text-gray-400' : 'text-gray-600' }}">
                                                {{ $isSelectedRoom ? 'Assigned' : ($isAssignedOtherRoom ? 'Unavailable' : 'Available') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-primary text-white px-8 py-4 rounded-lg hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer min-w-[200px]">
                        Update Assignments
                    </button>
                </div>
            </form>
        @endif

        <!-- Confirmation Modal -->
        <div id="confirmModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
            onclick="closeModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
                onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl text-gray-900">Confirm <span class="text-secondary">Unassignment</span></h2>
                        <button onclick="closeModal()"
                            class="text-gray-400 hover:text-secondary transition-colors duration-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                Are you sure you want to unassign <span id="modalMessage"
                                    class="text-secondary font-semibold"></span>?
                                This will remove them from the current room.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium border border-gray-400 hover:text-white hover:bg-gray-400 rounded-lg transition-all duration-300 cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" id="confirmBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border border-secondary rounded-lg hover:bg-white hover:text-secondary focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-all duration-300 cursor-pointer">
                            Unassign Staff
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let currentStaffId = null;

            function openModal(staffId, staffName) {
                const modal = document.getElementById('confirmModal');
                const nameSpan = document.getElementById('modalMessage');

                currentStaffId = staffId;
                nameSpan.textContent = staffName;

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

            document.addEventListener('DOMContentLoaded', function() {
                // Add click handlers for checkboxes
                document.querySelectorAll('#staffCards input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('click', function(e) {
                        if (!this.checked) { // only trigger on uncheck
                            e.preventDefault();
                            this.checked = true; // keep checked until confirmed
                            const staffCard = this.closest('.staff-card');
                            const staffName = staffCard.querySelector('h3').textContent.trim();
                            openModal(this.dataset.staffId, staffName);
                        } else {
                            // Add visual feedback for assignment
                            const staffCard = this.closest('.staff-card');
                            staffCard.classList.add('border-primary', 'bg-primary/5');
                            staffCard.classList.remove('border-gray-200');
                        }
                    });
                });

                // Confirmation button handler
                document.getElementById('confirmBtn').addEventListener('click', () => {
                    if (!currentStaffId) return;

                    fetch(`/admin/rooms/staff/${currentStaffId}/remove`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            closeModal();
                            if (data.success) {
                                // Refresh the page to stay on current room after unassignment
                                const currentRoomId = document.querySelector('input[name="room_id"]').value;
                                window.location.href =
                                    `{{ route('room.assign') }}?roomId=${currentRoomId}`;
                            } else {
                                alert(data.message || 'Failed to unassign staff.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Server error.');
                            closeModal();
                        });
                });

                // Close modal on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeModal();
                });
            });
        </script>
    </div>
@endsection

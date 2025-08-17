@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-6">
        <x-floating-actions />

        <h1 class="text-3xl font-bold mb-6 text-gray-800"><span class="text-primary">Assign</span> Staff to Room</h1>

        {{-- Room selection GET form --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('room.assign') }}">
                <select name="roomId" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Staff assignment POST form --}}
        @if (isset($selectedRoom))
            <form action="{{ route('room.assign.update') }}" method="POST" id="assignForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                <div class="mb-6">
                    <label class="block font-semibold text-gray-800 mb-4">Select Staff:</label>
                    <div id="staffCheckboxes" class="space-y-3">
                        @foreach ($staff as $member)
                            @php
                                $assignedRoomId = $member->room_id;
                                $isSelectedRoom = isset($selectedRoom) && $assignedRoomId == $selectedRoom->id;
                                $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                                $textClass = $isAssignedOtherRoom ? 'text-gray-400' : 'text-gray-800';
                            @endphp
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                <input type="checkbox" data-staff-id="{{ $member->id }}" name="staff_ids[]"
                                    value="{{ $member->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-2"
                                    @if ($isSelectedRoom) checked @endif
                                    @if ($isAssignedOtherRoom) disabled @endif>
                                <span class="{{ $textClass }} font-medium">{{ $member->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-primary text-white px-6 py-3 rounded-lg hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer font-medium">
                        Assign
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
                                Are you sure you want to unassign <span id="modalMessage" class="text-secondary"></span>?
                                This will remove from the current room.
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
                document.querySelectorAll('#staffCheckboxes input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('click', function(e) {
                        if (!this.checked) { // only trigger on uncheck
                            e.preventDefault();
                            this.checked = true; // keep checked until confirmed
                            const staffName = this.nextElementSibling.innerText;
                            openModal(this.dataset.staffId, staffName);
                        }
                    });
                });

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

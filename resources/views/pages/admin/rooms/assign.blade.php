@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-4">
        <x-floating-actions />

        <h1 class="text-2xl font-bold mb-4">Assign Staff to Room</h1>

        {{-- Room selection GET form --}}
        <form method="GET" action="{{ route('room.assign') }}" class="mb-4">
            <select name="roomId" onchange="this.form.submit()" class="w-full border rounded p-2">
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}"
                        {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- Staff assignment POST form --}}
        <form action="{{ route('room.assign.update') }}" method="POST" id="assignForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="room_id" value="{{ $selectedRoom->id ?? '' }}">

            <div class="mb-4">
                <label class="block font-medium">Select Staff:</label>
                <div id="staffCheckboxes" class="space-y-2">
                    @foreach ($staff as $member)
                        @php
                            $assignedRoomId = $member->room_id;
                            $isSelectedRoom = isset($selectedRoom) && $assignedRoomId == $selectedRoom->id;
                            $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                            $textClass = $isAssignedOtherRoom ? 'text-gray-400' : ''; // gray out name
                        @endphp
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" data-staff-id="{{ $member->id }}" name="staff_ids[]"
                                value="{{ $member->id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                @if ($isSelectedRoom) checked @endif
                                @if ($isAssignedOtherRoom) disabled @endif>
                            <span class="{{ $textClass }}">{{ $member->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                Assign
            </button>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                <h2 class="text-xl mb-4">Confirm <span class="text-secondary">Unassign</span></h2>
                <p id="modalMessage" class="mb-4 text-gray-700"></p>
                <div class="flex justify-end space-x-4">
                    <button id="cancelBtn"
                        class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded cursor-pointer">Cancel</button>
                    <button id="confirmBtn"
                        class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border-2 border-secondary cursor-pointer">Confirm</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('confirmModal');
                const cancelBtn = document.getElementById('cancelBtn');
                const confirmBtn = document.getElementById('confirmBtn');
                let currentCheckbox = null;
                let currentStaffId = null;

                document.querySelectorAll('#staffCheckboxes input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('click', function(e) {
                        if (!this.checked) { // only trigger on uncheck
                            e.preventDefault();
                            currentCheckbox = this;
                            currentStaffId = this.dataset.staffId;
                            const staffName = this.nextElementSibling.innerText;

                            document.getElementById('modalMessage').innerText =
                                `Are you sure you want to unassign ${staffName}?`;

                            modal.classList.remove('hidden');
                        }
                    });
                });

                cancelBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    if (currentCheckbox) currentCheckbox.checked = true; // revert
                    currentCheckbox = null;
                    currentStaffId = null;
                });

                confirmBtn.addEventListener('click', () => {
                    if (!currentStaffId) return;

                    fetch(`/admin/staff/remove/${currentStaffId}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            modal.classList.add('hidden');
                            if (data.success) {
                                currentCheckbox.checked = false; // uncheck
                                alert(data.message); // <-- show the message here
                            } else {
                                alert(data.message || 'Failed to unassign staff.');
                            }

                            currentCheckbox = null;
                            currentStaffId = null;
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Server error.');
                            modal.classList.add('hidden');
                        });
                });
            });
        </script>
    @endsection

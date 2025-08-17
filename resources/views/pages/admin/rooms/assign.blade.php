@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-6">
        <x-floating-actions />

        <h1 class="text-3xl font-bold mb-6 text-gray-800">Assign Staff to Room</h1>

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
        @if(isset($selectedRoom))
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
                        <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-all duration-200 cursor-pointer">
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
        <div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full mx-4">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Confirm <span class="text-secondary">Unassign</span></h2>
                <p id="modalMessage" class="mb-6 text-gray-700 leading-relaxed"></p>
                <div class="flex justify-end space-x-4">
                    <button id="cancelBtn"
                        class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded-lg cursor-pointer font-medium transition-all duration-200">Cancel</button>
                    <button id="confirmBtn"
                        class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-white hover:text-secondary border-2 border-secondary cursor-pointer font-medium transition-all duration-200">Confirm</button>
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
                            modal.classList.add('hidden');
                            if (data.success) {
                                // Refresh the page to stay on current room after unassignment
                                const currentRoomId = document.querySelector('input[name="room_id"]').value;
                                window.location.href = `{{ route('room.assign') }}?roomId=${currentRoomId}`;
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
    </div>
@endsection
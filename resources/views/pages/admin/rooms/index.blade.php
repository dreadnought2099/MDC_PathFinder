@extends('layouts.app')

@section('content')
    <div class="p-4">

        <x-floating-actions />

        <h2 class="text-2xl text-center font-bold mb-4"><span class="text-primary">Room</span> List</h2>

        <table class="w-full border-collapse border">
            <thead>
                <tr>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td class="border p-2">{{ $room->name }}</td>
                        <td class="border p-2">
                            <a href="{{ route('room.show', $room->id) }}"
                                class="text-primary hover-underline hover:scale-105 transform transition duration-200">
                                View
                            </a>
                            <a href="{{ route('room.edit', $room->id) }}"
                                class="text-edit hover-underline-edit hover:scale-105 transform transition duration-200">
                                Edit
                            </a>
                            <a href="{{ route('room.assign', $room->id) }}"
                                class="text-primary hover-underline hover:scale-105 transform transition duration-200">
                                Assign Staff
                            </a>

                            <button onclick="openRoomModal('{{ $room->id }}', '{{ addslashes($room->name) }}')"
                                class="text-secondary hover-underline-delete hover:scale-105 transform transition duration-200 cursor-pointer">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Room Delete Modal with animation -->
    <div id="roomDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closeRoomModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
            onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl text-gray-900">Confirm Deletion</h2>
                    <button onclick="closeRoomModal()"
                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm leading-relaxed">
                    Are you sure you want to delete <span id="roomName" class="text-secondary"></span>?
                    This action cannot be undone.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                <form id="roomDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRoomModal()"
                            class="px-4 py-2 text-sm font-medium border border-gray-400 hover:text-white hover:bg-gray-400 rounded-lg transition-all duration-300 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border border-red-600 rounded-lg hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer">
                            Delete Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRoomModal(id, name) {
            const modal = document.getElementById('roomDeleteModal');
            const nameSpan = document.getElementById('roomName');
            const form = document.getElementById('roomDeleteForm');

            nameSpan.textContent = name;
            form.action = `/admin/rooms/${id}`;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
                modal.querySelector('.bg-white').classList.add('scale-100');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        function closeRoomModal() {
            const modal = document.getElementById('roomDeleteModal');
            const modalContent = modal.querySelector('.bg-white');

            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRoomModal();
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('roomDeleteModal');
            if (e.target === modal) closeRoomModal();
        });
    </script>
@endsection

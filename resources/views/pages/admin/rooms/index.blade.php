@extends('layouts.app')

@section('content')
    <div class="p-4">

        <x-floating-actions />

        <h2 class="text-xl text-center font-bold mb-4">Room List</h2>

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
                            <a href="{{ route('room.edit', $room->id) }}"
                                class="text-primary hover-underline hover:scale-105 transform transition duration-200">Edit</a>

                            <button onclick="openRoomModal('{{ $room->id }}', '{{ addslashes($room->name) }}')" class="text-secondary hover-underline-delete hover:scale-105 transform transition duration-200 cursor-pointer">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="roomDeleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-lg">
            <h2 class="text-xl mb-4">Confirm Deletion</h2>
            <p class="mb-4">Are you sure you want to delete <span id="roomName" class="text-primary"></span>?</p>

            <form id="roomDeleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeRoomModal()"
                        class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border-2 border-secondary transition-all duration-300 cursor-pointer">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRoomModal(id, name) {
            const modal = document.getElementById('roomDeleteModal');
            const nameSpan = document.getElementById('roomName');
            const form = document.getElementById('roomDeleteForm');

            nameSpan.textContent = name;
            form.action = `/room/${id}`; // Adjust if your route prefix differs
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRoomModal() {
            const modal = document.getElementById('roomDeleteModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeRoomModal();
        });
    </script>
@endsection
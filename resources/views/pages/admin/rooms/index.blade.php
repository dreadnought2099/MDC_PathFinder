@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header Section -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="text-primary">Room</span> Management
            </h1>
            <p class="text-gray-600">Manage rooms and assign staff members</p>
        </div>

        <!-- Floating Actions -->
        <div class="mb-6">
            <x-floating-actions />
        </div>

        <!-- Room Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                Room Name
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rooms as $room)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <span class="text-primary font-medium text-sm">{{ $room->id }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $room->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end space-x-3">
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
                                        <button
                                            onclick="openRoomModal('{{ $room->id }}', '{{ addslashes($room->name) }}')"
                                            class="text-secondary hover-underline-delete hover:scale-105 transform transition duration-200 cursor-pointer">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No rooms found</h3>
                                            <p class="text-gray-500 text-sm">Add your first room to get started.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Room Delete Modal (animated, same style as Staff modal) -->
    <div id="roomDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closeRoomModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
            onclick="event.stopPropagation()">
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

            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm leading-relaxed">
                    Are you sure you want to delete <span id="roomName" class="text-red-600"></span>?
                    This action cannot be undone.
                </p>
            </div>

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

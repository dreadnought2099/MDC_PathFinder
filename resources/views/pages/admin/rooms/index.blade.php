@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-8 text-center sticky top-0 z-48">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 dark:text-gray-100">
                <span class="text-primary">Office</span> Management
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Manage offices and assign staff members</p>
            <div class="mt-4 flex justify-center">
                {{ $rooms->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            <div class="py-4">
                <x-sort-by :route="route('room.index')" :fields="['name' => 'Name', 'created_at' => 'Created At']" :current-sort="$sort" :current-direction="$direction" />

            </div>
        </div>

        <!-- Floating Actions -->
        <div class="mb-6">
            <x-floating-actions />
        </div>

        <!-- Room Table Container -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                ID
                            </th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Office Name
                            </th>
                            <th
                                class="px-6 py-4 text-right text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($rooms as $room)
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->room_id == $room->id)
                                <tr
                                    class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                                    <!-- ID -->
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $room->id }}
                                    </td>

                                    <!-- Room Name -->
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $room->name }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end space-x-3">
                                            {{-- View --}}
                                            <div class="relative inline-block group">
                                                @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view rooms'))
                                                    <a href="{{ route('room.show', $room->id) }}"
                                                        class=" text-primary hover-underline hover:scale-115 transform transition duration-200">
                                                        <img src="{{ asset('icons/view.png') }}" alt="View Icon"
                                                            class="w-8 h-8 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                    text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                    group-hover:opacity-100 group-hover:visible transition-all duration-300 
                    whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        View Office
                                                        <!-- Arrow -->
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                        border-l-4 border-l-gray-900 dark:border-l-gray-700
                        border-t-4 border-t-transparent 
                        border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="relative inline-block group">
                                                {{-- Edit --}}
                                                @if (auth()->user()->hasRole('Admin') || auth()->user()->can('edit rooms'))
                                                    <a href="{{ route('room.edit', $room->id) }}"
                                                        class="text-edit hover-underline-edit hover:scale-115 transform transition duration-200">
                                                        <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon"
                                                            class="w-8 h-8 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                    text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                    group-hover:opacity-100 group-hover:visible transition-all duration-300 
                    whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Edit Office
                                                        <!-- Arrow -->
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                        border-l-4 border-l-gray-900 dark:border-l-gray-700
                        border-t-4 border-t-transparent 
                        border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="relative inline-block group">
                                                {{-- Assign Staff (Admin or Room Manager) --}}
                                                @if (auth()->user()->hasRole('Admin'))
                                                    <a href="{{ route('room.assign', $room->id) }}"
                                                        class="text-tertiary hover-underline hover:scale-115 transform transition duration-200">
                                                        <img src="{{ asset('icons/assign-staff.png') }}"
                                                            alt="Assign Staff Icon" class="w-8 h-8 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                    text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                    group-hover:opacity-100 group-hover:visible transition-all duration-300 
                    whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Assign Staff
                                                        <!-- Arrow -->
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                        border-l-4 border-l-gray-900 dark:border-l-gray-700
                        border-t-4 border-t-transparent 
                        border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="relative inline-block group">
                                                {{-- Create User (Admin only) --}}
                                                @if (auth()->user()->hasRole('Admin'))
                                                    <a href="{{ route('room-user.create', ['roomId' => $room->id]) }}"
                                                        class="text-green-600 hover-underline hover:scale-115 transform transition duration-200">
                                                        <img src="{{ asset('icons/manager.png') }}" alt="Create Room User"
                                                            class="w-8 h-8 object-contain">
                                                    </a>
                                                @endif
                                                <div
                                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                    text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                    group-hover:opacity-100 group-hover:visible transition-all duration-300 
                    whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                    Create Room User Account
                                                    <!-- Arrow -->
                                                    <div
                                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                        border-l-4 border-l-gray-900 dark:border-l-gray-700
                        border-t-4 border-t-transparent 
                        border-b-4 border-b-transparent">
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="relative inline-block group">
                                                {{-- Delete (Admin only) --}}
                                                @if (auth()->user()->hasRole('Admin'))
                                                    <button
                                                        onclick="openRoomModal('{{ $room->id }}', '{{ addslashes($room->name) }}')"
                                                        class="text-secondary hover-underline-delete hover:scale-115 transform transition duration-200 cursor-pointer">
                                                        <img src="{{ asset('icons/trash.png') }}" alt="Trash Icon"
                                                            class="w-8 h-8 object-contain">
                                                    </button>
                                                @endif
                                                <div
                                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                    text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                    group-hover:opacity-100 group-hover:visible transition-all duration-300 
                    whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                    Recycle Office
                                                    <!-- Arrow -->
                                                    <div
                                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                        border-l-4 border-l-gray-900 dark:border-l-gray-700
                        border-t-4 border-t-transparent 
                        border-b-4 border-b-transparent">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr class="dark:bg-gray-800">
                                <td colspan="3" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div
                                            class="w-16 h-16 bg-primary-10 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                            <img src="{{ asset('icons/offices.png') }}" alt="Group icon" class="w-11 h-10">
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">No
                                                rooms
                                                found</h3>
                                            <p class="text-gray-500 text-sm dark:text-gray-400">Add your first room to
                                                get
                                                started.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Room Delete Modal (animated, same style as Staff modal) -->
        <div id="roomDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
            onclick="closeRoomModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-secondary"
                onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-secondary">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm <span
                                class="text-secondary">Deletion</span></h2>
                        <button onclick="closeRoomModal()"
                            class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
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
                                <img src="{{ asset('icons/warning-red.png') }}" class="w-8 h-8" alt="Warning">
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                                Are you sure you want to delete <span id="roomName" class="text-red-600"></span>?
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
                    <form id="roomDeleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeRoomModal()"
                                class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
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
    @endpush

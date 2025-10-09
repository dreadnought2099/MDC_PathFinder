@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-6 text-center sticky top-0 z-48 px-4">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                <span class="text-primary">Office</span> Management
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                Manage offices and assign staff members
            </p>

            <!-- Pagination -->
            <div class="mt-3 flex justify-center">
                {{ $rooms->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Sort Options -->
            <div class="py-3">
                <x-sort-by :route="route('room.index')" :fields="[
                    'name' => 'Office Name',
                    'created_at' => 'Date Created',
                    'updated_at' => 'Date Modified',
                ]" :currentSort="$sort" :currentDirection="$direction" />
            </div>
        </div>

        <!-- Floating Actions -->
        <div class="mb-4">
            <x-floating-actions />
        </div>

        <!-- Room Table Container -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px]"> {{-- Prevent table from collapsing --}}
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                ID
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Office Name
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($rooms as $room)
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->room_id == $room->id)
                                <tr
                                    class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                                    <!-- ID -->
                                    <td
                                        class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-gray-300">
                                        {{ $room->id }}
                                    </td>

                                    <!-- Room Name -->
                                    <td
                                        class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-gray-300">
                                        {{ $room->name }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                                        <div class="flex flex-wrap justify-end gap-2 sm:gap-3 items-center">
                                            {{-- View --}}
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view rooms'))
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('room.show', $room->id) }}"
                                                        class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                            alt="View Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        View
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Edit --}}
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->can('edit rooms'))
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('room.edit', $room->id) }}"
                                                        class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                            alt="Edit Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Edit
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Assign --}}
                                            @if (auth()->user()->hasRole('Admin'))
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('room.assign', $room->id) }}"
                                                        class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/assign-staff.png"
                                                            alt="Assign Staff Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Assign Staff
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Delete --}}
                                            @if (auth()->user()->hasRole('Admin'))
                                                <div class="relative inline-block group">
                                                    <button type="button"
                                                        onclick="openRoomModal('{{ $room->id }}', '{{ addslashes($room->name) }}')"
                                                        class="hover-underline-delete inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200 appearance-none bg-transparent border-0 cursor-pointer">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/trash.png"
                                                            alt="Trash Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </button>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Delete
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr class="dark:bg-gray-800">
                                <td colspan="3" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div
                                            class="w-14 h-14 sm:w-16 sm:h-16 bg-primary-10 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/offices.png"
                                                alt="Group icon" class="w-9 h-8 sm:w-11 sm:h-10">
                                        </div>
                                        <div class="text-center">
                                            <h3
                                                class="text-base sm:text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">
                                                No rooms found
                                            </h3>
                                            <p class="text-gray-500 text-xs sm:text-sm dark:text-gray-400">
                                                Add your first room to get started.
                                            </p>
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
    <!-- Room Delete Modal -->
    <div id="roomDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closeRoomModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 sm:mx-6 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-secondary"
            onclick="event.stopPropagation()">

            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 border-b border-secondary">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl text-gray-900 dark:text-gray-300">
                        Confirm <span class="text-secondary">Deletion</span>
                    </h2>
                    <button onclick="closeRoomModal()"
                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-4">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                                class="w-8 h-8" alt="Warning">
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

            <!-- Footer -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
                <form id="roomDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                        <button type="button" onclick="closeRoomModal()"
                            class="w-full sm:w-auto px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                            Cancel
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
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

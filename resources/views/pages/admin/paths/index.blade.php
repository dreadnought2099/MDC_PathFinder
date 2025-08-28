@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-8 text-center sticky top-0 z-48">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 dark:text-gray-100">
                <span class="text-primary">Paths</span> Management
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Manage paths between rooms</p>
            <div class="mt-4 flex justify-center">
                {{ $paths->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>

        <x-sort-by :route="route('path.index')" :fields="[
            'id' => 'ID',
            'from_room_id' => 'From Room',
            'to_room_id' => 'To Room',
            'created_at' => 'Created At',
        ]" :current-sort="$sort" :current-direction="$direction" />

        <!-- Floating Actions -->
        <div class="mb-6">
            <x-floating-actions />
        </div>

        <!-- Paths Table -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                From Room</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                To Room</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Path Direction</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Created At</th>
                            <th
                                class="px-6 py-4 text-right text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($paths as $path)
                            <tr
                                class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                        {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                        {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <i class="fas fa-arrow-right text-gray-400 mr-1"></i>
                                    <small
                                        class="text-gray-500">{{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                                        → {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}</small>
                                </td>
                                <td class="px-6 py-4">
                                    <small
                                        class="text-gray-500">{{ $path->created_at ? $path->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('path.show', $path->id) }}"
                                            class="hover-underline text-primary hover:scale-105 transform transition duration-200">
                                            View
                                        </a>
                                        <button
                                            onclick="openPathModal('{{ $path->id }}', '{{ addslashes($path->fromRoom->name ?? 'Room #' . $path->from_room_id) }} → {{ addslashes($path->toRoom->name ?? 'Room #' . $path->to_room_id) }}')"
                                            class="hover-underline-delete text-secondary hover:scale-105 transform transition duration-200 cursor-pointer">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div
                                            class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                            <i class="fas fa-route fa-2x text-gray-400"></i>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">No paths
                                                found</h3>
                                            <p class="text-gray-500 text-sm dark:text-gray-400">Add your first path to get
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

        <!-- Path Delete Modal -->
        <div id="pathDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
            onclick="closePathModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800"
                onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm <span
                                class="text-secondary">Deletion</span></h2>
                        <button onclick="closePathModal()"
                            class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
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
                                Are you sure you want to delete <span id="pathName" class="text-red-600"></span>?
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
                    <form id="pathDeleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closePathModal()"
                                class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-lg transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-red-600 rounded-lg hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800">
                                Delete Path
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openPathModal(id, name) {
            const modal = document.getElementById('pathDeleteModal');
            const nameSpan = document.getElementById('pathName');
            const form = document.getElementById('pathDeleteForm');

            nameSpan.textContent = name;
            form.action = `/admin/paths/${id}`;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
                modal.querySelector('.bg-white').classList.add('scale-100');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        function closePathModal() {
            const modal = document.getElementById('pathDeleteModal');
            const modalContent = modal.querySelector('.bg-white');

            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePathModal();
        });
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('pathDeleteModal');
            if (e.target === modal) closePathModal();
        });
    </script>
@endpush

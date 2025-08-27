@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="container mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white mb-8 text-center sticky top-0 z-48">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="text-primary">Path</span> Management
            </h1>
            <p class="text-gray-600">Manage paths between rooms</p>
            <div class="mt-4 flex justify-center">
                {{ $paths->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Path Table -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide">ID</th>
                            <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide">From Room</th>
                            <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide">To Room</th>
                            <th class="px-6 py-4 text-right text-sm text-gray-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($paths as $path)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">{{ $path->id }}</td>
                                <td class="px-6 py-4">{{ $path->fromRoom->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $path->toRoom->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('path_images.index', ['path_id' => $path->id]) }}"
                                            class="text-primary hover-underline hover:scale-105 transform transition duration-200">
                                            View Directions
                                        </a>
                                        <button onclick="openPathModal('{{ $path->id }}')"
                                            class="text-secondary hover-underline-delete hover:scale-105 transform transition duration-200 cursor-pointer">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                    No paths available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="pathDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closePathModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
            onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl text-gray-900">Confirm <span class="text-secondary">Deletion</span></h2>
                    <button onclick="closePathModal()"
                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
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
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Are you sure you want to delete this path? This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                <form id="pathDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePathModal()"
                            class="px-4 py-2 text-sm font-medium border border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-lg transition-all duration-300 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border border-red-600 rounded-lg hover:bg-white hover:text-secondary transition-all duration-300 cursor-pointer">
                            Delete Path
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openPathModal(id) {
            const modal = document.getElementById('pathDeleteModal');
            const form = document.getElementById('pathDeleteForm');

            form.action = `/admin/paths/${id}`; // adjust route if needed

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

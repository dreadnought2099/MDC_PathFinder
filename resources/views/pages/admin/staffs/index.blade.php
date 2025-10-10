@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-6 text-center sticky top-0 z-48 px-4">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                <span class="text-primary">Staff</span> Management
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                Manage your team members and their information
            </p>

            <!-- Sort Options -->
            <div class="py-3">
                <x-filter-header :route="route('staff.index')" placeholder="staff" :fields="[
                    'full_name' => 'Full Name',
                    'created_at' => 'Date Created',
                    'updated_at' => 'Date Modified',
                ]" :currentSort="$sort" :currentDirection="$direction"
                    :currentSearch="$search" />
            </div>
        </div>

        <!-- Floating Actions -->
        <div class="mb-4">
            <x-floating-actions />
        </div>

        <!-- Staff Table Container -->
        <div id="records-table" class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                @include('pages.admin.staffs.partials.staff-table', ['staffs' => $staffs])
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3 flex justify-center">
            {{ $staffs->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Enhanced Modal -->
    <div id="deleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
        onclick="closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-secondary"
            onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-secondary">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm <span
                            class="text-secondary">Deletion</span></h2>
                    <button onclick="closeModal()"
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
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                                class="w-8 h-8" alt="Warning">
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                            Are you sure you want to delete
                            <span id="modalStaffName" class="text-red-600"></span>?
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                            Delete Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openModal(id, name) {
            const modal = document.getElementById('deleteModal');
            const nameSpan = document.getElementById('modalStaffName');
            const form = document.getElementById('deleteForm');

            nameSpan.textContent = name;
            form.action = '{{ route('staff.destroy', '__ID__') }}'.replace('__ID__', id);

            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
                modal.querySelector('.bg-white').classList.add('scale-100');
            }, 10);

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('deleteModal');
            const modalContent = modal.querySelector('.bg-white');

            // Animate out
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
            if (e.key === 'Escape') closeModal();
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('deleteModal');
            if (e.target === modal) closeModal();
        });
    </script>
@endpush

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

            <!-- Pagination -->
            <div class="mt-3 flex justify-center">
                {{ $staffs->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Sort Options -->
            <div class="py-3">
                <x-sort-by :route="route('staff.index')" :fields="['first_name' => 'First Name', 'last_name' => 'Last Name', 'email' => 'Email']" :current-sort="$sort" :current-direction="$direction" />
            </div>
        </div>

        <!-- Floating Actions -->
        <div class="mb-4">
            <x-floating-actions />
        </div>

        <!-- Staff Table Container -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px]"> {{-- Prevent table collapsing --}}
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Staff ID
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Full Name
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Office Assigned
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Room ID
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @php $hasVisibleStaff = false; @endphp

                        @foreach ($staffs as $staff)
                            @can('view', $staff)
                                @php $hasVisibleStaff = true; @endphp
                                <tr
                                    class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                                    <!-- Staff ID -->
                                    <td
                                        class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                                        {{ $staff->id }}
                                    </td>

                                    <!-- Staff Name -->
                                    <td
                                        class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                                        {{ $staff->full_name }}
                                    </td>

                                    <!-- Office Name -->
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-400">
                                        {{ $staff->room->name ?? 'N/A' }}
                                    </td>

                                    <!-- Room ID -->
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-400">
                                        {{ $staff->room_id ?? 'N/A' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                                        <div class="flex flex-wrap justify-end gap-2 sm:gap-3 items-center">
                                            {{-- View --}}
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view staffs'))
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('staff.show', $staff->id) }}"
                                                        class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                            alt="View Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        View Staff
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Edit --}}
                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->can('edit staffs'))
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('staff.edit', $staff->id) }}"
                                                        class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                            alt="Edit Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </a>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Edit Staff
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
                                                        onclick="openModal('{{ $staff->id }}', '{{ addslashes($staff->first_name . ' ' . $staff->last_name) }}')"
                                                        class="hover-underline-delete inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200 appearance-none bg-transparent border-0 cursor-pointer">
                                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/trash.png"
                                                            alt="Delete Icon"
                                                            class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                    </button>
                                                    <div
                                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                        Delete Staff
                                                        <div
                                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endcan
                        @endforeach

                        @if (!$hasVisibleStaff)
                            <tr class="dark:bg-gray-800">
                                <td colspan="5" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div
                                            class="w-14 h-14 sm:w-16 sm:h-16 bg-primary-10 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/group.png"
                                                alt="Group icon" class="w-9 h-8 sm:w-11 sm:h-10">
                                        </div>
                                        <div class="text-center">
                                            <h3
                                                class="text-base sm:text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">
                                                No staff members found
                                            </h3>
                                            @if (auth()->user()->hasRole('Admin'))
                                                <p class="text-gray-500 text-xs sm:text-sm dark:text-gray-400">
                                                    Get started by adding your first team member.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
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

@props(['items', 'routePrefix', 'title', 'emptyMessage', 'tab'])

<div class="mb-12">
    <h2 class="text-2xl font-semibold text-center mb-8">{{ $title }}</h2>

    @if ($items->isEmpty())
        <div class="text-center text-gray-600 py-12">{{ $emptyMessage }}</div>
    @else
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Deleted At</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($items as $item)
                            {{-- Fallback for Staff Name --}}
                            @php
                                $displayName = $item->name ?? trim($item->first_name . ' ' . $item->last_name);
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $displayName }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        {{ $item->deleted_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <!-- Restore Button -->
                                        <button type="button"
                                            onclick="showModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                            class="text-primary hover-underline cursor-pointer text-sm font-medium">
                                            Restore
                                        </button>

                                        <!-- Permanently Delete Button -->
                                        <button type="button"
                                            onclick="showModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                            class="text-secondary hover-underline-delete cursor-pointer text-sm font-medium">
                                            Delete Permanently
                                        </button>
                                    </div>

                                    <!-- Restore Modal -->
                                    <div id="restoreModal-{{ $routePrefix }}-{{ $item->id }}"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
                                        onclick="closeModal(event, this)">
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
                                            onclick="event.stopPropagation()">
                                            <!-- Modal Header -->
                                            <div class="px-6 py-4 border-b border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <h2 class="text-xl font-semibold text-gray-900">Confirm <span
                                                            class="text-primary">Restore</span></h2>
                                                    <button
                                                        onclick="hideModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Modal Body -->
                                            <div class="px-6 py-6">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-blue-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-700 leading-relaxed">
                                                            Are you sure you want to restore
                                                            <span
                                                                class="font-medium text-blue-600">{{ $displayName }}</span>?
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Footer -->
                                            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                                                <form action="{{ route("{$routePrefix}.restore", $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="tab" value="{{ $tab }}">
                                                    <div class="flex justify-end space-x-3">
                                                        <button type="button"
                                                            onclick="hideModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                            class="px-4 py-2 text-sm font-medium border border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-lg transition-all duration-300 cursor-pointer">
                                                            Cancel
                                                        </button>
                                                        <button type="submit"
                                                            class="bg-primary text-white text-sm font-medium px-4 py-2 bg-primary rounded-lg hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div id="deleteModal-{{ $routePrefix }}-{{ $item->id }}"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
                                        onclick="closeModal(event, this)">
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95"
                                            onclick="event.stopPropagation()">
                                            <!-- Modal Header -->
                                            <div class="px-6 py-4 border-b border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <h2 class="text-xl text-gray-900">Confirm <span
                                                            class="text-secondary">Permanent Deletion</span></h2>
                                                    <button
                                                        onclick="hideModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Modal Body -->
                                            <div class="px-6 py-6">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-red-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm">
                                                        <p class="text-gray-700 leading-relaxed">
                                                            Are you sure you want to permanently delete
                                                            <span
                                                                class="font-medium text-red-600">{{ $displayName }}</span>?
                                                            This action cannot be undone.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Footer -->
                                            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                                                <form action="{{ route("{$routePrefix}.forceDelete", $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="tab"
                                                        value="{{ $tab }}">
                                                    <div class="flex justify-end space-x-3">
                                                        <button type="button"
                                                            onclick="hideModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                            class="px-4 py-2 text-sm font-medium border border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-lg transition-all duration-300 cursor-pointer">
                                                            Cancel
                                                        </button>
                                                        <button type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-secondary border border-red-600 rounded-lg hover:bg-white hover:text-secondary focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 cursor-pointer">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error(`Modal with ID ${modalId} not found`);
                return;
            }
            const transformElement = modal.querySelector('.transform');
            if (!transformElement) {
                console.error(`Transform element not found in modal ${modalId}`);
                return;
            }
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                transformElement.classList.remove('scale-95');
            }, 10);
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error(`Modal with ID ${modalId} not found`);
                return;
            }
            const transformElement = modal.querySelector('.transform');
            if (!transformElement) {
                console.error(`Transform element not found in modal ${modalId}`);
                return;
            }
            modal.classList.add('opacity-0');
            transformElement.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function closeModal(event, element) {
            if (event.target === element) {
                const modalId = element.id;
                hideModal(modalId);
            }
        }

        // Expose functions to global scope for onclick handlers
        window.showModal = showModal;
        window.hideModal = hideModal;
        window.closeModal = closeModal;
    });
</script>

@props(['items', 'routePrefix', 'title', 'emptyMessage', 'tab'])

<div class="mb-12">
    <h2 class="text-2xl text-center mb-8 dark:text-gray-100">{{ $title }}</h2>

    @if ($items->isEmpty())
        <div class="text-center text-gray-600 py-12">{{ $emptyMessage }}</div>
    @else
        <div class="bg-white dark:bg-gray-800  rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Name
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Deleted
                                At</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($items as $item)
                            {{-- Fallback for Staff Name --}}
                            @php
                                $displayName = $item->name ?? trim($item->first_name . ' ' . $item->last_name);
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors duration-200 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $displayName }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $item->deleted_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <!-- Restore Button -->
                                        @if (auth()->user()->hasRole('Admin') || ($routePrefix === 'staff' && auth()->user()->hasRole('Office Manager')))
                                            <button type="button"
                                                onclick="showModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                class="text-primary hover-underline cursor-pointer text-sm font-medium">
                                                Restore
                                            </button>
                                        @endif
                                        
                                        @if (auth()->user()->hasRole('Admin') || ($routePrefix === 'staff' && auth()->user()->hasRole('Office Manager')))
                                            <!-- Permanently Delete Button -->
                                            <button type="button"
                                                onclick="showModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                class="text-secondary hover-underline-delete cursor-pointer text-sm font-medium">
                                                Delete Permanently
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Restore Modal -->
                                    <div id="restoreModal-{{ $routePrefix }}-{{ $item->id }}"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
                                        onclick="closeModal(event, this)">
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-primary"
                                            onclick="event.stopPropagation()">
                                            <!-- Modal Header -->
                                            <div class="px-6 py-4 border-b border-primary dark-border-b-primary">
                                                <div class="flex items-center justify-between">
                                                    <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm <span
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
                                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/restore.png"
                                                                class="h-8 w-8" alt="Restore">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                                            Are you sure you want to restore
                                                            <span
                                                                class="font-medium text-blue-600">{{ $displayName }}</span>?
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Footer -->
                                            <div class="px-6 py-4 bg-white rounded-b-2xl dark:bg-gray-800">
                                                <form action="{{ route("{$routePrefix}.restore", $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="tab" value="{{ $tab }}">
                                                    <div class="flex justify-end space-x-3">
                                                        <button type="button"
                                                            onclick="hideModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                            class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                                                            Cancel
                                                        </button>
                                                        <button type="submit"
                                                            class="bg-primary text-white text-sm font-medium px-4 py-2 bg-primary rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                                                            Restore
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    @if (auth()->user()->hasRole('Admin') || ($routePrefix === 'staff' && auth()->user()->hasRole('Office Manager')))
                                        <div id="deleteModal-{{ $routePrefix }}-{{ $item->id }}"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
                                            onclick="closeModal(event, this)">
                                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-secondary"
                                                onclick="event.stopPropagation()">
                                                <!-- Modal Header -->
                                                <div class="px-6 py-4 border-b border-secondary">
                                                    <div class="flex items-center justify-between">
                                                        <h2 class="text-xl text-gray-900 dark:text-gray-300">Confirm
                                                            <span class="text-secondary">Permanent Deletion</span>
                                                        </h2>
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
                                                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                                                                    class="w-8 h-8" alt="Warning">
                                                            </div>
                                                        </div>
                                                        <div class="text-sm">
                                                            <p class="text-gray-700 leading-relaxed dark:text-gray-300">
                                                                Are you sure you want to permanently delete
                                                                <span
                                                                    class="font-medium text-red-600">{{ $displayName }}</span>?
                                                                This action cannot be undone.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Footer -->
                                                <div class="px-6 py-4 bg-white rounded-b-2xl dark:bg-gray-800">
                                                    <form action="{{ route("{$routePrefix}.forceDelete", $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="tab"
                                                            value="{{ $tab }}">
                                                        <div class="flex justify-end space-x-3">
                                                            <button type="button"
                                                                onclick="hideModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                                class="px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                                                                Cancel
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

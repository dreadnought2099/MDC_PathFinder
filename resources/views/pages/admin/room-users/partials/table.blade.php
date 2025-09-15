<!-- Office Users Table -->
<div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                    <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Name
                    </th>
                    <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Username
                    </th>
                    <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Room
                    </th>
                    <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Position
                    </th>
                    <th class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600 font-sofia">
                @foreach ($users as $u)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $u->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $u->username }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $u->room->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $u->getRoleNames()->implode(', ') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 flex items-center space-x-3">
                            <!-- Edit -->
                            <div class="relative inline-block group">
                                <a href="{{ route('room-user.edit', $u->id) }}"
                                    class="hover-underline-edit hover:scale-115 transform transition duration-200">
                                    <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon"
                                        class="w-8 h-8 object-contain">
                                </a>
                                <div
                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                        text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                        group-hover:opacity-100 group-hover:visible transition-all duration-300 
                        whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                    Edit User
                                    <div
                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                            border-l-4 border-l-gray-900 dark:border-l-gray-700
                            border-t-4 border-t-transparent 
                            border-b-4 border-b-transparent">
                                    </div>
                                </div>
                            </div>

                            <!-- Delete -->
                            @can('delete', $u)
                                <div class="relative inline-block group">
                                    <button onclick="openUserModal('{{ $u->id }}', '{{ addslashes($u->username) }}')"
                                        class="hover-underline-delete hover:scale-115 transform transition duration-200">
                                        <img src="{{ asset('icons/trash.png') }}" alt="Delete Icon"
                                            class="w-8 h-8 object-contain">
                                    </button>
                                    <div
                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
            text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
            group-hover:opacity-100 group-hover:visible transition-all duration-300 
            whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                        Delete User
                                        <div
                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                border-l-4 border-l-gray-900 dark:border-l-gray-700
                border-t-4 border-t-transparent 
                border-b-4 border-b-transparent">
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- User Delete Modal -->
<div id="userDeleteModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0"
    onclick="closeUserModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 dark:bg-gray-800 border border-secondary"
        onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-secondary">
            <div class="flex items-center justify-between">
                <h2 class="text-xl text-gray-900 dark:text-gray-300">
                    Confirm <span class="text-secondary">Deletion</span>
                </h2>
                <button onclick="closeUserModal()"
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
                    <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                        Are you sure you want to delete user <span id="userName" class="text-red-600"></span>?
                        This action cannot be undone.
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl dark:bg-gray-800">
            <form id="userDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeUserModal()"
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

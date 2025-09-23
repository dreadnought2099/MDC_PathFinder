<div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                    <th
                        class="px-4 sm:px-6 py-2 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Name</th>
                    <th
                        class="px-4 sm:px-6 py-2 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Username</th>
                    <th
                        class="px-4 sm:px-6 py-2 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Room</th>
                    <th
                        class="px-4 sm:px-6 py-2 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Position</th>
                    <th
                        class="px-4 sm:px-6 py-2 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600 font-sofia">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                        <td class="px-4 sm:px-6 py-2 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $user->name ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-2 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $user->username }}</td>
                        <td class="px-4 sm:px-6 py-2 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $user->room->name ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-2 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $user->getRoleNames()->implode(', ') ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-2 flex flex-wrap items-center space-x-1 sm:space-x-3">
                            <!-- Actions icons -->
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view room users'))
                                <div class="relative inline-block group">
                                    <a href="{{ route('room-user.show', $user->id) }}"
                                        class="hover-underline hover:scale-110 transform transition duration-200">
                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                            alt="View Icon" class="w-6 sm:w-8 h-6 sm:h-8 object-contain">
                                    </a>
                                    <!-- Tooltip -->
                                    <div
                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                                                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                                                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                                                whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                        View User
                                        <div
                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                                                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                                                    border-t-4 border-t-transparent 
                                                    border-b-4 border-b-transparent">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (auth()->user()->hasRole('Admin'))
                                <div class="relative inline-block group">
                                    <a href="{{ route('room-user.edit', $user->id) }}"
                                        class="hover-underline-edit hover:scale-110 transform transition duration-200">
                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                            alt="Edit Icon" class="w-6 sm:w-8 h-6 sm:h-8 object-contain">
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
                                <div class="relative inline-block group">
                                    <button type="button"
                                        onclick="openToggleModal('{{ $user->id }}', '{{ $user->username }}', '{{ $user->is_active ? 'disable' : 'enable' }}')"
                                        class="hover:scale-110 transform transition duration-200 cursor-pointer
                                                {{ $user->is_active ? 'hover-underline-delete' : 'hover-underline' }}">
                                        <img src="{{ $user->is_active
                                            ? 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/off.png'
                                            : 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/on.png' }}"
                                            alt="Toggle Active" class="w-6 sm:w-8 h-6 sm:h-8 object-contain">
                                    </button>

                                    <!-- Tooltip -->
                                    <div
                                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                                            text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                                            group-hover:opacity-100 group-hover:visible transition-all duration-300 
                                            whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                        {{ $user->is_active ? 'Disable User' : 'Enable User' }}
                                        <div
                                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                                                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                                                    border-t-4 border-t-transparent 
                                                    border-b-4 border-b-transparent">
                                        </div>
                                    </div>
                                </div>
                                <div class="relative inline-block group">
                                    <button
                                        onclick="openUserModal('{{ $user->id }}', '{{ addslashes($user->username) }}')"
                                        class="hover-underline-delete hover:scale-110 transform transition duration-200 cursor-pointer">
                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/trash.png"
                                            alt="Delete Icon" class="w-6 sm:w-8 h-6 sm:h-8 object-contain">
                                    </button>
                                    <!-- Tooltip -->
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
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modals -->
<div id="userToggleModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0">
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xs sm:max-w-md mx-4 transform transition-all duration-300 scale-95 border border-secondary">
        <!-- Header -->
        <div class="px-4 sm:px-6 py-3 border-b border-secondary flex items-center justify-between">
            <h2 class="text-base sm:text-lg text-gray-900 dark:text-gray-300">Confirm <span class="text-secondary"
                    id="toggleActionLabel"></span></h2>
            <button onclick="closeToggleModal()"
                class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <!-- Body -->
        <div class="px-4 sm:px-6 py-3">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                            class="w-6 sm:w-8 h-6 sm:h-8" alt="Warning">
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Are you sure you want to <span id="toggleActionText"></span> user <span id="toggleUserName"
                        class="text-secondary"></span>?
                </p>
            </div>
            <div
                class="px-0 sm:px-6 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-2xl flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                <form id="userToggleForm" method="POST"
                    class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    @csrf
                    @method('PATCH')
                    <button type="button" onclick="closeToggleModal()"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="userDeleteModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300 opacity-0">
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xs sm:max-w-md mx-4 transform transition-all duration-300 scale-95 border border-secondary">
        <!-- Header -->
        <div class="px-4 sm:px-6 py-3 border-b border-secondary flex items-center justify-between">
            <h2 class="text-base sm:text-lg text-gray-900 dark:text-gray-300">Confirm <span
                    class="text-secondary">Deletion</span></h2>
            <button onclick="closeUserModal()"
                class="text-gray-400 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <!-- Body -->
        <div class="px-4 sm:px-6 py-3">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                            class="w-6 sm:w-8 h-6 sm:h-8" alt="Warning">
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Are you sure you want to delete user <span id="userName" class="text-red-600"></span>? This
                    action cannot be undone.
                </p>
            </div>
            <div
                class="px-0 sm:px-6 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-2xl flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                <form id="userDeleteForm" method="POST"
                    class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeUserModal()"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

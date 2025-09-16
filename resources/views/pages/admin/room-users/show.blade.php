@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Office User Details</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        View office user information and details
                    </p>
                </div>

                @can('update', $user)
                    <div class="mt-4 sm:mt-0 sm:ml-4 flex space-x-3">
                        <div class="relative inline-block group">
                            <a href="{{ route('room-user.edit', $user->id) }}"
                                class="hover:scale-115 transition-transform duration-300">
                                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon" class="w-8 h-8 object-contain">
                            </a>

                            <!-- Tooltip -->
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
                    </div>
                @endcan
            </div>
        </div>

        <!-- User Information Card -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 rounded-full bg-white/20 flex items-center justify-center">
                            @if ($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}"
                                    alt="{{ $user->name ?? $user->username }}"
                                    class="h-20 w-20 rounded-full object-cover border-4 border-white/30">
                            @else
                                <svg class="h-12 w-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <h2 class="text-xl font-bold text-white">
                            {{ $user->name ?? 'No Name Set' }}
                        </h2>
                        <p class="text-primary-light">@{{ $user - > username }}</p>
                        <div class="mt-2 flex items-center text-sm text-primary-light">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $user->room ? $user->room->name : 'No Room Assigned' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details -->
            <div class="px-6 py-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->name ?? 'Not provided' }}
                        </dd>
                    </div>

                    <!-- Username -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->username }}
                        </dd>
                    </div>

                    <!-- Room -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Room</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if ($user->room)
                                <div class="flex items-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">
                                        {{ $user->room->name }}
                                    </span>
                                    @if ($user->room->description)
                                        <span class="ml-2 text-gray-500 dark:text-gray-400">
                                            {{ $user->room->description }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">No room assigned</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Role -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if ($user->roles->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $role->name === 'Admin'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                : ($role->name === 'Room Manager'
                                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">No roles assigned</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Email Verification -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Status</dt>
                        <dd class="mt-1 text-sm">
                            @if ($user->email_verified_at)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Not Verified
                                </span>
                            @endif
                        </dd>
                    </div>

                    <!-- Account Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</dt>
                        <dd class="mt-1 text-sm">
                            @if ($user->deleted_at)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Deleted
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Active
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Timestamps -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 text-sm">
                    <div>
                        <span class="font-medium text-gray-500 dark:text-gray-400">Created:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">
                            {{ $user->created_at ? $user->created_at->format('M d, Y g:i A') : 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500 dark:text-gray-400">Last Updated:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">
                            {{ $user->updated_at ? $user->updated_at->format('M d, Y g:i A') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Panel (Admin Only) -->
        @can('delete', $user)
            <div class="mt-8">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Danger Zone
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <p>Once you delete this user, all of their data will be archived. This action can be undone from
                                    the recycle bin.</p>
                            </div>
                            <div class="mt-4">
                                <button type="button"
                                    onclick="openUserDeleteModal('{{ $user->username }}', '{{ route('room-user.destroy', $user) }}')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <img src="{{ asset('icons/trash.png') }}" alt="Delete Icon"
                                        class="w-8 h-8 object-contain">
                                    Delete User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Delete Confirmation Modal -->
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
                            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm leading-relaxed dark:text-gray-300">
                            Are you sure you want to delete user <span id="userName"
                                class="text-red-600 font-semibold"></span>?
                            This action can be undone from the recycle bin.
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
@endsection

@push('scripts')
    <script>
        function openUserDeleteModal(userName, deleteRoute) {
            // Set the user name in the modal
            document.getElementById('userName').textContent = userName;

            // Set the form action to the delete route
            document.getElementById('userDeleteForm').action = deleteRoute;

            // Show the modal with animation
            const modal = document.getElementById('userDeleteModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-95');
            }, 10);
        }

        function closeUserModal() {
            // Hide the modal with animation
            const modal = document.getElementById('userDeleteModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUserModal();
            }
        });
    </script>
@endpush

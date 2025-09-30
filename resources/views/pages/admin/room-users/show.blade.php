@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-8 relative">
            <!-- Centered Header -->
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-300">
                    {{-- Auth::user() is only used for permission checks, not for displaying the target user’s info. --}}
                    <span class="text-primary">{{ $user->name ?? $user->username }}</span> Office User
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    View office user information and details
                </p>
            </div>

            <!-- Edit Button (absolute right) -->
            @can('update', $user)
                <div class="absolute top-0 right-0 flex space-x-3">
                    <div class="relative inline-block group">
                        <a href="{{ route('room-user.edit', $user->id) }}" class="hover-underline-delete inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200 appearance-none bg-transparent border-0 cursor-pointer">
                            <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon"
                                class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
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
        
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border-2 border-primary overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <div class="mt-2 flex items-center text-sm dark:text-gray-300">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/location.png"
                                class="block h-4 w-4 object-contain" alt="Location Icon">
                            {{ $user->room ? $user->room->name : 'No Office Assigned' }}
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Office</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if ($user->room)
                                <div class="flex items-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $user->room->name }}
                                    </span>
                                    @if ($user->room->description)
                                        <span class="ml-2 text-gray-500 dark:text-gray-400">
                                            {{ $user->room->description }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">No office assigned</span>
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
                                                : ($role->name === 'Office Manager'
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
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png"
                                        alt="Warning Icon" class="block w-3 h-3 object-contain">
                                    Email Unavailable
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
        </div>
    </div>
@endsection

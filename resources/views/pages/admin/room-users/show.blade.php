@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-8 relative">
            <!-- Centered Header -->
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-300">
                    {{-- Auth::user() is only used for permission checks, not for displaying the target user's info. --}}
                    <span class="text-primary">{{ $user->name ?? $user->username }}</span> User
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    View office user information and details
                </p>
            </div>

            <!-- Edit Button - Absolute positioning for larger screens, centered for mobile -->
            @can('update', $user)
                <div class="flex justify-center mt-4 sm:mt-0 sm:absolute sm:top-0 sm:right-0">
                    <div class="relative inline-block group">
                        <a href="{{ route('room-user.edit', $user->id) }}"
                            class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200 appearance-none bg-transparent border-0 cursor-pointer">
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="bg-gradient-to-tr from-blue-500 to-blue-700 px-8 py-10">
                <div class="flex items-center gap-4">
                    <div
                        class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-2xl font-bold">
                        {{ substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white">{{ $user->name ?? 'Not provided' }}</h2>
                        <p class="text-blue-100 mt-1">{{ $user->username }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Assigned Office -->
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned
                            Office</h3>
                        @if ($user->room)
                            <div class="flex items-center gap-3">
                                <span
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $user->room->name }}
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No office assigned</p>
                        @endif
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role
                        </h3>
                        @if ($user->roles->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="px-3 py-1.5 rounded-lg text-sm font-medium
                                {{ $role->name === 'Admin'
                                    ? 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                    : ($role->name === 'Office Manager'
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No roles assigned</p>
                        @endif
                    </div>

                    <!-- Email Status -->
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email
                            Status</h3>
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Verified
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png"
                                    alt="Warning" class="w-4 h-4">
                                Email Unavailable
                            </span>
                        @endif
                    </div>

                    <!-- Account Status -->
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account
                            Status</h3>
                        @if (!$user->is_active)
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                Deactivated
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-8 py-4 border-t border-gray-200 dark:border-gray-600">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Member since:</span>
                    <span class="ml-2">{{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white dark:bg-gray-900 py-6 px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 dark:text-white mb-3">
                    <span class="text-primary">Admin</span> Dashboard
                </h1>
                <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300">Manage your organization's staff and office
                    spaces</p>
            </div>

            <!-- Quick Stats Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-2 border-primary mb-8">
                <h2 class="text-xl font-semibold text-primary mb-6 text-center">Quick Overview</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Total Staff -->
                    <div
                        class="text-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-primary dark:border-blue-800">
                        <div class="text-2xl lg:text-3xl font-bold text-blue-600 mb-1 dark:text-blue-400">
                            {{ \App\Models\Staff::count() }}
                        </div>
                        <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Total Staff</div>
                    </div>

                    <!-- Total Rooms -->
                    <div
                        class="text-center p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-tertiary dark:border-green-800">
                        <div class="text-2xl lg:text-3xl font-bold text-green-600 mb-1 dark:text-green-400">
                            {{ \App\Models\Room::count() }}
                        </div>
                        <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Total Rooms</div>
                    </div>

                    <!-- Assignments -->
                    <div
                        class="text-center p-4 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple dark:border-purple-800">
                        <div class="text-2xl lg:text-3xl font-bold text-purple-600 mb-1 dark:text-purple-400">
                            {{ \App\Models\Staff::whereNotNull('room_id')->count() }}
                        </div>
                        <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Assignments</div>
                    </div>

                    <!-- Paths -->
                    <div
                        class="text-center p-4 bg-orange-50 dark:bg-orange-900/30 rounded-lg border border-orange dark:border-orange-800">
                        <div class="text-2xl lg:text-3xl font-bold text-orange-600 mb-1 dark:text-orange-400">
                            {{ \App\Models\Path::whereHas('fromRoom')->whereHas('toRoom')->count() }}
                        </div>
                        <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Paths</div>
                    </div>
                </div>
            </div>

            <!-- Floating Actions Component -->
            <div class="mb-6">
                <x-floating-actions />
            </div>

            <!-- Main Actions Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

                <a href="{{ route('path.index') }}"
                    class="group card-shadow-hover shadow-primary-hover p-5 border-2 border-primary bg-white dark:bg-gray-800 rounded-lg transition-all duration-300">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="bg-primary-10 hover:bg-primary-20 p-3 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/navigation.png') }}" alt="Manage Paths"
                                class="h-10 w-10 object-contain group-hover:scale-120 transition-transform duration-300" />
                        </div>
                        <div>
                            <h3
                                class="text-base font-medium text-gray-800 dark:text-white group-hover:text-primary transition-colors">
                                Path Navigation
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Manage office paths</p>
                        </div>
                    </div>
                </a>

                <!-- Manage Rooms Card -->
                <a href="{{ route('room.index') }}"
                    class="group card-shadow-hover shadow-primary-hover p-5 border-2 border-primary bg-white dark:bg-gray-800 rounded-lg transition-all duration-300">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="bg-primary-10 hover:bg-primary-20 p-3 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/manage-office1.png') }}" alt="Manage Offices"
                                class="h-10 w-10 object-contain group-hover:scale-120 transition-transform duration-300" />
                        </div>
                        <div>
                            <h3
                                class="text-base font-medium text-gray-800 dark:text-white group-hover:text-primary transition-colors">
                                Manage Offices
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Configure office spaces</p>
                        </div>
                    </div>
                </a>

                <!-- Manage Staff Card -->
                <a href="{{ route('staff.index') }}"
                    class="group card-shadow-hover shadow-primary-hover p-5 border-2 border-primary bg-white dark:bg-gray-800 rounded-lg transition-all duration-300">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="bg-primary-10 hover:bg-primary-20 p-3 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/manager-1.png') }}" alt="Manage Staff"
                                class="h-10 w-10 object-contain group-hover:scale-120 transition-transform duration-300" />
                        </div>
                        <div>
                            <h3
                                class="text-base font-medium text-gray-800 dark:text-white group-hover:text-primary transition-colors">
                                Manage Staff
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Organize staff members</p>
                        </div>
                    </div>
                </a>

                <!-- Assign Staff Card -->
                <a href="{{ route('room.assign') }}"
                    class="group card-shadow-hover shadow-primary-hover p-5 border-2 border-primary bg-white dark:bg-gray-800 rounded-lg transition-all duration-300">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="bg-primary-10 hover:bg-primary-20 p-3 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/assign-staff.png') }}" alt="Assign Staff"
                                class="h-10 w-10 object-contain group-hover:scale-120 transition-transform duration-300" />
                        </div>
                        <div>
                            <h3
                                class="text-base font-medium text-gray-800 dark:text-white group-hover:text-primary transition-colors">
                                Assign Staff
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Link staff to rooms</p>
                        </div>
                    </div>
                </a>

                <!-- Recycle Bin Card -->
                <a href="{{ route('room.recycle-bin') }}"
                    class="group card-shadow-hover shadow-primary-hover p-5 border-2 border-primary bg-white dark:bg-gray-800 rounded-lg transition-all duration-300">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="bg-primary-10 hover:bg-primary-20 p-3 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/recycle-bin.png') }}" alt="Recycle Bin"
                                class="h-10 w-10 object-contain group-hover:scale-120 transition-transform duration-300" />
                        </div>
                        <div>
                            <h3
                                class="text-base font-medium text-gray-800 dark:text-white group-hover:text-primary transition-colors">
                                Recycle Bin
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Restore deleted items</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

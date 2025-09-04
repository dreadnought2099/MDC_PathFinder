@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white dark:bg-gray-900 py-6 sm:py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="text-center mb-8 sm:mb-12 lg:mb-16">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-800 dark:text-white mb-3 sm:mb-4 lg:mb-6 leading-tight">
                    <span class="text-primary">Admin</span> Dashboard
                </h1>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto px-4">
                    Manage your organization's staff and office spaces
                </p>
            </div>

            <!-- Floating Actions Component -->
            <div class="mb-6 sm:mb-8 lg:mb-10">
                <x-floating-actions />
            </div>

            <!-- Main Actions Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mb-8 sm:mb-12 lg:mb-16">

                <!-- Path Navigation Card -->
                <div class="col-span-1">
                    <a href="{{ route('path.index') }}"
                        class="group card-shadow-hover shadow-primary-hover block h-full p-4 sm:p-6 lg:p-8 border-2 border-primary bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center space-y-3 sm:space-y-4 lg:space-y-6 h-full justify-center">
                            <div class="bg-primary-10 hover:bg-primary-20 p-3 sm:p-4 lg:p-5 rounded-full transition-all duration-300 group-hover:scale-110">
                                <img src="{{ asset('icons/navigation.png') }}" alt="Manage Room/Office"
                                    class="h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 object-contain" />
                            </div>
                            <div class="space-y-2 sm:space-y-3">
                                <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 dark:text-white group-hover:text-primary transition-colors leading-tight">
                                    Manage Path Navigations
                                </h3>
                                <p class="text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Manage paths between offices
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Manage Rooms Card -->
                <div class="col-span-1">
                    <a href="{{ route('room.index') }}"
                        class="group card-shadow-hover shadow-primary-hover block h-full p-4 sm:p-6 lg:p-8 border-2 border-primary bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center space-y-3 sm:space-y-4 lg:space-y-6 h-full justify-center">
                            <div class="bg-primary-10 hover:bg-primary-20 p-3 sm:p-4 lg:p-5 rounded-full transition-all duration-300 group-hover:scale-110">
                                <img src="{{ asset('icons/manage-office1.png') }}" alt="Manage Room/Office"
                                    class="h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 object-contain" />
                            </div>
                            <div class="space-y-2 sm:space-y-3">
                                <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 dark:text-white group-hover:text-primary transition-colors leading-tight">
                                    Manage Offices
                                </h3>
                                <p class="text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Configure office spaces and rooms
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Manage Staff Card -->
                <div class="col-span-1">
                    <a href="{{ route('staff.index') }}"
                        class="group card-shadow-hover shadow-primary-hover block h-full p-4 sm:p-6 lg:p-8 border-2 border-primary bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center space-y-3 sm:space-y-4 lg:space-y-6 h-full justify-center">
                            <div class="bg-primary-10 hover:bg-primary-20 p-3 sm:p-4 lg:p-5 rounded-full transition-all duration-300 group-hover:scale-110">
                                <img src="{{ asset('icons/manager-1.png') }}" alt="Manage Staff"
                                    class="h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 object-contain" />
                            </div>
                            <div class="space-y-2 sm:space-y-3">
                                <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 dark:text-white group-hover:text-primary transition-colors leading-tight">
                                    Manage Staff
                                </h3>
                                <p class="text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Organize staff members
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Assign Staff Card -->
                <div class="col-span-1">
                    <a href="{{ route('room.assign') }}"
                        class="group card-shadow-hover shadow-primary-hover block h-full p-4 sm:p-6 lg:p-8 border-2 border-primary bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center space-y-3 sm:space-y-4 lg:space-y-6 h-full justify-center">
                            <div class="bg-primary-10 hover:bg-primary-20 p-3 sm:p-4 lg:p-5 rounded-full transition-all duration-300 group-hover:scale-110">
                                <img src="{{ asset('icons/assign-staff.png') }}" alt="Assign Staff"
                                    class="h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 object-contain" />
                            </div>
                            <div class="space-y-2 sm:space-y-3">
                                <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 dark:text-white group-hover:text-primary transition-colors leading-tight">
                                    Assign Staff
                                </h3>
                                <p class="text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Link staff members to rooms
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Recycle Bin Card - spans 2 columns on sm, 1 on lg+ -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-1 sm:max-w-sm sm:mx-auto lg:max-w-none">
                    <a href="{{ route('room.recycle-bin') }}"
                        class="group card-shadow-hover shadow-primary-hover block h-full p-4 sm:p-6 lg:p-8 border-2 border-primary bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center space-y-3 sm:space-y-4 lg:space-y-6 h-full justify-center">
                            <div class="bg-primary-10 hover:bg-primary-20 p-3 sm:p-4 lg:p-5 rounded-full transition-all duration-300 group-hover:scale-110">
                                <img src="{{ asset('icons/recycle-bin.png') }}" alt="Recycle Bin"
                                    class="h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 object-contain" />
                            </div>
                            <div class="space-y-2 sm:space-y-3">
                                <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 dark:text-white group-hover:text-primary transition-colors leading-tight">
                                    Recycle Bin
                                </h3>
                                <p class="text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Restore or permanently delete items
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-6 sm:p-8 lg:p-10 border-2 border-primary dark:border-gray-700">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mb-6 sm:mb-8 lg:mb-10 dark:text-white text-center">
                    Quick Overview
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">

                    <!-- Total Staff -->
                    <div class="text-center p-4 sm:p-6 lg:p-8 bg-blue-50 dark:bg-blue-900/50 rounded-lg sm:rounded-xl border-2 border-primary dark:border-gray-700 hover:shadow-md transition-all duration-300">
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-2 sm:mb-3 lg:mb-4 dark:text-blue-400">
                            {{ \App\Models\Staff::count() }}
                        </div>
                        <div class="text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-300 font-medium">
                            Total Staff Members
                        </div>
                    </div>

                    <!-- Total Rooms -->
                    <div class="text-center p-4 sm:p-6 lg:p-8 bg-green-50 dark:bg-green-900/50 rounded-lg sm:rounded-xl border-2 border-green-600 dark:border-green-400 hover:shadow-md transition-all duration-300">
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-600 mb-2 sm:mb-3 lg:mb-4 dark:text-green-400">
                            {{ \App\Models\Room::count() }}
                        </div>
                        <div class="text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-300 font-medium">
                            Total Rooms
                        </div>
                    </div>

                    <!-- Assignments -->
                    <div class="text-center p-4 sm:p-6 lg:p-8 bg-purple-50 dark:bg-purple-900/50 rounded-lg sm:rounded-xl border-2 border-purple-600 dark:border-purple-400 hover:shadow-md transition-all duration-300 sm:col-span-2 lg:col-span-1">
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-purple-600 mb-2 sm:mb-3 lg:mb-4 dark:text-purple-400">
                            {{ \App\Models\Staff::whereNotNull('room_id')->count() }}
                        </div>
                        <div class="text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-300 font-medium">
                            Staff Assignments
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
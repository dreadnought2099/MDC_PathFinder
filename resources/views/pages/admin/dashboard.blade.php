@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-gray-800 mb-4">
                    <span class="text-primary">Admin</span> Dashboard
                </h1>
                <p class="text-lg text-gray-600">Manage your organization's staff and office spaces</p>
            </div>

            <!-- Floating Actions Component -->
            <div class="mb-8">
                <x-floating-actions />
            </div>

            <!-- Main Actions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Manage Staff Card -->
                <a href="{{ route('staff.index') }}" class="group card-shadow-hover shadow-primary-hover p-6">

                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="bg-primary-10 hover:bg-primary-20 p-4 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/manager-1.png') }}" alt="Manage Staff"
                                class="h-12 w-12 object-contain" />
                        </div>
                        <div>
                            <h3
                                class="text-lg text-gray-800 group-hover:text-primary transition-colors duration-300">
                                Manage Staff
                            </h3>
                            <p class="text-sm text-gray-600 mt-1 group-hover:text-gray-700 transition-colors duration-300">
                                Organize staff members
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Manage Rooms Card -->
                <a href="{{ route('room.index') }}"
                    class="group card-shadow-hover shadow-primary-hover p-6">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div
                            class="bg-primary-10 hover:bg-primary-20 p-4 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/manage-office1.png') }}" alt="Manage Room/Office"
                                class="h-12 w-12 object-contain" />
                        </div>
                        <div>
                            <h3
                                class="text-lg text-gray-800 group-hover:text-primary transition-colors duration-300">
                                Manage Rooms/Offices
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Configure office spaces and rooms</p>
                        </div>
                    </div>
                </a>

                <!-- Assign Staff Card -->
                <a href="{{ route('room.assign') }}"
                    class="group card-shadow-hover shadow-primary-hover p-6">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div
                            class="bg-primary-10 hover:bg-primary-20 p-4 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/assign-staff.png') }}" alt="Assign Staff"
                                class="h-12 w-12 object-contain" />
                        </div>
                        <div>
                            <h3
                                class="text-lg text-gray-800 group-hover:text-primary transition-colors duration-300">
                                Assign Staff
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Link staff members to rooms</p>
                        </div>
                    </div>
                </a>

                <!-- Recycle Bin Card -->
                <a href="{{ route('room.recycle-bin') }}"
                    class="group card-shadow-hover shadow-primary-hover p-6">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="bg-primary-10 hover:bg-primary-20 p-4 rounded-full transition-all duration-300">
                            <img src="{{ asset('icons/recycle-bin.png') }}" alt="Recycle Bin"
                                class="h-12 w-12 object-contain" />
                        </div>
                        <div>
                            <h3
                                class="text-lg text-gray-800 group-hover:text-primary transition-colors duration-300">
                                Recycle Bin
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Restore or permanently delete items</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Quick Stats Section (Optional) -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl text-gray-800 mb-6 text-center">Quick Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Total Staff -->
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600 mb-2">
                            {{ \App\Models\Staff::count() }}
                        </div>
                        <div class="text-sm text-gray-600">Total Staff Members</div>
                    </div>

                    <!-- Total Rooms -->
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600 mb-2">
                            {{ \App\Models\Room::count() }}
                        </div>
                        <div class="text-sm text-gray-600">Total Rooms</div>
                    </div>

                    <!-- Assignments -->
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600 mb-2">
                            {{ \App\Models\Staff::whereNotNull('room_id')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Staff Assignments</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

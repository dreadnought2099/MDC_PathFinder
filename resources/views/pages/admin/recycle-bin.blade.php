@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <x-modal-scripts />
    <div x-data="{ tab: sessionStorage.getItem('activeTab') || 'rooms' }" x-init="sessionStorage.setItem('activeTab', tab);
    $watch('tab', value => sessionStorage.setItem('activeTab', value))" class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Navigation -->
        <nav class="mb-6 flex justify-center space-x-6">
            <button @click="tab = 'rooms'"
                :class="{ 'text-primary border-b-2': tab === 'rooms', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'rooms' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Rooms
            </button>
            <button @click="tab = 'staff'"
                :class="{ 'text-primary border-b-2': tab === 'staff', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'staff' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Staff
            </button>
        </nav>

        <!-- Content Container with smooth transitions -->
        <div class="relative min-h-96">
            <!-- Rooms Tab Content -->
            <div x-show="tab === 'rooms'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-4" class="absolute w-full">
                <x-recycle-bin-table :items="$rooms" route-prefix="room" title="Trashed Rooms"
                    empty-message="No trashed rooms found." tab="rooms" />
            </div>

            <!-- Staff Tab Content -->
            <div x-show="tab === 'staff'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-4" class="absolute w-full">
                <x-recycle-bin-table :items="$staffs" route-prefix="staff" title="Trashed Staff Members"
                    empty-message="No trashed staff members found." tab="staff" />
            </div>
        </div>
    </div>
@endsection

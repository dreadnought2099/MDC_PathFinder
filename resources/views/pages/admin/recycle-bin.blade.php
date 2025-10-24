@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <div x-data="{
        tab: sessionStorage.getItem('activeTab') || 'rooms',
        setTab(newTab) {
            this.tab = newTab;
            sessionStorage.setItem('activeTab', newTab);
            // Dispatch custom event to notify navbar
            window.dispatchEvent(new CustomEvent('tab-changed', { detail: { tab: newTab } }));
        }
        }" x-init="sessionStorage.setItem('activeTab', tab);
        // Initial dispatch
        window.dispatchEvent(new CustomEvent('tab-changed', { detail: { tab: tab } }));" class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Navigation -->
        <nav class="mb-6 flex justify-center space-x-6">
            <button @click="setTab('rooms')"
                :class="{ 'text-primary border-b-2': tab === 'rooms', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'rooms' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Offices
            </button>

            <button @click="setTab('staff')"
                :class="{ 'text-primary border-b-2': tab === 'staff', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'staff' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Staff
            </button>

            <button @click="setTab('users')"
                :class="{ 'text-primary border-b-2': tab === 'users', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'users' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Users
            </button>
        </nav>

        <!-- Content -->
        <div class="relative min-h-96">
            <!-- Rooms Tab -->
            <div x-show="tab === 'rooms'" x-transition class="absolute w-full">
                <x-recycle-bin-table :items="$rooms" route-prefix="room" title="Trashed Offices"
                    empty-message="No trashed offices found." tab="rooms" />
            </div>

            <!-- Staff Tab -->
            <div x-show="tab === 'staff'" x-transition class="absolute w-full">
                <x-recycle-bin-table :items="$staffs" route-prefix="staff" title="Trashed Staff Members"
                    empty-message="No trashed staff members found." tab="staff" />
            </div>

            <!-- Users Tab -->
            <div x-show="tab === 'users'" x-transition class="absolute w-full">
                <x-recycle-bin-table :items="$users" route-prefix="room-user" title="Trashed Office Users"
                    empty-message="No trashed office users found." tab="users" />
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function hideModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function closeModal(event, modal) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
@endpush
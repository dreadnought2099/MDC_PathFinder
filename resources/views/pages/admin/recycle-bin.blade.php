@extends('layouts.app')

@section('content')
    <div x-data="{
        tab: new URLSearchParams(window.location.search).get('tab') || sessionStorage.getItem('activeTab') || '{{ auth()->user()->hasRole('Admin') ? 'rooms' : 'staff' }}',
        setTab(newTab) {
            this.tab = newTab;
            sessionStorage.setItem('activeTab', newTab);
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', newTab);
            window.history.pushState({}, '', url);
            // Dispatch custom event to notify navbar
            window.dispatchEvent(new CustomEvent('tab-changed', { detail: { tab: newTab } }));
        }
    }" x-init="sessionStorage.setItem('activeTab', tab);
    // Initial dispatch
    window.dispatchEvent(new CustomEvent('tab-changed', { detail: { tab: tab } }));" class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Navigation -->
        <nav class="mb-6 flex justify-center space-x-6">
            @if (auth()->user()->hasRole('Admin'))
                <button @click="setTab('rooms')"
                    :class="{ 'text-primary border-b-2': tab === 'rooms', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'rooms' }"
                    class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                    Trashed Offices
                </button>
            @endif

            <button @click="setTab('staff')"
                :class="{ 'text-primary border-b-2': tab === 'staff', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'staff' }"
                class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                Trashed Staff
            </button>

            @if (auth()->user()->hasRole('Admin'))
                <button @click="setTab('users')"
                    :class="{ 'text-primary border-b-2': tab === 'users', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'users' }"
                    class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                    Trashed Users
                </button>

                <button @click="setTab('feedback')"
                    :class="{ 'text-primary border-b-2': tab === 'feedback', 'text-gray-600 dark:text-gray-100 hover:text-primary': tab !== 'feedback' }"
                    class="px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out hover-underline cursor-pointer transform hover:scale-105 active:scale-95">
                    Trashed Feedback
                </button>
            @endif
        </nav>

        <!-- Content -->
        <div class="relative min-h-96">
            <!-- Rooms Tab -->
            @if (auth()->user()->hasRole('Admin'))
                <div x-show="tab === 'rooms'" x-transition class="absolute w-full">
                    <x-recycle-bin-table :items="$rooms" route-prefix="room" title="Trashed Offices"
                        empty-message="No trashed offices found." tab="rooms" />
                </div>
            @endif

            <!-- Staff Tab -->
            <div x-show="tab === 'staff'" x-transition class="absolute w-full">
                <x-recycle-bin-table :items="$staffs" route-prefix="staff" title="Trashed Staff Members"
                    empty-message="No trashed staff members found." tab="staff" />
            </div>


            @if (auth()->user()->hasRole('Admin'))
                <!-- Users Tab -->
                <div x-show="tab === 'users'" x-transition class="absolute w-full">
                    <x-recycle-bin-table :items="$users" route-prefix="room-user" title="Trashed Office Users"
                        empty-message="No trashed office users found." tab="users" />
                </div>

                <!-- Feedback Tab -->
                <div x-show="tab === 'feedback'" x-transition class="absolute w-full">
                    <x-recycle-bin-table :items="$feedback" route-prefix="feedback" title="Trashed Feedback"
                        empty-message="No trashed feedback found." tab="feedback" />
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Show modal with transition
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) {
                    console.warn(`Modal with ID "${modalId}" not found`);
                    return;
                }

                const transformElement = modal.querySelector('.transform');
                if (!transformElement) {
                    console.warn(`Transform element not found in modal "${modalId}"`);
                    return;
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    transformElement.classList.remove('scale-95');
                }, 10);
            }

            // Hide modal with transition
            function hideModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) {
                    console.warn(`Modal with ID "${modalId}" not found`);
                    return;
                }

                const transformElement = modal.querySelector('.transform');
                if (!transformElement) {
                    console.warn(`Transform element not found in modal "${modalId}"`);
                    return;
                }

                modal.classList.add('opacity-0');
                transformElement.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Close modal when clicking outside content
            function closeModal(event, element) {
                if (event.target === element) {
                    hideModal(element.id);
                }
            }

            // Close any open modal when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.fixed.inset-0:not(.hidden)').forEach(modal => {
                        if (modal.id) hideModal(modal.id);
                    });
                }
            });

            // Expose functions globally for onclick bindings
            window.showModal = showModal;
            window.hideModal = hideModal;
            window.closeModal = closeModal;
        });
    </script>
@endpush

@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
        <!-- Page Title -->
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                <img-reveal>
                    <span class="trigger-text text-primary">Office User</span>
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/gif/spider-hacker.gif"
                        alt="GIF" class="reveal-img">
                </img-reveal>
                Management
            </h1>
            <p class="text-sm sm:text-base lg:text-base text-gray-700 dark:text-gray-300 mt-1 sm:mt-2">
                Manage users across different offices.
            </p>
        </div>

        <!-- Filter Dropdown -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-start mb-6 space-y-2 sm:space-y-0 sm:space-x-4"
            x-data="{
                roomId: '{{ $roomId }}',
                search: '',
                isOpen: false,
                selectedName: @js($rooms->firstWhere('id', $roomId)?->name ?? 'All Office Users'),
                filteredRooms: [{ id: '', name: 'All Office Users' }, ...@js($rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values())],
            
                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.search = '';
                        this.filteredRooms = [{ id: '', name: 'All Office Users' }, ...@js($rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values())];
                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    }
                },
                closeDropdown() {
                    this.isOpen = false;
                    this.search = '';
                    this.filteredRooms = [{ id: '', name: 'All Office Users' }, ...@js($rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values())];
                },
                filterRooms() {
                    const allRooms = [{ id: '', name: 'All Office Users' }, ...@js($rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values())];
                    this.filteredRooms = this.search === '' ?
                        allRooms :
                        allRooms.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                },
                selectRoom(room) {
                    this.roomId = room.id;
                    this.selectedName = room.name;
                    this.isOpen = false;
                    this.search = '';
                    this.filteredRooms = [{ id: '', name: 'All Office Users' }, ...@js($rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values())];
                    this.fetchUsers();
                },
                fetchUsers() {
                    window.showSpinner();
                    let url = `{{ route('room-user.index') }}${this.roomId ? '?roomId=' + this.roomId : ''}`;
                    window.history.replaceState({}, '', url);
            
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const table = doc.querySelector('#users-table');
                            if (table) document.querySelector('#users-table').innerHTML = table.innerHTML;
                        })
                        .catch(console.error)
                        .finally(() => window.hideSpinner());
                }
            }" @click.away="closeDropdown()">

            <!-- Sticky Combobox Container -->
            <div class="relative w-full sm:w-auto sticky top-16 z-50 bg-white dark:bg-gray-900 p-2 rounded-md shadow">
                <!-- Button -->
                <button type="button" @click="toggleDropdown()"
                    class="w-full sm:w-64 border border-primary rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-primary dark:text-gray-300 bg-white dark:bg-gray-800 shadow-md flex items-center justify-between text-left truncate">
                    <span x-text="selectedName || 'All Office Users'" class="truncate"></span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': isOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Panel -->
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 w-full sm:w-64 mt-2 bg-white dark:bg-gray-800 border border-primary rounded-md shadow-lg overflow-hidden"
                    style="display: none; min-width: 16rem;">

                    <!-- Search Input -->
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input x-ref="searchInput" type="text" x-model.debounce.300ms="search" @input="filterRooms()"
                            placeholder="Search offices"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-gray-300 text-sm">
                    </div>

                    <!-- Room Options -->
                    <div class="max-h-60 overflow-auto">
                        <template x-for="room in filteredRooms" :key="room.id">
                            <button type="button" @click="selectRoom(room)"
                                class="w-full text-left px-4 py-3 hover:bg-primary hover:text-white hover:bg-opacity-10 dark:hover:bg-gray-700 hover:pl-6 hover:border-l-4 hover:border-primary transition-all duration-200 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 last:border-b-0 truncate"
                                :class="{ 'bg-primary bg-opacity-5 font-medium text-white': roomId == room.id }">
                                <span x-text="room.name" class="truncate"></span>
                                <span x-show="roomId == room.id" class="float-right text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                        </template>

                        <div x-show="filteredRooms.length === 0"
                            class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                            No offices found
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table (dynamic reload zone) -->
        <div id="users-table">
            @include('pages.admin.room-users.partials.table', ['users' => $users])
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        function openUserModal(id, username) {
            const modal = document.getElementById('userDeleteModal');
            const nameSpan = document.getElementById('userName');
            const form = document.getElementById('userDeleteForm');

            // Set username in modal
            nameSpan.textContent = username;

            // Set form action using Laravel route helper
            form.action = "{{ route('room-user.destroy', ':id') }}".replace(':id', id);

            // Show modal with smooth scale and opacity animation
            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            // Prevent background scrolling while modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeUserModal() {
            const modal = document.getElementById('userDeleteModal');
            const content = modal.querySelector('.bg-white');

            // Animate out: scale down and fade
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 310); // duration matches CSS transition
        }

        function openToggleModal(id, username, action) {
            // Update modal text
            document.getElementById('toggleUserName').textContent = username;
            document.getElementById('toggleActionLabel').textContent = action.charAt(0).toUpperCase() + action.slice(1);
            document.getElementById('toggleActionText').textContent = action;

            // Use Laravel route helper instead of hardcoded URL
            const form = document.getElementById('userToggleForm');
            form.action = "{{ route('room-user.toggle-status', ['user' => ':id']) }}".replace(':id', id);


            // Show modal with smooth scale and opacity animation
            const modal = document.getElementById('userToggleModal');
            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            // Prevent background scrolling
            document.body.style.overflow = 'hidden';
        }

        function closeToggleModal() {
            const modal = document.getElementById('userToggleModal');
            const content = modal.querySelector('.bg-white');

            // Animate out: scale down and fade
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300); // duration matches CSS transition
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUserModal();
                closeToggleModal();
            }
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            // Select all elements that act as modals
            const modals = document.querySelectorAll('.modal-overlay');

            modals.forEach(modal => {
                // Close when clicking the semi-transparent background
                if (e.target === modal) {
                    if (modal.id === 'userDeleteModal') closeUserModal();
                    if (modal.id === 'userToggleModal') closeToggleModal();
                }
            });
        });
    </script>
@endpush

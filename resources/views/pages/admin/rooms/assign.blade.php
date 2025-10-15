@extends('layouts.app')

@section('content')
    @if (auth()->user()->hasRole('Admin'))
        <x-floating-actions />

        <div class="max-w-7xl mx-auto mt-8">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-3xl lg:text-3xl font-bold text-gray-800 dark:text-white">
                    <span class="text-primary">Assign</span> Staff to Office
                </h1>
                <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300">
                    Select an office and assign staff members to it
                </p>
            </div>

            <!-- Room Selection Combobox -->
            <div x-data="{
                roomId: '{{ $selectedRoom->id ?? '' }}',
                staffSearch: '{{ $search ?? '' }}',
                currentPage: {{ request('page', 1) }},
                isOpen: false,
                search: '',
                selectedName: @js($selectedRoom->name ?? ''),
                filteredRooms: [],
                rooms: @js($rooms->map(fn($room) => ['id' => $room->id, 'name' => $room->name])->values()),
            
                init() {
                    this.filteredRooms = this.rooms;
                    this.setupEventListeners();
                },
            
                filterRooms() {
                    this.filteredRooms = this.search === '' ?
                        this.rooms :
                        this.rooms.filter(room =>
                            room.name.toLowerCase().includes(this.search.toLowerCase())
                        );
                },
            
                filterStaff() {
                    this.currentPage = 1;
                    this.fetchRoomData();
                },
            
                selectRoom(room) {
                    this.selectedName = room.name;
                    this.roomId = room.id;
                    this.search = '';
                    this.isOpen = false;
                    this.filteredRooms = this.rooms;
                    this.currentPage = 1;
                    this.fetchRoomData();
                },
            
                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.search = '';
                        this.filteredRooms = this.rooms;
                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    }
                },
            
                closeDropdown() {
                    this.isOpen = false;
                    this.search = '';
                    this.filteredRooms = this.rooms;
                },
            
                fetchRoomData(page = null) {
                    if (!this.roomId) {
                        document.querySelector('#staff-content').innerHTML =
                            '<div class=\'text-center py-12 text-gray-500 dark:text-gray-400\'>Select an office to assign staff</div>';
                        return;
                    }
            
                    if (page) this.currentPage = page;
            
                    window.showSpinner();
                       let url = `{{ route('room.assign') }}?roomId=${this.roomId}&search=${encodeURIComponent(this.staffSearch)}&page=${this.currentPage}`;
            
                    window.history.replaceState({}, '', url);
            
                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            document.querySelector('#staff-content').innerHTML = data.html;
                            this.$nextTick(() => this.setupEventListeners());
                        })
                        .catch(err => {
                            console.error('Fetch error:', err);
                            showTemporaryMessage('Failed to load staff data. Please try again.', 'error');
                        })
                        .finally(() => window.hideSpinner());
                },
            
                setupEventListeners() {
                    const content = document.querySelector('#staff-content');
                    if (!content) return;
            
                    content.querySelectorAll('a[href*=\'page=\']').forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const page = url.searchParams.get('page');
                            if (page) this.fetchRoomData(parseInt(page));
                        });
                    });
            
                    const form = content.querySelector('#assignForm');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            e.preventDefault();
                            this.submitForm(form);
                        });
                    }
            
                    content.querySelectorAll('input[type=\'checkbox\'][data-staff-id]').forEach(checkbox => {
                        checkbox.addEventListener('click', (e) => {
                            if (!e.target.checked) {
                                e.preventDefault();
                                e.target.checked = true;
                                const staffCard = e.target.closest('.staff-card');
                                const staffName = staffCard.querySelector('h3').textContent.trim();
                                window.currentAlpineInstance = this;
                                window.openModal(e.target.dataset.staffId, staffName);
                            }
                        });
                    });
                },
            
                submitForm(form) {
                    const formData = new FormData(form);
            
                    window.showSpinner();
            
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.fetchRoomData();
                                showTemporaryMessage(data.message, 'success');
                            } else {
                                showTemporaryMessage(data.message || 'An error occurred', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Submit error:', err);
                            showTemporaryMessage('Failed to update assignment', 'error');
                        })
                        .finally(() => window.hideSpinner());
                },
            
                unassignStaff(staffId) {
                    window.showSpinner();
            
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name=\'csrf-token\']').content);
                    formData.append('_method', 'DELETE');
            
                    fetch(`/admin/rooms/staff/${staffId}/remove?page=${this.currentPage}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.closeModal();
                                this.fetchRoomData();
                                showTemporaryMessage(data.message, 'success');
                            } else {
                                showTemporaryMessage(data.message || 'Failed to unassign staff', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Unassign error:', err);
                            showTemporaryMessage('Failed to unassign staff', 'error');
                        })
                        .finally(() => window.hideSpinner());
                    }
                }">

                <!-- Sticky Combobox -->
                <!-- Sticky container for dropdown and staff search -->
                <div
                    class="font-sofia sticky top-16 z-49 bg-white dark:bg-gray-900 py-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div
                        class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

                        <!-- Office Dropdown -->
                        <div class="relative w-full md:w-1/2" @click.away="closeDropdown()">
                            <button type="button" @click="toggleDropdown()"
                                class="w-full border border-primary rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-primary dark:text-gray-300 bg-white dark:bg-gray-800 shadow-md flex items-center justify-between text-left">
                                <span x-text="selectedName || 'Select an office'"
                                    :class="!selectedName ? 'text-gray-400' : ''">
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Panel -->
                            <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 w-full mt-2 bg-white text-gray-800 dark:bg-gray-800 border border-primary rounded-md shadow-lg overflow-hidden"
                                style="display: none;">

                                <!-- Search input inside dropdown -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input x-ref="searchInput" type="text" x-model="search" @input="filterRooms()"
                                        placeholder="Search offices"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-gray-300 text-sm">
                                </div>

                                <!-- Room Options -->
                                <div class="max-h-60 overflow-auto">
                                    <div x-show="filteredRooms.length === 0"
                                        class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                                        No offices found
                                    </div>

                                    <template x-for="room in filteredRooms" :key="room.id">
                                        <button type="button" @click="selectRoom(room)"
                                            class="w-full text-left px-4 py-3 hover:bg-primary hover:text-white hover:bg-opacity-10 dark:hover:bg-gray-700 hover:pl-6 hover:border-l-4 hover:border-primary transition-all duration-200 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                            :class="{
                                                'bg-primary bg-opacity-5 font-medium text-white': roomId == room.id,
                                                'text-gray-700 dark:text-gray-300': roomId != room.id
                                            }">
                                            <span x-text="room.name"></span>
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
                                </div>
                            </div>
                        </div>

                        <!-- Staff Search Input aligned to dropdown width -->
                        <div class="w-full md:w-1/2">
                            <input type="text" x-model="staffSearch" @input.debounce.300="filterStaff()"
                                placeholder="Search staff"
                                class="w-full px-3 py-2 border border-primary rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-800 dark:text-gray-300">
                        </div>
                    </div>
                </div>

                <!-- Staff Content Area -->
                <div id="staff-content">
                    @if (isset($selectedRoom))
                        @include('pages.admin.rooms.partials.staff-assignment', [
                            'selectedRoom' => $selectedRoom,
                            'staff' => $staff,
                        ])
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        let currentStaffId = null;

        // Make these functions globally accessible
        window.openModal = function(staffId, staffName) {
            const modal = document.getElementById('confirmModal');
            const nameSpan = document.getElementById('modalMessage');
            const currentPage = new URLSearchParams(window.location.search).get('page') || '1';

            currentStaffId = staffId;
            nameSpan.textContent = staffName;

            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            document.body.style.overflow = 'hidden';
        }

        window.closeModal = function() {
            const modal = document.getElementById('confirmModal');
            const content = modal.querySelector('.bg-white');

            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentStaffId = null;
            }, 300);
        }

        // Add the missing confirmUnassign function
        window.confirmUnassign = function() {
            if (!currentStaffId) {
                showTemporaryMessage('No staff selected', 'error');
                return;
            }

            // Call the Alpine.js unassignStaff method
            if (window.currentAlpineInstance) {
                window.currentAlpineInstance.unassignStaff(currentStaffId);
            } else {
                showTemporaryMessage('Unable to process request. Please refresh the page.', 'error');
            }
        }

        // Escape key closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeModal();
            }
        });
    </script>
@endpush

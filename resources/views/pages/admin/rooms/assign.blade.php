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
                    Select an office and assign staff members to it.
                </p>
            </div>

            <!-- Room Selection Combobox -->
            <div x-data="{
                roomId: '{{ $selectedRoom->id ?? '' }}',
                isOpen: false,
                search: '',
                selectedName: @js($selectedRoom->name ?? ''),
                filteredRooms: [],
                rooms: @js($rooms->map(fn($room) => ['id' => $room->id, 'name' => $room->name])->values()),
            
                init() {
                    this.filteredRooms = this.rooms;
                },
            
                filterRooms() {
                    this.filteredRooms = this.search === '' ?
                        this.rooms :
                        this.rooms.filter(room =>
                            room.name.toLowerCase().includes(this.search.toLowerCase())
                        );
                },
            
                selectRoom(room) {
                    this.selectedName = room.name;
                    this.roomId = room.id;
                    this.search = '';
                    this.isOpen = false;
                    this.filteredRooms = this.rooms;
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
            
                fetchRoomData() {
                    if (!this.roomId) {
                        document.querySelector('#staff-content').innerHTML =
                            '<div class=\'text-center py-12 text-gray-500 dark:text-gray-400\'>Select a room to assign staff</div>';
                        return;
                    }
            
                    window.showSpinner();
                    let url = `{{ route('room.assign') }}?roomId=${this.roomId}`;
            
                    window.history.replaceState({}, '', url);
            
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            let parser = new DOMParser();
                            let doc = parser.parseFromString(html, 'text/html');
                            let content = doc.querySelector('#staff-content');
                            if (content) {
                                document.querySelector('#staff-content').innerHTML = content.innerHTML;
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => window.hideSpinner());
                }
            }">

                <!-- Sticky Combobox -->
                <div class="sticky top-16 z-[49] bg-white dark:bg-gray-900 py-4 -mx-4 px-4 mb-6">
                    <div class="max-w-7xl mx-auto flex justify-center">
                        <div @click.away="closeDropdown()" class="relative w-full max-w-md">

                            <!-- Display Button -->
                            <button type="button" @click="toggleDropdown()"
                                class="w-full border border-primary dark:bg-gray-800 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-primary dark:text-gray-300 bg-white dark:bg-gray-800 shadow-md flex items-center justify-between text-left">
                                <span x-text="selectedName || 'Select a room...'"
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

                                <!-- Search Input -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input x-ref="searchInput" type="text" x-model="search" @input="filterRooms()"
                                        placeholder="Search offices..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-gray-300 text-sm">
                                </div>

                                <!-- Room Options List -->
                                <div class="max-h-60 overflow-auto">
                                    <!-- No Results -->
                                    <div x-show="filteredRooms.length === 0"
                                        class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                                        No offices found
                                    </div>

                                    <!-- Room Options -->
                                    <template x-for="room in filteredRooms" :key="room.id">
                                        <button type="button" @click="selectRoom(room)"
                                            class="w-full text-left px-4 py-3 hover:bg-primary hover:text-white hover:bg-opacity-10 dark:hover:bg-gray-700 hover:pl-6 hover:border-l-4 hover:border-primary transition-all duration-200 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                            :class="{
                                                'bg-primary bg-opacity-5 font-medium text-white': roomId == room.id,
                                                'text-gray-700 dark:text-gray-300': roomId != room.id
                                            }">
                                            <span x-text="room.name"></span>

                                            <!-- Checkmark for selected -->
                                            <span x-show="roomId == room.id" class="float-right text-primary">
                                                ✓
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </div>
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

        function openModal(staffId, staffName) {
            const modal = document.getElementById('confirmModal');
            const nameSpan = document.getElementById('modalMessage');
            const unassignForm = document.getElementById('unassignForm');
            const currentPage = new URLSearchParams(window.location.search).get('page') || '1';

            currentStaffId = staffId;
            nameSpan.textContent = staffName;
            unassignForm.action = `/admin/rooms/staff/${staffId}/remove?page=${currentPage}`;

            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
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

        // Handle checkbox clicks for unassignment confirmation
        document.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' && e.target.hasAttribute('data-staff-id')) {
                if (!e.target.checked) {
                    e.preventDefault();
                    e.target.checked = true;
                    const staffCard = e.target.closest('.staff-card');
                    const staffName = staffCard.querySelector('h3').textContent.trim();
                    openModal(e.target.dataset.staffId, staffName);
                }
            }
        });

        // Escape key closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endpush

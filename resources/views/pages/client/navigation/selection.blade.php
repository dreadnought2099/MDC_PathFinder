@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div class="w-full flex items-center p-2 sm:p-4 bg-white dark:bg-gray-900 sticky top-0 z-50">
            <div class="w-auto sm:w-48 flex items-center justify-start">
                <!-- Left: Back button -->
                <a href="{{ route('index') }}"
                    class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300 text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-6 sm:w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="font-medium hidden sm:inline">Back to Home</span>
                    <span class="font-medium sm:hidden">Back</span>
                </a>
            </div>

            <div class="flex-1"></div>

            <!-- Right controls -->
            <div class="flex items-center gap-3 sm:gap-6">
                <x-team-modal />
                <x-about-page />
                <x-dark-mode-toggle />
            </div>
        </div>

        <x-floating-q-r href="{{ route('scan.index') }}"
            icon="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/qr-code.png" alt="Scan Office"
            title="Scan office to know more" />

        <div class="flex justify-center mt-4 sm:mt-8 px-4 sm:px-0 font-sofia">
            <div x-data="staffSearch()" class="relative w-full max-w-md">
                <input type="text" x-model="query" @input.debounce.300ms="filterStaff" placeholder="Search staff"
                    class="w-full border border-primary focus:ring-2 focus:border-primary focus:ring-primary focus:outline-none rounded-lg p-2 pr-8 text-sm sm:text-base dark:bg-gray-700 dark:text-gray-300 transition-all duration-200">

                <ul x-show="results.length"
                    class="absolute w-full bg-white dark:bg-gray-800 dark:text-gray-300 border border-primary rounded-lg mt-1 max-h-48 sm:max-h-60 overflow-y-auto z-50 shadow-lg">
                    <template x-for="staff in results" :key="staff.id">
                        <li @click="selectStaff(staff)"
                            class="p-2 sm:p-3 cursor-pointer hover:bg-primary hover:text-white transition-colors duration-150">
                            <span x-text="staff.name" class="text-sm sm:text-base font-medium"></span>
                            <small x-text="staff.room ? '(' + staff.room.name + ')' : ''"
                                class="ml-2 text-xs sm:text-sm text-gray-500 hover:text-gray-200"></small>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-lg bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-md p-6">
                <h2 class="text-2xl mb-6 text-center dark:text-gray-200">
                    Select Starting Point & Destination
                </h2>
                <form action="{{ route('paths.results') }}" method="POST">
                    @csrf

                    <!-- From Room Combobox -->
                    <div class="mb-4">
                        <label for="from_room_search" class="block text-sm font-medium mb-2 dark:text-gray-300">
                            Starting Point
                        </label>

                        <div class="relative">
                            <input type="text" id="from_room_search" autocomplete="off"
                                placeholder="Search or select an office"
                                class="w-full border border-primary focus:ring-2 focus:border-primary focus:ring-primary focus:outline-none rounded-lg p-2 pr-8 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200">
                            <svg class="absolute right-2 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>

                            <input type="hidden" id="from_room" name="from_room">

                            <div id="from_room_dropdown"
                                class="hidden absolute z-10 w-full mt-1 dark:text-gray-300 bg-white dark:bg-gray-700 border border-primary rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div id="from_room_options" class="py-1">
                                    @foreach ($rooms as $room)
                                        <div class="from-room-option px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                                            data-value="{{ $room->id }}" data-label="{{ $room->name }}">
                                            {{ $room->name }}
                                        </div>
                                    @endforeach
                                </div>

                                <div id="from_room_no_results"
                                    class="hidden px-4 py-2 text-gray-500 dark:text-gray-400 text-sm">
                                    No office found
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- To Room Combobox -->
                    <div class="mb-6">
                        <label for="to_room_search" class="block text-sm font-medium mb-2 dark:text-gray-300">
                            Destination
                        </label>

                        <div class="relative">
                            <input type="text" id="to_room_search" autocomplete="off"
                                placeholder="Search or select an office"
                                class="w-full border border-primary focus:ring-2 focus:border-primary focus:ring-primary focus:outline-none rounded-lg p-2 pr-8 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200">
                            <svg class="absolute right-2 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>

                            <input type="hidden" id="to_room" name="to_room">

                            <div id="to_room_dropdown"
                                class="hidden absolute z-10 w-full mt-1 dark:text-gray-300 bg-white dark:bg-gray-700 border border-primary rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div id="to_room_options" class="py-1">
                                    @foreach ($rooms as $room)
                                        <div class="to-room-option px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                                            data-value="{{ $room->id }}" data-label="{{ $room->name }}">
                                            {{ $room->name }}
                                        </div>
                                    @endforeach
                                </div>

                                <div id="to_room_no_results"
                                    class="hidden px-4 py-2 text-gray-500 dark:text-gray-400 text-sm">
                                    No office found
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" id="startBtn"
                            class="px-6 py-2 rounded-md bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary dark:hover:bg-gray-800 shadow-primary-light transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            Start Navigation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function staffSearch() {
            return {
                query: '',
                results: [],
                filterStaff() {
                    if (this.query.length < 1) {
                        this.results = [];
                        return;
                    }
                    fetch(`/staff/search?q=${encodeURIComponent(this.query)}`)
                        .then(res => res.json())
                        .then(data => {
                            // Filter out staff whose rooms are already selected
                            const fromValue = fromCombobox.getValue();
                            const toValue = toCombobox.getValue();

                            this.results = data.filter(staff => {
                                if (!staff.room) return true;
                                return staff.room.id != fromValue && staff.room.id != toValue;
                            });
                        });
                },
                selectStaff(staff) {
                    this.query = staff.name;
                    this.results = [];

                    if (staff.room) {
                        // Use the combobox's selectOption method to properly update everything
                        toCombobox.selectOption(staff.room.id.toString(), staff.room.name);
                    }
                }
            }
        }

        class Combobox {
            constructor(searchInputId, hiddenInputId, dropdownId, optionsContainerId, noResultsId, optionClass) {
                this.searchInput = document.getElementById(searchInputId);
                this.hiddenInput = document.getElementById(hiddenInputId);
                this.dropdown = document.getElementById(dropdownId);
                this.optionsContainer = document.getElementById(optionsContainerId);
                this.noResults = document.getElementById(noResultsId);
                this.optionClass = optionClass;
                this.options = Array.from(document.querySelectorAll(`.${optionClass}`));
                this.selectedValue = '';
                this.selectedLabel = '';

                this.init();
            }

            init() {
                // Show dropdown on focus
                this.searchInput.addEventListener('focus', () => {
                    this.filterOptions('');
                    this.dropdown.classList.remove('hidden');
                });

                // Filter options on input
                this.searchInput.addEventListener('input', (e) => {
                    this.filterOptions(e.target.value);
                });

                // Handle option click
                this.options.forEach(option => {
                    option.addEventListener('click', () => {
                        this.selectOption(option.dataset.value, option.dataset.label);
                    });
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!this.searchInput.contains(e.target) && !this.dropdown.contains(e.target)) {
                        this.dropdown.classList.add('hidden');
                        // Restore selected value or clear
                        if (this.selectedLabel) {
                            this.searchInput.value = this.selectedLabel;
                        }
                    }
                });
            }

            filterOptions(searchTerm) {
                const term = searchTerm.toLowerCase();
                let visibleCount = 0;

                this.options.forEach(option => {
                    const label = option.dataset.label.toLowerCase();
                    const isDisabled = option.classList.contains('opacity-50');

                    if (label.includes(term) && !isDisabled) {
                        option.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        option.classList.add('hidden');
                    }
                });

                if (visibleCount === 0) {
                    this.noResults.classList.remove('hidden');
                    this.optionsContainer.classList.add('hidden');
                } else {
                    this.noResults.classList.add('hidden');
                    this.optionsContainer.classList.remove('hidden');
                }
            }

            selectOption(value, label) {
                this.selectedValue = value;
                this.selectedLabel = label;
                this.searchInput.value = label;
                this.hiddenInput.value = value;
                this.dropdown.classList.add('hidden');

                // Trigger change event for cross-combobox updates
                this.hiddenInput.dispatchEvent(new Event('change'));
            }

            disableOption(value) {
                this.options.forEach(option => {
                    if (option.dataset.value === value) {
                        option.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    }
                });
            }

            enableOption(value) {
                this.options.forEach(option => {
                    if (option.dataset.value === value) {
                        option.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    }
                });
            }

            enableAllOptions() {
                this.options.forEach(option => {
                    option.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                });
            }

            getValue() {
                return this.selectedValue;
            }

            clear() {
                this.selectedValue = '';
                this.selectedLabel = '';
                this.searchInput.value = '';
                this.hiddenInput.value = '';
            }
        }

        // Initialize comboboxes
        const fromCombobox = new Combobox(
            'from_room_search',
            'from_room',
            'from_room_dropdown',
            'from_room_options',
            'from_room_no_results',
            'from-room-option'
        );

        const toCombobox = new Combobox(
            'to_room_search',
            'to_room',
            'to_room_dropdown',
            'to_room_options',
            'to_room_no_results',
            'to-room-option'
        );

        const startBtn = document.getElementById('startBtn');
        const fromHidden = document.getElementById('from_room');
        const toHidden = document.getElementById('to_room');

        function updateDisabledOptions() {
            const fromValue = fromCombobox.getValue();
            const toValue = toCombobox.getValue();

            // Enable all options first
            fromCombobox.enableAllOptions();
            toCombobox.enableAllOptions();

            // Disable the selected destination in starting point combobox
            if (toValue) {
                fromCombobox.disableOption(toValue);
            }

            // Disable the selected starting point in destination combobox
            if (fromValue) {
                toCombobox.disableOption(fromValue);
            }

            // Enable/disable start button
            startBtn.disabled = !(fromValue && toValue);
        }

        fromHidden.addEventListener('change', updateDisabledOptions);
        toHidden.addEventListener('change', updateDisabledOptions);

        // Preselect from_room if provided
        @if (isset($preselectedFromRoom) && $preselectedFromRoom)
            const preselectedRoomId = '{{ $preselectedFromRoom }}';
            const preselectedOption = document.querySelector(`.from-room-option[data-value="${preselectedRoomId}"]`);

            if (preselectedOption) {
                fromCombobox.selectOption(preselectedRoomId, preselectedOption.dataset.label);
            }
        @endif
    </script>
@endpush

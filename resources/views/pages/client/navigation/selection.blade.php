@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div
            class="w-full flex justify-between items-center p-4 border-b-2 border-primary dark:border-primary bg-white dark:bg-gray-900 sticky top-0 z-50">

            <!-- Left: Back button -->
            <a href="{{ route('index') }}"
                class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="font-medium">Back to Home</span>
            </a>

            <!-- Right: About + Dark Mode -->
            <div class="flex items-center space-x-4">
                <!-- About icon -->
                <x-about-page />

                <!-- Dark Mode Toggle -->
                <x-dark-mode-toggle />
            </div>

        </div>

        <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
            title="Scan office to know more" />

        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-lg bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-6 text-center dark:text-gray-200">
                    Select Starting Point & Destination
                </h2>
                <form action="{{ route('paths.results') }}" method="POST">
                    @csrf

                    <!-- From Room -->
                    <div class="mb-4">
                        <label for="from_room" class="block text-sm font-medium mb-2 dark:text-gray-300">
                            Starting Point
                        </label>
                        <select id="from_room" name="from_room"
                            class="w-full border border-primary rounded-lg p-2 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">-- Select a room --</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- To Room -->
                    <div class="mb-6">
                        <label for="to_room" class="block text-sm font-medium mb-2 dark:text-gray-300">
                            Destination
                        </label>
                        <select id="to_room" name="to_room"
                            class="w-full border border-primary rounded-lg p-2 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">-- Select a room --</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-6 py-2 rounded-full bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary dark:hover:bg-gray-800 shadow-primary-light transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            Start Navigation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            const fromSelect = document.getElementById('from_room');
            const toSelect = document.getElementById('to_room');

            function updateDisabledOptions() {
                const fromValue = fromSelect.value;
                const toValue = toSelect.value;

                // Enable all options first
                [...fromSelect.options].forEach(opt => opt.disabled = false);
                [...toSelect.options].forEach(opt => opt.disabled = false);

                // Disable the selected destination in starting point dropdown
                if (toValue) {
                    [...fromSelect.options].forEach(opt => {
                        if (opt.value === toValue) opt.disabled = true;
                    });
                }

                // Disable the selected starting point in destination dropdown
                if (fromValue) {
                    [...toSelect.options].forEach(opt => {
                        if (opt.value === fromValue) opt.disabled = true;
                    });
                }
            }

            fromSelect.addEventListener('change', updateDisabledOptions);
            toSelect.addEventListener('change', updateDisabledOptions);
        </script>
    @endpush

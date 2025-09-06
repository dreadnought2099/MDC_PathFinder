@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 dark:bg-gray-900">
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
                        class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-200">
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
                        class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-200">
                        <option value="">-- Select a room --</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                        class="px-6 py-2 rounded-full bg-primary text-white font-semibold shadow hover:shadow-lg transition">
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
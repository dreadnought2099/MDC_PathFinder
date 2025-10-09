@props(['route', 'fields' => [], 'currentSort' => 'id', 'currentDirection' => 'asc', 'placeholder' => 'records'])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <form method="GET" action="{{ $route }}" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search {{ $placeholder }}..."
            class="border border-gray-300 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:ring-primary/40 dark:bg-gray-800 dark:text-white">

        {{-- Preserve sort & direction on search --}}
        <input type="hidden" name="sort" value="{{ request('sort', $currentSort) }}">
        <input type="hidden" name="direction" value="{{ request('direction', $currentDirection) }}">

        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/80 transition">
            Search
        </button>

        @if (request('search'))
            <a href="{{ $route }}" class="text-sm text-gray-500 hover:text-gray-700">
                Clear
            </a>
        @endif
    </form>

    <div class="flex items-center gap-2 dark:text-gray-300">
        <label for="sort" class="text-sm font-medium">Sort By:</label>
        <form method="GET" action="{{ $route }}" id="sort-form" class="flex gap-2">
            {{-- Preserve search --}}
            @foreach (request()->except(['sort', 'direction', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <select name="sort" onchange="this.form.submit()"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                @foreach ($fields as $key => $label)
                    <option value="{{ $key }}" {{ $currentSort === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="direction" onchange="this.form.submit()"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                <option value="asc" {{ $currentDirection === 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ $currentDirection === 'desc' ? 'selected' : '' }}>Descending</option>
            </select>
        </form>
    </div>
</div>

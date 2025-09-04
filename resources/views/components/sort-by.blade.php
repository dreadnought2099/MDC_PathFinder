<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->

    @props(['route', 'fields' => [], 'currentSort' => 'id', 'currentDirection' => 'asc'])

    <div class="flex items-center gap-2 dark:text-gray-300">
        <label for="sort" class="text-sm font-medium">Sort By:</label>
        <form method="GET" action="{{ $route }}" id="sort-form" class="flex gap-2">
            @foreach (request()->except(['sort', 'direction']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <select name="sort" onchange="this.form.submit()" class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                @foreach ($fields as $key => $label)
                    <option value="{{ $key }}" {{ $currentSort === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="direction" onchange="this.form.submit()" class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                <option value="asc" {{ $currentDirection === 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ $currentDirection === 'desc' ? 'selected' : '' }}>Descending</option>
            </select>
        </form>
    </div>
</div>

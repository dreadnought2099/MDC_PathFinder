@props(['route', 'fields' => [], 'currentSort' => 'id', 'currentDirection' => 'asc', 'placeholder' => 'records'])

<div x-data="searchSortHandler('{{ $route }}')" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <!-- Sort -->
    <div class="flex items-center gap-2 dark:text-gray-300">
        <label for="sort" class="text-sm font-medium">Sort By:</label>
        <div class="flex gap-2">
            <select x-model="sort" @change="submitSearch"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                @foreach ($fields as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <select x-model="direction" @change="submitSearch"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>

    <!-- Search -->
    <form @submit.prevent="submitSearch" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="text" x-model="search" value="{{ request('search', '') }}"
            placeholder="Search {{ $placeholder }}..."
            class="font-sofia border border-gray-300 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:ring-primary/40 dark:bg-gray-800 dark:text-white">

        <button type="submit"
            class="px-4 bg-primary text-white py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
            Search
        </button>

        <template x-if="search">
            <button type="button" @click="clearSearch"
                class="px-4 bg-secondary text-white py-2 rounded-md hover:text-secondary border-2 border-secondary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                Clear
            </button>
        </template>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchSortHandler', (route) => ({
            search: '',
            sort: '',
            direction: '',

            init() {
                // Restore previous state from sessionStorage
                const storedSearch = sessionStorage.getItem('search');
                const storedSort = sessionStorage.getItem('sort');
                const storedDirection = sessionStorage.getItem('direction');

                this.search = storedSearch ?? @json(request('search', ''));
                this.sort = storedSort ?? @json(request('sort', $currentSort));
                this.direction = storedDirection ?? @json(request('direction', $currentDirection));

                // Persist future changes
                this.$watch('search', value => sessionStorage.setItem('search', value));
                this.$watch('sort', value => sessionStorage.setItem('sort', value));
                this.$watch('direction', value => sessionStorage.setItem('direction', value));
            },

            async submitSearch() {
                const params = new URLSearchParams({
                    search: this.search,
                    sort: this.sort,
                    direction: this.direction,
                });

                try {
                    const response = await fetch(`${route}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();
                    if (data.html) {
                        document.querySelector('#records-table').innerHTML = data.html;
                    }
                } catch (err) {
                    console.error(err);
                }
            },

            clearSearch() {
                this.search = '';
                sessionStorage.removeItem('search');
                this.submitSearch();
            }
        }));
    });
</script>

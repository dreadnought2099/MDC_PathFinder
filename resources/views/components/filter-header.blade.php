@props([
    'route',
    'fields' => [],
    'currentSort' => 'id',
    'currentDirection' => 'asc',
    'placeholder' => 'records',
    'currentSearch' => '',
])

<div x-data="searchSortHandler()" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <!-- Sort -->
    <div class="flex items-center gap-2 dark:text-gray-300">
        <label for="sort" class="text-sm font-medium">Sort By:</label>
        <div class="flex gap-2">
            <select x-model="sort" @change="handleChange"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                @foreach ($fields as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <select x-model="direction" @change="handleChange"
                class="border border-primary rounded p-1 text-sm dark:bg-gray-800 dark:text-white">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>

    <!-- Search -->
    <form @submit.prevent="handleChange" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="text" x-model="search" placeholder="Search {{ $placeholder }}"
            @input.debounce.500ms="handleChange"
            class="font-sofia border border-primary rounded-md px-3 py-2 w-full sm:w-64 outline-none focus:ring focus:ring-primary focus:border-primary dark:bg-gray-800 dark:text-white">

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
        Alpine.data('searchSortHandler', () => ({
            search: @json($currentSearch ?? ''),
            sort: @json($currentSort),
            direction: @json($currentDirection),
            route: @json($route),

            init() {
                // Listen for pagination link clicks
                this.$nextTick(() => {
                    this.setupPaginationListener();
                });
            },

            setupPaginationListener() {
                // Delegate event listener for pagination links
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a[href*="page="]');
                    if (link && link.href.includes(this.route.split('?')[0])) {
                        e.preventDefault();
                        const url = new URL(link.href);
                        const page = url.searchParams.get('page');
                        this.loadPage(page);
                    }
                });
            },

            async loadPage(page = 1) {
                const params = new URLSearchParams({
                    search: this.search || '',
                    sort: this.sort,
                    direction: this.direction,
                    page: page
                });

                const url = `${this.route}?${params.toString()}`;

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();

                    if (data.html) {
                        const tableElement = document.querySelector('#records-table');
                        if (tableElement) {
                            tableElement.innerHTML = data.html;
                            // Re-setup pagination listener for new links
                            this.$nextTick(() => {
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            });
                        }
                    }

                    // Update browser URL without reload
                    window.history.pushState({}, '', url);
                } catch (err) {
                    console.error('Error fetching data:', err);
                    // Fallback to full page reload if AJAX fails
                    window.location.href = url;
                }
            },

            async handleChange() {
                await this.loadPage(1); // Reset to page 1 when filtering
            },

            clearSearch() {
                this.search = '';
                this.handleChange();
            }
        }));
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        window.location.reload();
    });
</script>
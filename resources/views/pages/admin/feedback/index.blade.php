@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">User Feedback</h1>
            <a href="{{ route('feedback.export', request()->query()) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                Export to CSV
            </a>
        </div>

        {{-- Analytics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Feedback</div>
                <div class="text-2xl font-bold">{{ $analytics['total_count'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="text-sm text-gray-600 dark:text-gray-400">Average Rating</div>
                <div class="text-2xl font-bold">{{ $analytics['average_rating'] ?? 'N/A' }}/5</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="text-sm text-gray-600 dark:text-gray-400">Average Score</div>
                <div class="text-2xl font-bold">{{ $analytics['average_score'] ?? 'N/A' }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="text-sm text-gray-600 dark:text-gray-400">Pending Review</div>
                <div class="text-2xl font-bold">{{ $analytics['by_status']['pending'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Search, Sort & Filter Bar --}}
        <div x-data="{ showFilters: {{ request()->hasAny(['type', 'status', 'rating', 'date_from', 'date_to', 'score_min', 'score_max', 'per_page']) ? 'true' : 'false' }} }" class="mb-6">

            {{-- Top Bar: Search, Sort, and Filter Toggle --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-4">
                <form id="search-sort-form" method="GET" action="{{ route('feedback.index') }}" class="space-y-4">
                    {{-- Keep current filters --}}
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="score_min" value="{{ request('score_min') }}">
                    <input type="hidden" name="score_max" value="{{ request('score_max') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-5">
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search-input" value="{{ request('search', '') }}"
                                placeholder="Search feedback..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        {{-- Sort By --}}
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Sort By</label>
                            <select name="sort" id="sort-select"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="created_at"
                                    {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                                <option value="recaptcha_score"
                                    {{ request('sort') == 'recaptcha_score' ? 'selected' : '' }}>reCAPTCHA Score</option>
                                <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="feedback_type" {{ request('sort') == 'feedback_type' ? 'selected' : '' }}>
                                    Type</option>
                            </select>
                        </div>

                        {{-- Sort Direction --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Order</label>
                            <select name="direction" id="direction-select"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>
                                    Descending</option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending
                                </option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="lg:col-span-2 flex items-end gap-2">
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors">
                                Apply
                            </button>
                            <button @click="showFilters = !showFilters" type="button"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors whitespace-nowrap">
                                <span x-text="showFilters ? '▼' : '▶'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Advanced Filters Panel (Collapsible) --}}
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <form id="advanced-filters-form" method="GET" action="{{ route('feedback.index') }}" class="space-y-4">
                    {{-- Keep current search and sort --}}
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

                    <h3 class="text-lg font-semibold mb-4 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Advanced Filters
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Type Filter --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Type</label>
                            <select name="type"
                                class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="">All Types</option>
                                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General
                                </option>
                                <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>Bug</option>
                                <option value="feature" {{ request('type') == 'feature' ? 'selected' : '' }}>Feature
                                    Request</option>
                                <option value="navigation" {{ request('type') == 'navigation' ? 'selected' : '' }}>
                                    Navigation Issue</option>
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Status</label>
                            <select name="status"
                                class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed
                                </option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved
                                </option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                        </div>

                        {{-- Rating Filter --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Rating</label>
                            <select name="rating"
                                class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="">All Ratings</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Per Page --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Results Per Page</label>
                            <select name="per_page"
                                class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Date Range --}}
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Date Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" name="date_from" class="filter-input"
                                    value="{{ request('date_from') }}" placeholder="From"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <input type="date" name="date_to" class="filter-input"
                                    value="{{ request('date_to') }}" placeholder="To"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        {{-- Score Range --}}
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">reCAPTCHA Score Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="score_min" class="filter-input"
                                    value="{{ request('score_min') }}" step="0.1" min="0" max="1"
                                    placeholder="Min (0.0)"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <input type="number" name="score_max" class="filter-input"
                                    value="{{ request('score_max') }}" step="0.1" min="0" max="1"
                                    placeholder="Max (1.0)"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded hover:bg-primary-dark transition-colors">
                            Apply Filters
                        </button>
                        <button type="button" onclick="clearFilters()"
                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Actions --}}
        <form id="bulk-form" method="POST" class="mb-4">
            @csrf
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="flex gap-2 items-center flex-wrap">
                    <select id="bulk-action"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                        <option value="">Bulk Actions</option>
                        <option value="update-status">Update Status</option>
                        <option value="delete">Delete</option>
                    </select>

                    <select id="bulk-status"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white hidden">
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                        <option value="archived">Archived</option>
                    </select>

                    <button type="button" onclick="executeBulkAction()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">
                        Apply
                    </button>
                    <span id="selected-count" class="text-sm text-gray-600 dark:text-gray-400 font-medium"></span>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div id="records-table">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Rating
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Message
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Score
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                            @forelse($feedback as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox"
                                            class="feedback-checkbox rounded border-gray-300 text-primary focus:ring-primary"
                                            value="{{ $item->id }}" onchange="updateSelectedCount()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                                        {{ $item->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs">
                                            {{ ucfirst($item->feedback_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($item->rating)
                                            <div class="flex">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm max-w-md dark:text-gray-300">
                                        {{ Str::limit($item->message, 100) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($item->recaptcha_score)
                                            <span
                                                class="px-2 py-1 rounded text-xs {{ $item->recaptcha_score >= 0.7 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($item->recaptcha_score >= 0.5 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                                {{ number_format($item->recaptcha_score, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="{{ route('feedback.updateStatus', $item) }}"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                class="px-2 py-1 rounded text-xs border-0 cursor-pointer {{ $item->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : ($item->status == 'reviewed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : ($item->status == 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                                                <option value="pending"
                                                    {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="reviewed"
                                                    {{ $item->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                                <option value="resolved"
                                                    {{ $item->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="archived"
                                                    {{ $item->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <a href="{{ route('feedback.show', $item) }}"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('feedback.destroy', $item) }}"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No feedback found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $feedback->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Fetch and update table via AJAX
        function fetchFeedback() {
            const searchForm = document.getElementById('search-sort-form');
            const advancedForm = document.getElementById('advanced-filters-form');
            const formData = new FormData(searchForm);

            // Add advanced filter data
            const advancedData = new FormData(advancedForm);
            for (let [key, value] of advancedData.entries()) {
                if (value && !formData.has(key)) {
                    formData.append(key, value);
                }
            }

            const params = new URLSearchParams(formData);
            const url = `{{ route('feedback.index') }}?${params.toString()}`;

            // Update URL without reload
            window.history.pushState({}, '', url);

            // Show loading state
            const tableContainer = document.getElementById('records-table');
            tableContainer.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        tableContainer.innerHTML = data.html;
                        tableContainer.style.opacity = '1';

                        // Re-attach checkbox listeners
                        updateSelectedCount();
                    }
                })
                .catch(error => {
                    console.error('Error fetching feedback:', error);
                    tableContainer.style.opacity = '1';
                });
        }

        // Search input with debounce
        const searchInput = document.getElementById('search-input');
        const debouncedSearch = debounce(() => {
            fetchFeedback();
        }, 500);

        searchInput.addEventListener('input', debouncedSearch);

        // Sort selects - instant update
        document.getElementById('sort-select').addEventListener('change', fetchFeedback);
        document.getElementById('direction-select').addEventListener('change', fetchFeedback);

        // Advanced filters - instant update
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('change', fetchFeedback);
        });

        // Form submissions
        document.getElementById('search-sort-form').addEventListener('submit', (e) => {
            e.preventDefault();
            fetchFeedback();
        });

        document.getElementById('advanced-filters-form').addEventListener('submit', (e) => {
            e.preventDefault();
            fetchFeedback();
        });

        // Clear filters function
        function clearFilters() {
            const searchInput = document.getElementById('search-input');
            const sortSelect = document.getElementById('sort-select');
            const directionSelect = document.getElementById('direction-select');

            // Clear advanced filters
            document.querySelectorAll('.filter-input').forEach(input => {
                if (input.type === 'date' || input.type === 'number' || input.tagName === 'SELECT') {
                    input.value = '';
                }
            });

            // Update hidden fields
            document.querySelectorAll(
                'input[type="hidden"][name="type"], input[type="hidden"][name="status"], input[type="hidden"][name="rating"], input[type="hidden"][name="date_from"], input[type="hidden"][name="date_to"], input[type="hidden"][name="score_min"], input[type="hidden"][name="score_max"], input[type="hidden"][name="per_page"]'
                ).forEach(input => {
                input.value = '';
            });

            fetchFeedback();
        }

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.feedback-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.feedback-checkbox:checked').length;
            const countEl = document.getElementById('selected-count');
            countEl.textContent = checked > 0 ? `${checked} item(s) selected` : '';

            // Update select-all checkbox state
            const selectAll = document.getElementById('select-all');
            const allCheckboxes = document.querySelectorAll('.feedback-checkbox');
            if (allCheckboxes.length > 0) {
                selectAll.checked = checked === allCheckboxes.length && checked > 0;
            }
        }

        document.getElementById('bulk-action').addEventListener('change', function() {
            const statusSelect = document.getElementById('bulk-status');
            if (this.value === 'update-status') {
                statusSelect.classList.remove('hidden');
            } else {
                statusSelect.classList.add('hidden');
            }
        });

        function executeBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const checked = Array.from(document.querySelectorAll('.feedback-checkbox:checked'))
                .map(cb => cb.value);

            if (checked.length === 0) {
                alert('Please select at least one item');
                return;
            }

            if (!action) {
                alert('Please select an action');
                return;
            }

            const form = document.getElementById('bulk-form');

            if (action === 'update-status') {
                const status = document.getElementById('bulk-status').value;
                form.action = '{{ route('feedback.bulkUpdateStatus') }}';
                form.innerHTML = `
                @csrf
                ${checked.map(id => `<input type="hidden" name="feedback_ids[]" value="${id}">`).join('')}
                <input type="hidden" name="status" value="${status}">
            `;
                form.submit();
            } else if (action === 'delete') {
                if (confirm(`Are you sure you want to delete ${checked.length} item(s)?`)) {
                    form.action = '{{ route('feedback.bulkDelete') }}';
                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    ${checked.map(id => `<input type="hidden" name="feedback_ids[]" value="${id}">`).join('')}
                `;
                    form.submit();
                }
            }
        }
    </script>
@endpush
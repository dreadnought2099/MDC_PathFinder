@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <div class="bg-white dark:bg-gray-900 mb-6 text-center sticky top-0 z-48 px-4 sm:px-6 lg:px-8">
            <img-reveal>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                    User <span class="trigger-text text-primary">Feedback</span>
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/gif/feedback.gif" alt="Feedback Gif" class="reveal-img">
                </h1>
            </img-reveal>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                Monitor user feedback and suggestions.
            </p>
        </div>

        <a href="{{ route('feedback.export', request()->query()) }}" class="fixed right-0 top-32 z-50 group"
            title="Export to CSV">
            <div
                class="flex flex-col items-center justify-center px-3 py-6 bg-green-600 text-white rounded-l-full shadow-xl transition-all duration-300 group-hover:bg-green-700">
                <svg class="w-6 h-6 mb-2 group-hover:animate-bounce" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="text-xs font-bold tracking-wider transform whitespace-nowrap">EXPORT</span>
            </div>

            <div
                class="absolute right-full top-1/2 -translate-y-1/2 mr-2 px-4 py-3 bg-green-600 text-white rounded-lg shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-hover:mr-3 transition-all duration-300 whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="font-semibold">Export Feedback to CSV</span>
                </div>
                <div
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-full w-0 h-0 border-t-8 border-t-transparent border-b-8 border-b-transparent border-l-8 border-l-green-600">
                </div>
            </div>
        </a>

        {{-- Analytics Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-blue-50 dark:bg-blue-900/30 p-4 lg:p-6 rounded-lg shadow border-2 border-primary dark:border-blue-800">
                <div class="text-xs lg:text-sm text-blue-600 dark:text-blue-400">Total Feedback</div>
                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-300">{{ $analytics['total_count'] }}
                </div>
            </div>
            <div
                class="bg-green-50 dark:bg-green-900/30 p-4 lg:p-6 rounded-lg shadow border-2 border-tertiary dark:border-green-800">
                <div class="text-xs lg:text-sm text-green-600 mb-1 dark:text-green-400">Average Rating</div>
                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-300">
                    {{ $analytics['average_rating'] ?? 'N/A' }}/5</div>
            </div>
            <div class="dark:bg-purple-900/30 p-4 lg:p-6 rounded-lg shadow border-2 border-purple dark:border-purple-800">
                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Average Score</div>
                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-300">
                    {{ $analytics['average_score'] ?? 'N/A' }}</div>
            </div>
            <div
                class="bg-orange-50 dark:bg-orange-900/30 p-4 lg:p-6 rounded-lg shadow border-2 border-orange dark:border-orange-800">
                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-300">Pending Review</div>
                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-300">
                    {{ $analytics['by_status']['pending'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Search and Actions Bar --}}
        <div x-data="{ showFilters: false, showBulkActions: false }" class="space-y-4 mb-6">

            {{-- Main Search Bar --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 border-2 border-primary">
                <form id="search-sort-form" method="GET" action="{{ route('feedback.index') }}">
                    {{-- Keep current filters --}}
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="score_min" value="{{ request('score_min') }}">
                    <input type="hidden" name="score_max" value="{{ request('score_max') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">

                    <div class="flex flex-col lg:flex-row gap-3">
                        {{-- Search Input --}}
                        <div class="flex-1">
                            <input type="text" name="search" id="search-input" value="{{ request('search', '') }}"
                                placeholder="Search feedback..."
                                class="w-full px-4 py-2 rounded-md border border-primary dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        {{-- Sort Controls --}}
                        <div class="flex gap-2">
                            <select name="sort" id="sort-select"
                                class="px-3 py-2 rounded-md dark:bg-gray-700 border border-primary dark:text-white focus:ring-2 focus:ring-primary">
                                <option value="created_at"
                                    {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                                <option value="recaptcha_score"
                                    {{ request('sort') == 'recaptcha_score' ? 'selected' : '' }}>
                                    Score</option>
                                <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="feedback_type" {{ request('sort') == 'feedback_type' ? 'selected' : '' }}>
                                    Type
                                </option>
                            </select>

                            <select name="direction" id="direction-select"
                                class="px-3 py-2 border border-primary rounded-md dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                                <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>↓
                                </option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>↑</option>
                            </select>
                        </div>

                        {{-- Action Buttons Group --}}
                        <div class="flex gap-2">
                            <button @click.prevent="showFilters = !showFilters" type="button"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                <span x-text="showFilters ? 'Hide Filters' : 'Filters'"></span>
                            </button>
                            <button @click.prevent="showBulkActions = !showBulkActions" type="button"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                <span x-text="showBulkActions ? 'Hide Bulk' : 'Bulk'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Advanced Filters Panel (Collapsible) --}}
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 lg:p-6 border border-gray-200 dark:border-gray-700">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Advanced Filters</h3>
                    <button @click="showFilters = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="advanced-filters-form" method="GET" action="{{ route('feedback.index') }}"
                    class="space-y-4">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

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
                                    Request
                                </option>
                                <option value="navigation" {{ request('type') == 'navigation' ? 'selected' : '' }}>
                                    Navigation
                                    Issue</option>
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
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Results Per Page --}}
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Per Page</label>
                            <select name="per_page"
                                class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    {{-- Date & Score Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Date Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" name="date_from"
                                    class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white"
                                    value="{{ request('date_from') }}">
                                <input type="date" name="date_to"
                                    class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white"
                                    value="{{ request('date_to') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">reCAPTCHA Score Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="score_min"
                                    class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white"
                                    value="{{ request('score_min') }}" step="0.1" min="0" max="1"
                                    placeholder="Min (0.0)">
                                <input type="number" name="score_max"
                                    class="filter-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white"
                                    value="{{ request('score_max') }}" step="0.1" min="0" max="1"
                                    placeholder="Max (1.0)">
                            </div>
                        </div>
                    </div>

                    {{-- Apply & Clear --}}
                    <div class="flex gap-2 pt-2">
                        <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded hover:bg-primary-dark transition-colors">
                            Apply Filters
                        </button>
                        <button type="button" onclick="clearFilters()"
                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors">
                            Clear All
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bulk Actions Panel (Collapsible) --}}
            <div x-show="showBulkActions" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 border border-gray-200 dark:border-gray-700">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bulk Actions</h3>
                    <button @click="showBulkActions = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="bulk-form" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        <select id="bulk-action"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                            <option value="">Select Action</option>
                            <option value="update-status">Update Status</option>
                            <option value="delete">Delete Selected</option>
                        </select>

                        <select id="bulk-status"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white hidden">
                            <option value="pending">Pending</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="resolved">Resolved</option>
                            <option value="archived">Archived</option>
                        </select>

                        <button type="button" onclick="executeBulkAction()"
                            class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors whitespace-nowrap">
                            Apply Action
                        </button>

                        <span id="selected-count" class="text-sm text-gray-600 dark:text-gray-400 font-medium"></span>
                    </div>
                </form>
            </div>
        </div>

        <div id="records-table">
            @include('pages.admin.feedback.partials.feedback-table', ['feedback' => $feedback])
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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

        function fetchFeedback() {
            const searchForm = document.getElementById('search-sort-form');
            const advancedForm = document.getElementById('advanced-filters-form');
            const formData = new FormData(searchForm);

            const advancedData = new FormData(advancedForm);
            for (let [key, value] of advancedData.entries()) {
                if (value && !formData.has(key)) {
                    formData.append(key, value);
                }
            }

            const params = new URLSearchParams(formData);
            const url = `{{ route('feedback.index') }}?${params.toString()}`;

            window.history.pushState({}, '', url);

            if (typeof window.showSpinner === 'function') {
                window.showSpinner();
            }
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
                        updateSelectedCount();
                    }
                })
                .catch(error => {
                    console.error('Error fetching feedback:', error);
                    tableContainer.style.opacity = '1';
                })
                .finally(() => {
                    if (typeof window.hideSpinner === 'function') {
                        window.hideSpinner();
                    }
                });
        }

        const searchInput = document.getElementById('search-input');
        const debouncedSearch = debounce(() => {
            fetchFeedback();
        }, 500);

        searchInput.addEventListener('input', debouncedSearch);
        document.getElementById('sort-select').addEventListener('change', fetchFeedback);
        document.getElementById('direction-select').addEventListener('change', fetchFeedback);

        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('change', fetchFeedback);
        });

        document.getElementById('search-sort-form').addEventListener('submit', (e) => {
            e.preventDefault();
            fetchFeedback();
        });

        document.getElementById('advanced-filters-form').addEventListener('submit', (e) => {
            e.preventDefault();
            fetchFeedback();
        });

        function clearFilters() {
            const searchInput = document.getElementById('search-input');
            const sortSelect = document.getElementById('sort-select');
            const directionSelect = document.getElementById('direction-select');

            document.querySelectorAll('.filter-input').forEach(input => {
                if (input.type === 'date' || input.type === 'number' || input.tagName === 'SELECT') {
                    input.value = '';
                }
            });

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

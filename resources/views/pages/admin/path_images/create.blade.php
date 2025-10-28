@extends('layouts.app')

@section('title', 'Upload Path Images')

@section('content')
    @if (auth()->user()->hasRole('Admin'))
        <x-floating-actions />

        <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 mb-8">
            <h2 class="text-2xl text-center mb-6 dark:text-gray-300">
                <span class="text-primary">Upload</span> Path Images
            </h2>
            <x-upload-progress-modal>
                {{-- Upload Form --}}
                <form id="uploadForm" action="{{ route('path-image.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Path Selector --}}
                    <div class="mb-4 dark:text-gray-300">
                        <div x-data="pathDropdown({{ $paths->toJson() }}, {{ $defaultPath->id ?? 'null' }})" class="mb-4 dark:text-gray-300 relative w-full"
                            @click.away="closeDropdown()">
                            <label class="block text-gray-700 mb-2 dark:text-gray-300">Select Path</label>

                            <!-- Dropdown Button -->
                            <button type="button" @click="toggleDropdown()"
                                class="w-full border border-primary rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-primary 
                                    dark:text-gray-300 bg-white dark:bg-blue-800/5 shadow-md flex items-center justify-between text-left">
                                <span x-text="selectedName || 'Select a path'"
                                    :class="!selectedName ? 'text-gray-400' : ''"></span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Panel -->
                            <div x-show="isOpen" x-transition
                                class="absolute z-50 w-full mt-2 bg-white text-gray-800 dark:bg-gray-800 
                                        border border-primary rounded-md shadow-lg overflow-hidden"
                                style="display: none;">

                                <!-- Search input inside dropdown -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input x-ref="searchInput" type="text" x-model="search" @input="filterPaths()"
                                        placeholder="Search paths"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                        focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-gray-300 text-sm">
                                </div>

                                <!-- Path Options -->
                                <div class="max-h-60 overflow-auto">
                                    <div x-show="filteredPaths.length === 0"
                                        class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                                        No paths found
                                    </div>

                                    <template x-for="path in filteredPaths" :key="path.id">
                                        <button type="button" @click="selectPath(path)"
                                            class="w-full text-left px-4 py-3 hover:bg-primary hover:text-white hover:bg-opacity-10 
                                                dark:hover:bg-gray-700 hover:pl-6 hover:border-l-4 hover:border-primary 
                                                transition-all duration-200 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                            :class="{
                                                'bg-primary bg-opacity-5 font-medium text-white': selectedId == path.id,
                                                'text-gray-700 dark:text-gray-300': selectedId != path.id
                                            }">
                                            <span x-text="path.display_name"></span>
                                            <span x-show="selectedId == path.id" class="float-right text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586
                                                                                6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1
                                                                                0 001.414 0l7-7a1 1 0 000-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Hidden input to store selected path -->
                            <input type="hidden" name="path_id" x-model="selectedId">
                        </div>
                    </div>

                    {{-- Path Image Limit Display --}}
                    <div id="pathLimitDisplay"
                        class="mb-4 p-3 rounded-lg border border-primary bg-blue-50 dark:bg-blue-800/5">
                        @php
                            $percentage = $maxImagesPerPath > 0 ? ($currentImageCount / $maxImagesPerPath) * 25 : 0;
                            $colorClass = 'text-green-600 dark:text-green-400';
                            if ($percentage >= 90) {
                                $colorClass = 'text-red-600 dark:text-red-400';
                            } elseif ($percentage >= 70) {
                                $colorClass = 'text-yellow-600 dark:text-yellow-400';
                            }
                        @endphp

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Path Images:</span>
                                <span class="{{ $colorClass }} text-lg font-bold ml-2">
                                    {{ $currentImageCount }} / {{ $maxImagesPerPath }}
                                </span>
                            </div>

                            @if ($remainingSlots > 0)
                                <span class="text-sm {{ $colorClass }} font-medium">
                                    {{ $remainingSlots }} slot{{ $remainingSlots !== 1 ? 's' : '' }} remaining
                                </span>
                            @else
                                <span class="text-sm text-red-600 dark:text-red-400 font-bold">
                                    ⚠️ PATH FULL
                                </span>
                            @endif
                        </div>

                        {{-- Progress bar --}}
                        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 
                                @if ($percentage >= 90) bg-secondary
                                @elseif($percentage >= 70) bg-orange
                                @else bg-tertiary @endif"
                                style="width: {{ min($percentage, 100) }}%">
                            </div>
                        </div>

                        @if ($remainingSlots === 0)
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">
                                This path has reached the maximum limit. Please delete some images or select a different
                                path.
                            </p>
                        @endif
                    </div>

                    {{-- Dropzone --}}
                    {{-- Update the dropzone label text to show the limit --}}
                    <label for="fileInput"
                        class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary dark:hover:bg-gray-800 transition-colors p-4 overflow-auto relative
                            {{ $remainingSlots === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">

                        @if ($remainingSlots > 0)
                            <span class="text-gray-600 dark:text-gray-300 mb-2">Drop images here or click to browse</span>
                            <span class="text-xs text-gray-400">
                                JPG, JPEG, PNG, GIF, BMP, SVG, WEBP | max 10 MB each | max 20 per upload
                            </span>
                            <span class="text-xs text-primary font-medium mt-1">
                                Path can accept {{ $remainingSlots }} more image(s)
                            </span>
                        @else
                            <span class="text-red-600 dark:text-red-400 font-bold mb-2">
                                ⚠️ This path has reached the maximum limit ({{ $maxImagesPerPath }} images)
                            </span>
                            <span class="text-xs text-gray-500">
                                Please delete some images or select a different path to upload
                            </span>
                        @endif

                        <input type="file" id="fileInput" multiple accept="image/*" class="hidden"
                            {{ $remainingSlots === 0 ? 'disabled' : '' }}>
                    </label>

                    <div id="fileError" class="text-red-500 text-sm mt-2 hidden"></div>
                    <div id="selectedFiles" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-primary text-white px-4 py-2 rounded-md border-2 border-primary duration-300 transition-all ease-in-out mt-4 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:bg-white hover:text-primary dark:hover:bg-gray-800 shadow-primary-hover"
                        disabled>
                        Upload Images
                    </button>
                </form>
            </x-upload-progress-modal>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // ====================================================================
        // GLOBAL VARIABLES AND FUNCTIONS (for Alpine.js access)
        // ====================================================================

        // Path limit tracking - make these global
        const MAX_IMAGES_PER_PATH = {{ $maxImagesPerPath ?? 25 }};
        let currentPathImageCount = {{ $currentImageCount ?? 0 }};
        let remainingSlots = {{ $remainingSlots ?? 25 }};

        // ====================================================================
        // FETCH PATH IMAGE COUNT (WITH GLOBAL SPINNER)
        // ====================================================================
        async function fetchPathImageCount(pathId) {
            // Prevent multiple simultaneous calls for the same path
            if (window.fetchingPathData === pathId) {
                return;
            }

            window.fetchingPathData = pathId;

            if (window.showSpinner) {
                window.showSpinner();
            }

            try {
                const response = await fetch(`{{ route('path-image.count') }}?path_id=${pathId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    currentPathImageCount = data.current_count;
                    remainingSlots = data.remaining_slots;

                    updatePathLimitDisplay();
                    updateDropzoneState();

                    if (data.is_full) {
                        // Use window.files to access the global files array
                        if (window.files !== undefined) {
                            window.files = [];
                            if (typeof renderPreviews === 'function') {
                                renderPreviews();
                            }
                            if (typeof updateSubmitButton === 'function') {
                                updateSubmitButton();
                            }
                        }
                    }
                } else {
                    throw new Error('Failed to fetch path info');
                }
            } catch (error) {
                console.error('❌ Failed to fetch path image count:', error);
                const limitDisplay = document.getElementById('pathLimitDisplay');
                if (limitDisplay) {
                    limitDisplay.innerHTML = `
                    <div class="text-red-600 dark:text-red-400 text-sm p-3">
                        ⚠️ Failed to load path information. Please refresh the page.
                    </div>
                `;
                }

            } finally {
                if (window.hideSpinner) {
                    window.hideSpinner();
                }
                // Clear the lock
                window.fetchingPathData = null;
            }
        }

        // ====================================================================
        // UPDATE PATH LIMIT DISPLAY (Make this global)
        // ====================================================================
        function updatePathLimitDisplay() {
            const limitDisplay = document.getElementById('pathLimitDisplay');
            if (!limitDisplay) return;

            const percentage = (currentPathImageCount / MAX_IMAGES_PER_PATH) * 100;
            let colorClass = 'text-green-600 dark:text-green-400';
            let bgClass = 'bg-tertiary';

            if (percentage >= 90) {
                colorClass = 'text-red-600 dark:text-red-400';
                bgClass = 'bg-secondary';
            } else if (percentage >= 70) {
                colorClass = 'text-yellow-600 dark:text-yellow-400';
                bgClass = 'bg-orange';
            }

            limitDisplay.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Path Images:</span>
                    <span class="${colorClass} text-lg font-bold ml-2">
                        ${currentPathImageCount} / ${MAX_IMAGES_PER_PATH}
                    </span>
                </div>
                ${remainingSlots > 0 
                    ? `<span class="text-sm ${colorClass} font-medium">${remainingSlots} slot${remainingSlots !== 1 ? 's' : ''} remaining</span>`
                    : `<span class="text-sm text-red-600 dark:text-red-400 font-bold">⚠️ PATH FULL</span>`
                }
                    </div>
                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300 ${bgClass}" style="width: ${Math.min(percentage, 100)}%"></div>
                    </div>
                    ${remainingSlots === 0 
                        ? `<p class="text-xs text-red-600 dark:text-red-400 mt-2">This path has reached the maximum limit. Please delete some images or select a different path.</p>`
                        : ''
                }
            `;
        }

        // ====================================================================
        // UPDATE DROPZONE STATE (Make this global)
        // ====================================================================
        function updateDropzoneState() {
            const fileInput = document.getElementById('fileInput');
            const dropzone = fileInput?.closest('label');

            if (!dropzone) return;

            if (remainingSlots === 0) {
                dropzone.className =
                    'flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded p-4 overflow-auto relative opacity-50 cursor-not-allowed';

                fileInput.disabled = true;

                // Update only the text content, keep the file input
                const spans = dropzone.querySelectorAll('span');
                if (spans.length >= 1) {
                    dropzone.innerHTML = `
                    <span class="text-red-600 dark:text-red-400 font-bold mb-2">
                        ⚠️ This path has reached the maximum limit (${MAX_IMAGES_PER_PATH} images)
                    </span>
                    <span class="text-xs text-gray-500">
                        Please delete some images or select a different path to upload
                    </span>
                    <input type="file" id="fileInput" multiple accept="image/*" class="hidden" disabled>
                `;
                }
            } else {
                dropzone.className =
                    'flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary dark:hover:bg-gray-800 transition-colors p-4 overflow-auto relative';

                const currentInput = document.getElementById('fileInput');
                if (currentInput) {
                    currentInput.disabled = false;
                }

                dropzone.innerHTML = `
                <span class="text-gray-600 dark:text-gray-300 mb-2">Drop images here or click to browse</span>
                <span class="text-xs text-gray-400">
                    JPG, JPEG, PNG, GIF, BMP, SVG, WEBP | max 10 MB each | max 20 per upload
                </span>
                <span class="text-xs text-primary font-medium mt-1">
                    Path can accept ${remainingSlots} more image(s)
                </span>
                <input type="file" id="fileInput" multiple accept="image/*" class="hidden">
            `;
            }
        }

        // ====================================================================
        // ALPINE.JS PATH DROPDOWN COMPONENT
        // ====================================================================
        function pathDropdown(paths, defaultPathId) {
            return {
                isOpen: false,
                search: '',
                paths: paths.map(p => ({
                    id: p.id,
                    display_name: `${p.from_room?.name || 'Room #' + p.from_room_id} → ${p.to_room?.name || 'Room #' + p.to_room_id}`
                })),
                filteredPaths: [],
                selectedId: defaultPathId,
                selectedName: '',

                init: async function() {
                    const storedId = sessionStorage.getItem('selectedPathId');
                    const targetId = storedId ? parseInt(storedId, 10) : this.selectedId;
                    const selected = this.paths.find(p => p.id === targetId);

                    if (selected) {
                        this.selectedId = selected.id;
                        this.selectedName = selected.display_name;
                        sessionStorage.setItem('selectedPathId', this.selectedId);

                        if (this.selectedId !== defaultPathId) {
                            this.updateBrowserUrl();
                        }

                        // Dispatch event so other modules can react (floating link etc.)
                        window.dispatchEvent(new CustomEvent("path-changed", {
                            detail: {
                                pathId: this.selectedId
                            }
                        }));

                        // Call the global fetch function (not this.fetchPathImageCount)
                        if (typeof fetchPathImageCount === 'function') {
                            await fetchPathImageCount(this.selectedId);
                        } else if (typeof window.fetchPathImageCount === 'function') {
                            await window.fetchPathImageCount(this.selectedId);
                        }
                    } else {
                        this.selectedId = defaultPathId;
                        const defaultPath = this.paths.find(p => p.id === defaultPathId);
                        this.selectedName = defaultPath ? defaultPath.display_name : '';

                        if (defaultPathId) {
                            window.dispatchEvent(new CustomEvent("path-changed", {
                                detail: {
                                    pathId: defaultPathId
                                }
                            }));

                            if (typeof fetchPathImageCount === 'function') {
                                await fetchPathImageCount(defaultPathId);
                            } else if (typeof window.fetchPathImageCount === 'function') {
                                await window.fetchPathImageCount(defaultPathId);
                            }
                        }
                    }

                    this.filteredPaths = this.paths;
                },

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                closeDropdown() {
                    this.isOpen = false;
                },

                filterPaths() {
                    const term = this.search.toLowerCase();
                    this.filteredPaths = this.paths.filter(p =>
                        p.display_name.toLowerCase().includes(term)
                    );
                },

                selectPath(path) {

                    // 1️⃣ Update local state
                    this.selectedId = path.id;
                    this.selectedName = path.display_name;
                    this.isOpen = false;
                    this.search = '';
                    this.filterPaths();

                    // 2️⃣ Save selection globally (and trigger "path-changed" event)
                    if (typeof savePathSelection === 'function') {
                        savePathSelection(path.id);
                    } else {
                        // Fallback if module isn’t loaded
                        sessionStorage.setItem('selectedPathId', path.id);
                        window.dispatchEvent(
                            new CustomEvent('path-changed', {
                                detail: {
                                    pathId: path.id
                                }
                            })
                        );
                    }

                    // 3️⃣ Update browser URL (no reload)
                    const baseUrl = "{{ route('path-image.create', ':pathId') }}";
                    const newUrl = baseUrl.replace(':pathId', path.id);
                    window.history.replaceState({}, '', newUrl);

                    // 4️⃣ Update hidden form input (for Laravel form submission)
                    const pathIdInput = document.querySelector('input[name="path_id"]');
                    if (pathIdInput) {
                        pathIdInput.value = path.id;
                    }

                    // 5️⃣ Clear previous files (reset UI cleanly)
                    if (window.files !== undefined) {
                        window.files = [];
                        if (typeof renderPreviews === 'function') renderPreviews();
                        if (typeof updateSubmitButton === 'function') updateSubmitButton();
                    }

                    // 6️⃣ Immediately fetch the new path's image count
                    // (use global or window function depending on how it's declared)
                    if (typeof fetchPathImageCount === 'function') {
                        fetchPathImageCount(path.id);
                    } else if (typeof window.fetchPathImageCount === 'function') {
                        window.fetchPathImageCount(path.id);
                    } else {
                        console.warn('⚠️ fetchPathImageCount not found — cannot update path info.');
                    }
                },

                updateBrowserUrl() {
                    const baseUrl = "{{ route('path-image.create', ':pathId') }}";
                    const newUrl = baseUrl.replace(':pathId', this.selectedId);
                    window.history.replaceState({}, '', newUrl);
                }
            };
        }

        // ==========================================================
        // DYNAMIC PATH LIMIT DISPLAY SYSTEM
        // ==========================================================

        document.addEventListener('DOMContentLoaded', function() {
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const submitBtn = document.getElementById('submitBtn');
            const uploadForm = document.getElementById('uploadForm');
            const pathIdInput = document.querySelector('input[name="path_id"]');

            // Exit early if required elements don't exist
            if (!selectedFilesContainer || !submitBtn || !uploadForm || !pathIdInput) {
                console.error('Required form elements not found');
                return;
            }

            // Make files global so Alpine.js can access it
            window.files = [];
            let isSubmitting = false;

            // ===== CONFIGURATION =====
            const MAX_FILES = 25;
            const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
            const MAX_TOTAL_SIZE = 250 * 1024 * 1024; // 100 MB
            const ALLOWED_TYPES = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                'image/bmp', 'image/svg+xml', 'image/webp'
            ];
            const COMPRESS_ENABLED = true;
            const MAX_DIMENSION = 2000;
            const COMPRESSION_QUALITY = 0.85;

            // ====================================================================
            // EVENT HANDLER FUNCTIONS (DEFINED FIRST)
            // ====================================================================
            function handleFileInputChange(e) {
                addFiles(Array.from(e.target.files));
            }

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function handleDragEnter(e) {
                if (remainingSlots > 0) {
                    const dropzone = e.currentTarget;
                    dropzone.classList.add('border-primary', 'bg-primary/5');
                }
            }

            function handleDragLeave(e) {
                const dropzone = e.currentTarget;
                dropzone.classList.remove('border-primary', 'bg-primary/5');
            }

            function handleDrop(e) {
                const dropzone = e.currentTarget;
                dropzone.classList.remove('border-primary', 'bg-primary/5');
                if (remainingSlots === 0) return;
                const dt = e.dataTransfer;
                const droppedFiles = Array.from(dt.files);
                addFiles(droppedFiles);
            }

            // ====================================================================
            // HELPER: ATTACH EVENT LISTENERS USING EVENT DELEGATION
            // ====================================================================
            function attachEventListeners() {
                // Use the form as our stable container for event delegation
                if (!uploadForm) {
                    console.error('Upload form not found');
                    return;
                }

                // File input change handler (delegated)
                uploadForm.addEventListener('change', function(e) {
                    if (e.target.id === 'fileInput') {
                        handleFileInputChange(e);
                    }
                });

                // Drag and drop handlers (delegated to form)
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    uploadForm.addEventListener(eventName, function(e) {
                        const fileInput = document.getElementById('fileInput');
                        const dropzone = fileInput?.closest('label');

                        if (dropzone && (e.target === dropzone || dropzone.contains(e.target))) {
                            preventDefaults(e);

                            if (eventName === 'dragenter' || eventName === 'dragover') {
                                handleDragEnter(e);
                            } else if (eventName === 'dragleave') {
                                handleDragLeave(e);
                            } else if (eventName === 'drop') {
                                handleDrop(e);
                            }
                        }
                    }, false);
                });
            }

            // ====================================================================
            // LISTEN FOR PATH CHANGES (RESET + REFETCH)
            // ====================================================================
            window.addEventListener('path-changed', async function(e) {
                if (e.detail && e.detail.pathId) {
                    const newPathId = e.detail.pathId;

                    // 1️⃣ Reset global tracking variables
                    currentPathImageCount = 0;
                    remainingSlots = MAX_IMAGES_PER_PATH;

                    // 2️⃣ Clear files and UI immediately
                    window.files = [];
                    if (typeof renderPreviews === 'function') renderPreviews();
                    if (typeof updateSubmitButton === 'function') updateSubmitButton();

                    // 3️⃣ Update limit display to show reset state
                    updatePathLimitDisplay();
                    updateDropzoneState();

                    // 4️⃣ Fetch new path data cleanly
                    await fetchPathImageCount(newPathId);
                }
            });

            // ====================================================================
            // UTILITY FUNCTIONS
            // ====================================================================
            function updateSubmitButton() {
                const isDisabled = window.files.length === 0 || isSubmitting || remainingSlots === 0;
                submitBtn.disabled = isDisabled;

                if (remainingSlots === 0) {
                    submitBtn.textContent = 'Path Full - Cannot Upload';
                } else if (window.files.length > 0) {
                    submitBtn.textContent =
                        `Upload ${window.files.length} Image${window.files.length > 1 ? 's' : ''}`;
                } else {
                    submitBtn.textContent = 'Upload Images';
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            function getTotalSize() {
                return window.files.reduce((total, file) => total + file.size, 0);
            }

            function validateFiles(newFiles) {
                const errors = [];
                const currentCount = window.files.length;
                const potentialTotal = currentCount + newFiles.length;

                if (potentialTotal > MAX_FILES) {
                    errors.push(
                        `Maximum ${MAX_FILES} files per upload. Currently selected: ${currentCount}, trying to add: ${newFiles.length}`
                    );
                    return {
                        valid: false,
                        errors,
                        validFiles: []
                    };
                }

                const totalImagesAfterUpload = currentPathImageCount + potentialTotal;
                if (totalImagesAfterUpload > MAX_IMAGES_PER_PATH) {
                    const allowedToAdd = Math.max(0, remainingSlots - currentCount);
                    if (allowedToAdd === 0) {
                        errors.push(
                            `This path has reached the maximum limit of ${MAX_IMAGES_PER_PATH} images. Please delete some images first.`
                        );
                    } else {
                        errors.push(
                            `This path can only accept ${allowedToAdd} more image(s). Currently has ${currentPathImageCount} images (max: ${MAX_IMAGES_PER_PATH}).`
                        );
                    }
                    return {
                        valid: false,
                        errors,
                        validFiles: []
                    };
                }

                const validFiles = [];

                newFiles.forEach(file => {
                    if (!ALLOWED_TYPES.includes(file.type)) {
                        errors.push(`"${file.name}" - Invalid file type. Only images are allowed.`);
                        return;
                    }

                    if (file.size > MAX_FILE_SIZE) {
                        errors.push(
                            `"${file.name}" - File too large (${formatFileSize(file.size)}). Maximum ${formatFileSize(MAX_FILE_SIZE)} per file.`
                        );
                        return;
                    }

                    const exists = window.files.some(f => f.name === file.name && f.size === file.size);
                    if (exists) {
                        errors.push(`"${file.name}" - Already added.`);
                        return;
                    }

                    validFiles.push(file);
                });

                const currentSize = getTotalSize();
                const newFilesSize = validFiles.reduce((total, file) => total + file.size, 0);
                const totalSize = currentSize + newFilesSize;

                if (totalSize > MAX_TOTAL_SIZE) {
                    errors.push(
                        `Total size would exceed ${formatFileSize(MAX_TOTAL_SIZE)}. Current: ${formatFileSize(currentSize)}, trying to add: ${formatFileSize(newFilesSize)}`
                    );
                    return {
                        valid: false,
                        errors,
                        validFiles: []
                    };
                }

                return {
                    valid: errors.length === 0,
                    errors,
                    validFiles
                };
            }

            function showError(messages) {
                const fileError = document.getElementById('fileError');
                if (!fileError) return;

                fileError.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded p-3 text-sm text-red-800 dark:text-red-200">
                    <div class="font-semibold mb-1">⚠ Upload Warning:</div>
                    ${messages.map(msg => `<div class="mt-1">${msg}</div>`).join('')}
                </div>
            `;
                fileError.classList.remove('hidden');

                // Fade out gently after 10 seconds
                setTimeout(() => fileError.classList.add('hidden'), 10000);
            }

            async function compressImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;

                            if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
                                if (width > height) {
                                    height = Math.round((height * MAX_DIMENSION) / width);
                                    width = MAX_DIMENSION;
                                } else {
                                    width = Math.round((width * MAX_DIMENSION) / height);
                                    height = MAX_DIMENSION;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob(
                                (blob) => {
                                    if (blob && blob.size < file.size) {
                                        const compressedFile = new File([blob], file.name, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now()
                                        });
                                        resolve(compressedFile);
                                    } else {
                                        resolve(file);
                                    }
                                },
                                'image/jpeg',
                                COMPRESSION_QUALITY
                            );
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            async function processFilesWithCompression(filesToProcess) {
                const fileError = document.getElementById('fileError');
                let compressionInfo = [];

                fileError.innerHTML = '<div class="text-blue-600">⏳ Compressing images...</div>';
                fileError.classList.remove('hidden');

                for (const file of filesToProcess) {
                    try {
                        const originalSize = file.size;
                        const compressed = await compressImage(file);
                        const savedBytes = originalSize - compressed.size;

                        if (savedBytes > 0) {
                            compressionInfo.push(
                                `"${file.name}" compressed: ${formatFileSize(originalSize)} → ${formatFileSize(compressed.size)}`
                            );
                        }
                        window.files.push(compressed);
                    } catch (error) {
                        console.error('Compression failed for', file.name, error);
                        window.files.push(file);
                    }
                }

                fileError.classList.add('hidden');

                if (compressionInfo.length > 0) {
                    const successDiv = document.createElement('div');
                    successDiv.className =
                        'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded p-3 text-sm text-green-800 dark:text-green-200 mt-2';
                    successDiv.innerHTML = `
                    <div class="font-semibold mb-1">✓ Images optimized for upload:</div>
                    ${compressionInfo.map(info => `<div class="text-xs">${info}</div>`).join('')}
                `;
                    selectedFilesContainer.insertAdjacentElement('beforebegin', successDiv);
                    setTimeout(() => successDiv.remove(), 8000);
                }

                renderPreviews();
                updateSubmitButton();
            }

            function addFiles(newFiles) {
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');

                const validation = validateFiles(newFiles);

                if (validation.errors.length > 0) {
                    showError(validation.errors);
                }

                if (validation.validFiles.length > 0) {
                    if (COMPRESS_ENABLED) {
                        processFilesWithCompression(validation.validFiles);
                    } else {
                        window.files.push(...validation.validFiles);
                        renderPreviews();
                        updateSubmitButton();
                    }
                }
            }

            function removeFile(index) {
                window.files.splice(index, 1);
                renderPreviews();
                updateSubmitButton();
            }

            function renderPreviews() {
                selectedFilesContainer.innerHTML = '';
                if (!window.files.length) return;

                const totalSize = getTotalSize();
                const afterUploadTotal = currentPathImageCount + window.files.length;
                const limitColor = afterUploadTotal > MAX_IMAGES_PER_PATH * 0.9 ? 'text-red-600' : 'text-blue-800';

                const infoBanner = document.createElement('div');
                infoBanner.className =
                    'col-span-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3 text-sm';
                infoBanner.innerHTML = `
                <div class="flex justify-between items-center text-blue-800 dark:text-blue-200">
                    <span><strong>${window.files.length}</strong> of ${MAX_FILES} files selected</span>
                    <span><strong>${formatFileSize(totalSize)}</strong> of ${formatFileSize(MAX_TOTAL_SIZE)}</span>
                </div>
                <div class="mt-2 ${limitColor} dark:${limitColor} font-medium">
                    Path will have <strong>${afterUploadTotal} / ${MAX_IMAGES_PER_PATH}</strong> images after upload
                </div>
            `;
                selectedFilesContainer.appendChild(infoBanner);

                window.files.forEach((file, index) => {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden border shadow-sm group';

                    reader.onload = e => {
                        div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs p-1 truncate">
                            ${file.name}
                        </div>
                        <div class="absolute top-1 left-1 bg-black/60 text-white text-xs px-1 rounded">
                            ${formatFileSize(file.size)}
                        </div>
                        <button type="button" 
                            class="absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full
                            flex items-center justify-center text-sm hover:bg-red-600 transition-colors
                            opacity-0 group-hover:opacity-100"
                            data-index="${index}" 
                            title="Remove">×</button>
                    `;

                        const removeBtn = div.querySelector('button');
                        removeBtn.addEventListener('click', function() {
                            removeFile(parseInt(this.dataset.index));
                        });
                    };

                    reader.readAsDataURL(file);
                    selectedFilesContainer.appendChild(div);
                });
            }

            // ====================================================================
            // FORM SUBMIT HANDLER
            // ====================================================================
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isSubmitting || window.files.length === 0) return;

                const totalSize = getTotalSize();
                if (totalSize > MAX_TOTAL_SIZE) {
                    showError([
                        `Total size (${formatFileSize(totalSize)}) exceeds maximum allowed (${formatFileSize(MAX_TOTAL_SIZE)})`
                    ]);
                    return;
                }

                if (window.files.length > MAX_FILES) {
                    showError([
                        `Number of files (${window.files.length}) exceeds maximum allowed (${MAX_FILES})`
                    ]);
                    return;
                }

                isSubmitting = true;
                updateSubmitButton();

                window.dispatchEvent(new CustomEvent('upload-start'));

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('path_id', pathIdInput.value);

                window.files.forEach(file => {
                    formData.append('files[]', file);
                });

                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        window.dispatchEvent(new CustomEvent('upload-progress', {
                            detail: {
                                progress: percentComplete
                            }
                        }));
                    }
                });

                xhr.addEventListener('load', function() {
                    window.dispatchEvent(new CustomEvent('upload-finish'));
                    if (xhr.status >= 200 && xhr.status < 400) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        showError(['Upload failed. Please try again.']);
                        isSubmitting = false;
                        updateSubmitButton();
                    }
                });

                xhr.addEventListener('error', function() {
                    window.dispatchEvent(new CustomEvent('upload-finish'));
                    showError(['Network error. Please check your connection and try again.']);
                    isSubmitting = false;
                    updateSubmitButton();
                });

                xhr.addEventListener('timeout', function() {
                    window.dispatchEvent(new CustomEvent('upload-finish'));
                    showError([
                        'Upload timeout. The files may be too large or your connection is slow.'
                    ]);
                    isSubmitting = false;
                    updateSubmitButton();
                });

                xhr.open('POST', uploadForm.action);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.timeout = 300000;
                xhr.send(formData);
            });

            // ====================================================================
            // INITIALIZE
            // ====================================================================
            attachEventListeners();
            updateSubmitButton();
            updatePathLimitDisplay();
        });
    </script>
@endpush

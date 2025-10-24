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
                                    dark:text-gray-300 bg-white dark:bg-gray-800 shadow-md flex items-center justify-between text-left">
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
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586
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

                    {{-- Dropzone --}}
                    <label for="fileInput"
                        class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary dark:hover:bg-gray-800 transition-colors p-4 overflow-auto relative">
                        <span class="text-gray-600 dark:text-gray-300 mb-2">Drop images here or click to browse</span>
                        <span class="text-xs text-gray-400">
                            JPG, JPEG, PNG, GIF, BMP, SVG, WEBP | max 10 MB each | multiple allowed
                        </span>

                        <input type="file" id="fileInput" multiple accept="image/*" class="hidden">
                    </label>

                    <div id="fileError" class="text-red-500 text-sm mt-2 hidden"></div>
                    <div id="selectedFiles" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-primary text-white px-4 py-2 rounded-md border-2 border-primary duration-300 transition-all ease-in-out mt-4 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:bg-white hover:text-primary dark:hover:bg-gray-800 shadow-primary-hover"
                        disabled>
                        Upload Images
                    </button>
                </form>
                {{-- Progress Modal handled in the component --}}
            </x-upload-progress-modal>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
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

                init() {
                    // Priority: sessionStorage > defaultPathId (from URL)
                    const storedId = sessionStorage.getItem('selectedPathId');

                    // If there's a stored ID, use it; otherwise use the URL parameter
                    const targetId = storedId ? parseInt(storedId, 10) : this.selectedId;

                    const selected = this.paths.find(p => p.id === targetId);

                    if (selected) {
                        this.selectedId = selected.id;
                        this.selectedName = selected.display_name;

                        // Save back to sessionStorage
                        sessionStorage.setItem('selectedPathId', this.selectedId);

                        // Update the URL if different from what was passed
                        if (this.selectedId !== defaultPathId) {
                            this.updateBrowserUrl();
                        }
                    } else {
                        // Fallback to default
                        this.selectedId = defaultPathId;
                        const defaultPath = this.paths.find(p => p.id === defaultPathId);
                        this.selectedName = defaultPath ? defaultPath.display_name : '';
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
                    this.selectedId = path.id;
                    this.selectedName = path.display_name;
                    this.isOpen = false;
                    this.search = '';
                    this.filterPaths();

                    // Save selection to sessionStorage
                    sessionStorage.setItem('selectedPathId', path.id);

                    // Update the browser URL
                    this.updateBrowserUrl();
                },

                updateBrowserUrl() {
                    // Update URL without page reload
                    const baseUrl = "{{ route('path-image.create', ':pathId') }}";
                    const newUrl = baseUrl.replace(':pathId', this.selectedId);
                    window.history.replaceState({}, '', newUrl);
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const submitBtn = document.getElementById('submitBtn');
            const uploadForm = document.getElementById('uploadForm');
            const pathIdInput = document.querySelector('input[name="path_id"]');

            // Exit early if required elements don't exist
            if (!fileInput || !selectedFilesContainer || !submitBtn || !uploadForm || !pathIdInput) {
                console.error('Required form elements not found');
                return;
            }

            let files = [];
            let isSubmitting = false;

            // ===== CONFIGURATION =====
            const MAX_FILES = 20;
            const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
            const MAX_TOTAL_SIZE = 100 * 1024 * 1024; // 100 MB
            const ALLOWED_TYPES = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                'image/bmp', 'image/svg+xml', 'image/webp'
            ];

            // Client-side compression settings
            const COMPRESS_ENABLED = true;
            const MAX_DIMENSION = 2000;
            const COMPRESSION_QUALITY = 0.85;

            function updateSubmitButton() {
                submitBtn.disabled = files.length === 0 || isSubmitting;

                if (files.length > 0) {
                    submitBtn.textContent = `Upload ${files.length} Image${files.length > 1 ? 's' : ''}`;
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
                return files.reduce((total, file) => total + file.size, 0);
            }

            function validateFiles(newFiles) {
                const errors = [];
                const currentCount = files.length;
                const potentialTotal = currentCount + newFiles.length;

                if (potentialTotal > MAX_FILES) {
                    errors.push(
                        `Maximum ${MAX_FILES} files allowed. Currently selected: ${currentCount}, trying to add: ${newFiles.length}`
                    );
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

                    const exists = files.some(f => f.name === file.name && f.size === file.size);
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
                fileError.innerHTML = messages.map(msg => `<div>⚠ ${msg}</div>`).join('');
                fileError.classList.remove('hidden');

                setTimeout(() => {
                    fileError.classList.add('hidden');
                }, 8000);
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

                        files.push(compressed);
                    } catch (error) {
                        console.error('Compression failed for', file.name, error);
                        files.push(file);
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
                        files.push(...validation.validFiles);
                        renderPreviews();
                        updateSubmitButton();
                    }
                }

                fileInput.value = '';
            }

            function removeFile(index) {
                files.splice(index, 1);
                renderPreviews();
                updateSubmitButton();
            }

            function renderPreviews() {
                selectedFilesContainer.innerHTML = '';
                if (!files.length) return;

                const totalSize = getTotalSize();
                const infoBanner = document.createElement('div');
                infoBanner.className =
                    'col-span-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3 text-sm';
                infoBanner.innerHTML = `
            <div class="flex justify-between items-center text-blue-800 dark:text-blue-200">
                <span><strong>${files.length}</strong> of ${MAX_FILES} files</span>
                <span><strong>${formatFileSize(totalSize)}</strong> of ${formatFileSize(MAX_TOTAL_SIZE)}</span>
            </div>
            `;
                selectedFilesContainer.appendChild(infoBanner);

                files.forEach((file, index) => {
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
                        title="Remove">
                        ×
                    </button>
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

            // File input change handler
            fileInput.addEventListener('change', function() {
                addFiles(Array.from(this.files));
            });

            // Drag and drop handlers
            const dropzone = fileInput.parentElement;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('border-primary', 'bg-primary/5');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('border-primary', 'bg-primary/5');
                });
            });

            dropzone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const droppedFiles = Array.from(dt.files);
                addFiles(droppedFiles);
            });

            // Form submit handler
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isSubmitting || files.length === 0) {
                    return;
                }

                const totalSize = getTotalSize();
                if (totalSize > MAX_TOTAL_SIZE) {
                    showError([
                        `Total size (${formatFileSize(totalSize)}) exceeds maximum allowed (${formatFileSize(MAX_TOTAL_SIZE)})`
                    ]);
                    return;
                }

                if (files.length > MAX_FILES) {
                    showError([
                        `Number of files (${files.length}) exceeds maximum allowed (${MAX_FILES})`
                    ]);
                    return;
                }

                isSubmitting = true;
                updateSubmitButton();

                // Show modal via Alpine event
                window.dispatchEvent(new CustomEvent('upload-start'));

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('path_id', pathIdInput.value);

                files.forEach(file => {
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
                xhr.timeout = 300000; // 5 minutes
                xhr.send(formData);
            });

            updateSubmitButton();
        });
    </script>
@endpush

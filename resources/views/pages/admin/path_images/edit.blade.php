@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-6xl dark:text-gray-300">
        <!-- Header -->
        <div class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-300">
                    <span class="text-primary">Edit</span> Path Images
                </h1>
                <p class="text-primary mt-1">
                    {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form id="pathImagesForm" action="{{ route('path-image.update-multiple', $path) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="path_id" value="{{ $path->id }}">

            <!-- Pagination - Top -->
            @if (method_exists($pathImages, 'links'))
                <div id="paginationTop" class="mb-4">
                    {{ $pathImages->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            @endif

            <!-- Images Grid with Add Button -->
            <div class="relative mb-8">
                <!-- Add Path Image Button -->
                <div class="absolute -top-3 -right-3 z-10 group">
                    <a href="{{ route('path-image.create', $path->id) }}"
                        class="hover-underline inline-flex items-center justify-center p-2 rounded-md hover:scale-125 transition duration-200 bg-white dark:bg-gray-800 shadow-md border border-primary">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                            alt="Add Icon" class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                    </a>
                    <div
                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                        Add Path Image
                        <div
                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                        </div>
                    </div>
                </div>

                <!-- Grid Container -->
                <div id="imagesGridContainer">
                    @include('pages.admin.path_images.partials.images-grid')
                </div>
            </div>

            <!-- Pagination - Bottom -->
            @if (method_exists($pathImages, 'links'))
                <div id="paginationBottom" class="mb-6">
                    {{ $pathImages->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            @endif

            <!-- Spacer -->
            <div class="h-28 sm:h-24"></div>
        </form>

        <!-- Actions - Fixed at bottom -->
        <div class="fixed bottom-0 left-0 right-0 z-40 flex justify-center px-4 pt-3 pb-24 sm:pb-20 md:pb-4">
            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-2xl border border-primary shadow-lg rounded-lg px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 w-full max-w-2xl">
                <button type="button" onclick="toggleAllDeletes()"
                    class="px-4 py-2 text-sm font-medium border-2 border-secondary text-white bg-secondary hover:text-secondary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-secondary shadow-secondary-hover w-full sm:w-auto order-2 sm:order-1">
                    Toggle All Deletes
                </button>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto order-1 sm:order-2">
                    <button type="submit" id="submitButton" form="pathImagesForm"
                        class="px-4 py-2 text-sm font-medium border-2 border-primary text-white bg-primary hover:text-primary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-primary shadow-primary-hover w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary disabled:hover:text-white">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const MAX_FILE_SIZE_ORIGINAL = 10 * 1024 * 1024;
        const MAX_FILE_SIZE_COMPRESSED = 5 * 1024 * 1024;
        const MAX_DIMENSION = 2000;
        const COMPRESSION_QUALITY = 0.85;

        const compressedFiles = new Map();
        const submitButton = document.getElementById('submitButton');
        const form = document.getElementById('pathImagesForm');

        sessionStorage.setItem('selectedPathId', '{{ $path->id }}');

        // ===== STATE MANAGEMENT FOR PAGINATION =====
        const formState = {
            orders: {},
            deletes: new Set(),
            files: {},
            compressedFiles: {}
        };

        function initializeHandlers() {
            // Order input changes
            document.querySelectorAll('.order-input').forEach(input => {
                input.addEventListener('change', function() {
                    const imageId = this.dataset.imageId;
                    formState.orders[imageId] = this.value;

                    const badge = this.closest('.image-card')?.querySelector('.order-badge-value');
                    if (badge) badge.textContent = this.value;
                });
            });

            // Delete checkbox changes
            document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const imageId = this.dataset.imageId;
                    if (this.checked) {
                        formState.deletes.add(imageId);
                    } else {
                        formState.deletes.delete(imageId);
                    }
                });
            });

            // File input handling with compression
            document.querySelectorAll('.image-file-input').forEach(input => {
                input.addEventListener('change', function() {
                    validateAndCompressFile(this);
                });
            });

            // Clear file buttons
            document.querySelectorAll('.clear-file-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageId = this.dataset.imageId;
                    const fileInput = document.querySelector(
                        `.image-file-input[data-image-id="${imageId}"]`);
                    const fileNameDisplay = document.querySelector(
                        `.file-name-display[data-image-id="${imageId}"]`);
                    const fileSizeError = document.querySelector(
                        `.file-size-error[data-image-id="${imageId}"]`);

                    fileInput.value = '';
                    this.classList.add('hidden');
                    fileNameDisplay.classList.add('hidden');
                    fileSizeError.classList.add('hidden');
                    fileInput.classList.remove('border-red-500', 'border-green-500', 'border-blue-500');
                    fileInput.classList.add('border-primary');

                    delete formState.files[imageId];
                    delete formState.compressedFiles[imageId];
                    compressedFiles.delete(imageId);

                    validateAllFileSizes();
                });
            });
        }

        function restoreFormState() {
            // Restore orders
            Object.keys(formState.orders).forEach(imageId => {
                const input = document.querySelector(`.order-input[data-image-id="${imageId}"]`);
                if (input) {
                    input.value = formState.orders[imageId];
                    const badge = input.closest('.image-card')?.querySelector('.order-badge-value');
                    if (badge) badge.textContent = formState.orders[imageId];
                }
            });

            // Restore deletes
            formState.deletes.forEach(imageId => {
                const checkbox = document.querySelector(`.delete-checkbox[data-image-id="${imageId}"]`);
                if (checkbox) checkbox.checked = true;
            });

            // Restore files
            Object.keys(formState.files).forEach(imageId => {
                const fileInput = document.querySelector(`.image-file-input[data-image-id="${imageId}"]`);
                const clearBtn = document.querySelector(`.clear-file-btn[data-image-id="${imageId}"]`);
                const fileNameDisplay = document.querySelector(`.file-name-display[data-image-id="${imageId}"]`);

                if (fileInput && formState.files[imageId]) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(formState.files[imageId]);
                    fileInput.files = dataTransfer.files;

                    if (clearBtn) clearBtn.classList.remove('hidden');

                    // Restore compressed file if exists
                    if (formState.compressedFiles[imageId]) {
                        compressedFiles.set(imageId, formState.compressedFiles[imageId]);

                        const file = formState.files[imageId];
                        const compressed = formState.compressedFiles[imageId];
                        const savedBytes = file.size - compressed.size;
                        const savingsPercent = Math.round((savedBytes / file.size) * 100);

                        fileInput.classList.remove('border-red-500', 'border-blue-500', 'border-primary');
                        fileInput.classList.add('border-green-500');

                        if (fileNameDisplay) {
                            fileNameDisplay.innerHTML = `
                                <span class="font-medium text-green-600 dark:text-green-400">✓ Ready to upload</span><br>
                                <span class="text-gray-600 dark:text-gray-400">
                                    ${formatFileSize(file.size)} → ${formatFileSize(compressed.size)} 
                                    <span class="text-green-600 dark:text-green-400">(${savingsPercent}% smaller)</span>
                                </span>
                            `;
                            fileNameDisplay.classList.remove('hidden');
                        }
                    }
                }
            });
        }

        // ===== AJAX PAGINATION =====
        function setupPagination() {
            document.querySelectorAll('#paginationTop a, #paginationBottom a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');

                    // Show spinner
                    if (window.showSpinner) window.showSpinner();

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('imagesGridContainer').innerHTML = data.html;

                            if (data.pagination) {
                                const paginationTop = document.getElementById('paginationTop');
                                const paginationBottom = document.getElementById('paginationBottom');

                                if (paginationTop) paginationTop.innerHTML = data.pagination;
                                if (paginationBottom) paginationBottom.innerHTML = data.pagination;
                            }

                            initializeHandlers();
                            restoreFormState();
                            setupPagination();

                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        })
                        .catch(error => {
                            console.error('Error loading page:', error);
                            alert('Failed to load images. Please try again.');
                        })
                        .finally(() => {
                            // Hide spinner
                            if (window.hideSpinner) window.hideSpinner();
                        });
                });
            });
        }

        // ===== FILE COMPRESSION =====
        async function validateAndCompressFile(input) {
            const imageId = input.dataset.imageId;
            const errorMsg = document.querySelector(`.file-size-error[data-image-id="${imageId}"]`);
            const clearBtn = document.querySelector(`.clear-file-btn[data-image-id="${imageId}"]`);
            const fileNameDisplay = document.querySelector(`.file-name-display[data-image-id="${imageId}"]`);

            errorMsg.classList.add('hidden');
            compressedFiles.delete(imageId);
            delete formState.compressedFiles[imageId];

            if (!input.files || !input.files[0]) {
                clearBtn.classList.add('hidden');
                fileNameDisplay.classList.add('hidden');
                input.classList.remove('border-red-500', 'border-green-500', 'border-blue-500');
                input.classList.add('border-primary');
                delete formState.files[imageId];
                return true;
            }

            const file = input.files[0];
            formState.files[imageId] = file;
            clearBtn.classList.remove('hidden');

            if (file.size > MAX_FILE_SIZE_ORIGINAL) {
                errorMsg.textContent =
                    `File too large (${formatFileSize(file.size)}). Maximum ${formatFileSize(MAX_FILE_SIZE_ORIGINAL)}`;
                errorMsg.classList.remove('hidden');
                fileNameDisplay.classList.add('hidden');
                input.classList.add('border-red-500');
                input.classList.remove('border-primary', 'border-green-500', 'border-blue-500');
                return false;
            }

            try {
                input.classList.remove('border-red-500', 'border-green-500');
                input.classList.add('border-blue-500');

                fileNameDisplay.innerHTML =
                    `<span class="text-blue-600 dark:text-blue-400">⏳ Compressing "${file.name}"...</span>`;
                fileNameDisplay.classList.remove('hidden');

                const compressed = await compressImage(file);
                compressedFiles.set(imageId, compressed);
                formState.compressedFiles[imageId] = compressed;

                if (compressed.size > MAX_FILE_SIZE_COMPRESSED) {
                    errorMsg.textContent =
                        `Compressed file still too large (${formatFileSize(compressed.size)}). Please use a smaller image.`;
                    errorMsg.classList.remove('hidden');
                    fileNameDisplay.classList.add('hidden');
                    input.classList.add('border-red-500');
                    input.classList.remove('border-primary', 'border-green-500', 'border-blue-500');
                    return false;
                }

                input.classList.remove('border-red-500', 'border-blue-500');
                input.classList.add('border-green-500');

                const savedBytes = file.size - compressed.size;
                const savingsPercent = Math.round((savedBytes / file.size) * 100);

                fileNameDisplay.innerHTML = `
                    <span class="font-medium text-green-600 dark:text-green-400">✓ Ready to upload</span><br>
                    <span class="text-gray-600 dark:text-gray-400">
                        ${formatFileSize(file.size)} → ${formatFileSize(compressed.size)} 
                        <span class="text-green-600 dark:text-green-400">(${savingsPercent}% smaller)</span>
                    </span>
                `;
                fileNameDisplay.classList.remove('hidden');

                return true;
            } catch (error) {
                console.error('Compression error:', error);
                errorMsg.textContent = 'Failed to compress image. Please try a different file.';
                errorMsg.classList.remove('hidden');
                fileNameDisplay.classList.add('hidden');
                input.classList.add('border-red-500');
                input.classList.remove('border-primary', 'border-green-500', 'border-blue-500');
                return false;
            }
        }

        async function validateAllFileSizes() {
            const fileInputs = document.querySelectorAll('.image-file-input');
            const validationPromises = [];

            fileInputs.forEach(input => {
                if (input.files && input.files[0]) {
                    validationPromises.push(validateAndCompressFile(input));
                }
            });

            if (validationPromises.length === 0) {
                submitButton.disabled = false;
                return true;
            }

            const results = await Promise.all(validationPromises);
            const allValid = results.every(result => result === true);

            submitButton.disabled = !allValid;
            return allValid;
        }

        async function compressImage(file) {
            return new Promise((resolve, reject) => {
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
                                if (blob) {
                                    const compressedFile = new File([blob], file.name, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    resolve(compressedFile);
                                } else {
                                    reject(new Error('Compression failed'));
                                }
                            },
                            'image/jpeg',
                            COMPRESSION_QUALITY
                        );
                    };

                    img.onerror = () => reject(new Error('Failed to load image'));
                    img.src = e.target.result;
                };

                reader.onerror = () => reject(new Error('Failed to read file'));
                reader.readAsDataURL(file);
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function toggleAllDeletes() {
            const checkboxes = document.querySelectorAll('.delete-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                const imageId = cb.dataset.imageId;
                cb.checked = !allChecked;

                if (cb.checked) {
                    formState.deletes.add(imageId);
                } else {
                    formState.deletes.delete(imageId);
                }
            });
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const isValid = await validateAllFileSizes();
            if (!isValid) {
                alert('Please fix the file size errors before submitting.');
                return;
            }

            // Count total deletes from formState (not just visible checkboxes)
            if (formState.deletes.size > 0) {
                if (!confirm(`Delete ${formState.deletes.size} image(s)? This cannot be undone.`)) {
                    return;
                }
            }

            const formData = new FormData(form);
            const newFormData = new FormData();

            // Copy all non-file fields first
            for (let [key, value] of formData.entries()) {
                if (!key.includes('[image_file]')) {
                    newFormData.append(key, value);
                }
            }

            // Track which image IDs we've already added
            const processedImageIds = new Set();

            // Collect image IDs from current page's hidden inputs
            document.querySelectorAll('input[name^="images["][name$="][id]"]').forEach(input => {
                const matches = input.name.match(/images\[(\d+)\]\[id\]/);
                if (matches) {
                    processedImageIds.add(matches[1]);
                }
            });

            // Add ALL delete checkboxes from formState (including from other pages)
            formState.deletes.forEach(imageId => {
                newFormData.append(`images[${imageId}][delete]`, '1');

                // Ensure we have the image ID - always add it
                if (!processedImageIds.has(imageId)) {
                    newFormData.append(`images[${imageId}][id]`, imageId);
                    processedImageIds.add(imageId);
                }
            });

            // Add ALL order changes from formState (including from other pages)
            Object.keys(formState.orders).forEach(imageId => {
                // Check if this order is already in the form data
                let orderExists = false;
                for (let [key] of formData.entries()) {
                    if (key === `images[${imageId}][image_order]`) {
                        orderExists = true;
                        break;
                    }
                }

                // Set the order value (will update if exists, add if not)
                newFormData.set(`images[${imageId}][image_order]`, formState.orders[imageId]);

                // Ensure we have the image ID
                if (!processedImageIds.has(imageId)) {
                    newFormData.append(`images[${imageId}][id]`, imageId);
                    processedImageIds.add(imageId);
                }
            });

            // Add compressed files
            compressedFiles.forEach((file, imageId) => {
                newFormData.append(`images[${imageId}][image_file]`, file);

                // Ensure we have the image ID for file uploads too
                if (!processedImageIds.has(imageId)) {
                    newFormData.append(`images[${imageId}][id]`, imageId);
                    processedImageIds.add(imageId);
                }
            });

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    submitButton.textContent = `Saving... ${percentComplete}%`;
                }
            });

            xhr.addEventListener('load', function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    window.location.reload();
                } else {
                    alert('Upload failed. Please try again.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Save Changes';
                }
            });

            xhr.addEventListener('error', function() {
                alert('Network error. Please check your connection and try again.');
                submitButton.disabled = false;
                submitButton.textContent = 'Save Changes';
            });

            xhr.open('POST', form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            xhr.send(newFormData);
        });

        // Initialize
        initializeHandlers();
        setupPagination();
    </script>
@endpush

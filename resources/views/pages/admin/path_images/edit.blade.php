@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <div class="container mx-auto px-4 py-8 max-w-6xl dark:text-gray-300">
        <!-- Header -->
        <div class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-300">
                    <span class="text-primary">Edit</span> Path Images
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('path-image.update-multiple', $path) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="path_id" value="{{ $path->id }}">

            <!-- Images Grid with Add Button -->
            <div class="relative mb-8">
                <!-- Add Path Image Button - Positioned at top right corner -->
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

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($pathImages as $index => $image)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-primary">
                            <input type="hidden" name="images[{{ $index }}][id]" value="{{ $image->id }}">

                            <!-- Image -->
                            <div class="relative mb-4">
                                <img src="{{ asset('storage/' . $image->image_file) }}"
                                    alt="Image {{ $image->image_order }}" class="w-full h-48 object-cover rounded">

                                <!-- Order Badge -->
                                <div
                                    class="absolute top-2 left-2 bg-primary text-white px-2 py-1 rounded text-sm font-medium">
                                    {{ $image->image_order }}
                                </div>

                                <!-- Delete Checkbox -->
                                <div class="absolute top-2 right-2">
                                    <label
                                        class="flex items-center bg-white rounded px-2 py-1 text-sm cursor-pointer text-red-600">
                                        <input type="checkbox" name="images[{{ $index }}][delete]" value="1"
                                            class="text-red-600 mr-1 custom-time-input">
                                        Delete
                                    </label>
                                </div>
                            </div>

                            <!-- Controls -->
                            <div class="space-y-3">
                                <!-- Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Order
                                    </label>
                                    <input type="number" name="images[{{ $index }}][image_order]"
                                        value="{{ $image->image_order }}" min="1"
                                        class="w-full px-3 py-2 border border-primary rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                </div>

                                <!-- Replace File -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Replace Image
                                    </label>
                                    <input type="file" name="images[{{ $index }}][image_file]" accept="image/*"
                                        class="w-full text-sm border border-primary rounded px-3 py-2 
                                      file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 
                                      file:bg-[#157ee1] fle:hover:bg-white file:text-white file:text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sticky bottom-0 p-4 bg-white dark:bg-gray-800 rounded-md border border-primary">

                <button type="button" onclick="toggleAllDeletes()"
                    class="px-4 py-2 text-sm border-2 border-secondary text-white bg-secondary hover:text-secondary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-secondary shadow-secondary-hover w-full sm:w-auto">
                    Toggle All Deletes
                </button>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="{{ route('path.show', $path) }}"
                        class="px-4 py-2 text-sm border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover w-full sm:w-auto text-center">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm border-2 border-primary text-white bg-primary hover:text-primary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-primary shadow-primary-hover w-full sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[action*="path-image.update-multiple"]');
            if (!form) return;

            const fileInputs = form.querySelectorAll('input[type="file"][name*="[image_file]"]');
            const submitButton = form.querySelector('button[type="submit"]');
            const deleteCheckboxes = form.querySelectorAll('input[type="checkbox"][name*="[delete]"]');
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            // Track invalid files
            const invalidFiles = new Set();
            let formSubmitting = false;

            // Helper: Format file size
            function formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            }

            // Validate one file input
            function validateFile(input) {
                // Remove previous error messages
                input.parentElement.querySelectorAll('.file-error, .file-size').forEach(el => el.remove());

                const file = input.files[0];

                // No file selected - valid
                if (!file) {
                    invalidFiles.delete(input);
                    input.classList.remove('border-red-500', 'focus:ring-red-500');
                    return true;
                }

                let valid = true;
                let message = '';

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    valid = false;
                    message = 'Invalid file type. Only JPEG, PNG, JPG, and WEBP are allowed.';
                }
                // Check file size
                else if (file.size > maxSize) {
                    valid = false;
                    message = `File too large (${formatSize(file.size)}). Max size is 5MB.`;
                }

                // Show file size
                const sizeEl = document.createElement('p');
                sizeEl.textContent = `Size: ${formatSize(file.size)}`;
                sizeEl.className =
                    `mt-1 text-sm ${valid ? 'text-gray-500 dark:text-gray-400' : 'text-red-500'} file-size`;
                input.insertAdjacentElement('afterend', sizeEl);

                // Show error if invalid
                if (!valid) {
                    invalidFiles.add(input);

                    const errEl = document.createElement('p');
                    errEl.textContent = message;
                    errEl.className = 'mt-1 text-sm text-red-500 font-semibold file-error';
                    input.insertAdjacentElement('afterend', errEl);
                    input.classList.add('border-red-500', 'focus:ring-red-500');
                } else {
                    invalidFiles.delete(input);
                    input.classList.remove('border-red-500', 'focus:ring-red-500');
                }

                return valid;
            }

            // Update submit button state
            function refreshSubmitState() {
                const hasInvalidFiles = invalidFiles.size > 0;

                if (hasInvalidFiles) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    submitButton.title = 'Fix invalid or oversized images before submitting';
                } else {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitButton.title = '';
                }
            }

            // Clear file input and validation
            function clearFileInput(input) {
                input.value = '';
                input.parentElement.querySelectorAll('.file-error, .file-size').forEach(el => el.remove());
                input.classList.remove('border-red-500', 'focus:ring-red-500');
                invalidFiles.delete(input);
            }

            // Validate when file changes
            fileInputs.forEach(input => {
                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];

                    // --- if user cleared input ---
                    if (!file) {
                        clearFileInput(input);
                        refreshSubmitState();
                        return;
                    }

                    // --- validate immediately ---
                    const isValid = validateFile(input);

                    // --- always refresh button state after validation ---
                    refreshSubmitState();

                    // --- if invalid (too large or wrong type) ---
                    if (!isValid) {
                        // instantly disable submit before alert
                        submitButton.disabled = true;
                        submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                        const reason = file.size > maxSize ?
                            `The file size (${formatSize(file.size)}) exceeds the 5MB limit.` :
                            'The file type is not allowed.';

                        showTemporaryMessage(
                            `❌ Cannot use this file: <strong>${file.name}</strong><br>${reason}<br>Please choose a JPEG, PNG, JPG, or WEBP image under 5MB.`,
                            'error'
                        );

                        // clear invalid input so user must pick again
                        clearFileInput(input);
                        refreshSubmitState(); // keep disabled until fixed
                        return;
                    }

                    // --- valid file ---
                    invalidFiles.delete(input);
                    refreshSubmitState();
                });
            });

            // Intercept form submission - CRITICAL
            form.addEventListener('submit', (e) => {
                // Prevent double submission
                if (formSubmitting) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }

                let allValid = true;
                let invalidFileList = [];

                // Validate each file input
                fileInputs.forEach(input => {
                    const file = input.files[0];
                    if (file) {
                        if (!validateFile(input)) {
                            allValid = false;
                            const reason = file.size > maxSize ?
                                `(${formatSize(file.size)} - exceeds 5MB)` :
                                '(invalid type)';
                            invalidFileList.push(`• ${file.name} ${reason}`);
                        }
                    }
                });

                // BLOCK submission if any files are invalid
                if (!allValid || invalidFiles.size > 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                    showTemporaryMessage(
                        `❌ Cannot submit form!<br><br>The following files are invalid:<br>${invalidFileList.join('<br>')}<br><br>⚠️ Please remove or replace these files before submitting.`,
                        'error'
                    );

                    // Scroll to first invalid file
                    const firstInvalidInput = Array.from(invalidFiles)[0];
                    if (firstInvalidInput) {
                        firstInvalidInput.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalidInput.focus();
                    }

                    return false;
                }

                // Check for delete confirmation
                const checkedDeletes = Array.from(deleteCheckboxes).filter(cb => cb.checked);
                if (checkedDeletes.length > 0) {
                    if (!confirm(
                            `⚠️ Delete ${checkedDeletes.length} image(s)?\n\nThis action cannot be undone.`
                        )) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                }

                // All validation passed - allow submission
                formSubmitting = true;
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
                submitButton.classList.add('opacity-75');

                return true;
            });

            // Prevent form submission via Enter key if invalid
            form.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && invalidFiles.size > 0) {
                    e.preventDefault();
                    showTemporaryMessage('⚠️ Please fix invalid files before submitting.', 'warning');
                    return false;
                }
            });

            // Toggle all deletes function
            window.toggleAllDeletes = function() {
                const allChecked = Array.from(deleteCheckboxes).every(cb => cb.checked);
                deleteCheckboxes.forEach(cb => cb.checked = !allChecked);
            };

            // Initial validation check on page load
            fileInputs.forEach(input => {
                if (input.files[0]) {
                    validateFile(input);
                }
            });
            refreshSubmitState();
        });
    </script>
@endpush

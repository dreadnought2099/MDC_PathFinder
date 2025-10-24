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
        <form id="pathImagesForm" action="{{ route('path-image.update-multiple', $path) }}" method="POST"
            enctype="multipart/form-data">
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
                                    <div class="relative">
                                        <input type="file" name="images[{{ $index }}][image_file]"
                                            accept="image/*"
                                            class="image-file-input w-full text-sm border border-primary rounded px-3 py-2 
                                          file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 
                                          file:bg-[#157ee1] file:hover:bg-white file:text-white file:text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                            data-index="{{ $index }}">
                                        <!-- Clear button (hidden by default) -->
                                        <button type="button"
                                            class="clear-file-btn hidden absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors duration-200"
                                            data-index="{{ $index }}" title="Clear selected file">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Error message for file size -->
                                    <p class="file-size-error hidden text-red-600 text-xs mt-1"
                                        data-index="{{ $index }}">
                                        File size must be less than 5MB
                                    </p>
                                    <!-- File name display -->
                                    <p class="file-name-display hidden text-gray-600 dark:text-gray-400 text-xs mt-1"
                                        data-index="{{ $index }}">
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Spacer to prevent content from being hidden behind fixed buttons -->
            <div class="h-20"></div>
        </form>

        <!-- Actions - Fixed at bottom -->
        <div
            class="fixed bottom-0 left-0 right-0 z-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-4 bg-white dark:bg-gray-800 border-t-2 border-primary shadow-lg">
            <div
                class="container mx-auto px-4 max-w-6xl flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <button type="button" onclick="toggleAllDeletes()"
                    class="px-4 py-2 text-sm border-2 border-secondary text-white bg-secondary hover:text-secondary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-secondary shadow-secondary-hover w-full sm:w-auto">
                    Toggle All Deletes
                </button>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <button type="submit" id="submitButton" form="pathImagesForm"
                        class="px-4 py-2 text-sm border-2 border-primary text-white bg-primary hover:text-primary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-primary shadow-primary-hover w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary disabled:hover:text-white">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes
        const submitButton = document.getElementById('submitButton');
        const form = document.getElementById('pathImagesForm');

        // Save the current path ID to sessionStorage for the floating action button
        sessionStorage.setItem('selectedPathId', '{{ $path->id }}');

        // Validate file size for all file inputs
        function validateAllFileSizes() {
            const fileInputs = document.querySelectorAll('.image-file-input');
            let allValid = true;

            fileInputs.forEach(input => {
                const index = input.dataset.index;
                const errorMsg = document.querySelector(`.file-size-error[data-index="${index}"]`);
                const clearBtn = document.querySelector(`.clear-file-btn[data-index="${index}"]`);
                const fileNameDisplay = document.querySelector(`.file-name-display[data-index="${index}"]`);

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const fileSize = file.size;

                    // Show clear button and file name
                    clearBtn.classList.remove('hidden');
                    fileNameDisplay.classList.remove('hidden');
                    fileNameDisplay.textContent = `Selected: ${file.name}`;

                    if (fileSize > MAX_FILE_SIZE) {
                        errorMsg.classList.remove('hidden');
                        input.classList.add('border-red-500');
                        input.classList.remove('border-primary');
                        allValid = false;
                    } else {
                        errorMsg.classList.add('hidden');
                        input.classList.remove('border-red-500');
                        input.classList.add('border-primary');
                    }
                } else {
                    // Hide clear button, error message, and file name when no file selected
                    clearBtn.classList.add('hidden');
                    errorMsg.classList.add('hidden');
                    fileNameDisplay.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    input.classList.add('border-primary');
                }
            });

            // Enable/disable submit button based on validation
            submitButton.disabled = !allValid;

            return allValid;
        }

        // Add event listeners to all file inputs
        document.querySelectorAll('.image-file-input').forEach(input => {
            input.addEventListener('change', function() {
                validateAllFileSizes();
            });
        });

        // Add event listeners to all clear buttons
        document.querySelectorAll('.clear-file-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.dataset.index;
                const fileInput = document.querySelector(`.image-file-input[data-index="${index}"]`);

                // Clear the file input
                fileInput.value = '';

                // Trigger validation to update UI
                validateAllFileSizes();
            });
        });

        // Toggle all delete checkboxes
        function toggleAllDeletes() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name*="[delete]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
        }

        // Confirm before submitting if deletions are selected
        form.addEventListener('submit', function(e) {
            // Validate file sizes one more time before submission
            if (!validateAllFileSizes()) {
                e.preventDefault();
                alert('Please fix the file size errors before submitting.');
                return;
            }

            const deleteCheckboxes = document.querySelectorAll('input[type="checkbox"][name*="[delete]"]:checked');

            if (deleteCheckboxes.length > 0) {
                if (!confirm(`Delete ${deleteCheckboxes.length} image(s)? This cannot be undone.`)) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endpush

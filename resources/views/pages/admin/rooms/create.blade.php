@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl">
            <div class="flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-lg font-medium dark:text-gray-300">Processing images...</span>
            </div>
        </div>
    </div>

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6 dark:text-gray-300"><span class="text-primary">Add</span> New Office</h2>

        <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data" id="room-form" data-upload>
            @csrf

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';
                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <div class="relative mb-4">
                <input type="text" name="name" id="name" placeholder="Office Name" class="{{ $inputClasses }}"
                    value="{{ old('name') }}" required>
                <label class="{{ $labelClasses }}">Office Name</label>
                <span id="name-feedback" class="text-red-500 text-sm"></span>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="relative mb-4">
                <textarea name="description" placeholder="Description" class="{{ $inputClasses }}" rows="3">{{ old('description') }}</textarea>
                <label class="{{ $labelClasses }}">Description</label>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium dark:text-gray-300">Room Type</label>
                <select name="room_type" id="room_type"
                    class="w-full border dark:text-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent border-gray-500 dark:bg-gray-800"
                    required>
                    <option value="regular" {{ old('room_type') === 'regular' ? 'selected' : '' }}>
                        Regular Office
                    </option>
                    <option value="entrance_point" {{ old('room_type') === 'entrance_point' ? 'selected' : '' }}>
                        Entrance Point
                    </option>
                </select>
                <p class="text-sm text-gray-600 mt-1 dark:text-gray-300">
                    Entrance gates automatically connect to all other rooms for navigation purposes.
                </p>
                @error('room_type')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4 conditional-field" id="cover-image-section">
                <label class="block mb-2 dark:text-gray-300">Cover Image (optional, max 10MB)</label>

                <div id="uploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
                    border-2 border-dashed border-gray-300 dark:border-gray-600 
                    rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                    dark:hover:border-primary dark:hover:bg-gray-800
                    transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                        alt="Image Icon" class="w-8 h-8" onerror="this.style.display='none'">
                    <span id="uploadText" class="text-gray-500 dark:text-gray-300">
                        Click to upload cover image
                    </span>
                    <img id="previewImage" class="absolute inset-0 object-cover w-full h-full hidden" alt="Image preview" />
                </div>

                <input type="file" name="image_path" id="image_path" class="hidden" accept="image/*" />
                @error('image_path')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4 max-w-xl mx-auto conditional-field" id="carousel-images-section">
                <label class="block mb-2 dark:text-gray-300">Carousel Images (optional, max 50 images, 10MB each)</label>
                <div id="carouselUploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
                    border-2 border-dashed border-gray-300 dark:border-gray-600 
                    rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                    dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                        alt="Image Icon" class="w-8 h-8" onerror="this.style.display='none'">
                    <span id="carouselUploadText" class="text-gray-500 mb-4 dark:text-gray-300">
                        Click to upload images
                    </span>
                    <div id="carouselPreviewContainer" class="flex flex-wrap gap-3 w-full justify-start"></div>
                </div>
                <input type="file" name="carousel_images[]" id="carousel_images" class="hidden"
                    accept="image/jpeg,image/jpg,image/png" multiple />
                @error('carousel_images')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                @error('carousel_images.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4 max-w-xl mx-auto conditional-field" id="video-section">
                <label class="block mb-2 dark:text-gray-300">Short Video (optional, max 50MB)</label>
                <div id="videoDropZone"
                    class="flex flex-col items-center justify-center w-full h-40 
                    border-2 border-dashed border-gray-300 dark:border-gray-600 
                    rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                    dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/video.png"
                        alt="Video Icon" class="w-9 h-9" onerror="this.style.display='none'">
                    <p class="mb-2 dark:text-gray-300">Drag & drop a video file here or click to select</p>
                    <p class="text-xs text-gray-400 dark:text-gray-300">(mp4, avi, mpeg)</p>
                </div>
                <input type="file" id="video_path" name="video_path" accept="video/mp4,video/avi,video/mpeg"
                    class="hidden" />

                <div id="videoPreviewContainer"
                    class="mt-4 hidden relative rounded border border-gray-300 overflow-hidden">
                    <video id="videoPreview" controls class="w-full h-auto bg-black"></video>
                    <button type="button" id="removeVideoBtn"
                        class="absolute top-2 right-2 bg-secondary text-white rounded-full p-1 hover:bg-red-700 transition-colors"
                        title="Remove video">&times;</button>
                </div>
                @error('video_path')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6 conditional-field" id="office-hours-section">
                <label class="block font-semibold mb-2 dark:text-gray-300">Office Hours</label>

                <div class="mb-6 p-4 border border-primary rounded dark:bg-gray-800">
                    <p class="font-semibold mb-3 dark:text-gray-300">Set Time Range for Multiple Days</p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Select Days:</label>
                        <div class="flex gap-2 flex-wrap">
                            @php
                                $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            @endphp
                            @foreach ($daysOfWeek as $day)
                                <label
                                    class="flex items-center bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                    <input type="checkbox" class="bulk-day-checkbox mr-2 text-primary focus:ring-primary"
                                        value="{{ $day }}">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-2 flex gap-2 flex-wrap">
                            <button type="button"
                                class="quick-select bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors"
                                data-days="Mon,Tue,Wed,Thu,Fri">Weekdays</button>
                            <button type="button"
                                class="quick-select bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors"
                                data-days="Sat,Sun">Weekends</button>
                            <button type="button"
                                class="quick-select bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors"
                                data-days="Mon,Tue,Wed,Thu,Fri,Sat,Sun">All Days</button>
                            <button type="button"
                                class="clear-select bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-3 py-1 rounded text-sm transition-colors">Clear
                                All</button>
                        </div>
                    </div>

                    <div class="bulk-time-ranges mb-4">
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Time Range:</label>
                        <div class="bulk-ranges-container">
                            <div class="flex gap-2 mb-2 bulk-range-row">
                                <div class="relative flex-1">
                                    <input type="time"
                                        class="custom-time-input bulk-start-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                        title="Clear">&times;</button>
                                </div>
                                <div class="relative flex-1">
                                    <input type="time"
                                        class="custom-time-input bulk-end-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                        title="Clear">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="apply-bulk bg-primary text-center text-white px-4 py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 duration-300 ease-in-out transition-all cursor-pointer shadow-primary-hover">
                        Apply to Selected Days
                    </button>
                </div>

                <div class="p-4 border border-primary rounded dark:bg-gray-800">
                    <p class="mb-3 dark:text-gray-300">Saved Office Hours</p>
                    <ul id="officeHoursDisplay" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                </div>
                @error('office_hours')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <button type="submit" id="submit-btn"
                    class="w-full bg-primary text-white px-4 py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Save Office
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Browser Image Compression Library --}}
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const MAX_CAROUSEL_FILES = 50;
            const MAX_IMAGE_SIZE_MB = 10;
            const MAX_VIDEO_SIZE_MB = 50;

            // Track which files have been compressed
            let compressedFileNames = new Set();

            // Image compression options
            const compressionOptions = {
                maxSizeMB: 2, // Target max size 2MB after compression
                maxWidthOrHeight: 2000, // Max dimension
                useWebWorker: true,
                fileType: 'image/jpeg',
                initialQuality: 0.8
            };

            const form = document.getElementById('room-form');
            const loadingOverlay = document.getElementById('loading-overlay');
            const nameInput = document.querySelector('#name');
            const feedback = document.querySelector('#name-feedback');
            const submitBtn = document.querySelector('#submit-btn');

            // Show loading overlay on form submit
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submission

                const carouselFiles = document.getElementById('carousel_images').files;
                const coverFile = document.getElementById('image_path').files[0];

                // Check if we need to show loading (images to compress)
                if ((carouselFiles && carouselFiles.length > 0) || coverFile) {
                    loadingOverlay.classList.remove('hidden');
                    submitBtn.disabled = true;

                    // Compress images then submit
                    compressAndSubmitForm();
                } else {
                    // No images, submit normally
                    submitBtn.disabled = true;
                    form.submit();
                }
            });

            // Name uniqueness check with debouncing
            let typingTimer;
            const typingDelay = 500;

            nameInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(async () => {
                    const name = nameInput.value.trim();
                    if (!name) {
                        feedback.textContent = '';
                        submitBtn.disabled = false;
                        return;
                    }

                    try {
                        const response = await fetch(
                            `/admin/rooms/check-name?name=${encodeURIComponent(name)}`);
                        const data = await response.json();

                        if (data.exists) {
                            feedback.textContent = 'This office name is already taken!';
                            submitBtn.disabled = true;
                        } else {
                            feedback.textContent = '';
                            submitBtn.disabled = false;
                        }
                    } catch (error) {
                        console.error('Name check failed:', error);
                    }
                }, typingDelay);
            });

            nameInput.addEventListener('keydown', () => clearTimeout(typingTimer));

            // Room type change handler
            const roomTypeSelect = document.getElementById('room_type');
            const conditionalFields = document.querySelectorAll('.conditional-field');

            function toggleConditionalFields() {
                const isEntrancePoint = roomTypeSelect.value === 'entrance_point';

                conditionalFields.forEach(field => {
                    field.style.display = isEntrancePoint ? 'none' : 'block';
                    const inputs = field.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.disabled = isEntrancePoint;
                        if (isEntrancePoint && input.type === 'file' && !isPageLoad) {
                            input.value = '';
                        }
                    });
                });

                if (isEntrancePoint && !isPageLoad) {
                    resetCoverImage();
                    resetCarouselImages();
                    clearVideo();
                    officeHoursData = {};
                    renderOfficeHours();
                }
            }

            let isPageLoad = true;
            toggleConditionalFields();
            setTimeout(() => {
                isPageLoad = false;
            }, 100);
            roomTypeSelect.addEventListener('change', () => {
                isPageLoad = false;
                toggleConditionalFields();
            });

            // Cover image functionality
            const coverInput = document.getElementById('image_path');
            const coverPreview = document.getElementById('previewImage');
            const coverUploadBox = document.getElementById('uploadBox');

            coverUploadBox.addEventListener('click', () => coverInput.click());
            coverInput.addEventListener('change', () => {
                if (coverInput.files && coverInput.files[0]) {
                    if (validateImageFile(coverInput.files[0])) {
                        compressAndPreviewCoverImage(coverInput.files[0]);
                    } else {
                        coverInput.value = '';
                    }
                }
            });

            ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                coverUploadBox.addEventListener(eventName, handleDragEvent);
            });

            function handleDragEvent(e) {
                e.preventDefault();
                if (e.type === 'dragover') {
                    coverUploadBox.classList.add('border-primary', 'bg-gray-50');
                } else if (e.type === 'dragleave') {
                    coverUploadBox.classList.remove('border-primary', 'bg-gray-50');
                } else if (e.type === 'drop') {
                    coverUploadBox.classList.remove('border-primary', 'bg-gray-50');
                    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                        coverInput.files = e.dataTransfer.files;
                        if (validateImageFile(e.dataTransfer.files[0])) {
                            compressAndPreviewCoverImage(e.dataTransfer.files[0]);
                        } else {
                            coverInput.value = '';
                        }
                    }
                }
            }

            async function compressAndPreviewCoverImage(file) {
                try {
                    showTemporaryMessage('Compressing cover image...', 'info');

                    const compressedFile = await imageCompression(file, compressionOptions);

                    // Mark as compressed
                    compressedFileNames.add(file.name);

                    // Update the file input with compressed file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(new File([compressedFile], file.name, {
                        type: compressedFile.type,
                        lastModified: Date.now()
                    }));
                    coverInput.files = dataTransfer.files;

                    showCoverPreview(compressedFile);

                    const originalSizeMB = (file.size / 1024 / 1024).toFixed(2);
                    const compressedSizeMB = (compressedFile.size / 1024 / 1024).toFixed(2);
                    showTemporaryMessage(`Cover image compressed: ${originalSizeMB}MB → ${compressedSizeMB}MB`,
                        'success');
                } catch (error) {
                    console.error('Compression failed:', error);
                    showTemporaryMessage('Compression failed, using original image', 'error');
                    showCoverPreview(file);
                }
            }

            function showCoverPreview(file) {
                const reader = new FileReader();
                reader.onload = e => {
                    coverPreview.src = e.target.result;
                    coverPreview.classList.remove('hidden');
                    const icon = coverUploadBox.querySelector('img:not(#previewImage)');
                    const text = coverUploadBox.querySelector('span');
                    if (icon) icon.style.display = 'none';
                    if (text) text.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            function resetCoverImage() {
                coverPreview.classList.add('hidden');
                coverPreview.src = '';
                const icon = coverUploadBox.querySelector('img:not(#previewImage)');
                const text = coverUploadBox.querySelector('span');
                if (icon) icon.style.display = '';
                if (text) text.style.display = '';
                coverInput.value = '';
            }

            // Carousel images functionality
            const carouselInput = document.getElementById('carousel_images');
            const carouselUploadBox = document.getElementById('carouselUploadBox');
            const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
            let selectedFiles = [];

            carouselUploadBox.addEventListener('click', () => carouselInput.click());

            carouselInput.addEventListener('change', () => {
                handleCarouselFiles(Array.from(carouselInput.files || []));
            });

            ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                carouselUploadBox.addEventListener(eventName, handleCarouselDrag);
            });

            function handleCarouselDrag(e) {
                e.preventDefault();
                if (e.type === 'dragover') {
                    carouselUploadBox.classList.add('border-primary', 'bg-gray-50');
                } else if (e.type === 'dragleave') {
                    carouselUploadBox.classList.remove('border-primary', 'bg-gray-50');
                } else if (e.type === 'drop') {
                    carouselUploadBox.classList.remove('border-primary', 'bg-gray-50');
                    if (e.dataTransfer.files) {
                        handleCarouselFiles(Array.from(e.dataTransfer.files));
                    }
                }
            }

            function handleCarouselFiles(newFiles) {
                carouselInput.value = '';

                if (selectedFiles.length + newFiles.length > MAX_CAROUSEL_FILES) {
                    showTemporaryMessage(`You can upload max ${MAX_CAROUSEL_FILES} images.`, 'error');
                    return;
                }

                const invalidFiles = newFiles.filter(file => !validateImageFile(file, false));
                if (invalidFiles.length > 0) {
                    return;
                }

                // Compress images before adding
                compressCarouselImages(newFiles);
            }

            async function compressCarouselImages(newFiles) {
                showTemporaryMessage(`Compressing ${newFiles.length} image(s)...`, 'info');
                loadingOverlay.classList.remove('hidden');

                try {
                    const compressedFiles = [];

                    for (let i = 0; i < newFiles.length; i++) {
                        const file = newFiles[i];
                        try {
                            const compressedFile = await imageCompression(file, compressionOptions);

                            // Convert to File object with original name
                            const finalFile = new File([compressedFile], file.name, {
                                type: compressedFile.type,
                                lastModified: Date.now()
                            });

                            // Mark as compressed
                            compressedFileNames.add(file.name);

                            compressedFiles.push(finalFile);
                        } catch (error) {
                            console.error(`Failed to compress ${file.name}:`, error);
                            // Use original if compression fails
                            compressedFiles.push(file);
                        }
                    }

                    selectedFiles = selectedFiles.concat(compressedFiles);
                    renderCarouselPreviews();
                    updateCarouselInputFiles();

                    const totalOriginalSize = newFiles.reduce((sum, f) => sum + f.size, 0) / 1024 / 1024;
                    const totalCompressedSize = compressedFiles.reduce((sum, f) => sum + f.size, 0) / 1024 /
                        1024;

                    showTemporaryMessage(
                        `${newFiles.length} image(s) compressed: ${totalOriginalSize.toFixed(2)}MB → ${totalCompressedSize.toFixed(2)}MB`,
                        'success'
                    );
                } catch (error) {
                    console.error('Batch compression failed:', error);
                    showTemporaryMessage('Some images could not be compressed', 'error');
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            }

            function renderCarouselPreviews() {
                carouselPreviewContainer.innerHTML = '';

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const container = document.createElement('div');
                        container.className =
                            'relative w-24 h-24 rounded overflow-hidden shadow border border-gray-300';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover rounded';

                        const removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.className =
                            'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow hover:bg-red-600 transition-colors';
                        removeBtn.onclick = ev => {
                            ev.preventDefault();
                            selectedFiles.splice(index, 1);
                            renderCarouselPreviews();
                            updateCarouselInputFiles();
                        };

                        container.appendChild(img);
                        container.appendChild(removeBtn);
                        carouselPreviewContainer.appendChild(container);
                    };
                    reader.readAsDataURL(file);
                });

                updateUploadIconVisibility();
            }

            function updateCarouselInputFiles() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                carouselInput.files = dt.files;
            }

            function updateUploadIconVisibility() {
                const icon = carouselUploadBox.querySelector('img:first-child');
                const text = carouselUploadBox.querySelector('span');
                const display = selectedFiles.length > 0 ? 'none' : '';
                if (icon) icon.style.display = display;
                if (text) text.style.display = display;
            }

            function resetCarouselImages() {
                selectedFiles = [];
                carouselPreviewContainer.innerHTML = '';
                carouselInput.value = '';
                updateUploadIconVisibility();
            }

            // Video upload functionality
            const dropZone = document.getElementById('videoDropZone');
            const videoInput = document.getElementById('video_path');
            const videoPreviewContainer = document.getElementById('videoPreviewContainer');
            const videoPreview = document.getElementById('videoPreview');
            const removeVideoBtn = document.getElementById('removeVideoBtn');

            dropZone.addEventListener('click', () => videoInput.click());
            videoInput.addEventListener('change', () => {
                if (videoInput.files && videoInput.files[0]) {
                    if (validateVideoFile(videoInput.files[0])) {
                        showVideoPreview(videoInput.files[0]);
                    } else {
                        videoInput.value = '';
                    }
                }
            });

            ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, handleVideoDrag);
            });

            function handleVideoDrag(e) {
                e.preventDefault();
                if (e.type === 'dragover') {
                    dropZone.classList.add('border-primary', 'bg-gray-50');
                } else if (e.type === 'dragleave') {
                    dropZone.classList.remove('border-primary', 'bg-gray-50');
                } else if (e.type === 'drop') {
                    dropZone.classList.remove('border-primary', 'bg-gray-50');
                    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                        videoInput.files = e.dataTransfer.files;
                        if (validateVideoFile(e.dataTransfer.files[0])) {
                            showVideoPreview(e.dataTransfer.files[0]);
                        } else {
                            videoInput.value = '';
                        }
                    }
                }
            }

            removeVideoBtn.addEventListener('click', () => {
                clearVideo();
                videoInput.value = '';
            });

            function showVideoPreview(file) {
                const url = URL.createObjectURL(file);
                videoPreview.src = url;
                videoPreviewContainer.classList.remove('hidden');
            }

            function clearVideo() {
                videoPreview.src = '';
                videoPreviewContainer.classList.add('hidden');
            }

            // Validation functions
            function validateImageFile(file, showError = true) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                if (!allowedTypes.includes(file.type)) {
                    if (showError) {
                        showTemporaryMessage(
                            `"${file.name}" is not a valid image. Only JPG, JPEG, and PNG are allowed.`, 'error'
                            );
                    }
                    return false;
                }

                if (file.size > MAX_IMAGE_SIZE_MB * 1024 * 1024) {
                    if (showError) {
                        showTemporaryMessage(`"${file.name}" is too large. Max size is ${MAX_IMAGE_SIZE_MB}MB.`,
                            'error');
                    }
                    return false;
                }

                return true;
            }

            function validateVideoFile(file) {
                const allowedTypes = ['video/mp4', 'video/avi', 'video/mpeg'];

                if (!allowedTypes.includes(file.type)) {
                    showTemporaryMessage(
                        `"${file.name}" is not a valid video. Only MP4, AVI, and MPEG are allowed.`, 'error');
                    return false;
                }

                if (file.size > MAX_VIDEO_SIZE_MB * 1024 * 1024) {
                    showTemporaryMessage(`"${file.name}" is too large. Max size is ${MAX_VIDEO_SIZE_MB}MB.`,
                        'error');
                    return false;
                }

                return true;
            }

            // Office Hours with Day.js
            const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            let officeHoursData = {};

            function formatTime12Hour(time24) {
                try {
                    if (!time24 || !time24.match(/^\d{2}:\d{2}$/)) return time24;
                    const timeObj = dayjs(`2000-01-01 ${time24}:00`);
                    return timeObj.isValid() ? timeObj.format('h:mm A') : time24;
                } catch (error) {
                    console.warn('Time formatting error:', error);
                    return time24;
                }
            }

            function validateTimeRange(startTime, endTime) {
                try {
                    const start = dayjs(`2000-01-01 ${startTime}:00`);
                    const end = dayjs(`2000-01-01 ${endTime}:00`);

                    if (!start.isValid() || !end.isValid()) {
                        return {
                            valid: false,
                            error: 'Invalid time format'
                        };
                    }

                    if (start.isAfter(end) || start.isSame(end)) {
                        return {
                            valid: false,
                            error: 'End time must be after start time'
                        };
                    }

                    return {
                        valid: true
                    };
                } catch (error) {
                    return {
                        valid: false,
                        error: 'Time validation failed'
                    };
                }
            }

            function hasOverlapDayJs(ranges) {
                const sortedRanges = ranges.map(range => ({
                    start: dayjs(`2000-01-01 ${range.start}:00`),
                    end: dayjs(`2000-01-01 ${range.end}:00`),
                    original: range
                })).sort((a, b) => a.start.isBefore(b.start) ? -1 : 1);

                for (let i = 0; i < sortedRanges.length - 1; i++) {
                    const current = sortedRanges[i];
                    const next = sortedRanges[i + 1];

                    if (current.end.isAfter(next.start)) {
                        return {
                            hasOverlap: true,
                            conflictingRanges: [current.original, next.original]
                        };
                    }
                }

                return {
                    hasOverlap: false
                };
            }

            function formatDuration(startTime, endTime) {
                const start = dayjs(`2000-01-01 ${startTime}:00`);
                const end = dayjs(`2000-01-01 ${endTime}:00`);
                const diffMinutes = end.diff(start, 'minute');
                const hours = Math.floor(diffMinutes / 60);
                const minutes = diffMinutes % 60;

                if (hours === 0) return `${minutes}m`;
                if (minutes === 0) return `${hours}h`;
                return `${hours}h ${minutes}m`;
            }

            // Quick select functionality
            document.querySelectorAll('.quick-select').forEach(btn => {
                btn.addEventListener('click', () => {
                    const days = btn.dataset.days.split(',');
                    document.querySelectorAll('.bulk-day-checkbox').forEach(cb => cb.checked =
                        false);
                    days.forEach(day => {
                        const cb = document.querySelector(
                            `.bulk-day-checkbox[value="${day}"]`);
                        if (cb) cb.checked = true;
                    });
                });
            });

            document.querySelector('.clear-select').addEventListener('click', () => {
                document.querySelectorAll('.bulk-day-checkbox').forEach(cb => cb.checked = false);
            });

            // Apply bulk changes
            document.querySelector('.apply-bulk').addEventListener('click', function() {
                const selectedDays = Array.from(document.querySelectorAll('.bulk-day-checkbox:checked'))
                    .map(cb => cb.value);

                if (!selectedDays.length) {
                    return showTemporaryMessage("Please select at least one day.", 'error');
                }

                const ranges = collectBulkRanges();
                if (!ranges) return;

                const isEditMode = checkIfEditMode(selectedDays, ranges);
                if (isEditMode) {
                    clearExistingScheduleForEdit(selectedDays, ranges);
                }

                selectedDays.forEach(day => {
                    officeHoursData[day] = ranges;
                });

                renderOfficeHours();
                showTemporaryFeedback(this, "Applied Successfully!");
                showTemporaryMessage("Office hours updated for selected days!", "success");
            });

            function checkIfEditMode(selectedDays, newRanges) {
                return selectedDays.some(day => officeHoursData[day] && officeHoursData[day].length > 0);
            }

            function clearExistingScheduleForEdit(selectedDays, newRanges) {
                const existingGroups = {};
                daysOfWeek.forEach(day => {
                    const ranges = officeHoursData[day] || [];
                    const rangeKey = ranges.length ? ranges.map(r => `${r.start}-${r.end}`).join(",") :
                        "closed";
                    if (!existingGroups[rangeKey]) existingGroups[rangeKey] = [];
                    existingGroups[rangeKey].push(day);
                });

                Object.entries(existingGroups).forEach(([rangeKey, groupDays]) => {
                    if (rangeKey !== "closed") {
                        const hasOverlap = groupDays.some(day => selectedDays.includes(day));
                        if (hasOverlap) {
                            groupDays.forEach(day => {
                                if (!selectedDays.includes(day)) {
                                    delete officeHoursData[day];
                                }
                            });
                        }
                    }
                });
            }

            // Clear time input
            document.addEventListener('click', e => {
                const button = e.target.closest('.clear-time');
                if (button) {
                    const input = button.previousElementSibling;
                    if (input && input.type === "time") {
                        input.value = "";
                    }
                }
            });

            function collectBulkRanges() {
                const ranges = [];
                let valid = true;

                document.querySelectorAll('.bulk-range-row').forEach(row => {
                    const start = row.querySelector('.bulk-start-time').value;
                    const end = row.querySelector('.bulk-end-time').value;
                    clearError(row);

                    if (start && end) {
                        const validation = validateTimeRange(start, end);
                        if (!validation.valid) {
                            showError(row, validation.error);
                            valid = false;
                            return;
                        }
                        ranges.push({
                            start,
                            end
                        });
                    }
                });

                if (!valid) return null;
                if (!ranges.length) {
                    showTemporaryMessage("Please enter at least one valid time range.", 'error');
                    return null;
                }

                const overlapCheck = hasOverlapDayJs(ranges);
                if (overlapCheck.hasOverlap) {
                    showTemporaryMessage("Time ranges overlap. Fix them first.", 'error');
                    return null;
                }

                return ranges;
            }

            function renderOfficeHours() {
                const container = document.getElementById("officeHoursDisplay");
                container.innerHTML = "";

                const groupedSchedule = {};
                daysOfWeek.forEach(day => {
                    const ranges = officeHoursData[day] || [];
                    const rangeKey = ranges.length ? ranges.map(r => `${r.start}-${r.end}`).join(",") :
                        "closed";
                    if (!groupedSchedule[rangeKey]) {
                        groupedSchedule[rangeKey] = {
                            days: [],
                            ranges: ranges
                        };
                    }
                    groupedSchedule[rangeKey].days.push(day);
                });

                Object.entries(groupedSchedule).forEach(([rangeKey, group]) => {
                    const li = document.createElement("li");
                    li.className =
                        "mb-3 p-3 bg-white rounded border relative dark:bg-gray-800 border border-primary";

                    const daysText = formatDaysGroup(group.days);
                    let timeText;

                    if (rangeKey === "closed") {
                        timeText = "Closed";
                    } else {
                        timeText = group.ranges.map(r => {
                            const timeRange =
                                `${formatTime12Hour(r.start)} - ${formatTime12Hour(r.end)}`;
                            const duration = formatDuration(r.start, r.end);
                            return `${timeRange} <span class="text-gray-500 text-xs">(${duration})</span>`;
                        }).join(", ");
                    }

                    li.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 dark:text-gray-300">${daysText}</div>
                                <div class="text-sm text-gray-600 mt-1 dark:text-gray-300">${timeText}</div>
                            </div>
                            ${rangeKey !== "closed" ? `
                                    <div class="flex gap-2 ml-4">
                                        <button type="button" class="edit-schedule-btn bg-primary text-white hover:text-primary hover:bg-white text-sm px-2 py-1 rounded-md border border-primary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800" 
                                                data-days='${JSON.stringify(group.days)}' data-ranges='${JSON.stringify(group.ranges)}'>
                                            Edit
                                        </button>
                                        <button type="button" class="delete-schedule-btn bg-secondary text-white hover:text-secondary hover:bg-white text-sm px-2 py-1 rounded-md border border-secondary transition-all duration-300 cursor-pointer dark:hover:bg-gray-800" 
                                                data-days='${JSON.stringify(group.days)}'>
                                            Delete
                                        </button>
                                    </div>
                                ` : ''}
                        </div>
                    `;

                    group.days.forEach(day => {
                        group.ranges.forEach((range, idx) => {
                            const startInput = document.createElement("input");
                            startInput.type = "hidden";
                            startInput.name = `office_hours[${day}][${idx}][start]`;
                            startInput.value = range.start;

                            const endInput = document.createElement("input");
                            endInput.type = "hidden";
                            endInput.name = `office_hours[${day}][${idx}][end]`;
                            endInput.value = range.end;

                            li.appendChild(startInput);
                            li.appendChild(endInput);
                        });
                    });

                    container.appendChild(li);
                });

                attachScheduleActionListeners();
            }

            function attachScheduleActionListeners() {
                document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const days = JSON.parse(this.dataset.days);
                        const daysText = formatDaysGroup(days);

                        if (confirm(
                            `Are you sure you want to remove office hours for ${daysText}?`)) {
                            days.forEach(day => delete officeHoursData[day]);
                            renderOfficeHours();
                            showTemporaryMessage("Office hours deleted successfully!", "success");
                        }
                    });
                });

                document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const days = JSON.parse(this.dataset.days);
                        const ranges = JSON.parse(this.dataset.ranges);

                        document.querySelectorAll('.bulk-day-checkbox').forEach(cb => {
                            cb.checked = days.includes(cb.value);
                        });

                        if (ranges.length > 0) {
                            document.querySelector('.bulk-start-time').value = ranges[0].start;
                            document.querySelector('.bulk-end-time').value = ranges[0].end;
                        }

                        document.querySelector('.bulk-time-ranges').scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        const bulkSection = document.querySelector(
                            '.mb-6.p-4.border.border-primary.rounded');
                        if (bulkSection) {
                            bulkSection.classList.add('ring-2', 'ring-blue-400');
                            setTimeout(() => bulkSection.classList.remove('ring-2',
                                'ring-blue-400'), 2000);
                        }

                        showTemporaryMessage(
                            "Schedule loaded for editing. Modify time and click 'Apply'.",
                            "info");
                    });
                });
            }

            function formatDaysGroup(days) {
                if (days.length === 0) return "";
                if (days.length === 1) return days[0];

                const sortedDays = days.sort((a, b) => daysOfWeek.indexOf(a) - daysOfWeek.indexOf(b));

                const isExactMatch = (pattern) => {
                    return sortedDays.length === pattern.length &&
                        sortedDays.every((day, index) => day === pattern[index]);
                };

                if (isExactMatch(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])) return "Daily";
                if (isExactMatch(['Mon', 'Tue', 'Wed', 'Thu', 'Fri'])) return "Weekdays";
                if (isExactMatch(['Sat', 'Sun'])) return "Weekends";

                const isConsecutive = () => {
                    for (let i = 0; i < sortedDays.length - 1; i++) {
                        const currentIndex = daysOfWeek.indexOf(sortedDays[i]);
                        const nextIndex = daysOfWeek.indexOf(sortedDays[i + 1]);
                        if (nextIndex !== currentIndex + 1) return false;
                    }
                    return true;
                };

                if (isConsecutive() && sortedDays.length > 2) {
                    return `${sortedDays[0]} - ${sortedDays[sortedDays.length - 1]}`;
                }

                return sortedDays.join(", ");
            }

            function showTemporaryMessage(message, type = "info") {
                const existing = document.getElementById("temp-message");
                if (existing) existing.remove();

                const div = document.createElement("div");
                div.id = "temp-message";
                div.textContent = message;

                const base =
                    "fixed top-24 right-4 p-3 rounded shadow-lg z-50 transition-opacity duration-500 border-l-4";
                const colors = {
                    success: "bg-green-100 text-green-700 border-green-500 dark:bg-green-800 dark:text-green-200 dark:border-green-600",
                    error: "bg-red-100 text-red-700 border-red-500 dark:bg-red-800 dark:text-red-200 dark:border-red-600",
                    info: "bg-yellow-100 text-yellow-700 border-yellow-500 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-500"
                };

                div.className = `${base} ${colors[type] || colors.info}`;
                document.body.appendChild(div);

                setTimeout(() => {
                    div.style.opacity = "0";
                    setTimeout(() => div.remove(), 500);
                }, 3000);
            }

            function showTemporaryFeedback(button, text) {
                const old = button.textContent;
                button.textContent = text;
                button.classList.add('bg-green-500');
                button.classList.remove('bg-primary');
                setTimeout(() => {
                    button.textContent = old;
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-primary');
                }, 2000);
            }

            function showError(row, msg) {
                row.classList.add("bg-red-50", "border", "border-red-400", "dark:bg-red-900",
                "dark:border-red-600");
                if (!row.querySelector(".error-msg")) {
                    const p = document.createElement("p");
                    p.className = "error-msg text-red-600 dark:text-red-300 text-xs mt-1";
                    p.textContent = msg;
                    row.appendChild(p);
                }
            }

            function clearError(row) {
                row.classList.remove("bg-red-50", "border", "border-red-400", "dark:bg-red-900",
                    "dark:border-red-600");
                const msg = row.querySelector(".error-msg");
                if (msg) msg.remove();
            }

            // Compress all images before final form submission
            async function compressAndSubmitForm() {
                try {
                    // Images should already be compressed from upload handlers
                    // Just submit the form directly
                    form.submit();

                } catch (error) {
                    console.error('Submission error:', error);
                    loadingOverlay.classList.add('hidden');
                    submitBtn.disabled = false;
                    showTemporaryMessage('Submission failed. Please try again.', 'error');
                }
            }

            renderOfficeHours();
        });
    </script>
@endpush
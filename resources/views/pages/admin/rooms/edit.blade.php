@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6 dark:text-gray-300"><span class="text-primary">Edit</span> Office</h2>

        <x-upload-progress-modal>
            <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data" id="room-form">
                @csrf
                @method('PUT')

                @php
                    $inputClasses =
                        'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';
                    $labelClasses =
                        'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
                @endphp

                <!-- Office Name and Room Type Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="relative">
                        <input type="text" name="name" id="name" placeholder="Office Name"
                            class="{{ $inputClasses }}" value="{{ old('name', $room->name) }}" required>
                        <label class="{{ $labelClasses }}">Office Name</label>
                        <span id="name-feedback" class="text-red-500 text-sm"></span>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative">
                        <select name="room_type" id="room_type"
                            class="peer py-3 w-full font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800"
                            required>
                            <option value="" disabled hidden></option>
                            <option value="regular"
                                {{ old('room_type', $room->room_type) === 'regular' ? 'selected' : '' }}>
                                Regular Office
                            </option>
                            <option value="entrance_point"
                                {{ old('room_type', $room->room_type) === 'entrance_point' ? 'selected' : '' }}>
                                Entrance Point
                            </option>
                        </select>
                        <label class="{{ $labelClasses }}">Room Type</label>
                        @error('room_type')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Room Type Info -->
                <p class="text-sm text-gray-600 mb-4 dark:text-gray-300">
                    Entrance gates automatically connect to all other rooms for navigation purposes.
                </p>

                <!-- Description (Full Width) -->
                <div class="relative mb-4">
                    <textarea name="description" id="description" placeholder="Description"
                        class="{{ $inputClasses }} resize-none overflow-hidden" rows="3">{{ old('description', $room->description) }}</textarea>
                    <label class="{{ $labelClasses }}">Description</label>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Media Uploads Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Cover Image -->
                    <div class="conditional-field" id="cover-image-section">
                        <label class="block mb-2 dark:text-gray-300">Cover Image (optional, max 10MB)</label>

                        @if ($room->image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $room->image_path) }}" alt="Current cover"
                                    class="w-full h-32 object-cover rounded">
                                <label class="flex items-center mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="checkbox" name="remove_cover_image" value="1" class="mr-2">
                                    Remove current image
                                </label>
                            </div>
                        @endif

                        <div id="uploadBox"
                            class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800
                        transition-colors overflow-hidden relative">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Image Icon" class="w-8 h-8" onerror="this.style.display='none'">
                            <span id="uploadText" class="text-gray-500 dark:text-gray-300 text-sm text-center px-2">
                                Click to upload new cover image
                            </span>
                            <img id="previewImage" class="absolute inset-0 object-cover w-full h-full hidden"
                                alt="Image preview" />
                        </div>
                        <input type="file" name="image_path" id="image_path" class="hidden" accept="image/*" />
                        @error('image_path')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Video Upload -->
                    <div class="conditional-field" id="video-section">
                        <label class="block mb-2 dark:text-gray-300">Short Video (optional, max 50MB)</label>

                        @if ($room->video_path)
                            <div class="mb-2">
                                <video src="{{ asset('storage/' . $room->video_path) }}"
                                    class="w-full h-32 object-cover rounded" controls></video>
                                <label class="flex items-center mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="checkbox" name="remove_video" value="1" class="mr-2">
                                    Remove current video
                                </label>
                            </div>
                        @endif

                        <div id="videoDropZone"
                            class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/video.png"
                                alt="Video Icon" id="videoIcon" class="w-9 h-9" onerror="this.style.display='none'">
                            <span id="videoUploadText" class="text-gray-500 dark:text-gray-300 text-sm text-center px-2">
                                Drag & drop or click to select new video
                            </span>
                            <p class="text-xs text-gray-400 dark:text-gray-300">(mp4, avi, mpeg)</p>

                            <div id="videoThumbnailPreview" class="absolute inset-0 hidden bg-black">
                                <video id="videoThumbnail" class="w-full h-full object-cover"></video>
                                <button type="button" id="removeVideoThumbnailBtn"
                                    class="absolute top-2 right-2 bg-secondary text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 transition-colors text-lg z-10"
                                    title="Remove video">&times;</button>
                            </div>
                        </div>
                        <input type="file" id="video_path" name="video_path" accept="video/mp4,video/avi,video/mpeg"
                            class="hidden" />
                        @error('video_path')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Carousel Images (Full Width) -->
                <div class="mb-4 conditional-field" id="carousel-images-section">
                    <label class="block mb-2 dark:text-gray-300">Carousel Images (optional, max 50 images, 10MB
                        each)</label>

                    @if ($room->carouselImages && $room->carouselImages->count() > 0)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Existing Images (click X to remove):
                            </p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                @foreach ($room->carouselImages as $carouselImage)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $carouselImage->image_path) }}"
                                            class="w-full h-20 object-cover rounded border">
                                        <label
                                            class="absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center cursor-pointer hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <input type="checkbox" name="remove_carousel_images[]"
                                                value="{{ $carouselImage->id }}" class="hidden">
                                            ×
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div id="carouselUploadBox"
                        class="flex flex-col items-center justify-center w-full min-h-40 p-4
                    border-2 border-dashed border-gray-300 dark:border-gray-600 
                    rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                    dark:hover:border-primary dark:hover:bg-gray-800 transition-colors relative">
                        <div id="carouselPlaceholder" class="text-center">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Image Icon" class="w-8 h-8 mx-auto" onerror="this.style.display='none'">
                            <span id="carouselUploadText" class="text-gray-500 dark:text-gray-300 block mt-2">
                                Click to upload additional images
                            </span>
                        </div>
                        <div id="carouselPreviewContainer"
                            class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 w-full mt-3"></div>
                    </div>
                    <input type="file" name="carousel_images[]" id="carousel_images" class="hidden"
                        accept="image/jpeg,image/jpg,image/png" multiple />
                    @error('carousel_images')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6 conditional-field" id="office-hours-section">
                    <label class="block font-semibold mb-3 text-lg dark:text-gray-300">Office Hours</label>

                    <!-- Setup Section -->
                    <div class="mb-4 p-5 border-2 border-primary rounded-lg dark:bg-gray-800 space-y-4">

                        <!-- Days Selection -->
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Select Days</label>

                            <div class="flex gap-2 mb-3 flex-wrap">
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white transition-colors"
                                    data-days="Mon,Tue,Wed,Thu,Fri">Weekdays</button>
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white transition-colors"
                                    data-days="Sat,Sun">Weekends</button>
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 text-white transition-colors"
                                    data-days="Mon,Tue,Wed,Thu,Fri,Sat,Sun">All Days</button>
                                <button type="button"
                                    class="clear-select px-3 py-1.5 rounded text-sm bg-secondary hover:bg-white hover:text-secondary border border-secondary dark:bg-gray-600 dark:hover:bg-gray-800 text-white transition-colors cursor-pointer shadow-secondary-hover">Clear
                                    All</button>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                @php
                                    $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                @endphp
                                @foreach ($daysOfWeek as $day)
                                    <label
                                        class="flex items-center bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <input type="checkbox"
                                            class="bulk-day-checkbox mr-2 text-primary focus:ring-primary"
                                            value="{{ $day }}">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Time Range</label>
                            <div class="bulk-time-ranges">
                                <div class="bulk-ranges-container">
                                    <div
                                        class="flex flex-col sm:flex-row gap-2 sm:gap-3 bulk-range-row max-w-full sm:max-w-md">
                                        <div class="relative flex-1">
                                            <input type="time"
                                                class="custom-time-input bulk-start-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                                            <button type="button"
                                                class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xl"
                                                title="Clear">&times;</button>
                                        </div>
                                        <div class="relative flex-1">
                                            <input type="time"
                                                class="custom-time-input bulk-end-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                                            <button type="button"
                                                class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xl"
                                                title="Clear">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Button -->
                        <button type="button"
                            class="apply-bulk bg-primary text-white px-4 py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 duration-300 ease-in-out transition-all cursor-pointer shadow-primary-hover">
                            Apply to Selected Days
                        </button>
                    </div>

                    <!-- Saved Hours Display -->
                    <div class="p-5 border-2 border-primary rounded-lg dark:bg-gray-800">
                        <p class="font-medium mb-3 dark:text-gray-300">Saved Office Hours</p>
                        <ul id="officeHoursDisplay" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                    </div>

                    @error('office_hours')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="submit-btn"
                        class="w-full px-8 bg-primary text-white py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                        Update Office
                    </button>
                </div>
            </form>
        </x-upload-progress-modal>
    </div>
@endsection

@push('scripts')
    <script>
        const existingOfficeHours = @json($room->officeHours ?? []);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isUploading = false;

            window.onbeforeunload = function(e) {
                if (isUploading) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            };

            const MAX_CAROUSEL_FILES = 50;
            const MAX_IMAGE_SIZE_MB = 10;
            const MAX_VIDEO_SIZE_MB = 50;

            // Track which files have been compressed
            let compressedFileNames = new Set();

            // Canvas-based image compression
            async function compressImageCanvas(file, maxDimension = 2000, quality = 0.85) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            let width = img.width;
                            let height = img.height;
                            if (width > maxDimension || height > maxDimension) {
                                if (width > height) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                } else {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }
                            const canvas = document.createElement('canvas');
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
                                quality
                            );
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            const form = document.getElementById('room-form');
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
                    compressAndSubmitForm();
                } else {
                    form.submit();
                }
            });

            // Name uniqueness check with debouncing
            let typingTimer;
            const typingDelay = 500;

            nameInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(async () => {
                    // TODO: AJAX uniqueness check here if needed
                }, typingDelay);
            });

            nameInput.addEventListener('keydown', () => clearTimeout(typingTimer));

            // Room type change handler
            const roomTypeSelect = document.getElementById('room_type');
            const conditionalFields = document.querySelectorAll('.conditional-field');

            function toggleConditionalFields() {
                const isEntrancePoint = roomTypeSelect.value === 'entrance_point';
                conditionalFields.forEach(field => {
                    field.style.display = isEntrancePoint ? 'none' : '';
                });
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
            const coverUploadBox = document.getElementById('uploadBox');
            let coverPreview = document.getElementById('previewImage');
            if (!coverPreview) {
                coverPreview = document.createElement('img');
                coverPreview.id = 'previewImage';
                coverPreview.className = 'hidden w-full h-32 object-cover rounded mt-2';
                coverUploadBox.appendChild(coverPreview);
            }

            coverUploadBox.addEventListener('click', () => coverInput.click());
            coverInput.addEventListener('change', () => {
                if (coverInput.files && coverInput.files[0]) {
                    compressAndPreviewCoverImage(coverInput.files[0]);
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
                    const files = Array.from(e.dataTransfer.files);
                    if (files.length > 0) {
                        compressAndPreviewCoverImage(files[0]);
                    }
                }
            }

            async function compressAndPreviewCoverImage(file) {
                try {
                    showTemporaryMessage('Compressing cover image...', 'info');
                    const compressedFile = await compressImageCanvas(file, 2000, 0.85);
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

            carouselUploadBox.addEventListener('click', function(e) {
                // Only block if clicking directly on an image preview div or remove button
                const clickedPreviewItem = e.target.closest('[data-carousel-index]');
                const clickedRemoveBtn = e.target.closest('.remove-carousel-btn');

                if (clickedPreviewItem || clickedRemoveBtn) {
                    return; // Don't open file dialog when clicking on existing images
                }

                carouselInput.click(); // Open file dialog for everything else
            });

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
                    const files = Array.from(e.dataTransfer.files);
                    handleCarouselFiles(files);
                }
            }

            function handleCarouselFiles(newFiles) {
                carouselInput.value = '';

                if (selectedFiles.length + newFiles.length > MAX_CAROUSEL_FILES) {
                    showTemporaryMessage(`Maximum ${MAX_CAROUSEL_FILES} images allowed.`, 'error');
                    return;
                }

                const invalidFiles = newFiles.filter(file => !validateImageFile(file, false));
                if (invalidFiles.length > 0) {
                    showTemporaryMessage('Some files are invalid or too large.', 'error');
                    return;
                }

                // Compress images before adding
                compressCarouselImages(newFiles);
            }

            async function compressCarouselImages(newFiles) {
                showTemporaryMessage(`Compressing ${newFiles.length} image(s)...`, 'info');
                try {
                    const compressedFiles = [];
                    for (let i = 0; i < newFiles.length; i++) {
                        const file = newFiles[i];
                        try {
                            const compressedFile = await compressImageCanvas(file, 2000, 0.85);
                            const finalFile = new File([compressedFile], file.name, {
                                type: compressedFile.type,
                                lastModified: Date.now()
                            });
                            compressedFileNames.add(file.name);
                            compressedFiles.push(finalFile);
                        } catch (error) {
                            console.error(`Failed to compress ${file.name}:`, error);
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
                } finally {}
            }

            function renderCarouselPreviews() {
                carouselPreviewContainer.innerHTML = '';

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden border shadow-sm group aspect-square';
                    div.dataset.carouselIndex = index; // Add this for identification

                    reader.onload = e => {
                        div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs p-1 truncate">
                    ${file.name}
                </div>
                <div class="absolute top-1 left-1 bg-black/60 text-white text-xs px-1 rounded">
                    ${(file.size / 1024 / 1024).toFixed(2)}MB
                </div>
                <button type="button" 
                    class="remove-carousel-btn absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full
                    flex items-center justify-center text-lg hover:bg-red-600 transition-colors
                    opacity-0 group-hover:opacity-100"
                    title="Remove">
                    ×
                </button>
            `;
                    };

                    reader.readAsDataURL(file);
                    carouselPreviewContainer.appendChild(div);
                });

                updateCarouselPlaceholderVisibility();
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

            function updateCarouselPlaceholderVisibility() {
                const carouselPlaceholder = document.getElementById('carouselPlaceholder');
                if (carouselPlaceholder) {
                    carouselPlaceholder.style.display = selectedFiles.length > 0 ? 'none' : '';
                }
            }

            function resetCarouselImages() {
                selectedFiles = [];
                carouselPreviewContainer.innerHTML = '';
                carouselInput.value = '';
                updateCarouselPlaceholderVisibility();
            }

            // Event delegation for carousel remove buttons - DEBUGGING VERSION
            carouselPreviewContainer.addEventListener('click', function(e) {

                const removeBtn = e.target.closest('.remove-carousel-btn');

                if (removeBtn) {
                    e.stopPropagation();
                    e.preventDefault();

                    const carouselItem = removeBtn.closest('[data-carousel-index]');

                    if (carouselItem) {
                        const index = parseInt(carouselItem.dataset.carouselIndex);
                        selectedFiles.splice(index, 1);
                        renderCarouselPreviews();
                        updateCarouselInputFiles();
                    }
                }
            }, true); // Added capture phase


            // Video upload functionality
            const maxVideoSizeMB = 50;
            const allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mpeg'];

            const videoDropZone = document.getElementById('videoDropZone');
            const videoInput = document.getElementById('video_path');
            const videoThumbnailPreview = document.getElementById('videoThumbnailPreview');
            const videoThumbnail = document.getElementById('videoThumbnail');
            const removeVideoThumbnailBtn = document.getElementById('removeVideoThumbnailBtn');
            const videoIcon = document.getElementById('videoIcon');
            const videoUploadText = document.getElementById('videoUploadText');
            const videoFormatText = videoDropZone.querySelector('p.text-xs');

            videoDropZone.addEventListener('click', (e) => {
                // Don't open file dialog if clicking on the remove button
                if (e.target.closest('#removeVideoThumbnailBtn')) {
                    e.stopPropagation();
                    e.preventDefault();
                    return;
                }
                videoInput.click();
            });

            videoInput.addEventListener('change', () => {
                if (videoInput.files && videoInput.files[0]) {
                    showVideoThumbnailPreview(videoInput.files[0]);
                }
            });

            videoDropZone.addEventListener('dragover', e => {
                e.preventDefault();
                videoDropZone.classList.add('border-primary', 'bg-gray-50');
            });

            videoDropZone.addEventListener('dragleave', e => {
                e.preventDefault();
                videoDropZone.classList.remove('border-primary', 'bg-gray-50');
            });

            videoDropZone.addEventListener('drop', e => {
                e.preventDefault();
                videoDropZone.classList.remove('border-primary', 'bg-gray-50');

                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    videoInput.files = e.dataTransfer.files;
                    showVideoThumbnailPreview(e.dataTransfer.files[0]);
                }
            });

            removeVideoThumbnailBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                clearVideoThumbnail();
                videoInput.value = '';
            });

            function showVideoThumbnailPreview(file) {
                // Size check
                if (file.size / 1024 / 1024 > maxVideoSizeMB) {
                    showTemporaryMessage(`"${file.name}" is too large. Max size is ${maxVideoSizeMB} MB.`, 'error');
                    clearVideoThumbnail();
                    videoInput.value = '';
                    return;
                }

                // Type check
                if (!allowedVideoTypes.includes(file.type)) {
                    showTemporaryMessage(`"${file.name}" is not a valid format. Only MP4, AVI, or MPEG allowed.`,
                        'error');
                    clearVideoThumbnail();
                    videoInput.value = '';
                    return;
                }

                // Show thumbnail preview
                const url = URL.createObjectURL(file);
                videoThumbnail.src = url;
                videoThumbnailPreview.classList.remove('hidden');

                // Hide upload icon and text
                if (videoIcon) videoIcon.style.display = 'none';
                if (videoUploadText) videoUploadText.style.display = 'none';
                if (videoFormatText) videoFormatText.style.display = 'none';
            }

            function clearVideoThumbnail() {
                videoThumbnail.src = '';
                videoThumbnailPreview.classList.add('hidden');

                // Show upload icon and text again
                if (videoIcon) videoIcon.style.display = '';
                if (videoUploadText) videoUploadText.style.display = '';
                if (videoFormatText) videoFormatText.style.display = '';
            }


            // Validation functions
            function validateImageFile(file, showError = true) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    if (showError) showTemporaryMessage('Invalid image type.', 'error');
                    return false;
                }
                if (file.size > MAX_IMAGE_SIZE_MB * 1024 * 1024) {
                    if (showError) showTemporaryMessage('Image too large.', 'error');
                    return false;
                }
                return true;
            }

            // Show temporary messages
            function showTemporaryMessage(message, type = "info") {
                let msgDiv = document.getElementById('temp-message');
                if (!msgDiv) {
                    msgDiv = document.createElement('div');
                    msgDiv.id = 'temp-message';
                    msgDiv.className =
                        'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4 py-2 rounded shadow-lg';
                    document.body.appendChild(msgDiv);
                }
                msgDiv.textContent = message;
                msgDiv.className =
                    'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4 py-2 rounded shadow-lg ' +
                    (type === 'success' ? 'bg-green-500 text-white' :
                        type === 'error' ? 'bg-red-500 text-white' :
                        'bg-blue-500 text-white');
                msgDiv.style.display = 'block';
                setTimeout(() => {
                    msgDiv.style.display = 'none';
                }, 3500);
            }

            async function compressAndSubmitForm() {
                isUploading = true;
                window.dispatchEvent(new CustomEvent('upload-start'));
                submitBtn.disabled = true;

                const carouselFiles = Array.from(document.getElementById('carousel_images').files || []);
                const coverFile = document.getElementById('image_path').files[0];

                try {
                    // Compress cover image if needed
                    if (coverFile && !compressedFileNames.has(coverFile.name)) {
                        const compressedCover = await compressImageCanvas(coverFile, 2000, 0.85);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedCover], coverFile.name, {
                            type: compressedCover.type,
                            lastModified: Date.now()
                        }));
                        document.getElementById('image_path').files = dataTransfer.files;
                    }

                    // Compress carousel images if needed
                    const filesToCompress = carouselFiles.filter(f => !compressedFileNames.has(f.name));
                    if (filesToCompress.length > 0) {
                        await compressCarouselImages(filesToCompress);
                    }

                    // Submit the form with AJAX to track progress
                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.upload.onprogress = function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            window.dispatchEvent(new CustomEvent('upload-progress', {
                                detail: {
                                    progress: percent
                                }
                            }));
                        }
                    };

                    xhr.onload = function() {
                        isUploading = false;
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        submitBtn.disabled = false;
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.redirect) {
                                    window.onbeforeunload = null;
                                    window.location.href = data.redirect;
                                    return;
                                }
                            } catch {}
                            window.onbeforeunload = null;
                            window.location.href = '/admin/rooms';
                        } else {
                            let msg = 'Submission failed.';
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.message) msg = data.message;
                            } catch {}
                            showTemporaryMessage(msg, 'error');
                        }
                    };

                    xhr.onerror = function() {
                        isUploading = false;
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        submitBtn.disabled = false;
                        showTemporaryMessage('Upload failed. Please try again.', 'error');
                    };

                    xhr.send(formData);

                } catch (error) {
                    isUploading = false;
                    window.dispatchEvent(new CustomEvent('upload-finish'));
                    submitBtn.disabled = false;
                    showTemporaryMessage('Error during image compression. Please try again.', 'error');
                }
            }

            // ================= Enhanced Office Hours with Day.js =================
            const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            let officeHoursData = {};

            // Day.js Enhanced Time Functions
            function formatTime12Hour(time24) {
                try {
                    if (!time24 || !time24.match(/^\d{2}:\d{2}$/)) {
                        return time24;
                    }

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

            // Apply bulk changes with proper edit handling
            document.querySelector('.apply-bulk').addEventListener('click', function() {
                const selectedDays = Array.from(document.querySelectorAll('.bulk-day-checkbox:checked'))
                    .map(cb => cb.value);
                if (!selectedDays.length) return showTemporaryMessage("Please select at least one day.");

                const ranges = collectBulkRanges();
                if (!ranges) return;

                // Check if we're in edit mode (if any existing schedule matches current selection)
                const isEditMode = checkIfEditMode(selectedDays, ranges);

                if (isEditMode) {
                    // In edit mode: Clear old schedule and apply new one
                    clearExistingScheduleForEdit(selectedDays, ranges);
                }

                // Apply the new schedule to selected days
                selectedDays.forEach(day => {
                    officeHoursData[day] = ranges;
                });

                renderOfficeHours();
                showTemporaryFeedback(this, "Applied Successfully!");
                showTemporaryMessage("Office hours updated for selected days!", "success");
            });

            // Helper function to check if we're editing an existing schedule
            function checkIfEditMode(selectedDays, newRanges) {
                // Check if any selected day already has office hours
                return selectedDays.some(day => officeHoursData[day] && officeHoursData[day].length > 0);
            }

            // Helper function to clear existing schedules when editing
            function clearExistingScheduleForEdit(selectedDays, newRanges) {
                // Find all days that have the same schedule as what we're trying to apply
                const newRangeKey = newRanges.map(r => `${r.start}-${r.end}`).join(",");

                // Find existing grouped schedules
                const existingGroups = {};
                daysOfWeek.forEach(day => {
                    const ranges = officeHoursData[day] || [];
                    const rangeKey = ranges.length ? ranges.map(r => `${r.start}-${r.end}`).join(",") :
                        "closed";

                    if (!existingGroups[rangeKey]) {
                        existingGroups[rangeKey] = [];
                    }
                    existingGroups[rangeKey].push(day);
                });

                // Clear days that are not in the new selection but were part of existing groups
                Object.entries(existingGroups).forEach(([rangeKey, groupDays]) => {
                    if (rangeKey !== "closed") {
                        // Check if any of the selected days belong to this group
                        const hasOverlap = groupDays.some(day => selectedDays.includes(day));

                        if (hasOverlap) {
                            // Clear all days in this group that are NOT in the new selection
                            groupDays.forEach(day => {
                                if (!selectedDays.includes(day)) {
                                    delete officeHoursData[day];
                                }
                            });
                        }
                    }
                });
            }

            // Clear time input when X is clicked
            document.addEventListener('click', e => {
                if (e.target.classList.contains('clear-time') || e.target.closest('.clear-time')) {
                    const button = e.target.classList.contains('clear-time') ? e.target : e.target.closest(
                        '.clear-time');
                    const input = button.previousElementSibling;
                    if (input && input.type === "time") {
                        input.value = "";
                    }
                }
            });

            // Enhanced collectBulkRanges with Day.js validation
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
                    showTemporaryMessage("Please enter at least one valid time range.");
                    return null;
                }

                const overlapCheck = hasOverlapDayJs(ranges);
                if (overlapCheck.hasOverlap) {
                    showTemporaryMessage("Time ranges overlap. Fix them first.");
                    return null;
                }

                return ranges;
            }

            // Enhanced renderOfficeHours
            function renderOfficeHours() {
                const container = document.getElementById("officeHoursDisplay");
                container.innerHTML = "";

                const groupedSchedule = {};

                daysOfWeek.forEach(day => {
                    const ranges = officeHoursData[day] || [];
                    const rangeKey = ranges.length ?
                        ranges.map(r => `${r.start}-${r.end}`).join(",") :
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
                                                                                                                                                                                                                                                        <button type="button" class="edit-schedule-btn bg-primary text-white hover:text-primary hover:bg-white text-sm px-2 py-1 rounded-md border border-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800" 
                                                                                                                                                                                                                                                                data-days='${JSON.stringify(group.days)}' data-ranges='${JSON.stringify(group.ranges)}'>
                                                                                                                                                                                                                                                            Edit
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                        <button type="button" class="delete-schedule-btn bg-secondary text-white hover:text-secondary hover:bg-white text-sm px-2 py-1 rounded-md border border-secondary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800" 
                                                                                                                                                                                                                                                                data-days='${JSON.stringify(group.days)}'>
                                                                                                                                                                                                                                                            Delete
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                ` : ''}
                        </div>
                    `;

                    // Add hidden inputs for form submission
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

            // Function to attach event listeners to edit and delete buttons
            function attachScheduleActionListeners() {
                // Delete functionality
                document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const days = JSON.parse(this.dataset.days);
                        const daysText = formatDaysGroup(days);

                        if (confirm(
                                `Are you sure you want to remove office hours for ${daysText}?`)) {
                            days.forEach(day => {
                                delete officeHoursData[day];
                            });
                            renderOfficeHours();
                            showTemporaryMessage("Office hours deleted successfully!", "success");
                        }
                    });
                });

                // Edit functionality
                document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const days = JSON.parse(this.dataset.days);
                        const ranges = JSON.parse(this.dataset.ranges);

                        // Pre-select the days
                        document.querySelectorAll('.bulk-day-checkbox').forEach(cb => {
                            cb.checked = days.includes(cb.value);
                        });

                        // Pre-fill the time inputs with the first range
                        if (ranges.length > 0) {
                            document.querySelector('.bulk-start-time').value = ranges[0].start;
                            document.querySelector('.bulk-end-time').value = ranges[0].end;
                        }

                        // Scroll to the bulk edit section
                        document.querySelector('.bulk-time-ranges').scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        const bulkSection = document.querySelector(
                            '.mb-6.p-4.border.border-primary.rounded');
                        if (bulkSection) {
                            bulkSection.classList.add('ring-2', 'ring-blue-400');
                            setTimeout(() => {
                                bulkSection.classList.remove('ring-2', 'ring-blue-400');
                            }, 2000);
                        }

                        showTemporaryMessage(
                            "Schedule loaded for editing. Modify time and click 'Apply'.",
                            "info");
                    });
                });
            }

            // FIXED: Enhanced formatDaysGroup function with precise pattern matching
            function formatDaysGroup(days) {
                if (days.length === 0) return "";
                if (days.length === 1) return days[0];

                // Sort days by their order in the week
                const sortedDays = days.sort((a, b) => daysOfWeek.indexOf(a) - daysOfWeek.indexOf(b));

                // Use array comparison for exact matching to prevent the "Daily" bug
                const isExactMatch = (pattern) => {
                    return sortedDays.length === pattern.length &&
                        sortedDays.every((day, index) => day === pattern[index]);
                };

                // Special cases for common patterns - EXACT matching
                if (isExactMatch(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])) {
                    return "Daily";
                }

                if (isExactMatch(['Mon', 'Tue', 'Wed', 'Thu', 'Fri'])) {
                    return "Weekdays";
                }

                if (isExactMatch(['Sat', 'Sun'])) {
                    return "Weekends";
                }

                // Check if days are consecutive
                const isConsecutive = () => {
                    for (let i = 0; i < sortedDays.length - 1; i++) {
                        const currentIndex = daysOfWeek.indexOf(sortedDays[i]);
                        const nextIndex = daysOfWeek.indexOf(sortedDays[i + 1]);
                        if (nextIndex !== currentIndex + 1) return false;
                    }
                    return true;
                };

                // Only format as range if consecutive AND more than 2 days (but not the special cases above)
                if (isConsecutive() && sortedDays.length > 2) {
                    return `${sortedDays[0]} - ${sortedDays[sortedDays.length - 1]}`;
                }

                // Otherwise, return comma-separated list
                return sortedDays.join(", ");
            }

            // Show temporary message notifications
            function showTemporaryMessage(message, type = "info") {
                const existing = document.getElementById("temp-message");
                if (existing) existing.remove();

                const div = document.createElement("div");
                div.id = "temp-message";
                div.textContent = message;

                const base =
                    "fixed top-24 right-4 p-3 rounded shadow-lg z-50 transition-opacity duration-500 border-l-4";
                const colors = {
                    success: "bg-green-100 text-green-700 border border-green-300 dark:bg-green-800 dark:text-green-200 dark:border-green-600",
                    error: "bg-red-100 text-red-700 border border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600",
                    info: "bg-yellow-100 text-yellow-700 border border-yellow-300 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-500"
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

                setTimeout(() => {
                    button.textContent = old;
                }, 2000);
            }

            function showError(row, msg) {
                row.classList.add("bg-red-50", "border", "border-red-400");
                if (!row.querySelector(".error-msg")) {
                    const p = document.createElement("p");
                    p.className = "error-msg text-red-600 text-xs mt-1";
                    p.textContent = msg;
                    row.appendChild(p);
                }
            }

            function clearError(row) {
                row.classList.remove("bg-red-50", "border", "border-red-400");
                const msg = row.querySelector(".error-msg");
                if (msg) msg.remove();
            }

            // Pre-populate office hours from existing data
            if (typeof existingOfficeHours !== 'undefined' && existingOfficeHours && existingOfficeHours.length >
                0) {
                existingOfficeHours.forEach(hour => {
                    // FIX: Use 'day' instead of 'day_of_week'
                    const dayKey = hour.day || hour.day_of_week;

                    if (!officeHoursData[dayKey]) {
                        officeHoursData[dayKey] = [];
                    }

                    // FIX: Trim seconds from time format (08:06:00 -> 08:06)
                    const startTime = hour.start_time ? hour.start_time.substring(0, 5) : hour.start_time;
                    const endTime = hour.end_time ? hour.end_time.substring(0, 5) : hour.end_time;

                    officeHoursData[dayKey].push({
                        start: startTime,
                        end: endTime
                    });
                });
            }

            // Initial render
            renderOfficeHours();

            // Handle existing carousel image removal checkboxes
            document.querySelectorAll('input[name="remove_carousel_images[]"]').forEach(checkbox => {
                const label = checkbox.closest('label');
                if (label) {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            label.classList.add('bg-red-100', 'border-red-500');
                        } else {
                            label.classList.remove('bg-red-100', 'border-red-500');
                        }
                    });
                }
            });

            // Auto-resize textarea based on content
            const descriptionTextarea = document.getElementById('description');
            if (descriptionTextarea) {
                function autoResize() {
                    descriptionTextarea.style.height = 'auto';
                    descriptionTextarea.style.height = descriptionTextarea.scrollHeight + 'px';
                }

                // Resize on page load if there's existing content
                if (descriptionTextarea.value) {
                    autoResize();
                }

                // Resize on input
                descriptionTextarea.addEventListener('input', autoResize);
            }
        });
    </script>
@endpush

@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6 dark:text-gray-300"><span class="text-primary">Edit</span> {{ $room->name }}
        </h2>

        <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data" data-upload onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf
            @method('PUT')

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';

                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <div class="relative mb-4">
                <input type="text" name="name" placeholder="Office Name" class="{{ $inputClasses }}"
                    value="{{ old('name', $room->name) }}" required>
                <label class="{{ $labelClasses }}">Office Name</label>
            </div>

            <div class="relative mb-4">
                <textarea name="description" placeholder="Description" class="{{ $inputClasses }}" rows="3">{{ old('description', $room->description) }}</textarea>
                <label class="{{ $labelClasses }}">Description</label>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium dark:text-gray-300">Room Type</label>
                <select name="room_type" id="room_type"
                    class="w-full border dark:text-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent border-gray-500 dark:bg-gray-800"
                    required>
                    <option value="regular" {{ old('room_type', $room->room_type) === 'regular' ? 'selected' : '' }}>
                        Regular Office
                    </option>
                    <option value="entrance_point"
                        {{ old('room_type', $room->room_type) === 'entrance_point' ? 'selected' : '' }}>
                        Entrance Point
                    </option>
                </select>
                <p class="text-sm text-gray-600 mt-1 dark:text-gray-300">
                    Entrance gates automatically connect to all other rooms for navigation purposes.
                </p>
            </div>

            {{-- Current Cover Image --}}
            <div class="mb-4 conditional-field" id="cover-image-section">
                @if ($room->image_path && Storage::disk('public')->exists($room->image_path))
                    <div class="mb-4">
                        <label class="block mb-2 font-medium dark:text-gray-300">Current Cover Image</label>
                        <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image"
                            class="w-48 rounded mb-2 border" />
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="remove_image_path" value="1" id="remove_image_path"
                                class="form-checkbox" />
                            <span class="dark:text-gray-300">Remove Cover Image</span>
                        </label>
                    </div>
                @endif

                <label class="block mb-2 dark:text-gray-300">Cover Image (optional)</label>

                <div id="uploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
                border-2 border-dashed border-gray-300 dark:border-gray-600 
                rounded cursor-pointer 
                hover:border-primary hover:bg-gray-50 
                dark:hover:border-primary dark:hover:bg-gray-800
                transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                        alt="Image Icon" class="w-8 h-8">
                    <span id="uploadText" class="text-gray-500 dark:text-gray-300">
                        Click to upload cover image(Max 10MB)
                    </span>

                    <img id="previewImage" class="absolute inset-0 object-cover w-full h-full hidden" alt="Image preview" />
                </div>

                {{-- Hidden input placed outside --}}
                <input type="file" name="image_path" id="image_path" class="hidden" accept="image/*" />
            </div>

            {{-- Carousel Images --}}
            <div class="mb-4 max-w-xl mx-auto conditional-field" id="carousel-images-section">
                <label class="block mb-2 dark:text-gray-300">Carousel Images (optional)</label>
                <div id="carouselUploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                        alt="Image Icon" class="w-8 h-8">
                    <span id="carouselUploadText" class="text-gray-500 mb-4 dark:text-gray-300">
                        Click to upload images (max 50, 10MB each)
                    </span>
                    <div id="carouselPreviewContainer" class="flex flex-wrap gap-3 w-full justify-start">
                        {{-- Existing images --}}
                        @if ($room->images && $room->images->count() > 0)
                            @foreach ($room->images as $image)
                                @if (Storage::disk('public')->exists($image->image_path))
                                    <div
                                        class="relative w-24 h-24 rounded overflow-hidden shadow border {{ $image->trashed() ? 'border-red-400 opacity-50' : 'border-gray-300' }}">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="Carousel Image"
                                            class="w-full h-full object-cover rounded" />
                                        @if ($image->trashed())
                                            <span
                                                class="absolute top-1 left-1 bg-yellow-600 text-white rounded px-1 text-xs">Deleted</span>
                                        @else
                                            <button type="button"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow"
                                                onclick="toggleImageRemoval(this, {{ $image->id }})"
                                                title="Remove this image">
                                                &times;
                                            </button>
                                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"
                                                class="hidden" />
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <input type="file" name="carousel_images[]" id="carousel_images" class="hidden" accept="image/*"
                    multiple />
            </div>

            {{-- Current Video --}}
            <div class="mb-4 max-w-xl mx-auto conditional-field" id="video-section">
                @if ($room->video_path && Storage::disk('public')->exists($room->video_path))
                    <div class="mb-4">
                        <label class="block mb-2 font-medium dark:text-gray-300">Current Video</label>
                        <video controls class="w-64 rounded mb-2 border">
                            <source src="{{ Storage::url($room->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="remove_video_path" value="1" id="remove_video_path"
                                class="form-checkbox" />
                            <span class="dark:text-gray-300">Remove Video</span>
                        </label>
                    </div>
                @endif

                <label class="block mb-2 dark:text-gray-300">Short Video (optional)</label>
                <div id="videoDropZone"
                    class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/video.png"
                        alt="Video Icon" class="w-9 h-9">
                    <p class="mb-2 dark:text-gray-300">Drag & drop a video file here or click to select</p>
                    <p class="text-xs text-gray-400 dark:text-gray-300">(mp4, avi, mpeg | max 50MB)</p>
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
            </div>

            <div class="mb-6 conditional-field" id="office-hours-section">
                <label class="block font-semibold mb-2 dark:text-gray-300">Office Hours</label>

                {{-- Bulk Day Selection Section --}}
                <div class="mb-6 p-4 border border-primary rounded dark:bg-gray-800">
                    <p class="font-semibold mb-3 dark:text-gray-300">Set Time Range for Multiple Days</p>

                    {{-- Day Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Select Days:</label>
                        <div class="flex gap-2 flex-wrap">
                            @php
                                $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            @endphp
                            @foreach ($daysOfWeek as $day)
                                <label
                                    class="flex items-center bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                    <input type="checkbox"
                                        class="bulk-day-checkbox mr-2 text-primary focus:ring-primary dark:focus:ring-primary"
                                        value="{{ $day }}">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Quick Select Buttons --}}
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

                    {{-- Time Range Input (Single Row Only) --}}
                    <div class="bulk-time-ranges mb-4">
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Time Range:</label>
                        <div class="bulk-ranges-container">
                            <div class="flex gap-2 mb-2 bulk-range-row">
                                <div class="relative flex-1">
                                    <input type="time"
                                        class="custom-time-input bulk-start-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                        title="Clear">
                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                                            class="w-3 h-3 cursor-pointer hover:scale-120 transition-all duration-300 ease-in-out"
                                            alt="Clear">
                                    </button>
                                </div>
                                <div class="relative flex-1">
                                    <input type="time"
                                        class="custom-time-input bulk-end-time border border-gray-300 dark:border-gray-600 rounded p-2 w-full pr-8 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                        title="Clear">
                                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                                            class="w-3 h-3 cursor-pointer hover:scale-120 transition-all duration-300 ease-in-out"
                                            alt="Clear">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Apply Button --}}
                    <button type="button"
                        class="apply-bulk bg-primary text-center text-white px-4 py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 duration-300 ease-in-out transition-all cursor-pointer shadow-primary-hover">
                        Apply to Selected Days
                    </button>
                </div>

                {{-- Display Saved Hours --}}
                <div class="p-4 border border-primary rounded dark:bg-gray-800">
                    <p class="mb-3 dark:text-gray-300">Saved Office Hours</p>
                    <ul id="officeHoursDisplay" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-2 bg-primary rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Update Office
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Room type change handler with same logic as create
            const roomTypeSelect = document.getElementById('room_type');
            const conditionalFields = document.querySelectorAll('.conditional-field');

            function toggleConditionalFields() {
                const isEntrancePoint = roomTypeSelect.value === 'entrance_point';

                conditionalFields.forEach(field => {
                    if (isEntrancePoint) {
                        field.style.display = 'none';
                        const inputs = field.querySelectorAll('input, textarea, select');
                        inputs.forEach(input => {
                            input.disabled = true;
                            if (input.type === 'file' && !isPageLoad) {
                                input.value = '';
                            }
                        });
                    } else {
                        field.style.display = 'block';
                        const inputs = field.querySelectorAll('input, textarea, select');
                        inputs.forEach(input => {
                            input.disabled = false;
                        });
                    }
                });

                if (isEntrancePoint && !isPageLoad) {
                    const coverPreview = document.getElementById('previewImage');
                    const coverUploadText = document.getElementById('uploadText');
                    const uploadBox = document.getElementById('uploadBox');
                    if (coverPreview) {
                        coverPreview.classList.add('hidden');
                        coverPreview.src = '';
                    }
                    if (coverUploadText) coverUploadText.style.display = '';
                    const uploadIcon = uploadBox?.querySelector('img');
                    if (uploadIcon) uploadIcon.style.display = '';

                    const carouselContainer = document.getElementById('carouselPreviewContainer');
                    if (carouselContainer) {
                        // Only clear new files, not existing images
                        [...carouselContainer.children].forEach(div => {
                            if (!div.querySelector('input[type="checkbox"]')) {
                                div.remove();
                            }
                        });
                    }
                    selectedFiles = [];
                    updateUploadIconVisibility();

                    clearVideo();
                    const videoInput = document.getElementById('video_path');
                    if (videoInput) videoInput.value = '';

                    officeHoursData = {};
                    renderOfficeHours();
                }

                sessionStorage.setItem('room_type', roomTypeSelect.value);
            }

            let isPageLoad = true;
            const savedRoomType = sessionStorage.getItem('room_type');
            if (savedRoomType && savedRoomType !== roomTypeSelect.value) {
                roomTypeSelect.value = savedRoomType;
            }

            toggleConditionalFields();

            setTimeout(() => {
                isPageLoad = false;
            }, 100);

            roomTypeSelect.addEventListener('change', () => {
                isPageLoad = false;
                toggleConditionalFields();
            });

            // Cover image upload preview + drag and drop
            const coverInput = document.getElementById('image_path');
            const coverPreview = document.getElementById('previewImage');
            const coverUploadBox = document.getElementById('uploadBox');

            coverUploadBox.addEventListener('click', () => coverInput.click());

            coverInput.addEventListener('change', () => {
                if (coverInput.files && coverInput.files[0]) {
                    showCoverPreview(coverInput.files[0]);
                }
            });

            coverUploadBox.addEventListener('dragover', e => {
                e.preventDefault();
                coverUploadBox.classList.add('border-primary', 'bg-gray-50');
            });

            coverUploadBox.addEventListener('dragleave', e => {
                e.preventDefault();
                coverUploadBox.classList.remove('border-primary', 'bg-gray-50');
            });

            coverUploadBox.addEventListener('drop', e => {
                e.preventDefault();
                coverUploadBox.classList.remove('border-primary', 'bg-gray-50');

                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    coverInput.files = e.dataTransfer.files;
                    showCoverPreview(e.dataTransfer.files[0]);
                }
            });

            function showCoverPreview(file) {
                const reader = new FileReader();
                reader.onload = e => {
                    coverPreview.src = e.target.result;
                    coverPreview.classList.remove('hidden');

                    const icon = coverUploadBox.querySelector('img');
                    const text = coverUploadBox.querySelector('span');
                    if (icon) icon.style.display = 'none';
                    if (text) text.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Carousel images functionality + drag and drop
            const maxFiles = 50;
            const maxSizeMB = 10;
            const carouselInput = document.getElementById('carousel_images');
            const carouselUploadBox = document.getElementById('carouselUploadBox');
            const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
            let selectedFiles = [];

            carouselUploadBox.addEventListener('click', () => carouselInput.click());

            function updateUploadIconVisibility() {
                const icon = carouselUploadBox.querySelector('img');
                const text = carouselUploadBox.querySelector('span');

                // Count existing images that aren't marked for removal
                const existingImages = [...carouselPreviewContainer.children].filter(div => {
                    const checkbox = div.querySelector('input[type="checkbox"]');
                    return !checkbox || !checkbox.checked;
                });

                const totalVisible = existingImages.length + selectedFiles.length;

                if (icon) icon.style.display = totalVisible > 0 ? 'none' : '';
                if (text) text.style.display = totalVisible > 0 ? 'none' : '';
            }

            function updateInputFiles() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                carouselInput.files = dt.files;
            }

            function renderPreviews() {
                // Remove only the new file previews (not existing images)
                [...carouselPreviewContainer.children].forEach(div => {
                    if (!div.querySelector('input[type="checkbox"]')) {
                        div.remove();
                    }
                });

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
                            'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow';
                        removeBtn.onclick = ev => {
                            ev.preventDefault();
                            selectedFiles.splice(index, 1);
                            renderPreviews();
                            updateInputFiles();
                            updateUploadIconVisibility();
                        };

                        container.appendChild(img);
                        container.appendChild(removeBtn);
                        carouselPreviewContainer.appendChild(container);
                    };
                    reader.readAsDataURL(file);
                });

                updateUploadIconVisibility();
            }

            carouselInput.addEventListener('change', () => handleCarouselFiles(Array.from(carouselInput.files ||
            [])));

            carouselUploadBox.addEventListener('dragover', e => {
                e.preventDefault();
                carouselUploadBox.classList.add('border-primary', 'bg-gray-50');
            });

            carouselUploadBox.addEventListener('dragleave', e => {
                e.preventDefault();
                carouselUploadBox.classList.remove('border-primary', 'bg-gray-50');
            });

            carouselUploadBox.addEventListener('drop', e => {
                e.preventDefault();
                carouselUploadBox.classList.remove('border-primary', 'bg-gray-50');

                if (e.dataTransfer.files) {
                    handleCarouselFiles(Array.from(e.dataTransfer.files));
                }
            });

            function handleCarouselFiles(newFiles) {
                carouselInput.value = '';

                // Count existing images that aren't marked for removal
                const existingCount = [...carouselPreviewContainer.children].filter(div => {
                    const checkbox = div.querySelector('input[type="checkbox"]');
                    return checkbox && !checkbox.checked;
                }).length;

                // Check number of files
                if (existingCount + selectedFiles.length + newFiles.length > maxFiles) {
                    alert(`You can upload max ${maxFiles} images.`);
                    return;
                }

                // Limit file sizes per file
                for (let file of newFiles) {
                    if (file.size > maxSizeMB * 1024 * 1024) {
                        alert(`"${file.name}" is too large. Max size is ${maxSizeMB} MB.`);
                        return;
                    }
                }

                selectedFiles = selectedFiles.concat(newFiles);
                renderPreviews();
                updateInputFiles();
            }

            // Function to toggle image removal for existing images
            window.toggleImageRemoval = function(button, imageId) {
                const container = button.parentElement;
                const checkbox = container.querySelector('input[type="checkbox"]');

                if (checkbox.checked) {
                    checkbox.checked = false;
                    container.style.opacity = '1';
                    button.innerHTML = '&times;';
                } else {
                    checkbox.checked = true;
                    container.style.opacity = '0.4';
                    button.innerHTML = '↶';
                }
                updateUploadIconVisibility();
            };

            updateUploadIconVisibility();

            // Video upload functionality
            const maxVideoSizeMB = 50;
            const allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mpeg'];

            const dropZone = document.getElementById('videoDropZone');
            const videoInput = document.getElementById('video_path');
            const videoPreviewContainer = document.getElementById('videoPreviewContainer');
            const videoPreview = document.getElementById('videoPreview');
            const removeVideoBtn = document.getElementById('removeVideoBtn');

            dropZone.addEventListener('click', () => videoInput.click());

            videoInput.addEventListener('change', () => {
                if (videoInput.files && videoInput.files[0]) {
                    showVideoPreview(videoInput.files[0]);
                }
            });

            dropZone.addEventListener('dragover', e => {
                e.preventDefault();
                dropZone.classList.add('border-primary', 'bg-gray-50');
            });

            dropZone.addEventListener('dragleave', e => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-gray-50');
            });

            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-gray-50');

                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    videoInput.files = e.dataTransfer.files;
                    showVideoPreview(e.dataTransfer.files[0]);
                }
            });

            removeVideoBtn.addEventListener('click', () => {
                clearVideo();
                videoInput.value = '';
            });

            function showVideoPreview(file) {
                // Size check
                if (file.size / 1024 / 1024 > maxVideoSizeMB) {
                    alert(`"${file.name}" is too large. Max size is ${maxVideoSizeMB} MB.`);
                    clearVideo();
                    videoInput.value = '';
                    return;
                }

                // Type check
                if (!allowedVideoTypes.includes(file.type)) {
                    alert(`"${file.name}" is not a valid format. Only MP4, AVI, or MPEG allowed.`);
                    clearVideo();
                    videoInput.value = '';
                    return;
                }

                // Show preview if valid
                const url = URL.createObjectURL(file);
                videoPreview.src = url;
                videoPreviewContainer.classList.remove('hidden');
            }

            function clearVideo() {
                videoPreview.src = '';
                videoPreviewContainer.classList.add('hidden');
            }

            // ================= Enhanced Office Hours with Day.js =================
            const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            let officeHoursData = {};

            // Populate existing data from the room model
            officeHoursData = @json($existingOfficeHours);

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
                    updateBulkInputsForSelectedDays();
                });
            });

            document.querySelector('.clear-select').addEventListener('click', () => {
                document.querySelectorAll('.bulk-day-checkbox').forEach(cb => cb.checked = false);
                updateBulkInputsForSelectedDays();
            });

            // Apply bulk changes with proper edit handling
            document.querySelector('.apply-bulk').addEventListener('click', function() {
                const selectedDays = Array.from(document.querySelectorAll('.bulk-day-checkbox:checked'))
                    .map(cb => cb.value);
                if (!selectedDays.length) return alert("Please select at least one day.");

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

            // Function to update bulk inputs when days are selected
            function updateBulkInputsForSelectedDays() {
                const selectedDays = Array.from(document.querySelectorAll('.bulk-day-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedDays.length === 0) {
                    document.querySelector('.bulk-start-time').value = '';
                    document.querySelector('.bulk-end-time').value = '';
                    return;
                }

                let commonRange = null;

                if (selectedDays.length === 1) {
                    const dayRanges = officeHoursData[selectedDays[0]];
                    if (dayRanges && dayRanges.length > 0) {
                        commonRange = dayRanges[0];
                    }
                } else {
                    const firstDayRanges = officeHoursData[selectedDays[0]];
                    if (firstDayRanges && firstDayRanges.length > 0) {
                        const candidateRange = firstDayRanges[0];

                        const allHaveSameRange = selectedDays.every(day => {
                            const dayRanges = officeHoursData[day];
                            return dayRanges && dayRanges.length > 0 &&
                                dayRanges[0].start === candidateRange.start &&
                                dayRanges[0].end === candidateRange.end;
                        });

                        if (allHaveSameRange) {
                            commonRange = candidateRange;
                        }
                    }
                }

                const startInput = document.querySelector('.bulk-start-time');
                const endInput = document.querySelector('.bulk-end-time');

                if (commonRange) {
                    startInput.value = commonRange.start;
                    endInput.value = commonRange.end;
                } else {
                    startInput.value = '';
                    endInput.value = '';
                }
            }

            // Add event listeners for day checkbox changes
            document.querySelectorAll('.bulk-day-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkInputsForSelectedDays);
            });

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
                    alert("Please enter at least one valid time range.");
                    return null;
                }

                const overlapCheck = hasOverlapDayJs(ranges);
                if (overlapCheck.hasOverlap) {
                    alert("Time ranges overlap. Fix them first.");
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
                button.classList.add('bg-green-500');
                button.classList.remove('bg-primary');
                setTimeout(() => {
                    button.textContent = old;
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-primary');
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

            // Initial render
            renderOfficeHours();
        });
    </script>
@endpush
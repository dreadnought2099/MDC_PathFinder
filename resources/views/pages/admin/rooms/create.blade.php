@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6 dark:text-gray-300"><span class="text-primary">Add</span> New Office</h2>

        <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data" data-upload>
            @csrf

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-700';

                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';

                $textClass =
                    'peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary outline-none bg-white dark:bg-gray-700';
            @endphp

            <div class="relative mb-4">
                <input type="text" name="name" placeholder="Office Name" class="{{ $inputClasses }}" required>
                <label class="{{ $labelClasses }}">Office Name</label>
            </div>

            <div class="relative mb-4">
                <textarea name="description" class="{{ $textClass }}" rows="3"></textarea>
                <label class="{{ $labelClasses }}">Description</label>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium dark:text-gray-300">Room Type</label>
                <select name="room_type"
                    class="w-full border dark:text-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent border-primary dark:bg-gray-800"
                    required>
                    <option value="regular" {{ old('room_type') === 'regular' ? 'selected' : '' }}>
                        Regular Room/Office
                    </option>
                    <option value="entrance_gate" {{ old('room_type') === 'entrance_gate' ? 'selected' : '' }}>
                        Entrance Gate
                    </option>
                </select>
                <p class="text-sm text-gray-600 mt-1 dark:text-gray-300">
                    Entrance gates automatically connect to all other rooms for navigation purposes.
                </p>
            </div>

            <div class="mb-4">
                <label class="block mb-2 dark:text-gray-300">Cover Image (optional)</label>

                <label for="image_path" id="uploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
               border-2 border-dashed border-gray-300 dark:border-gray-600 
               rounded cursor-pointer 
               hover:border-primary hover:bg-gray-50 
               dark:hover:border-primary dark:hover:bg-gray-800
               transition-colors overflow-hidden relative">
                    <img src="{{ asset('icons/image.png') }}" alt="Image Icon" class="w-8 h-8">
                    <span id="uploadText" class="text-gray-500 dark:text-gray-300">
                        Click to upload cover image
                    </span>

                    <input type="file" name="image_path" id="image_path" class="hidden" accept="image/*" />

                    <img id="previewImage" class="absolute inset-0 object-cover w-full h-full hidden" alt="Image preview" />
                </label>
            </div>

            {{-- Carousel Images --}}
            <div class="mb-4 max-w-xl mx-auto">
                <label class="block mb-2 dark:text-gray-300">Carousel Images (optional)</label>

                <label for="carousel_images" id="carouselUploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 
               border-2 border-dashed border-gray-300 dark:border-gray-600 
               rounded cursor-pointer 
               hover:border-primary hover:bg-gray-50 
               dark:hover:border-primary dark:hover:bg-gray-800
               transition-colors overflow-hidden relative">
                    <img src="{{ asset('icons/image.png') }}" alt="Image Icon" class="w-8 h-8">
                    <span id="carouselUploadText" class="text-gray-500 mb-4 dark:text-gray-300">Click to upload images (max
                        50)</span>

                    <input type="file" name="carousel_images[]" id="carousel_images" class="hidden" accept="image/*"
                        multiple />

                    {{-- Preview Container --}}
                    <div id="carouselPreviewContainer" class="flex flex-wrap gap-3 w-full justify-start"></div>
                </label>
            </div>

            <div class="mb-4 max-w-xl mx-auto">
                <label class="block mb-2 dark:text-gray-300">Short Video (optional)</label>

                <div id="videoDropZone"
                    class="flex flex-col items-center justify-center w-full h-40 
               border-2 border-dashed border-gray-300 dark:border-gray-600 
               rounded cursor-pointer 
               hover:border-primary hover:bg-gray-50 
               dark:hover:border-primary dark:hover:bg-gray-800
               transition-colors overflow-hidden relative">
                    <img src="{{ asset('icons/video.png') }}" alt="Video Icon" class="w-9 h-9">
                    <p class="mb-2 dark:text-gray-300">Drag & drop a video file here or click to select</p>
                    <p class="text-xs text-gray-400 dark:text-gray-300">(mp4, avi, mpeg | max 100MB)</p>

                    <input type="file" id="video_path" name="video_path" accept="video/mp4,video/avi,video/mpeg"
                        class="hidden" />
                </div>

                <div id="videoPreviewContainer" class="mt-4 hidden relative rounded border border-gray-300 overflow-hidden">
                    <video id="videoPreview" controls class="w-full h-auto bg-black"></video>
                    <button type="button" id="removeVideoBtn"
                        class="absolute top-2 right-2 bg-secondary text-white rounded-full p-1 hover:bg-red-700 transition-colors"
                        title="Remove video">&times;</button>
                </div>
            </div>

            <div class="mb-6">
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
                                        <img src="{{ asset('icons/exit.png') }}"
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
                                        <img src="{{ asset('icons/exit.png') }}"
                                            class="w-3 h-3 cursor-pointer hover:scale-120 transition-all duration-300 ease-in-out"
                                            alt="Clear">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Apply Button --}}
                    <button type="button"
                        class="apply-bulk bg-primary text-center text-white px-4 py-2 rounded-full hover:text-primary border-2 border-primary hover:bg-white dark:hover:bg-gray-800 duration-300 ease-in-out transition-all cursor-pointer shadow-primary-hover">
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
                    class="w-full bg-primary text-white px-4 py-2 bg-primary rounded-full hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Save Room
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cover image upload preview
            const coverInput = document.getElementById('image_path');
            const coverPreview = document.getElementById('previewImage');
            const coverUploadText = document.getElementById('uploadText');

            coverInput.addEventListener('change', () => {
                if (coverInput.files && coverInput.files[0]) {
                    const reader = new FileReader();

                    reader.onload = e => {
                        coverPreview.src = e.target.result;
                        coverPreview.classList.remove('hidden');
                        // Hide the upload text and icon when image is shown
                        const uploadBox = document.getElementById('uploadBox');
                        const icon = uploadBox.querySelector('img');
                        const text = uploadBox.querySelector('span');
                        if (icon) icon.style.display = 'none';
                        if (text) text.style.display = 'none';
                    };

                    reader.readAsDataURL(coverInput.files[0]);
                }
            });

            // Carousel images functionality
            const carouselInput = document.getElementById('carousel_images');
            const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
            const carouselUploadText = document.getElementById('carouselUploadText');

            let selectedFiles = [];

            function updateUploadIconVisibility() {
                // Get the carousel upload box elements
                const carouselUploadBox = document.getElementById('carouselUploadBox');
                const icon = carouselUploadBox.querySelector('img');
                const text = carouselUploadBox.querySelector('span');

                // Hide/show icon and text based on selected files
                if (icon) {
                    icon.style.display = selectedFiles.length > 0 ? 'none' : '';
                }
                if (text) {
                    text.style.display = selectedFiles.length > 0 ? 'none' : '';
                }
            }

            function updateInputFiles() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                carouselInput.files = dt.files;
            }

            function renderPreviews() {
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

            carouselInput.addEventListener('change', () => {
                const newFiles = Array.from(carouselInput.files);
                carouselInput.value = ''; // so same file can be reselected

                if (selectedFiles.length + newFiles.length > 50) {
                    alert('You can upload max 50 images.');
                    return;
                }

                selectedFiles = selectedFiles.concat(newFiles);
                renderPreviews();
                updateInputFiles();
            });

            updateUploadIconVisibility();

            // Video upload functionality
            const dropZone = document.getElementById('videoDropZone');
            const videoInput = document.getElementById('video_path');
            const videoPreviewContainer = document.getElementById('videoPreviewContainer');
            const videoPreview = document.getElementById('videoPreview');
            const removeVideoBtn = document.getElementById('removeVideoBtn');

            // Click drop zone triggers file input
            dropZone.addEventListener('click', () => {
                videoInput.click();
            });

            // Handle file selection
            videoInput.addEventListener('change', () => {
                if (videoInput.files && videoInput.files[0]) {
                    showVideoPreview(videoInput.files[0]);
                }
            });

            // Handle drag and drop
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

            // Remove video
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

            // ================= Enhanced Office Hours =================
            const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            let officeHoursData = {}; // stores applied ranges per day

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

            // Clear all selection
            document.querySelector('.clear-select').addEventListener('click', () => {
                document.querySelectorAll('.bulk-day-checkbox').forEach(cb => cb.checked = false);
            });

            // Apply bulk changes
            document.querySelector('.apply-bulk').addEventListener('click', function() {
                const selectedDays = Array.from(document.querySelectorAll('.bulk-day-checkbox:checked'))
                    .map(cb => cb.value);
                if (!selectedDays.length) return alert("Please select at least one day.");

                const ranges = collectBulkRanges();
                if (!ranges) return;

                selectedDays.forEach(day => {
                    officeHoursData[day] = ranges;
                });

                renderOfficeHours();
                showTemporaryFeedback(this, "Applied Successfully!");
                showTemporaryMessage("Office hours updated for selected days!", "success");
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

            // Helper Functions
            function collectBulkRanges() {
                const ranges = [];
                let valid = true;

                document.querySelectorAll('.bulk-range-row').forEach(row => {
                    const start = row.querySelector('.bulk-start-time').value;
                    const end = row.querySelector('.bulk-end-time').value;
                    clearError(row);

                    if (start && end) {
                        if (start >= end) {
                            showError(row, "End must be later than start");
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
                if (hasOverlap(ranges)) {
                    alert("Time ranges overlap. Fix them first.");
                    return null;
                }

                return ranges;
            }

            // Convert 24-hour time to 12-hour format
            function formatTime12Hour(time24) {
                const [hours, minutes] = time24.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
                return `${hour12}:${minutes} ${ampm}`;
            }

            // Enhanced renderOfficeHours with edit/delete functionality
            function renderOfficeHours() {
                const container = document.getElementById("officeHoursDisplay");
                container.innerHTML = "";

                // Group days by their time ranges
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

                // Render grouped schedule
                Object.entries(groupedSchedule).forEach(([rangeKey, group]) => {
                    const li = document.createElement("li");
                    li.className =
                        "mb-3 p-3 bg-white rounded border relative dark:bg-gray-800 border border-primary";

                    const daysText = formatDaysGroup(group.days);
                    let timeText;

                    if (rangeKey === "closed") {
                        timeText = "Closed";
                    } else {
                        timeText = group.ranges.map(r =>
                            `${formatTime12Hour(r.start)} - ${formatTime12Hour(r.end)}`
                        ).join(", ");
                    }

                    li.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 dark:text-gray-300">${daysText}</div>
                        <div class="text-sm text-gray-600 mt-1 dark:text-gray-300">${timeText}</div>
                    </div>
                    ${rangeKey !== "closed" ? `
                                        <div class="flex gap-2 ml-4">
                                            <button type="button" class="edit-schedule-btn bg-primary text-white hover:text-primary hover:bg-white text-sm px-2 py-1 rounded border border-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800" 
                                                    data-days='${JSON.stringify(group.days)}' data-ranges='${JSON.stringify(group.ranges)}'>
                                                Edit
                                            </button>
                                            <button type="button" class="delete-schedule-btn bg-secondary text-white hover:text-secondary hover:bg-white text-sm px-2 py-1 rounded border border-secondary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800" 
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

                // Add event listeners for edit and delete buttons
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
                            '.mb-6.p-4.border.border-primary.rounded'
                        );
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

            // Format consecutive days into ranges (e.g., "Mon - Wed" or "Mon, Wed, Fri")
            function formatDaysGroup(days) {
                if (days.length === 0) return "";
                if (days.length === 1) return days[0];

                // Sort days by their order in the week
                const sortedDays = days.sort((a, b) => daysOfWeek.indexOf(a) - daysOfWeek.indexOf(b));

                // Check if days are consecutive
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

            function hasOverlap(ranges) {
                const sorted = ranges.slice().sort((a, b) => a.start.localeCompare(b.start));
                for (let i = 0; i < sorted.length - 1; i++) {
                    if (sorted[i].end > sorted[i + 1].start) return true;
                }
                return false;
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

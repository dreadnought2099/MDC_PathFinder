@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-4xl mx-auto mt-10 p-10 bg-white border-2 border-primary rounded-lg shadow">

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-2"><span class="text-primary">Edit</span>
            {{ $room->name }}</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
            data-upload>
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block font-semibold mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block font-semibold mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('description', $room->description) }}</textarea>
            </div>

            {{-- Current Cover Image --}}
            @if ($room->image_path && Storage::disk('public')->exists($room->image_path))
                <div>
                    <label class="block font-semibold mb-1">Current Cover Image</label>
                    <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image" class="w-48 rounded mb-2 border" />
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="remove_image_path" value="1" id="remove_image_path"
                            class="form-checkbox" />
                        <span>Remove Cover Image</span>
                    </label>
                </div>
            @endif

            {{-- Cover Image Upload --}}
            <div>
                <label for="image_path" class="block font-semibold mb-1">Upload New Cover Image</label>
                <input type="file" id="image_path" name="image_path" accept="image/*" class="block" />
            </div>

            {{-- Current Video --}}
            @if ($room->video_path && Storage::disk('public')->exists($room->video_path))
                <div>
                    <label class="block font-semibold mb-1">Current Video</label>
                    <video controls class="w-64 rounded mb-2 border">
                        <source src="{{ Storage::url($room->video_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="remove_video_path" value="1" id="remove_video_path"
                            class="form-checkbox" />
                        <span>Remove Video</span>
                    </label>
                </div>
            @endif

            {{-- Video Upload --}}
            <div>
                <label for="video_path" class="block font-semibold mb-1">Upload New Video (mp4, avi, mpeg)</label>
                <input type="file" id="video_path" name="video_path" accept="video/mp4,video/avi,video/mpeg"
                    class="block" />
            </div>

            {{-- Enhanced Office Hours Section --}}
            <div class="mb-6">
                <label class="block font-semibold mb-2">Office Hours</label>

                {{-- Bulk Day Selection Section --}}
                <div class="mb-6 p-4 border rounded bg-blue-50">
                    <p class="font-semibold mb-3">Set Time Range for Multiple Days</p>

                    {{-- Day Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Select Days:</label>
                        <div class="flex gap-2 flex-wrap">
                            @php
                                $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            @endphp
                            @foreach ($daysOfWeek as $day)
                                <label
                                    class="flex items-center bg-white border rounded px-3 py-2 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" class="bulk-day-checkbox mr-2" value="{{ $day }}">
                                    <span class="text-sm">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Quick Select Buttons --}}
                        <div class="mt-2 flex gap-2 flex-wrap">
                            <button type="button"
                                class="quick-select bg-gray-500 text-white px-3 py-1 rounded text-sm cursor-pointer"
                                data-days="Mon,Tue,Wed,Thu,Fri">Weekdays</button>
                            <button type="button"
                                class="quick-select bg-gray-500 text-white px-3 py-1 rounded text-sm cursor-pointer"
                                data-days="Sat,Sun">Weekends</button>
                            <button type="button"
                                class="quick-select bg-gray-500 text-white px-3 py-1 rounded text-sm cursor-pointer"
                                data-days="Mon,Tue,Wed,Thu,Fri,Sat,Sun">All Days</button>
                            <button type="button"
                                class="clear-select bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm cursor-pointer">Clear
                                All</button>
                        </div>
                    </div>

                    {{-- Time Range Input (Single Row Only) --}}
                    <div class="bulk-time-ranges mb-4">
                        <label class="block text-sm font-medium mb-2">Time Range:</label>
                        <div class="bulk-ranges-container">
                            <div class="flex gap-2 mb-2 bulk-range-row">
                                <div class="relative flex-1">
                                    <input type="time" class="bulk-start-time border rounded p-2 w-full pr-8">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                                        title="Clear">
                                        <img src="{{ asset('icons/exit.png') }}"
                                            class="w-3 h-3 cursor-pointer hover:scale-120 transition-all duration-300 ease-in-out"
                                            alt="Clear">
                                    </button>
                                </div>
                                <div class="relative flex-1">
                                    <input type="time" class="bulk-end-time border rounded p-2 w-full pr-8">
                                    <button type="button"
                                        class="clear-time absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
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
                        class="apply-bulk bg-primary text-center text-white px-4 py-2 rounded hover:text-primary border-2 border-primary hover:bg-white duration-300 ease-in-out transition-all cursor-pointer">
                        Apply to Selected Days
                    </button>
                </div>

                {{-- Display Saved Hours --}}
                <div class="p-4 border rounded bg-gray-50">
                    <p class="font-semibold mb-3">Saved Office Hours</p>
                    <ul id="officeHoursDisplay" class="space-y-2 text-sm text-gray-700"></ul>
                </div>
            </div>

            {{-- Current Carousel Images --}}
            <div class="mb-4 text-gray-600 max-w-xl mx-auto">
                <label class="block mb-2 font-semibold text-gray-700">Carousel Images (optional)</label>

                <label for="carousel_images" id="carouselUploadBox"
                    class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary hover:bg-gray-50 transition-colors p-4 overflow-auto relative">

                    <svg id="carouselUploadIcon" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="carouselUploadText" class="text-gray-500 mb-4">Click to upload images</span>

                    <input type="file" name="carousel_images[]" id="carousel_images" class="hidden" accept="image/*"
                        multiple />

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
                                            <label
                                                class="absolute top-1 right-1 bg-red-600 text-white rounded px-1 text-xs cursor-pointer opacity-0 hover:opacity-100 transition-opacity"
                                                title="Remove this image">
                                                <input type="checkbox" name="remove_images[]"
                                                    value="{{ $image->id }}" class="hidden" />
                                                ✕
                                            </label>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </label>
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit"
                    class="w-full bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 px-6 py-3 rounded-xl shadow-md hover:shadow-lg cursor-pointer">
                    Update Room
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Carousel image handling (existing code)
            const carouselInput = document.getElementById('carousel_images');
            const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
            const carouselUploadIcon = document.getElementById('carouselUploadIcon');
            const carouselUploadText = document.getElementById('carouselUploadText');

            function updateUploadIconVisibility() {
                const visiblePreviews = [...carouselPreviewContainer.children].filter(div => {
                    const checkbox = div.querySelector('input[type="checkbox"]');
                    return !checkbox || !checkbox.checked;
                });
                carouselUploadIcon.style.display = visiblePreviews.length > 0 ? 'none' : 'block';
                carouselUploadText.style.display = visiblePreviews.length > 0 ? 'none' : 'block';
            }

            updateUploadIconVisibility();

            carouselInput?.addEventListener('change', () => {
                [...carouselPreviewContainer.children].forEach(div => {
                    if (!div.querySelector('input[type="checkbox"]')) {
                        div.remove();
                    }
                });

                const newFiles = Array.from(carouselInput.files);

                const existingCount = [...carouselPreviewContainer.children].filter(div => {
                    const checkbox = div.querySelector('input[type="checkbox"]');
                    return checkbox && !checkbox.checked;
                }).length;

                if (existingCount + newFiles.length > 50) {
                    alert('You can upload max 50 images in total.');
                    return;
                }

                newFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const container = document.createElement('div');
                        container.className =
                            'relative w-24 h-24 rounded overflow-hidden shadow border border-gray-300';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover rounded';

                        container.appendChild(img);
                        carouselPreviewContainer.appendChild(container);

                        updateUploadIconVisibility();
                    };
                    reader.readAsDataURL(file);
                });
            });

            carouselPreviewContainer?.addEventListener('change', e => {
                if (e.target.matches('input[type="checkbox"]')) {
                    const parentDiv = e.target.closest('div');
                    parentDiv.style.opacity = e.target.checked ? '0.4' : '1';
                    updateUploadIconVisibility();
                }
            });

            // Enhanced Office Hours Management
            const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

            // Initialize with existing office hours data
            let officeHoursData = {};

            // Populate existing data from the room model
            @php
                $existingOfficeHours = [];
                foreach ($daysOfWeek as $day) {
                    $ranges = $room->officeHours->where('day', $day)->values();
                    if ($ranges->isNotEmpty()) {
                        $existingOfficeHours[$day] = $ranges
                            ->map(function ($range) {
                                return [
                                    'start' => $range->start_time,
                                    'end' => $range->end_time,
                                ];
                            })
                            ->toArray();
                    }
                }
            @endphp

            officeHoursData = @json($existingOfficeHours);

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
            });

            // Helper functions
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

            // Convert 24-hour time to 12-hour format
            function formatTime12Hour(time24) {
                const [hours, minutes] = time24.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
                return `${hour12}:${minutes} ${ampm}`;
            }

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
                    li.className = "mb-3 p-3 bg-white rounded border";

                    const daysText = formatDaysGroup(group.days);
                    let timeText;

                    if (rangeKey === "closed") {
                        timeText = "Closed";
                    } else {
                        timeText = group.ranges.map(r =>
                            `${formatTime12Hour(r.start)} - ${formatTime12Hour(r.end)}`).join(", ");
                    }

                    li.innerHTML = `
                    <div class="font-medium text-gray-800">${daysText}</div>
                    <div class="text-sm text-gray-600 mt-1">${timeText}</div>
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

            // Initial render to show existing office hours
            renderOfficeHours();
        });
    </script>
@endpush

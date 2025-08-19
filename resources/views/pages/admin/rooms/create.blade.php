@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10 border-2 border-primary p-6 rounded-lg shadow-2xl">
        <h2 class="text-2xl text-center font-bold mb-6"><span class="text-primary">Add</span> New Office</h2>

        <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data" data-upload>
            @csrf

            <div class="mb-4">
                <label class="block">Office Name</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block">Description</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-2">Cover Image (optional)</label>

                <label for="image_path" id="uploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary hover:bg-gray-50 transition-colors overflow-hidden relative">
                    <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="uploadText" class="text-gray-500">Click to upload image</span>
                    <input type="file" name="image_path" id="image_path" class="hidden" accept="image/*" />
                    <img id="previewImage" class="absolute inset-0 object-cover w-full h-full hidden" alt="Image preview" />
                </label>
            </div>

            {{-- Carousel Images --}}
            <div class="mb-4 max-w-xl mx-auto">
                <label class="block mb-2">Carousel Images (optional)</label>

                <label for="carousel_images" id="carouselUploadBox"
                    class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary hover:bg-gray-50 transition-colors p-4 overflow-auto relative">

                    <svg id="carouselUploadIcon" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="carouselUploadText" class="text-gray-500 mb-4">Click to upload images (max 50)</span>

                    <input type="file" name="carousel_images[]" id="carousel_images" class="hidden" accept="image/*"
                        multiple />

                    {{-- Preview Container --}}
                    <div id="carouselPreviewContainer" class="flex flex-wrap gap-3 w-full justify-start"></div>
                </label>
            </div>


            <div class="mb-4 max-w-xl mx-auto">
                <label class="block mb-2">Short Video (optional)</label>

                <div id="videoDropZone"
                    class="relative w-full min-h-[150px] border-2 border-dashed border-gray-300 rounded flex flex-col items-center justify-center text-gray-500 cursor-pointer hover:border-primary hover:bg-gray-50 transition-colors p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 6v-6m0 0l-4.553 2.276A1 1 0 014 13.382V6.618a1 1 0 011.447-.894L9 8" />
                    </svg>
                    <p class="mb-2">Drag & drop a video file here or click to select</p>
                    <p class="text-xs text-gray-400">(mp4, avi, mpeg | max 50MB)</p>

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

            <div class="mb-4 max-w-sm">
                <label class="block mb-1">Office Hours</label>

                <div class="flex flex-wrap gap-4 mb-4">
                    @php
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        // Get previously selected days, fallback empty array
                        $selectedDays = old(
                            'office_days',
                            isset($room) && $room->office_days ? explode(',', $room->office_days) : [],
                        );
                    @endphp

                    @foreach ($days as $day)
                        <label class="inline-flex items-center cursor-pointer select-none gap-3">
                            <input type="checkbox" name="office_days[]" value="{{ $day }}" class="peer sr-only"
                                {{ in_array($day, $selectedDays) ? 'checked' : '' }} />

                            <div
                                class="w-6 h-6 border-2 rounded-md
           bg-white border-gray-400
           peer-checked:bg-primary
           peer-checked:border-blue-600
           peer-focus:ring-4 peer-focus:ring-blue-400
           transition-colors duration-300 ease-in-out
           relative flex items-center justify-center">
                                <!-- Checkmark -->
                                <svg class="w-5 h-5 text-white opacity-0 scale-75 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-300 ease-in-out"
                                    fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <span
                                class="peer-focus:text-blue-600 peer-checked:text-blue-600 transition-colors duration-200">
                                {{ $day }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Time inputs --}}
                <div class="flex gap-2 items-center">
                    <input type="time" name="office_hours_start" class="flex-1 border p-2 rounded"
                        placeholder="Start time" />
                    <span class="self-center">to</span>
                    <input type="time" name="office_hours_end" class="flex-1 border p-2 rounded"
                        placeholder="End time" />
                </div>
            </div>

            <div>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                    Save Room
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cover image upload preview
            const coverInput = document.getElementById('image_path');
            const coverPreview = document.getElementById('previewImage');
            const coverUploadIcon = document.getElementById('uploadIcon');
            const coverUploadText = document.getElementById('uploadText');

            coverInput.addEventListener('change', () => {
                if (coverInput.files && coverInput.files[0]) {
                    const reader = new FileReader();

                    reader.onload = e => {
                        coverPreview.src = e.target.result;
                        coverPreview.classList.remove('hidden');
                        coverUploadIcon.style.display = 'none';
                        coverUploadText.style.display = 'none';
                    };

                    reader.readAsDataURL(coverInput.files[0]);
                }
                // No else — keep existing preview if no file selected
            });

            const carouselInput = document.getElementById('carousel_images');
            const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
            const carouselUploadIcon = document.getElementById('carouselUploadIcon');
            const carouselUploadText = document.getElementById('carouselUploadText');

            let selectedFiles = [];

            function updateUploadIconVisibility() {
                carouselUploadIcon.style.display = selectedFiles.length > 0 ? 'none' : '';
                carouselUploadText.style.display = selectedFiles.length > 0 ? 'none' : '';
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
        });
    </script>
@endsection

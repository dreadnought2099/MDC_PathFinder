@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">

        <h2 class="text-2xl font-bold mb-6 text-center"><span class="text-primary">Edit {{ $room->name }}</span></h2>

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
                        <input type="checkbox" name="remove_image_path" value="1" id="remove_image_path" class="form-checkbox" />
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
                        <input type="checkbox" name="remove_video_path" value="1" id="remove_video_path" class="form-checkbox" />
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

            @php
                $officeDays = old('office_days', $room->office_days ? explode(',', $room->office_days) : []);
            @endphp

            <div>
                <label class="block font-semibold mb-1">Office Days</label>
                <div class="flex flex-wrap gap-4">
                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                        @php $inputId = "office_days_{$day}" @endphp
                        <label for="{{ $inputId }}" class="inline-flex items-center space-x-2">
                            <input type="checkbox" id="{{ $inputId }}" name="office_days[]"
                                value="{{ $day }}" {{ in_array($day, $officeDays) ? 'checked' : '' }}
                                class="form-checkbox" />
                            <span>{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="office_hours_start" class="block font-semibold mb-1">Office Hours Start</label>
                <input type="time" name="office_hours_start" id="office_hours_start"
                    value="{{ old('office_hours_start', $room->office_hours_start ? date('H:i', strtotime($room->office_hours_start)) : '') }}"
                    class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="office_hours_end" class="block font-semibold mb-1">Office Hours End</label>
                <input type="time" name="office_hours_end" id="office_hours_end"
                    value="{{ old('office_hours_end', $room->office_hours_end ? date('H:i', strtotime($room->office_hours_end)) : '') }}"
                    class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
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
                    <span id="carouselUploadText" class="text-gray-500 mb-4">Click to upload images (max 10)</span>

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
                                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"
                                                    class="hidden" />
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
                    class="bg-primary text-white px-4 py-2 rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                    Update Room
                </button>
            </div>
        </form>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carouselInput = document.getElementById('carousel_images');
        const carouselPreviewContainer = document.getElementById('carouselPreviewContainer');
        const carouselUploadIcon = document.getElementById('carouselUploadIcon');
        const carouselUploadText = document.getElementById('carouselUploadText');

        function updateUploadIconVisibility() {
            // Count existing images NOT marked for removal
            const visiblePreviews = [...carouselPreviewContainer.children].filter(div => {
                const checkbox = div.querySelector('input[type="checkbox"]');
                return !checkbox || !checkbox.checked;
            });
            carouselUploadIcon.style.display = visiblePreviews.length > 0 ? 'none' : 'block';
            carouselUploadText.style.display = visiblePreviews.length > 0 ? 'none' : 'block';
        }

        updateUploadIconVisibility();

        carouselInput.addEventListener('change', () => {
            // Remove previews of newly added files only (no checkbox means new)
            [...carouselPreviewContainer.children].forEach(div => {
                if (!div.querySelector('input[type="checkbox"]')) {
                    div.remove();
                }
            });

            // Get selected files from input
            const newFiles = Array.from(carouselInput.files);

            // Count existing images NOT marked for removal
            const existingCount = [...carouselPreviewContainer.children].filter(div => {
                const checkbox = div.querySelector('input[type="checkbox"]');
                return checkbox && !checkbox.checked;
            }).length;

            // Enforce max total images limit
            if (existingCount + newFiles.length > 50) {
                alert('You can upload max 50 images in total.');
                return;
            }

            // Show previews for new files
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

        // Toggle opacity on old images when their checkbox is toggled
        carouselPreviewContainer.addEventListener('change', e => {
            if (e.target.matches('input[type="checkbox"]')) {
                const parentDiv = e.target.closest('div');
                parentDiv.style.opacity = e.target.checked ? '0.4' : '1';
                updateUploadIconVisibility();
            }
        });
    });
</script>
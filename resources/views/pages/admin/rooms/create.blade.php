@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-12 dark:text-gray-300"><span class="text-primary">Add</span> New Office</h2>

        <x-upload-progress-modal>
            <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data" id="room-form">
                @csrf

                @php
                    $inputClasses =
                        'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';
                    $labelClasses =
                        'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
                @endphp

                <!-- Office Name and Room Type Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="relative">
                        <input type="text" name="name" id="name" placeholder="Office Name"
                            class="{{ $inputClasses }}" value="{{ old('name') }}" required>
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
                            <option value="" disabled selected hidden></option>
                            <option value="regular" {{ old('room_type') === 'regular' ? 'selected' : '' }}>
                                Regular Office
                            </option>
                            <option value="entrance_point" {{ old('room_type') === 'entrance_point' ? 'selected' : '' }}>
                                Entrance Point
                            </option>
                        </select>
                        <label class="{{ $labelClasses }}">Room Type</label>
                        @error('room_type')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description (Full Width) -->
                <div class="relative mb-6">
                    <textarea name="description" placeholder="Description" class="{{ $inputClasses }}" rows="3">{{ old('description') }}</textarea>
                    <label class="{{ $labelClasses }}">Description</label>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Media Uploads Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <!-- Cover Image -->
                    <div class="conditional-field" id="cover-image-section">
                        <label class="block mb-2 dark:text-gray-300">Cover Image (optional, max 10MB)</label>
                        <div id="uploadBox"
                            class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800
                        transition-colors overflow-hidden relative">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Image Icon" class="w-8 h-8" onerror="this.style.display='none'">
                            <span id="uploadText" class="text-gray-500 dark:text-gray-300 text-sm text-center px-2">
                                Click to upload cover image
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
                        <div id="videoDropZone"
                            class="flex flex-col items-center justify-center w-full h-40 
                        border-2 border-dashed border-gray-300 dark:border-gray-600 
                        rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                        dark:hover:border-primary dark:hover:bg-gray-800 transition-colors overflow-hidden relative">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/video.png"
                                alt="Video Icon" id="videoIcon" class="w-9 h-9" onerror="this.style.display='none'">
                            <span id="videoUploadText" class="text-gray-500 dark:text-gray-300 text-sm text-center px-2">
                                Drag & drop or click to select
                            </span>
                            <p class="text-xs text-gray-400 dark:text-gray-300">(mp4, avi, mpeg)</p>

                            <!-- Video Thumbnail Preview -->
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
                <div class="mb-8 conditional-field" id="carousel-images-section">
                    <label class="block mb-2 dark:text-gray-300">Carousel Images (optional, max 15 images, 10MB
                        each)</label>
                    <div id="carouselUploadBox"
                        class="flex flex-col items-center justify-center w-full min-h-40 p-4
                    border-2 border-dashed border-gray-300 dark:border-gray-600 
                    rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                    dark:hover:border-primary dark:hover:bg-gray-800 transition-colors relative">
                        <div id="carouselPlaceholder" class="text-center">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Image Icon" class="w-8 h-8 mx-auto" onerror="this.style.display='none'">
                            <span id="carouselUploadText" class="text-gray-500 dark:text-gray-300 block mt-2">
                                Click to upload images
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
                    @error('carousel_images.*')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Office Hours -->
                <div class="mb-6 conditional-field" id="office-hours-section">
                    <label class="block font-semibold mb-3 text-lg dark:text-gray-300">Office Hours</label>

                    <!-- Setup Section -->
                    <div class="mb-4 p-5 border-2 border-primary rounded-lg dark:bg-gray-800 space-y-4">

                        <!-- Days Selection -->
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Select Days</label>

                            <div class="flex gap-2 mb-3 flex-wrap">
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-white hover:text-gray-600 dark:bg-gray-600 dark:hover:bg-gray-800 dark:hover:text-white text-white border border-gray-600 transition-colors shadow-cancel-hover cursor-pointer"
                                    data-days="Mon,Tue,Wed,Thu,Fri">Weekdays</button>
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-white hover:text-gray-600 dark:bg-gray-600 dark:hover:bg-gray-800 dark:hover:text-white text-white border border-gray-600 transition-colors shadow-cancel-hover cursor-pointer"
                                    data-days="Sat,Sun">Weekends</button>
                                <button type="button"
                                    class="quick-select px-3 py-1.5 rounded text-sm bg-gray-500 hover:bg-white hover:text-gray-600 dark:bg-gray-600 dark:hover:bg-gray-800 dark:hover:text-white text-white border border-gray-600 transition-colors shadow-cancel-hover cursor-pointer"
                                    data-days="Mon,Tue,Wed,Thu,Fri,Sat,Sun">All Days</button>
                                <button type="button"
                                    class="clear-select px-3 py-1.5 rounded text-sm bg-secondary hover:bg-white hover:text-secondary border border-secondary dark:bg-gray-600 dark:hover:bg-gray-800 text-white transition-colors cursor-pointer shadow-secondary-hover">
                                    Clear All</button>
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
                        Save Office
                    </button>
                </div>
            </form>
        </x-upload-progress-modal>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/room-form.js')
@endpush

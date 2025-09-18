@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div
            class="w-full flex items-center p-4 dark:border-b border-b-primary dark:border-b-primary bg-white dark:bg-gray-900 sticky top-0 z-50">

            <div class="w-48 flex items-center">
                <!-- Left: Back button -->
                <a href="{{ route('paths.select') }}"
                    class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                    <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="font-medium">Back to Selection</span>
                </a>
            </div>

            <div class="flex-1"></div>

            <!-- Right controls -->
            <div class="w-48 flex items-center">
                <div class="flex-1 flex justify-end">
                    <x-about-page />
                </div>
                <div class="flex-1 flex justify-end">
                    <x-dark-mode-toggle />
                </div>
            </div>
        </div>

        <!-- Floating QR -->
        <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
            title="Scan office to know more" />

        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-5xl bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-md p-6">
                <h2 class="text-2xl mb-6 text-center text-primary">
                    <div class="flex items-center justify-center gap-3">
                        <span>{{ $fromRoom->name ?? ($fromRoom->room_name ?? 'Unknown Room') }}</span>
                        <img src="{{ asset('icons/arrow.png') }}" alt="Arrow" class="w-6 h-6">
                        <span>{{ $toRoom->name ?? ($toRoom->room_name ?? 'Unknown Room') }}</span>
                    </div>
                </h2>

                @if ($paths->isEmpty())
                    <p class="text-center text-gray-600 dark:text-gray-300">
                        No navigation path available between these rooms.
                    </p>
                @else
                    @foreach ($paths as $path)
                        <!-- Path Images Card -->
                        <div
                            class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 mb-8 text-center border-2 border-primary">
                            {{-- CHANGE: Updated title to use dynamic path name instead of static "Path Images (count)" --}}
                            <h2 class="text-xl mb-4 dark:text-gray-300">
                                <i class="fas fa-images mr-2"></i> <span
                                    class="path-title">{{ $path->name ?? 'Path ' . $loop->iteration }}</span>
                            </h2>

                            @if ($path->images->count() > 0)
                                <div class="viewer relative w-full h-[500px] bg-black rounded-md overflow-hidden"
                                    data-index="{{ $loop->index }}">
                                    @php
                                        $images = $path->images->sortBy('image_order')->pluck('image_file')->toArray();
                                    @endphp

                                    @if (count($images) > 0)
                                        <!-- Double layer technique -->
                                        <img src="{{ asset('storage/' . $images[0]) }}" class="photo-layer active"
                                            alt="Path Image">
                                        <img src="" class="photo-layer" alt="Next Image">

                                        {{-- CHANGE: Repositioned navigation buttons to bottom center with up/down arrows --}}
                                        <!-- Navigation buttons (centered at bottom) -->
                                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-4 z-10">
                                            <button
                                                class="nav-btn prev-btn bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md">
                                                ↓
                                            </button>
                                            <button
                                                class="nav-btn next-btn bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md">
                                                ↑
                                            </button>
                                        </div>

                                        {{-- CHANGE: Added image counter in top-right corner --}}
                                        <!-- Image counter -->
                                        <div
                                            class="absolute top-4 right-4 bg-black bg-opacity-75 text-white px-3 py-1 rounded-md text-sm z-10">
                                            <span class="image-counter">1 / {{ count($images) }}</span>
                                        </div>
                                    @else
                                        <p class="text-gray-400 text-center py-10">No Images Found</p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-10 text-gray-400">
                                    <i class="fas fa-image fa-3x mb-4"></i>
                                    <h4 class="text-lg">No Images Found</h4>
                                    <p class="text-sm">This path doesn't have any images yet.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <div class="mt-8 flex justify-center">
                    <a href="{{ route('paths.select') }}"
                        class="px-6 py-2 rounded-md bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary dark:hover:bg-gray-800 shadow-primary-light transition-all cursor-pointer">
                        Start New Navigation
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const allImageSets = @json($paths->map(fn($p) => $p->images->sortBy('image_order')->pluck('image_file')->toArray()));

            document.querySelectorAll('.viewer').forEach((viewer, index) => {
                const imageSet = allImageSets[index] || [];
                let currentIndex = 0;

                const [imgA, imgB] = viewer.querySelectorAll('.photo-layer');
                let showingA = true;

                // Preload images for smoother experience
                imageSet.forEach(src => {
                    const pre = new Image();
                    pre.src = '/storage/' + src;
                });

                // CHANGE: Updated path title to show dynamic path number instead of "Path X - Image Y"
                const updatePathTitle = () => {
                    const pathTitle = viewer.closest('.bg-white, .dark\\:bg-gray-800').querySelector(
                        '.path-title');
                    if (pathTitle && imageSet.length > 0) {
                        const baseName = pathTitle.textContent.split(' - ')[0]; // Get base path name
                        // CHANGE: Replace the path number with current image number (Path 1 -> Path 2, etc.)
                        pathTitle.textContent = `${baseName.replace(/\d+/, currentIndex + 1)}`;
                    }
                };

                // CHANGE: Added function to update image counter dynamically
                const updateImageCounter = () => {
                    const counter = viewer.querySelector('.image-counter');
                    if (counter) {
                        counter.textContent = `${currentIndex + 1} / ${imageSet.length}`;
                    }
                };

                const showImage = (newIndex, direction = 'forward') => {
                    if (!imageSet.length) return;

                    const nextImg = showingA ? imgB : imgA;
                    const currImg = showingA ? imgA : imgB;

                    nextImg.src = '/storage/' + imageSet[newIndex];
                    nextImg.className =
                        `photo-layer ${direction === 'forward' ? 'forward-new' : 'backward-new'}`;

                    requestAnimationFrame(() => {
                        nextImg.classList.add('active');
                        currImg.classList.remove('active');
                        currImg.classList.add(direction === 'forward' ? 'forward-old' :
                            'backward-old');
                    });

                    showingA = !showingA;
                    // CHANGE: Added calls to update both counter and title on navigation
                    updateImageCounter();
                    updatePathTitle();
                };

                // CHANGE: Initialize the title on page load
                updatePathTitle();

                // Button navigation
                viewer.querySelector('.prev-btn')?.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        showImage(currentIndex, 'forward');
                    }
                });

                viewer.querySelector('.next-btn')?.addEventListener('click', () => {
                    if (currentIndex < imageSet.length - 1) {
                        currentIndex++;
                        showImage(currentIndex, 'backward');
                    }
                });
            });
        });
    </script>
@endpush

<style>
    /* Image layers */
    .photo-layer {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        z-index: 1;
        transition: transform 0.6s ease, opacity 0.6s ease;
        will-change: transform, opacity;
    }

    .photo-layer.active {
        opacity: 1;
        z-index: 2;
    }

    /* Forward transition */
    .forward-new {
        transform: scale(1.1);
        opacity: 0;
    }

    .forward-new.active {
        transform: scale(1);
        opacity: 1;
    }

    .forward-old {
        transform: scale(1);
        opacity: 1;
    }

    .forward-old:not(.active) {
        transform: scale(0.9);
        opacity: 0;
    }

    /* Backward transition */
    .backward-new {
        transform: scale(0.9);
        opacity: 0;
    }

    .backward-new.active {
        transform: scale(1);
        opacity: 1;
    }

    .backward-old {
        transform: scale(1);
        opacity: 1;
    }

    .backward-old:not(.active) {
        transform: scale(1.1);
        opacity: 0;
    }

    /* CHANGE: Updated button styles for better visibility and centering */
    /* Buttons style */
    .nav-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: rgba(59, 130, 246, 0.9);
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: transform 0.2s ease, background-color 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .nav-btn:hover {
        transform: scale(1.1);
        background-color: rgba(59, 130, 246, 1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    }

    .nav-btn:active {
        transform: scale(0.95);
    }

    /* Optional: Hide buttons when there's only one image */
    .viewer[data-single-image] .nav-btn {
        display: none;
    }
</style>

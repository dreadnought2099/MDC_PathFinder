@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div
            class="w-full flex items-center p-2 sm:p-4 dark:border-b border-b-primary dark:border-b-primary bg-white dark:bg-gray-900 sticky top-0 z-50">
            <div class="w-full sm:w-48 flex items-center justify-start">
                <!-- Left: Back button -->
                <a href="{{ route('paths.select') }}"
                    class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300 text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-6 sm:w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="font-medium hidden sm:inline">Back to Selection</span>
                    <span class="font-medium sm:hidden">Back</span>
                </a>
            </div>

            <div class="flex-1 hidden sm:block"></div>

            <!-- Right controls -->
            <div class="w-full sm:w-48 flex items-center justify-end gap-2">
                <x-about-page />
                <x-dark-mode-toggle />
            </div>
        </div>

        <!-- Floating QR -->
        <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
            title="Scan office to know more" />

        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-2 sm:px-4 lg:px-8 py-4 sm:py-8">
            <div
                class="w-full max-w-sm sm:max-w-2xl lg:max-w-5xl bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-md p-3 sm:p-6">
                <h2 class="text-lg sm:text-2xl mb-4 sm:mb-6 text-center text-primary">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-3">
                        <span class="text-center">{{ $fromRoom->name ?? ($fromRoom->room_name ?? 'Unknown Room') }}</span>
                        <img src="{{ asset('icons/arrow.png') }}" alt="Arrow" class="w-4 h-4 sm:w-6 sm:h-6">
                        <span class="text-center">{{ $toRoom->name ?? ($toRoom->room_name ?? 'Unknown Room') }}</span>
                    </div>
                </h2>

                @if ($paths->isEmpty())
                    <p class="text-center text-gray-600 dark:text-gray-300 text-sm sm:text-base px-4">
                        No navigation path available between these rooms.
                    </p>
                @else
                    @foreach ($paths as $path)
                        <!-- Path Images Card -->
                        <div
                            class="bg-white dark:bg-gray-800 shadow rounded-lg p-3 sm:p-5 mb-4 sm:mb-8 text-center border-2 border-primary">
                            <h2 class="text-base sm:text-xl mb-3 sm:mb-4 dark:text-gray-300">
                                <i class="fas fa-images mr-1 sm:mr-2 text-sm sm:text-base"></i>
                                <span class="path-title">{{ $path->name ?? 'Path ' . $loop->iteration }}</span>
                            </h2>

                            @if ($path->images->count() > 0)
                                <!-- Image viewer with balanced responsive height -->
                                <div class="viewer relative w-full bg-black rounded-md overflow-hidden"
                                    style="height: clamp(240px, 30vw, 400px);" data-index="{{ $loop->index }}">
                                    @php
                                        $images = $path->images->sortBy('image_order')->pluck('image_file')->toArray();
                                    @endphp

                                    @if (count($images) > 0)
                                        <!-- Double layer technique -->
                                        <img src="{{ asset('storage/' . $images[0]) }}" class="photo-layer active"
                                            alt="Path Image">
                                        <img src="" class="photo-layer" alt="Next Image">

                                        <!-- Navigation buttons (centered at bottom) -->
                                        <div
                                            class="absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 flex gap-2 sm:gap-4 z-10">
                                            <button
                                                class="nav-btn prev-btn bg-primary hover:bg-primary/90 text-white w-8 h-8 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center shadow-md hover:shadow-lg text-sm sm:text-xl lg:text-2xl border-2 border-white/20 hover:scale-110 active:scale-95 transition-all duration-200 select-none">
                                                ↓
                                            </button>
                                            <button
                                                class="nav-btn next-btn bg-primary hover:bg-primary/90 text-white w-8 h-8 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center shadow-md hover:shadow-lg text-sm sm:text-xl lg:text-2xl border-2 border-white/20 hover:scale-110 active:scale-95 transition-all duration-200 select-none">
                                                ↑
                                            </button>
                                        </div>

                                        <!-- Image counter -->
                                        <div
                                            class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-black/75 text-white px-2 py-1 sm:px-3 sm:py-1 lg:px-4 lg:py-2 rounded-md text-xs sm:text-sm lg:text-base z-10">
                                            <span class="image-counter">1 / {{ count($images) }}</span>
                                        </div>
                                    @else
                                        <p class="text-gray-400 text-center py-8 sm:py-10 lg:py-12 text-sm sm:text-base">No
                                            Images Found</p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6 sm:py-10 lg:py-12 text-gray-400">
                                    <i class="fas fa-image text-2xl sm:text-3xl lg:text-4xl mb-2 sm:mb-4"></i>
                                    <h4 class="text-base sm:text-lg lg:text-xl">No Images Found</h4>
                                    <p class="text-xs sm:text-sm lg:text-base">This path doesn't have any images yet.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <!-- Start new navigation button -->
                <div class="mt-4 sm:mt-8 flex justify-center">
                    <a href="{{ route('paths.select') }}"
                        class="px-4 py-2 sm:px-6 sm:py-3 lg:px-8 lg:py-4 text-sm sm:text-base lg:text-lg rounded-md bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary dark:hover:bg-gray-800 shadow-primary-light transition-all cursor-pointer">
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

                const updatePathTitle = () => {
                    const pathTitle = viewer.closest('.bg-white, .dark\\:bg-gray-800').querySelector(
                        '.path-title');
                    if (pathTitle && imageSet.length > 0) {
                        const baseName = pathTitle.textContent.split(' - ')[0];
                        pathTitle.textContent = `${baseName.replace(/\d+/, currentIndex + 1)}`;
                    }
                };

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
                    updateImageCounter();
                    updatePathTitle();
                };

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

                // Touch/swipe support for mobile
                let touchStartX = 0;
                let touchEndX = 0;

                viewer.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });

                viewer.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    const swipeThreshold = 50;

                    if (Math.abs(touchStartX - touchEndX) > swipeThreshold) {
                        if (touchStartX > touchEndX && currentIndex < imageSet.length - 1) {
                            currentIndex++;
                            showImage(currentIndex, 'backward');
                        } else if (touchStartX < touchEndX && currentIndex > 0) {
                            currentIndex--;
                            showImage(currentIndex, 'forward');
                        }
                    }
                });
            });
        });
    </script>
@endpush

<style>
    /* Image layers - balanced sizing for good visibility */
    .photo-layer {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        opacity: 0;
        z-index: 1;
        transition: transform 0.6s ease, opacity 0.6s ease;
        will-change: transform, opacity;
        background: #000;
    }

    .photo-layer.active {
        opacity: 1;
        z-index: 2;
    }

    /* Balanced container - not too big, not too small */
    .viewer {
        /* Balanced: min(240px), preferred(30% of viewport width), max(400px) */
        height: clamp(240px, 30vw, 400px);
        max-height: 50vh;
        /* Never exceed 50% of screen height */
    }

    /* Reasonable mobile sizing */
    @media (max-width: 640px) {
        .viewer {
            height: clamp(200px, 35vw, 320px);
            max-height: 45vh;
        }
    }

    @media (max-width: 480px) {
        .viewer {
            height: clamp(180px, 40vw, 280px);
            max-height: 40vh;
        }
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

    /* Custom breakpoint removed - using standard Tailwind breakpoints only */
</style>
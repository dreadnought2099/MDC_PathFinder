@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col">
        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-2 sm:px-4 lg:px-8 py-4 sm:py-8">
            <div
                class="w-full max-w-sm sm:max-w-2xl lg:max-w-5xl bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-md p-3 sm:p-6">
                <h2 class="text-lg sm:text-2xl mb-4 sm:mb-6 text-center text-primary">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-3">
                        <span class="text-center">{{ $fromRoom->name ?? ($fromRoom->room_name ?? 'Unknown Room') }}</span>
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/arrow.png"
                            alt="Arrow" class="w-4 h-4 sm:w-6 sm:h-6">
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
                                <span class="path-title">{{ $path->name ?? 'Path ' . $loop->iteration }}</span>
                            </h2>

                            @if ($path->images->count() > 0)
                                <!-- Image viewer with balanced responsive height -->
                                <div class="viewer relative w-full bg-black rounded-md overflow-hidden"
                                    style="height: clamp(240px, 30vw, 400px);" data-index="{{ $loop->index }}"
                                    data-images="{{ json_encode($path->images->sortBy('image_order')->pluck('image_file')->toArray()) }}">

                                    <!-- Double layer technique -->
                                    <img src="{{ asset('storage/' . $path->images->sortBy('image_order')->first()->image_file) }}"
                                        class="photo-layer active" alt="Path Image" loading="lazy">
                                    <img src="" class="photo-layer" alt="Next Image" loading="lazy">

                                    <!-- Fullscreen button -->
                                    <button
                                        class="fullscreen-btn bg-black/75 text-white w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center border-2 border-white/20 hover:bg-black/90 hover:scale-110 transition-all duration-200 absolute top-2 sm:top-4 left-2 sm:left-4 z-10"
                                        title="Enter Fullscreen">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </button>

                                    <!-- Navigation buttons (centered at bottom) -->
                                    <div
                                        class="nav-container absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 flex gap-2 sm:gap-4 z-10">
                                        <button
                                            class="nav-btn prev-btn bg-primary hover:bg-primary/90 text-white w-8 h-8 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center shadow-md hover:shadow-lg text-sm sm:text-xl lg:text-2xl border-2 border-white/20 hover:scale-110 active:scale-95 transition-all duration-200 select-none disabled:opacity-50 disabled:cursor-not-allowed"
                                            disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="currentColor"
                                                class="icon icon-tabler icons-tabler-filled icon-tabler-caret-down">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M18 9c.852 0 1.297 .986 .783 1.623l-.076 .084l-6 6a1 1 0 0 1 -1.32 .083l-.094 -.083l-6 -6l-.083 -.094l-.054 -.077l-.054 -.096l-.017 -.036l-.027 -.067l-.032 -.108l-.01 -.053l-.01 -.06l-.004 -.057v-.118l.005 -.058l.009 -.06l.01 -.052l.032 -.108l.027 -.067l.07 -.132l.065 -.09l.073 -.081l.094 -.083l.077 -.054l.096 -.054l.036 -.017l.067 -.027l.108 -.032l.053 -.01l.06 -.01l.057 -.004l12.059 -.002z" />
                                            </svg>
                                        </button>
                                        <button
                                            class="nav-btn next-btn bg-primary hover:bg-primary/90 text-white w-8 h-8 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center shadow-md hover:shadow-lg text-sm sm:text-xl lg:text-2xl border-2 border-white/20 hover:scale-110 active:scale-95 transition-all duration-200 select-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="currentColor"
                                                class="icon icon-tabler icons-tabler-filled icon-tabler-caret-up">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M11.293 7.293a1 1 0 0 1 1.32 -.083l.094 .083l6 6l.083 .094l.054 .077l.054 .096l.017 .036l.027 .067l.032 .108l.01 .053l.01 .06l.004 .057l.002 .059l-.002 .059l-.005 .058l-.009 .06l-.01 .052l-.032 .108l-.027 .067l-.07 .132l-.065 .09l-.073 .081l-.094 .083l-.077 .054l-.096 .054l-.036 .017l-.067 .027l-.108 .032l-.053 .01l-.06 .01l-.057 .004l-.059 .002h-12c-.852 0 -1.297 -.986 -.783 -1.623l.076 -.084l6 -6z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Image counter -->
                                    <div
                                        class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-black/75 text-white px-2 py-1 sm:px-3 sm:py-1 lg:px-4 lg:py-2 rounded-md text-xs sm:text-sm lg:text-base z-10">
                                        <span class="image-counter">1 / {{ $path->images->count() }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-6 sm:py-10 lg:py-12 text-gray-400">
                                    <h4 class="text-base sm:text-lg lg:text-xl">No Images Found</h4>
                                    <p class="text-xs sm:text-sm lg:text-base">This path doesn't have any images yet.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <!-- Start new navigation button -->
                <div class="mt-4 sm:mt-8 flex justify-center">
                    <a href="{{ route('paths.select', ['from' => $toRoom->id]) }}"
                        class="px-4 py-2 sm:px-6 sm:py-3 lg:px-8 lg:py-4 text-sm sm:text-base lg:text-lg rounded-md bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary dark:hover:bg-gray-800 shadow-primary-hover transition-all cursor-pointer">
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
            // Generate a unique storage key based on the current path
            const storageKey = `pathNavigation_${btoa('{{ $fromRoom->id }}-{{ $toRoom->id }}')}`;

            // Function to save current state
            const saveNavigationState = (pathIndex, imageIndex) => {
                const state = {
                    pathIndex,
                    imageIndex,
                    timestamp: Date.now()
                };
                localStorage.setItem(storageKey, JSON.stringify(state));
            };

            // Function to load saved state (returns null if expired or invalid)
            const loadNavigationState = () => {
                try {
                    const saved = localStorage.getItem(storageKey);
                    if (!saved) return null;

                    const state = JSON.parse(saved);
                    const oneHour = 60 * 60 * 1000; // 1 hour expiration

                    // Check if state is expired (older than 1 hour)
                    if (Date.now() - state.timestamp > oneHour) {
                        localStorage.removeItem(storageKey);
                        return null;
                    }

                    return state;
                } catch (error) {
                    console.error('Error loading navigation state:', error);
                    localStorage.removeItem(storageKey);
                    return null;
                }
            };

            // Load saved state once at the beginning
            const savedState = loadNavigationState();
            let hasRestoredState = false;

            document.querySelectorAll('.viewer').forEach((viewer, pathIndex) => {
                const imageSet = JSON.parse(viewer.dataset.images || '[]');
                let currentIndex = 0;
                const preloadedImages = new Set();

                const [imgA, imgB] = viewer.querySelectorAll('.photo-layer');
                const prevBtn = viewer.querySelector('.prev-btn');
                const nextBtn = viewer.querySelector('.next-btn');
                const fullscreenBtn = viewer.querySelector('.fullscreen-btn');
                const imageCounter = viewer.querySelector('.image-counter');
                let showingA = true;
                let isFullscreen = false;

                // Check if we have a saved state for this specific path
                if (savedState && savedState.pathIndex === pathIndex) {
                    currentIndex = Math.min(savedState.imageIndex, imageSet.length - 1);
                    hasRestoredState = true;
                }

                const enterFullscreen = () => {
                    viewer.classList.add('fullscreen');
                    document.body.classList.add('viewer-fullscreen-active');
                    document.body.style.overflow = 'hidden';
                    isFullscreen = true;

                    // Update fullscreen button icon
                    fullscreenBtn.innerHTML = `
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    `;
                    fullscreenBtn.title = 'Exit Fullscreen';

                    // Force center the navigation container with more aggressive approach
                    const navContainer = viewer.querySelector('.nav-container');
                    if (navContainer) {
                        // Clear all existing classes
                        navContainer.className = 'nav-container';

                        // Apply inline styles with !important via cssText
                        navContainer.style.cssText = `
                            position: fixed !important;
                            bottom: 2rem !important;
                            left: 50% !important;
                            right: auto !important;
                            top: auto !important;
                            transform: translateX(-50%) !important;
                            -webkit-transform: translateX(-50%) !important;
                            z-index: 10001 !important;
                            display: flex !important;
                            flex-direction: row !important;
                            gap: 1rem !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            width: auto !important;
                            justify-content: center !important;
                            align-items: center !important;
                        `;
                    }
                };

                const exitFullscreen = () => {
                    viewer.classList.remove('fullscreen');
                    document.body.classList.remove('viewer-fullscreen-active');
                    document.body.style.overflow = '';
                    isFullscreen = false;

                    // Update fullscreen button icon
                    fullscreenBtn.innerHTML = `
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    `;
                    fullscreenBtn.title = 'Enter Fullscreen';

                    // Restore original navigation container styles
                    const navContainer = viewer.querySelector('.nav-container');
                    if (navContainer) {
                        // Restore Tailwind classes
                        navContainer.className =
                            'nav-container absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 flex gap-2 sm:gap-4 z-10';

                        // Clear inline styles
                        navContainer.style.cssText = '';
                    }
                };

                const toggleFullscreen = () => {
                    if (isFullscreen) {
                        exitFullscreen();
                    } else {
                        enterFullscreen();
                    }
                };

                // Fullscreen button event
                fullscreenBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleFullscreen();
                });

                // Exit fullscreen on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && isFullscreen) {
                        exitFullscreen();
                    }

                    // Keyboard navigation in fullscreen
                    if (isFullscreen) {
                        if (e.key === 'ArrowLeft' && currentIndex > 0) {
                            showImage(currentIndex - 1, 'forward');
                        } else if (e.key === 'ArrowRight' && currentIndex < imageSet.length - 1) {
                            showImage(currentIndex + 1, 'backward');
                        }
                    }
                });

                const initializeViewer = () => {
                    // Clear both images first
                    imgA.src = '';
                    imgB.src = '';
                    imgA.classList.remove('active', 'forward-new', 'forward-old', 'backward-new',
                        'backward-old');
                    imgB.classList.remove('active', 'forward-new', 'forward-old', 'backward-new',
                        'backward-old');

                    // Load the current image
                    if (imageSet.length > 0) {
                        const initialImage = imageSet[currentIndex];

                        if (hasRestoredState && currentIndex > 0) {
                            // For restored state, use imgA as active
                            imgA.src = '/storage/' + initialImage;
                            imgA.classList.add('active');
                            showingA = true;
                            preloadedImages.add(currentIndex);
                        } else {
                            // For fresh start, use the default first image
                            imgA.src = '/storage/' + initialImage;
                            imgA.classList.add('active');
                            showingA = true;
                            preloadedImages.add(0);
                        }

                        updateImageCounter();
                        updatePathTitle();
                        updateButtons();
                        preloadAdjacentImages();
                    }
                };

                const updateButtons = () => {
                    if (prevBtn) {
                        prevBtn.disabled = currentIndex === 0;
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentIndex === imageSet.length - 1;
                    }

                    // Save state whenever buttons are updated (on navigation)
                    saveNavigationState(pathIndex, currentIndex);
                };

                const updateImageCounter = () => {
                    if (imageCounter) {
                        imageCounter.textContent = `${currentIndex + 1} / ${imageSet.length}`;
                    }
                };

                const updatePathTitle = () => {
                    const pathTitle = viewer.closest('.bg-white, .dark\\:bg-gray-800').querySelector(
                        '.path-title');
                    if (pathTitle && imageSet.length > 0) {
                        const baseName = pathTitle.textContent.split(' - ')[0];
                        pathTitle.textContent = `${baseName.replace(/\d+/, currentIndex + 1)}`;
                    }
                };

                // Smart preloading - only preload adjacent images
                const preloadAdjacentImages = () => {
                    const indicesToPreload = [];

                    // Preload next image if exists
                    if (currentIndex < imageSet.length - 1) {
                        indicesToPreload.push(currentIndex + 1);
                    }

                    // Preload previous image if exists
                    if (currentIndex > 0) {
                        indicesToPreload.push(currentIndex - 1);
                    }

                    indicesToPreload.forEach(index => {
                        if (!preloadedImages.has(index)) {
                            const img = new Image();
                            img.src = '/storage/' + imageSet[index];
                            preloadedImages.add(index);
                        }
                    });
                };

                const showImage = (newIndex, direction = 'forward') => {
                    if (!imageSet.length) return;

                    const nextImg = showingA ? imgB : imgA;
                    const currImg = showingA ? imgA : imgB;

                    // Clear classes first
                    nextImg.classList.remove('active', 'forward-new', 'forward-old', 'backward-new',
                        'backward-old');
                    currImg.classList.remove('active', 'forward-new', 'forward-old', 'backward-new',
                        'backward-old');

                    // Load image if not already loaded
                    if (!preloadedImages.has(newIndex)) {
                        nextImg.src = '/storage/' + imageSet[newIndex];
                        preloadedImages.add(newIndex);
                    } else {
                        nextImg.src = '/storage/' + imageSet[newIndex];
                        nextImg.onload = null; // Clear any existing handlers
                        nextImg.decode().catch(() => {});
                    }

                    nextImg.className =
                        `photo-layer ${direction === 'forward' ? 'forward-new' : 'backward-new'}`;

                    // Use setTimeout to ensure DOM is ready
                    setTimeout(() => {
                        nextImg.classList.add('active');
                        currImg.classList.add(direction === 'forward' ? 'forward-old' :
                            'backward-old');
                    }, 10);

                    showingA = !showingA;
                    currentIndex = newIndex;

                    updateImageCounter();
                    updatePathTitle();
                    updateButtons();

                    // Preload adjacent images for next navigation
                    setTimeout(preloadAdjacentImages, 100);
                };

                // Initialize the viewer
                initializeViewer();

                // Button navigation
                prevBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentIndex > 0) {
                        showImage(currentIndex - 1, 'forward');
                    }
                });

                nextBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentIndex < imageSet.length - 1) {
                        showImage(currentIndex + 1, 'backward');
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
                            showImage(currentIndex + 1, 'backward');
                        } else if (touchStartX < touchEndX && currentIndex > 0) {
                            showImage(currentIndex - 1, 'forward');
                        }
                    }
                });

                // Keyboard navigation
                viewer.setAttribute('tabindex', '0');
                viewer.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft' && currentIndex > 0) {
                        showImage(currentIndex - 1, 'forward');
                    } else if (e.key === 'ArrowRight' && currentIndex < imageSet.length - 1) {
                        showImage(currentIndex + 1, 'backward');
                    }
                });

                // Save state when leaving the page
                window.addEventListener('beforeunload', () => {
                    saveNavigationState(pathIndex, currentIndex);
                });
            });
        });
    </script>
@endpush

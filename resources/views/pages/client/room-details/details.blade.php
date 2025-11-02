<div class="min-h-screen w-full p-4 sm:p-6 md:p-8 lg:p-10">
    <div
        class="w-full px-6 sm:px-10 md:px-16 lg:px-20 xl:px-24 py-8 sm:py-10 md:py-12 space-y-12 mx-auto max-w-7xl bg-white dark:bg-gray-900 rounded-2xl shadow-lg border-2 border-primary transition-colors duration-300">
        <!-- Cover Image -->
        <section
            class="relative w-full h-[400px] sm:h-[500px] md:h-[600px] rounded-2xl  border-2 border-primary mb-12 group transition-all duration-700 ease-out overflow-hidden">

            @php
                $hasImage = $room->image_path && Storage::exists('public/' . $room->image_path);
                $imageSrc = $hasImage ? Storage::url($room->image_path) : asset('images/pathfinder-bannerv2.png');
            @endphp

            <img src="{{ $room->image_path ? Storage::url($room->image_path) : asset('images/pathfinder-bannerv2.png') }}"
                alt="{{ $room->name }}"
                class="absolute inset-0 w-full h-full object-cover brightness-75 transition-transform duration-700 ease-out group-hover:scale-105 cursor-pointer"
                onclick="openModal('{{ $room->image_path ? Storage::url($room->image_path) : asset('images/pathfinder-bannerv2.png') }}')" />

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none">
            </div>

            <!-- Overlay Content -->
            <div
                class="absolute bottom-0 left-0 right-0 top-0 flex flex-col justify-end p-4 sm:p-8 md:p-12 text-white pointer-events-none">
                <div class="space-y-3 sm:space-y-4 max-w-3xl pointer-events-auto">
                    <h1
                        class="text-transparent bg-clip-text bg-gradient-to-tr from-blue-300 to-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold drop-shadow-2xl leading-tight">
                        {{ $room->name }}
                    </h1>

                    @if ($room->description)
                        <div class="relative max-w-3xl">
                            <div id="roomDescription"
                                class="font-sofia text-gray-200 text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl leading-relaxed drop-shadow transition-all duration-300 max-h-16 sm:max-h-20 md:max-h-24 lg:max-h-32 overflow-hidden pr-2 scrollbar-thin scrollbar-thumb-primary relative whitespace-pre-line space-y-3"
                                data-full-text="{{ $room->description }}">
                                {{ $room->description }}
                            </div>

                            <button id="toggleDescriptionBtn"
                                class="hidden mt-2 text-xs sm:text-sm md:text-base text-transparent bg-clip-text bg-gradient-to-tr from-blue-300 to-white font-semibold hover-underline focus:outline-none">
                                See more
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Video -->
        @if ($room->video_path)
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">Office Video
                </h2>
                <video controls class="w-full rounded-lg shadow-md border-2 border-primary max-h-[400px]">
                    <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif

        <!-- Gallery -->
        @if ($room->images && $room->images->count())
            <section x-data="showMoreHandler({ total: {{ $room->images->count() }}, lgLimit: 8, smLimit: 4 })" class="mt-8 w-full">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">
                    Gallery
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($room->images as $index => $image)
                        <a href="{{ asset('storage/' . $image->image_path) }}" class="glightbox"
                            data-gallery="room-{{ $room->id }}" data-title="Office Image {{ $index + 1 }}"
                            x-show="expanded || {{ $index }} < itemsToShow" x-transition>
                            <div
                                class="overflow-hidden rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-transform duration-300 cursor-pointer border-2 border-primary">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="Office Image {{ $index + 1 }}" class="w-full h-32 sm:h-40 object-cover" />
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Toggle Button -->
                <div class="text-center mt-5" x-show="shouldShowButton" x-transition x-cloak>
                    <button @click="toggle"
                        class="px-4 py-1.5 text-xs sm:text-sm font-medium rounded-full border border-primary 
                            text-primary hover:bg-primary hover:text-white transition-colors duration-200">
                        <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                    </button>
                </div>
            </section>
        @endif

        <section x-data="showMoreHandler({ total: {{ $room->staff->count() }}, lgLimit: 5, smLimit: 2 })" class="mt-8 w-full">
            <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">
                Assigned Staff
            </h2>

            @if ($room->staff->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
                    @foreach ($room->staff as $index => $member)
                        <div x-show="expanded || {{ $index }} < itemsToShow" x-transition
                            class="bg-gradient-to-br from-slate-50 to-white dark:from-gray-700 dark:to-gray-800 
                                rounded-lg shadow-sm hover:shadow-md border border-primary
                                overflow-hidden group transform hover:scale-[1.02] transition-all duration-300 ease-out origin-top">
                            <div class="cursor-pointer overflow-hidden"
                                @click="openModal('{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/pathfinder-bannerv2.png') }}')">
                                <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/pathfinder-bannerv2.png') }}"
                                    alt="{{ $member->full_name }}"
                                    class="w-full h-28 sm:h-32 md:h-36 object-cover group-hover:scale-110 transition-transform duration-500 ease-out"
                                    loading="lazy">
                            </div>

                            <div class="p-2 sm:p-3 text-center space-y-0.5">
                                <a href="{{ route('staff.client-show', $member->token) }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="block text-sm sm:text-base font-semibold text-slate-800 dark:text-gray-200 
                                    hover:text-primary transition-colors duration-200 truncate">
                                    {{ $member->full_name }}
                                </a>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-gray-300 truncate">
                                    {{ $member->position ?? 'No position' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Toggle Button -->
                <div class="text-center mt-5" x-show="shouldShowButton" x-transition x-cloak>
                    <button @click="toggle"
                        class="px-4 py-1.5 text-xs sm:text-sm font-medium rounded-full border border-primary 
                            text-primary hover:bg-primary hover:text-white transition-colors duration-200">
                        <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                    </button>
                </div>
            @else
                <p
                    class="text-center text-gray-500 dark:text-gray-400 dark:bg-gray-800 text-sm sm:text-base italic mt-4 border-2 border-primary rounded-lg p-4">
                    No staff assigned to this office.
                </p>
            @endif
        </section>

        <!-- Office Hours -->
        <div class="mt-6 w-full">
            <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">
                Office Hours
            </h2>

            <div
                class="bg-gray-50 dark:bg-gray-800 border-2 border-primary rounded-lg sm:rounded-xl p-4 sm:p-6 md:p-8 shadow-sm">

                @if ($room->grouped_office_hours && count($room->grouped_office_hours) > 0)
                    <div class="space-y-3 sm:space-y-4 md:space-y-5">
                        @foreach ($room->grouped_office_hours as $timeRange => $days)
                            <div class="flex flex-row justify-between items-center gap-2 sm:gap-4 md:gap-6">

                                <!-- Days -->
                                <div
                                    class="font-medium text-xs sm:text-sm md:text-base text-gray-800 dark:text-gray-300 flex-1 text-left">
                                    {{ $room->formatDaysGroup($days) }}
                                </div>

                                <!-- Time Range -->
                                <div class="text-xs sm:text-sm md:text-base text-right">
                                    @if (strtolower($timeRange) === 'closed')
                                        <span class="text-red-600 font-semibold">Closed</span>
                                    @else
                                        <span class="text-gray-600 dark:text-gray-300 font-medium">
                                            {{ $timeRange }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 italic text-sm sm:text-base">
                        No office hours specified
                    </div>
                @endif
            </div>
        </div>

        <!-- Fun Fact (if exists) -->
        @if (isset($fact) && $fact)
            <div class="mt-6 text-center">
                <h2 class="text-sm sm:text-base md:text-lg text-gray-800 dark:text-gray-200">Did you know?</h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 italic">{{ $fact }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Reusable Image Modal Markup -->
<div id="imageModal"
    class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <!-- Top-right controls -->
    <div class="absolute top-5 right-5 flex items-center space-x-4 sm:space-x-8">
        <!-- Download button -->
        <a id="downloadBtn" href="#" download title="Download Image"
            class="p-2 rounded-md transition-all hover:scale-120 ease-in-out duration-300">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/download-button.png"
                alt="Download" class="w-8 h-8 sm:w-10 sm:h-10">
        </a>

        <!-- Close button -->
        <button onclick="closeModal()"
            class="p-2 rounded-md transition-all hover:scale-120 ease-in-out duration-300 cursor-pointer"
            title="Close Modal">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                alt="Close Modal" class="w-8 h-8 sm:w-10 sm:h-10">
        </button>
    </div>

    <!-- Image -->
    <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded-md shadow-lg" />
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('showMoreHandler', ({
                total,
                lgLimit = 6,
                smLimit = 3
            }) => ({
                expanded: false,
                total,
                screenWidth: window.innerWidth,
                get itemsToShow() {
                    return this.screenWidth >= 1024 ? lgLimit : smLimit;
                },
                get shouldShowButton() {
                    return this.total > this.itemsToShow;
                },
                toggle() {
                    this.expanded = !this.expanded;
                },
                init() {
                    const updateWidth = () => this.screenWidth = window.innerWidth;
                    window.addEventListener('resize', updateWidth);
                    this.$watch('expanded', value => {
                        if (!value) window.scrollTo({
                            top: this.$el.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    });
                },
            }));
        });

        // -----------------------------
        // Existing logic (modal, desc, glightbox, etc.)
        // -----------------------------
        function initGlightbox() {
            if (window.glightboxInstance) window.glightboxInstance.destroy();
            window.glightboxInstance = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true,
                autoplayVideos: false,
                moreText: 'View Image',
                svg: {
                    close: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@f0f57bf/public/icons/exit.png" alt="Close"/>',
                    next: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@f0f57bf/public/icons/next.svg" alt="Next"/>',
                    prev: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@f0f57bf/public/icons/prev.svg" alt="Previous"/>',
                },
                // Add event callbacks to handle focus properly
                onOpen: () => {
                    // Remove focus from the triggering element
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                    // Store the element that triggered the lightbox
                    window.glightboxTrigger = document.activeElement;
                },
                onClose: () => {
                    // Return focus to the triggering element when closing
                    if (window.glightboxTrigger) {
                        window.glightboxTrigger.focus();
                        window.glightboxTrigger = null;
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initGlightbox();

            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadBtn');
            const desc = document.getElementById('roomDescription');
            const btn = document.getElementById('toggleDescriptionBtn');

            window.openModal = function(src) {
                modalImage.src = src.trim();
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                // Blur the active element to prevent focus issues
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            };

            window.closeModal = function() {
                modal.classList.add('hidden');
                modalImage.src = '';
                document.body.style.overflow = 'auto';
            };

            modal.addEventListener('click', e => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });

            downloadBtn.addEventListener('click', async e => {
                e.preventDefault();
                const imgSrc = modalImage.src;
                if (!imgSrc) return;
                try {
                    const response = await fetch(imgSrc);
                    const blob = await response.blob();
                    const bitmap = await createImageBitmap(blob);
                    const canvas = document.createElement('canvas');
                    canvas.width = bitmap.width;
                    canvas.height = bitmap.height;
                    canvas.getContext('2d').drawImage(bitmap, 0, 0);
                    canvas.toBlob(pngBlob => {
                        const pngUrl = URL.createObjectURL(pngBlob);
                        const a = document.createElement('a');
                        a.href = pngUrl;
                        a.download = 'downloaded-image.png';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(pngUrl);
                    }, 'image/png');
                } catch (err) {
                    console.error('Error converting image:', err);
                }
            });

            if (!desc || !btn) return;

            // Always ensure text is properly displayed
            const fullText = desc.dataset.fullText?.trim() ?? '';
            desc.textContent = fullText;

            // Helper function to check overflow
            function isOverflowing(el) {
                return el.scrollHeight > el.clientHeight;
            }

            // NEW: If the text does not overflow, show it fully and hide the button
            if (!isOverflowing(desc)) {
                desc.classList.remove(
                    'max-h-16',
                    'sm:max-h-20',
                    'md:max-h-24',
                    'lg:max-h-32',
                    'overflow-hidden'
                );
                btn.style.display = 'none';
                return;
            }

            // If the text overflows, keep “See more” button visible
            btn.style.display = 'inline-block';

            let expanded = false;
            btn.addEventListener('click', () => {
                expanded = !expanded;

                if (expanded) {
                    desc.classList.remove(
                        'max-h-16',
                        'sm:max-h-20',
                        'md:max-h-24',
                        'lg:max-h-32',
                        'overflow-hidden'
                    );
                    desc.classList.add('overflow-y-auto');
                    desc.style.maxHeight = '200px';
                    btn.textContent = 'See less';
                } else {
                    desc.classList.remove('overflow-y-auto');
                    desc.classList.add(
                        'max-h-16',
                        'sm:max-h-20',
                        'md:max-h-24',
                        'lg:max-h-32',
                        'overflow-hidden'
                    );
                    desc.style.maxHeight = null;
                    btn.textContent = 'See more';
                }
            });
        });
    </script>
@endpush

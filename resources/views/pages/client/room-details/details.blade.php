<div
    class="min-h-screen p-8 rounded-xl shadow-lg container mx-auto max-w-4xl space-y-12 border-2 border-primary dark:bg-gray-800">
    <!-- Room Title -->
    <h1
        class="text-4xl font-extrabold text-primary
           dark:border-b border-b-primary dark:border-b-primary text-center pb-3">
        {{ $room->name }}
    </h1>

    <!-- Description -->
    @if ($room->description)
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
            {{ $room->description }}
        </p>
    @endif

    <!-- Cover Image -->
    @if ($room->image_path)
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">Cover Image</h2>
            <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image"
                class="w-full max-h-[500px] object-cover cursor-pointer hover:scale-105 transition-all duration-500 rounded-lg"
                onclick="openModal('{{ asset('storage/' . $room->image_path) }}')" />
        </div>
    @endif

    <!-- Video -->
    @if ($room->video_path)
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">Room Video</h2>
            <video controls class="w-full rounded-lg shadow-md border-2 border-primary max-h-[400px]">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif

    <!-- Gallery -->
    @if ($room->images && $room->images->count())
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">Gallery</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @foreach ($room->images as $image)
                    <div
                        class="overflow-hidden rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-transform duration-300 cursor-pointer border-2 border-primary">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery Image"
                            class="w-full h-40 object-cover"
                            onclick="openModal('{{ asset('storage/' . $image->image_path) }}')" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Assigned Staff -->
    @if ($room->staff->isNotEmpty())
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 text-center">Assigned Staff</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($room->staff as $member)
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white dark:from-gray-700 dark:to-gray-600 rounded-xl shadow-md hover:shadow-lg border-2 border-primary dark:border-gray-600 overflow-hidden group hover:scale-105 transition-all duration-300">
                        <div class="cursor-pointer overflow-hidden"
                            onclick="openModal('{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc.png') }}')">
                            <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc.png') }}"
                                alt="{{ $member->full_name }}"
                                class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 text-center">
                            <a href="{{ route('staff.client-show', $member->token) }}"
                                class="block text-lg font-bold text-slate-800 dark:text-gray-200 hover:text-primary transition-colors mb-1"
                                target="_blank" rel="noopener noreferrer">
                                {{ $member->full_name }}
                            </a>
                            <p class="text-slate-600 dark:text-gray-300 text-sm">
                                {{ $member->position ?? 'No position assigned' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400">No staff assigned to this room.</p>
    @endif

    <!-- Office Hours -->
    <div class="mt-6">
        <h3 class="text-3xl font-bold text-slate-800 mb-2 text-center dark:text-gray-300">Office Hours</h3>
        <div class="bg-gray-50 p-4 rounded-2xl dark:bg-gray-800 border-2 border-primary">

            @if ($room->grouped_office_hours && count($room->grouped_office_hours) > 0)
                <div class="space-y-3">
                    @foreach ($room->grouped_office_hours as $timeRange => $days)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="font-medium text-gray-800 min-w-0 flex-1 dark:text-gray-300">
                                {{ $room->formatDaysGroup($days) }}
                            </div>
                            <div class="text-sm sm:text-right">
                                @if (strtolower($timeRange) === 'closed')
                                    <span class="text-red-600 font-medium">Closed</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-300">{{ $timeRange }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-500 italic">No office hours specified</div>
            @endif

        </div>
    </div>

    <!-- Fun Fact (if exists) -->
    @if (isset($fact) && $fact)
        <div class="mt-6 text-center">
            <h2 class="text-lg text-gray-800 dark:text-gray-200">Did you know?</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ $fact }}</p>
        </div>
    @endif

    <!-- Scan Another QR Code Button -->
    <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-600">
        <p class="text-sm text-gray-500 dark:text-gray-300 mb-3">Want to explore another room?</p>
        <a href="{{ route('scan.index') }}"
            class="inline-block bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg border border-primary transition-all duration-300 dark:hover:bg-gray-800 shadow-primary-hover">
            Scan Another QR Code
        </a>
    </div>
</div>

<!-- Reusable Image Modal Markup -->
<div id="imageModal"
    class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <!-- Top-right controls -->
    <div class="absolute top-5 right-5 flex items-center space-x-8">
        <!-- Download button -->
        <a id="downloadBtn" href="#" download title="Download Image"
            class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/download-button.png"
                alt="Download" class="w-10 h-10">
        </a>

        <!-- Close button -->
        <button onclick="closeModal()"
            class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6 cursor-pointer"
            title="Close Modal">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                alt="Close Modal" class="w-10 h-10">
        </button>
    </div>

    <!-- Image -->
    <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadBtn');

            modalImage.src = src;
            modal.classList.remove('hidden');

            // When image finishes loading, prepare PNG download
            modalImage.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = modalImage.naturalWidth;
                canvas.height = modalImage.naturalHeight;
                ctx.drawImage(modalImage, 0, 0);

                // Convert to PNG blob and set as downloadable link
                canvas.toBlob((blob) => {
                    const pngUrl = URL.createObjectURL(blob);
                    downloadBtn.href = pngUrl;
                    downloadBtn.download = 'image.png';
                }, 'image/png');
            };
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.classList.add('hidden');
            modalImage.src = '';
        }

        // Expose functions globally
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Click outside image to close
        const modal = document.getElementById('imageModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    });
</script>

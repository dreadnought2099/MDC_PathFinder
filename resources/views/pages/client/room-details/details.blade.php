<div class="min-h-screen p-8 rounded-xl shadow-lg container mx-auto max-w-4xl space-y-12 border-2 border-primary dark:bg-gray-800">
    <!-- Room Title -->
    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-300 
           border-b-2 border-b-primary dark:border-b-primary text-center pb-3">
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
                            onclick="openModal('{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc-logo.png') }}')">
                            <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc-logo.png') }}"
                                alt="{{ $member->full_name }}"
                                class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 text-center">
                            <a href="{{ route('staff.client-show', $member->id) }}"
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
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200 text-center">Office Hours</h3>
        <div
            class="whitespace-pre-line bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border-2 border-primary text-gray-800 dark:text-gray-300">
            {!! nl2br(e($room->formatted_office_hours)) !!}
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
            class="inline-block bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg shadow-md border border-primary transition-all duration-300 dark:hover:bg-gray-800">
            Scan Another QR Code
        </a>
    </div>
</div>

<!-- Reusable Image Modal Markup -->
<div id="imageModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
    <!-- Top-right controls -->
    <div class="absolute top-5 right-5 flex items-center space-x-8">
        <!-- Download button -->
        <a id="downloadBtn" href="#" download title="Download Image"
            class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6">
            <img src="{{ asset('icons/download-button.png') }}" alt="Download" class="w-10 h-10">
        </a>

        <!-- Close button -->
        <button onclick="closeModal()"
            class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6 cursor-pointer"
            title="Close Modal">
            <img src="{{ asset('icons/exit.png') }}" alt="Close Modal" class="w-10 h-10">
        </button>
    </div>

    <!-- Image -->
    <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
</div>

<!-- Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadBtn');
            modalImage.src = src;
            modal.classList.remove('hidden');

            downloadBtn.href = src; // set download link to the image
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = '';
            modal.classList.add('hidden');
        }

        // Expose functions globally for inline onclick
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Close modal when clicking outside the image
        const modal = document.getElementById('imageModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>

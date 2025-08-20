<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('ar.view') }}"
        class="inline-flex items-center text-gray-700 hover:text-primary transition-colors duration-200">
        <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="font-medium">Back</span>
    </a>
</div>

<!-- Main Container -->
<div class="min-h-screen p-8 rounded-xl shadow-lg container mx-auto max-w-4xl space-y-12 border-2 border-primary">

    <!-- Room Title -->
    <h1 class="text-4xl font-extrabold text-gray-900 border-b-2 border-primary pb-3">
        {{ $room->name }}
    </h1>

    <!-- Description -->
    @if ($room->description)
        <p class="text-gray-700 leading-relaxed text-lg">
            {{ $room->description }}
        </p>
    @endif

    <!-- Cover Image -->
    @if ($room->image_path)
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Cover Image</h2>
            <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image"
                class="w-full max-h-[500px] object-cover cursor-pointer hover:scale-105 transition-all duration-500 rounded-lg"
                onclick="openModal('{{ asset('storage/' . $room->image_path) }}')" />
        </div>
    @endif

    <!-- Video -->
    @if ($room->video_path)
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Room Video</h2>
            <video controls class="w-full rounded-lg shadow-md border border-gray-200 max-h-[400px]">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif

    <!-- Gallery -->
    @if ($room->images && $room->images->count())
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Gallery</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @foreach ($room->images as $image)
                    <div
                        class="overflow-hidden rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-transform duration-300 cursor-pointer border border-gray-200">
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Assigned Staff</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($room->staff as $member)
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-xl shadow-md hover:shadow-lg border border-slate-200 overflow-hidden group hover:scale-105 transition-all duration-300">
                        <div class="cursor-pointer overflow-hidden"
                            onclick="openModal('{{ Storage::url($member->photo_path ?? 'images/profile.jpeg') }}')">
                            <img src="{{ Storage::url($member->photo_path ?? 'images/default.jpg') }}"
                                alt="{{ $member->full_name }}"
                                class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 text-center">
                            <a href="{{ route('staff.client-show', $member->id) }}"
                                class="block text-lg font-bold text-slate-800 hover:text-primary transition-colors mb-1"
                                target="_blank" rel="noopener noreferrer">
                                {{ $member->full_name }}
                            </a>
                            <p class="text-slate-600 text-sm">
                                {{ $member->position ?? 'No position assigned' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-gray-500">No staff assigned to this room.</p>
    @endif

    <!-- Office Hours -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Office Hours</h3>
        <div class="whitespace-pre-line bg-gray-50 p-4 rounded border">
            {!! nl2br(e($room->formatted_office_hours)) !!}
        </div>
    </div>

    <!-- Scan Another QR Code Button -->
    <div class="text-center pt-8 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-3">Want to explore another room?</p>
        <a href="{{ route('ar.view') }}"
            class="inline-block bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg shadow-md border border-primary transition-all duration-300">
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

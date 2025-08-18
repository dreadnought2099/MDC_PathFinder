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
<div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 space-y-8">

    <!-- Room Title -->
    <h1 class="text-4xl font-extrabold mb-4 text-gray-900 border-b-2 border-primary pb-2">
        {{ $room->name }}
    </h1>

    <!-- Description -->
    @if ($room->description)
        <p class="text-gray-700 leading-relaxed text-lg">{{ $room->description }}</p>
    @endif

    <!-- Office Hours -->
    @if ($room->office_days && $room->office_hours_start && $room->office_hours_end)
        @php
            $daysFormatted = str_replace(',', ', ', $room->office_days);
            $start = \Carbon\Carbon::parse($room->office_hours_start)->format('H:i');
            $end = \Carbon\Carbon::parse($room->office_hours_end)->format('H:i');
        @endphp
        <div class="bg-gradient-to-tr from-blue-400 to-white rounded-lg p-4 shadow-md max-w-sm">
            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <img src="{{ asset('icons/calendar.png') }}" alt="Calendar" class="h-8 w-8 object-contain">
                Office Hours
            </h4>
            <p class="text-sm text-gray-700 mb-1">
                <span class="font-medium text-gray-900">Days:</span> {{ $daysFormatted }}
            </p>
            <p class="text-sm text-gray-700">
                <span class="font-medium text-gray-900">Time:</span> {{ $start }} - {{ $end }}
            </p>
        </div>
    @endif

    <!-- Cover Image -->
    @if ($room->image_path)
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-6">Cover Image</h2>
            <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image"
                class="w-full max-h-[500px] object-cover cursor-pointer hover:scale-105 transition-all duration-500 ease-out rounded-lg"
                onclick="openModal('{{ asset('storage/' . $room->image_path) }}')" />
        </div>
    @endif

    <!-- Video -->
    @if ($room->video_path)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Room Video</h2>
            <video controls class="w-full rounded-lg shadow-lg border border-gray-300 max-h-[400px]">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif

    <!-- Gallery -->
    @if ($room->images && $room->images->count())
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Gallery</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @foreach ($room->images as $image)
                    <div
                        class="overflow-hidden rounded-lg shadow-md hover:scale-105 transition-transform duration-300 cursor-pointer border border-gray-200">
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
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Assigned Staff</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($room->staff as $member)
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-xl shadow-md hover:shadow-xl border border-slate-200 overflow-hidden group transform hover:scale-105 transition-all duration-300">
                        <div class="cursor-pointer overflow-hidden"
                            onclick="openModal('{{ Storage::url($member->photo_path ?? 'images/profile.jpeg') }}')">
                            <img src="{{ Storage::url($member->photo_path ?? 'images/default.jpg') }}"
                                alt="{{ $member->name }}"
                                class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 text-center">
                            <a href="{{ route('staff.show', $member->id) }}"
                                class="block text-xl font-bold text-slate-800 hover:text-blue-600 transition-colors duration-300 mb-2">
                                {{ $member->name }}
                            </a>
                            <p class="text-slate-600 font-medium">
                                {{ $member->position ?? 'No position assigned' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-gray-500">No staff assigned to this room.</p>
    @endif

    <!-- Scan Another QR Code Button -->
    <div class="text-center pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Want to explore another room?</p>
        <a href="{{ route('ar.view') }}"
            class="inline-block bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg shadow-md border border-primary transition-all duration-300">
            Scan Another QR Code
        </a>
    </div>
</div>

<!-- Reusable Image Modal Markup -->
<div id="imageModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
    <button onclick="closeModal()"
        class="absolute top-5 right-5 text-gray-300 text-6xl hover:text-red-600 cursor-pointer">&times;</button>
    <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
</div>

<!-- Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = src;
            modal.classList.remove('hidden');
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

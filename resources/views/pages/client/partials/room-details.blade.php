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
    <div>
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ $room->name }}</h1>
        @if ($room->description)
            <p class="mt-2 text-gray-600 leading-relaxed">{{ $room->description }}</p>
        @endif
    </div>

    <!-- Main Image -->
    @if ($room->image_path)
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Room Preview</h2>
            <img src="{{ Storage::url($room->image_path) }}" alt="{{ $room->name }}"
                class="w-full rounded-lg object-cover aspect-video">
        </div>
    @endif

    <!-- Office Hours -->
    @if ($room->office_hours)
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">Office Hours</h2>
            <p class="text-gray-600">{{ $room->office_hours }}</p>
        </div>
    @endif

    <!-- Room Video -->
    @if ($room->video_path)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Room Video</h2>
            <video controls class="w-full rounded-lg aspect-video">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif

    <!-- Carousel Images -->
    @if ($room->images && $room->images->count())
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Gallery</h2>
            <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-lg overflow-hidden">
                    @foreach ($room->images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($image->image_path) }}"
                                class="d-block w-100 object-cover aspect-video"
                                alt="Carousel Image {{ $index + 1 }}">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
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
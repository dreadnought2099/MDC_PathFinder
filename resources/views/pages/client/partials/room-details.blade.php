<a href="{{ route('ar.view') }}"
    class="flex items-center text-black hover:text-[#157ee1] focus:outline-none cursor-pointer">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    <span class="ml-1">Back</span>
</a>

<div class="bg-white rounded-lg shadow-lg p-6 border border-primary">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $room->name }}</h1>

    @if ($room->description)
        <p class="text-gray-600 mb-4">{{ $room->description }}</p>
    @endif

    @if ($room->image_path)
        <div class="mb-4">
            <img src="{{ Storage::url($room->image_path) }}" alt="{{ $room->name }}"
                class="w-full object-cover rounded-lg">
        </div>
    @endif

    @if ($room->office_hours)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Office Hours:</h3>
            <p class="text-gray-600">{{ $room->office_hours }}</p>
        </div>
    @endif

    @if ($room->video_path)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Room Video:</h3>
            <video controls class="w-full rounded-lg">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif

    @if ($room->images && $room->images->count())
        <div id="roomCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($room->images as $index => $image)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ Storage::url($image->image_path) }}" class="d-block w-100 rounded-lg"
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
    @endif

    <!-- Back to scanner button -->
    <div class="mt-6 text-center">
        <a href="{{ route('ar.view') }}"
            class="bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg shadow-lg border border-primary transition-all duration-300">
            Scan Another QR Code
        </a>
    </div>
</div>

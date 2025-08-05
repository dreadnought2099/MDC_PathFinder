<div class="bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $room->name }}</h1>
    
    @if ($room->description)
        <p class="text-gray-600 mb-4">{{ $room->description }}</p>
    @endif

    @if ($room->image_path)
        <div class="mb-4">
            <img src="{{ asset($room->image_path) }}" alt="{{ $room->name }}" 
                 class="w-full h-64 object-cover rounded-lg">
        </div>
    @endif

    @if ($room->office_hours)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Office Hours:</h3>
            <p class="text-gray-600">{{ $room->office_hours }}</p>
        </div>
    @endif

    @if ($room->staff->count() > 0)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Staff in this Room:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($room->staff as $member)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if ($member->photo_path)
                            <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}"
                                 class="w-16 h-16 object-cover rounded-full mb-2">
                        @endif
                        <h4 class="font-semibold text-gray-800">{{ $member->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $member->position }}</p>
                        @if ($member->email)
                            <p class="text-sm text-blue-600">{{ $member->email }}</p>
                        @endif
                        @if ($member->bio)
                            <p class="text-sm text-gray-500 mt-1">{{ $member->bio }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($room->video_path)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Room Video:</h3>
            <video controls class="w-full rounded-lg">
                <source src="{{ asset($room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
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
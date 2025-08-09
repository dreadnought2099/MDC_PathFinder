@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <h2 class="text-2xl text-center font-bold mb-6">Edit Room/Office</h2>

        <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-600">Room/Office Name</label>
                <input type="text" name="name" value="{{ old('name', $room->name) }}" class="w-full border p-2 rounded"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Description</label>
                <textarea name="description" class="w-full border p-2 rounded">{{ old('description', $room->description) }}</textarea>
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Cover Image (optional)</label>
                @if ($room->image_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $room->image_path) }}" alt="Cover Image"
                            class="h-24 object-cover rounded">
                    </div>
                @endif
                <input type="file" name="image_path" class="w-full border p-2 rounded">
            </div>

            {{-- Existing Carousel Images --}}
            @if ($room->images->count() > 0)
                <div class="flex flex-wrap gap-4 mb-4">
                    @foreach ($room->images as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                class="h-24 w-24 object-cover rounded border">

                            {{-- Delete Carousel Image --}}
                            <form action="{{ route('room.carousel.remove', [$room->id, $image->id]) }}" method="POST"
                                class="absolute top-0 right-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-1 rounded text-xs"
                                    onclick="return confirm('Are you sure you want to remove this image?')">
                                    ×
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add New Carousel Images --}}
            <div class="mb-4">
                <label for="carousel_images" class="font-bold">Add Carousel Images</label>
                <input type="file" name="carousel_images[]" multiple class="w-full border p-2 rounded">
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Short Video Path (optional)</label>
                @if ($room->video_path)
                    <div class="mb-2">
                        <video src="{{ asset('storage/' . $room->video_path) }}" controls class="h-24 rounded"></video>
                    </div>
                @endif
                <input type="file" name="video_path" class="w-full border p-2 rounded">
            </div>


            <div class="mb-4 text-gray-600">
                <label class="block">Office Hours</label>
                <input type="text" value="{{ old('office_hours', $room->office_hours) }}" name="office_hours"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                    Save Room
                </button>
            </div>
        </form>
    </div>
@endsection

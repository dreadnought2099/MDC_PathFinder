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
                <label for="carousel_images" class="block">Add Carousel Images</label>
                <input type="file" name="carousel_images[]" multiple class="w-full border p-2 rounded">
            </div>

            <div class="mb-8 text-gray-800 max-w-lg mx-auto">
                <label class="block mb-3 font-semibold text-gray-900">Upload Short Video (optional)</label>

                @if ($room->video_path)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-5 border border-gray-200">
                        <video src="{{ asset('storage/' . $room->video_path) }}" controls preload="metadata"
                            class="block mx-auto max-w-full h-auto"></video>
                        <div class="p-3 text-center text-sm text-gray-600 truncate"
                            title="{{ basename($room->video_path) }}">
                            {{ basename($room->video_path) }}
                        </div>
                    </div>
                @endif

                <label for="video_path"
                    class="block text-center cursor-pointer bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-md px-6 py-2 transition duration-300">
                    Select
                </label>
                <input type="file" name="video_path" id="video_path" class="hidden"
                    accept="video/mp4,video/avi,video/mpeg" />
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

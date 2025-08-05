@extends('layouts.app')

@section('content')
     <div class="max-w-xl mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-6">Edit Room/Office</h2>

        <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-600">Room/Office Name</label>
                <input type="text" name="name" value="{{ old('name', $room->name) }}" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Description</label>
                <textarea name="description" value="{{ old('description', $room->description) }}" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Cover Image (optional)</label>
                <input type="file" name="image_path" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4 text-gray-600">
                <input type="file" class="filepond" name="carousel_images[]" multiple data-max-files="5" />
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Short Video Path (optional)</label>
                <input type="file" name="video_path" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Office Hours</label>
                <input type="text" value="{{ old('office_hours', $room->office_hours) }}" name="office_hours" class="w-full border p-2 rounded">
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
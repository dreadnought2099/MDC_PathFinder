@extends('layouts.app') 

@section('content')
<div class="max-w-xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">Add New Room/Office</h2>

    <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block">Room/Office Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block">Description</label>
            <textarea name="description" class="w-full border p-2 rounded"></textarea>
        </div>

        <div class="mb-4">
            <label class="block">Marker ID</label>
            <input type="text" name="marker_id" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block">Cover Image Path (optional)</label>
            <input type="file" name="image_path" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block">Carousel Images</label>
            <input type="file" name="carousel_images[]" class="w-full border p-2 rounded" multiple>
        </div>
        
        <div class="mb-4">
            <label class="block">Short Video Path (optional)</label>
            <input type="file" name="video_path" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block">Office Hours</label>
            <input type="text" name="office_hours" class="w-full border p-2 rounded">
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Save Room
            </button>
        </div>
    </form>
</div>
@endsection

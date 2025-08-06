@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <h2 class="text-2xl text-center font-bold mb-6">Add New <span class="text-primary">Room/Office</span></h2>

        <form action="{{ route('room.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-600">Room/Office Name</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Description</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>

            {{-- <div class="mb-4">
                <label for="staff_id">Assign Staff</label>
                <select name="staff_id" class="w-full border p-2 rounded" required>
                    <option value="">Select a Staff</option>
                    @foreach ($staffs as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div> --}}

            <div class="mb-4 text-gray-600">
                <label class="block">Cover Image (optional)</label>
                <input type="file" name="image_path" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4 text-gray-600">
                <input type="file" class="filepond" name="carousel_images[]" multiple data-max-files="10" />
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Short Video Path (optional)</label>
                <input type="file" name="video_path" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4 text-gray-600">
                <label class="block">Office Hours</label>
                <input type="text" name="office_hours" class="w-full border p-2 rounded">
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

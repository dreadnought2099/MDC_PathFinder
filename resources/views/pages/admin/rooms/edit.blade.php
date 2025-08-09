@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">

    <h2 class="text-2xl font-bold mb-6 text-center"><span class="text-primary">Edit {{ $room->name }}</span></h2>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('room.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block font-semibold mb-1">Description</label>
            <textarea id="description" name="description" rows="4"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('description', $room->description) }}</textarea>
        </div>

        {{-- Current Cover Image --}}
        @if($room->image_path)
        <div>
            <label class="block font-semibold mb-1">Current Cover Image</label>
            <img src="{{ asset('storage/' . $room->image_path) }}" alt="Cover Image" class="w-48 rounded mb-2 border" />
        </div>
        @endif

        {{-- Cover Image Upload --}}
        <div>
            <label for="image_path" class="block font-semibold mb-1">Upload New Cover Image</label>
            <input type="file" id="image_path" name="image_path" accept="image/*" class="block" />
        </div>

        {{-- Current Video --}}
        @if($room->video_path)
        <div>
            <label class="block font-semibold mb-1">Current Video</label>
            <video controls class="w-64 rounded mb-2 border">
                <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        @endif

        {{-- Video Upload --}}
        <div>
            <label for="video_path" class="block font-semibold mb-1">Upload New Video (mp4, avi, mpeg)</label>
            <input type="file" id="video_path" name="video_path" accept="video/mp4,video/avi,video/mpeg" class="block" />
        </div>

        {{-- Office Days --}}
        <div>
            <label class="block font-semibold mb-1">Office Days</label>
            <div class="flex flex-wrap gap-4">
                @php
                    $officeDays = old('office_days', explode(',', explode(' ', $room->office_hours ?? '')[0] ?? '')) ?? [];
                    $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                @endphp
                @foreach($weekDays as $day)
                <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" name="office_days[]" value="{{ $day }}" 
                        {{ in_array($day, $officeDays) ? 'checked' : '' }} class="form-checkbox" />
                    <span>{{ $day }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Office Hours Start --}}
        <div>
            @php
                $officeHoursTime = old('office_hours_start', explode('-', explode(' ', $room->office_hours ?? '')[1] ?? '')[0] ?? '');
            @endphp
            <label for="office_hours_start" class="block font-semibold mb-1">Office Hours Start</label>
            <input type="time" id="office_hours_start" name="office_hours_start" value="{{ $officeHoursTime }}"
                class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
        </div>

        {{-- Office Hours End --}}
        <div>
            @php
                $officeHoursEnd = old('office_hours_end', trim(explode('-', explode(' ', $room->office_hours ?? '')[1] ?? '')[1] ?? ''));
            @endphp
            <label for="office_hours_end" class="block font-semibold mb-1">Office Hours End</label>
            <input type="time" id="office_hours_end" name="office_hours_end" value="{{ $officeHoursEnd }}"
                class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
        </div>

        {{-- Current Carousel Images --}}
        @if($room->images && $room->images->count() > 0)
        <div>
            <label class="block font-semibold mb-2">Current Carousel Images</label>
            <div class="grid grid-cols-3 gap-4">
                @foreach ($room->images as $image)
                    <div class="relative border rounded overflow-hidden">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Carousel Image" class="w-full h-32 object-cover" />
                        <label class="absolute top-1 right-1 bg-red-600 text-white rounded px-2 text-xs cursor-pointer hover:bg-red-700"
                               title="Remove this image">
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="hidden" />
                            Remove
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Add Carousel Images --}}
        <div>
            <label for="carousel_images" class="block font-semibold mb-1">Add Carousel Images</label>
            <input type="file" id="carousel_images" name="carousel_images[]" multiple accept="image/*" class="block" />
        </div>

        {{-- Submit --}}
        <div>
            <button type="submit"
                class="bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                Update Room
            </button>
        </div>
    </form>
</div>
@endsection

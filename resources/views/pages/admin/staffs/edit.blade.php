@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl text-center font-bold mb-6">Edit Staff</h1>

        <x-floating-actions />

        <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="w-full border rounded p-2">
                @error('name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                    class="w-full border rounded p-2">
                @error('email')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Position</label>
                <input type="text" name="position" value="{{ old('position', $staff->position) }}"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block mb-1 font-medium">Phone Number</label>
                <input type="text" name="phone_num" value="{{ old('phone_num', $staff->phone_num) }}"
                    class="w-full border rounded p-2">
                @error('phone_num')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Bio</label>
                <textarea name="bio" class="w-full border rounded p-2">{{ old('bio', $staff->bio) }}</textarea>
            </div>

            <div>
                <label class="block mb-1 font-medium">Photo</label>
                <input type="file" name="photo_path" class="w-full">
                @if ($staff->photo_path)
                    <img src="{{ Storage::url($staff->photo_path) }}" class="h-20 mt-2">
                @endif
            </div>

            <div>
                <button type="submit"
                    class="bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer px-4 py-2 rounded">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
@endsection

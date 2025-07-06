@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg border border-primary">
            <h2 class="text-2xl font-bold text-center mb-4">
                Profil<span class="text-primary">e</span>
            </h2>

            {{-- Profile image preview --}}
            <div class="flex justify-center mb-4">
                <img src="{{ $user->profile_photo_url ?? asset('images/profile.jpeg') }}" alt="Profile"
                     class="h-32 w-32 rounded-full object-cover">
            </div>

            {{-- Upload form --}}
            <form method="POST" action="{{ route('admin.profile.updateImage') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="profile_image" class="block text-sm font-medium text-gray-700">Change Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" accept="image/*" required>
                </div>

                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark w-full">
                    Upload
                </button>
            </form>
            
            <div class="space-y-3 mt-6">
                <p>Name:{{ $user->name }}</p>
                <p>Email:{{ $user->email }}</p>
            </div>
        </div>
    </div>
@endsection

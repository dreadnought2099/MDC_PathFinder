@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <h2 class="text-2xl text-center mb-6">Add Staff Member</h2>

        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block">Full Name</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block">Position</label>
                <input type="text" name="position" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block">Bio</label>
                <textarea name="bio" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block">Email</label>
                <input type="email" name="email" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block">Phone Number</label>
                <input type="text" name="phone_num" class="w-full border p-2 rounded" placeholder="0900 000 0000">
            </div>

            <div class="mb-4">
                <label class="block">Photo</label>
                <input type="file" name="photo_path" class="w-full border p-2 rounded">
            </div>

            <div>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                    Create Staff
                </button>
            </div>
        </form>
    </div>
@endsection

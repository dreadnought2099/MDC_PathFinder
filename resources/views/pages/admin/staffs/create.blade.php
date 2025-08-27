@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6"><span class="text-primary">Add</span> <span class="dark:text-gray-300">Staff Member</span></h2>

        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" data-upload>
            @csrf

            <div class="mb-4">
                <label class="block dark:text-gray-300">First Name</label>
                <input type="text" name="first_name" class="w-full border p-2 rounded dark:border-gray-300" required>
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Last Name</label>
                <input type="text" name="last_name" class="w-full border p-2 rounded dark:border-gray-300" required>
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Middle Name</label>
                <input type="text" name="middle_name" class="w-full border p-2 rounded dark:border-gray-300">
            </div>

            <div class="mb-4">
                <label class="block font-medium dark:text-gray-300">Suffix (Optional)</label>
                <select name="suffix" class="w-full border p-2 rounded dark:border-gray-300">
                    <option value="">None</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="V">V</option>
                    <option value="VI">VI</option>
                    <option value="VII">VII</option>
                    <option value="VIII">VIII</option>
                    <option value="IX">IX</option>
                    <option value="X">X</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium dark:text-gray-300">Professional Credentials (Optional)</label>
                <input type="text" name="credentials" class="w-full border p-2 rounded dark:border-gray-300" placeholder="e.g. MD, CPA, RN">
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Position</label>
                <input type="text" name="position" class="w-full border p-2 rounded dark:border-gray-300">
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Bio</label>
                <textarea name="bio" class="w-full border p-2 rounded dark:border-gray-300"></textarea>
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Email</label>
                <input type="email" name="email" class="w-full border p-2 rounded dark:border-gray-300">
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Phone Number</label>
                <input type="text" name="phone_num" class="w-full border p-2 rounded dark:border-gray-300" placeholder="0900 000 0000">
            </div>

            <div class="mb-4">
                <label class="block dark:text-gray-300">Photo</label>
                <input type="file" name="photo_path" class="w-full border p-2 rounded dark:border-gray-300">
            </div>

            <div>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                    Save Staff
                </button>
            </div>
        </form>
    </div>
@endsection

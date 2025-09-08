@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-6"><span class="text-primary">Add</span> <span class="dark:text-gray-300">Staff
                Member</span></h2>

        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" data-upload>
            @csrf

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-700';

                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <div class="relative mb-4">
                <input type="text" name="first_name" placeholder="First Name" class="{{ $inputClasses }}" required>
                <label class="{{ $labelClasses }}">First Name</label>
            </div>

            <div class="relative mb-4">
                <input type="text" name="last_name" placeholder="Last Name" class="{{ $inputClasses }}" required>
                <label class="{{ $labelClasses }}">Last Name</label>
            </div>

            <div class="relative mb-4">
                <input type="text" name="middle_name" placeholder="Middle Name" class="{{ $inputClasses }}">
                <label class="{{ $labelClasses }}">Middle Name</label>
            </div>

            <div class="relative mb-4">
                <select name="suffix"
                    class="peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-700 appearance-none">
                    <option value="" hidden></option>
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
                <label
                    class="absolute cursor-text left-0 -top-3 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md">
                     Suffix (Optional)
                </label>

                <!-- Custom dropdown arrow -->
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="relative mb-4">
                <input type="text" name="credentials" placeholder="Professional Credentials (Optional)"
                    class="{{ $inputClasses }}">
                <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
            </div>

            <div class="relative mb-4">
                <input type="text" name="position" placeholder="Position" class="{{ $inputClasses }}">
                <label class="{{ $labelClasses }}">Position</label>
            </div>

            <div class="relative mb-4">
                <textarea name="bio" placeholder="Bio" class="{{ $inputClasses }}"></textarea>
                <label class="{{ $labelClasses }}">Bio</label>
            </div>

            <div class="relative mb-4">
                <input type="email" placeholder="Email" name="email" class="{{ $inputClasses }}">
                <label class="{{ $labelClasses }}">Email</label>
            </div>

            <div class="relative mb-4">
                <input type="text" name="phone_num" placeholder="Phone Number" class="{{ $inputClasses }}"
                    placeholder="0900 000 0000">
                <label class="{{ $labelClasses }}">Phone Number</label>
            </div>

            <div class="relative mb-4">
                <input type="file" name="photo_path" class="{{ $inputClasses }}">
                <label class="{{ $labelClasses }}">Photo</label>
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-2 bg-primary rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Save Staff
                </button>
            </div>
        </form>
    </div>
@endsection

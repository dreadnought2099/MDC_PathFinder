@extends('layouts.app')

@section('content')
    <div
        class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-4 sm:p-6 md:p-8 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-8 font-bold dark:text-gray-300 text-gray-800"><span class="text-primary">Add</span>
            Staff</h2>

        <x-upload-progress-modal>
            <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" id="staff-form">
                @csrf

                @php
                    $inputClasses =
                        'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';
                    $labelClasses =
                        'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
                @endphp

                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="first_name" placeholder="First Name" class="{{ $inputClasses }}"
                            required>
                        <label class="{{ $labelClasses }}">First Name</label>
                    </div>
                    <div class="relative">
                        <input type="text" name="last_name" placeholder="Last Name" class="{{ $inputClasses }}" required>
                        <label class="{{ $labelClasses }}">Last Name</label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="middle_name" placeholder="Middle Name" class="{{ $inputClasses }}">
                        <label class="{{ $labelClasses }}">Middle Name</label>
                    </div>
                    <div class="relative">
                        <select name="suffix"
                            class="peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800 appearance-none">
                            <option value="" hidden></option>
                            <option value="">None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="Sr.">Sr.</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="VI">VI</option>
                            <option value="VII">VII</option>
                            <option value="VIII">VIII</option>
                            <option value="IX">IX</option>
                            <option value="X">X</option>
                        </select>
                        <label
                            class="absolute cursor-text left-0 -top-3 text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md">
                            Suffix (Optional)
                        </label>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Professional Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="credentials" placeholder="Professional Credentials (Optional)"
                            class="{{ $inputClasses }}">
                        <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
                    </div>
                    <div class="relative">
                        <input type="text" name="position" placeholder="Position" class="{{ $inputClasses }}">
                        <label class="{{ $labelClasses }}">Position</label>
                    </div>
                </div>

                <!-- Bio -->
                <div class="relative mb-4">
                    <textarea name="bio" placeholder="Bio" rows="4" class="{{ $inputClasses }}"></textarea>
                    <label class="{{ $labelClasses }}">Bio</label>
                </div>

                <!-- Photo Upload -->
                <div class="mb-8">
                    <label class="block mb-2 dark:text-gray-300">Staff Photo (optional, max 5MB)</label>
                    <div id="staffUploadBox"
                        class="flex flex-col items-center justify-center w-full h-40 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded cursor-pointer hover:border-primary hover:bg-gray-50 dark:hover:border-primary dark:hover:bg-gray-800 transition-colors relative">
                        <div id="staffPlaceholder" class="text-center">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                alt="Image Icon" class="w-8 h-8 mx-auto" onerror="this.style.display='none'">
                            <span class="text-gray-500 dark:text-gray-300 block mt-2">Click to upload staff photo</span>
                        </div>
                        <div id="staffPreviewContainer" class="w-full h-full hidden"></div>
                    </div>
                    <input type="file" name="photo_path" id="photo_path" class="hidden"
                        accept="image/jpeg,image/jpg,image/png" data-max-size="5120" />
                    @error('photo_path')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button id="submitBtn" type="submit"
                        class="w-full bg-primary text-white px-4 py-2 rounded-md shadow-primary-hover hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800">
                        Save Staff
                    </button>
                </div>
            </form>
        </x-upload-progress-modal>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/staff-form.js')
@endpush

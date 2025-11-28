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

                @if (auth()->user()->hasRole('Office Manager'))
                    <div
                        class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                    Staff will be automatically assigned to your office:
                                    <span class="font-semibold">{{ auth()->user()->room->name ?? 'Not assigned' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="first_name" placeholder="First Name" class="{{ $inputClasses }}"
                            value="{{ old('first_name') }}" required>
                        <label class="{{ $labelClasses }}">First Name</label>
                        @error('first_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="relative">
                        <input type="text" name="last_name" placeholder="Last Name" class="{{ $inputClasses }}"
                            value="{{ old('last_name') }}" required>
                        <label class="{{ $labelClasses }}">Last Name</label>
                        @error('last_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="middle_name" placeholder="Middle Name" class="{{ $inputClasses }}"
                            value="{{ old('middle_name') }}">
                        <label class="{{ $labelClasses }}">Middle Name</label>
                        @error('middle_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="relative">
                        <select name="suffix"
                            class="peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800 appearance-none">
                            <option value="" hidden></option>
                            <option value="" {{ old('suffix') == '' ? 'selected' : '' }}>None</option>
                            <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                            <option value="V" {{ old('suffix') == 'V' ? 'selected' : '' }}>V</option>
                            <option value="VI" {{ old('suffix') == 'VI' ? 'selected' : '' }}>VI</option>
                            <option value="VII" {{ old('suffix') == 'VII' ? 'selected' : '' }}>VII</option>
                            <option value="VIII" {{ old('suffix') == 'VIII' ? 'selected' : '' }}>VIII</option>
                            <option value="IX" {{ old('suffix') == 'IX' ? 'selected' : '' }}>IX</option>
                            <option value="X" {{ old('suffix') == 'X' ? 'selected' : '' }}>X</option>
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
                        @error('suffix')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Professional Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="relative">
                        <input type="text" name="credentials" placeholder="Professional Credentials (Optional)"
                            class="{{ $inputClasses }}" value="{{ old('credentials') }}">
                        <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
                        @error('credentials')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="relative">
                        <input type="text" name="position" placeholder="Position" class="{{ $inputClasses }}"
                            value="{{ old('position') }}">
                        <label class="{{ $labelClasses }}">Position</label>
                        @error('position')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="relative mb-4">
                    <textarea name="bio" placeholder="Bio" rows="4" class="{{ $inputClasses }}">{{ old('bio') }}</textarea>
                    <label class="{{ $labelClasses }}">Bio</label>
                    @error('bio')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
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

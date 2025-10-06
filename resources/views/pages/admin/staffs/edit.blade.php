@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-4 sm:p-6 md:p-8 dark:bg-gray-800">
        <h2 class="text-xl sm:text-2xl text-center mb-6 sm:mb-8">
            <span class="text-primary">Edit</span>
            <span class="dark:text-gray-300">Staff Member</span>
        </h2>

        <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data" data-upload>
            @csrf
            @method('PUT')

            @php
                $inputClasses =
                    'w-full font-sofia dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300 bg-white dark:bg-gray-800';

                $labelClasses = 'block text-sm text-gray-800 mb-2 dark:text-gray-300';
            @endphp

            <!-- Name Fields - Two Column Layout -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4">
                <div>
                    <label class="{{ $labelClasses }}">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $staff->first_name) }}"
                        class="{{ $inputClasses }}" required>
                    @error('first_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $staff->last_name) }}"
                        class="{{ $inputClasses }}" required>
                    @error('last_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4">
                <div>
                    <label class="{{ $labelClasses }}">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $staff->middle_name) }}"
                        class="{{ $inputClasses }}">
                    @error('middle_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Suffix (Optional)</label>
                    <select name="suffix" class="{{ $inputClasses }} appearance-none">
                        <option value="">None</option>
                        <option value="Jr." {{ old('suffix', $staff->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                        <option value="Sr." {{ old('suffix', $staff->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                        <option value="II" {{ old('suffix', $staff->suffix) == 'II' ? 'selected' : '' }}>II</option>
                        <option value="III" {{ old('suffix', $staff->suffix) == 'III' ? 'selected' : '' }}>III</option>
                        <option value="IV" {{ old('suffix', $staff->suffix) == 'IV' ? 'selected' : '' }}>IV</option>
                        <option value="V" {{ old('suffix', $staff->suffix) == 'V' ? 'selected' : '' }}>V</option>
                        <option value="VI" {{ old('suffix', $staff->suffix) == 'VI' ? 'selected' : '' }}>VI</option>
                        <option value="VII" {{ old('suffix', $staff->suffix) == 'VII' ? 'selected' : '' }}>VII</option>
                        <option value="VIII" {{ old('suffix', $staff->suffix) == 'VIII' ? 'selected' : '' }}>VIII
                        </option>
                        <option value="IX" {{ old('suffix', $staff->suffix) == 'IX' ? 'selected' : '' }}>IX</option>
                        <option value="X" {{ old('suffix', $staff->suffix) == 'X' ? 'selected' : '' }}>X</option>
                    </select>
                    @error('suffix')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Professional Info - Two Column Layout -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4">
                <div>
                    <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
                    <input type="text" name="credentials" value="{{ old('credentials', $staff->credentials) }}"
                        class="{{ $inputClasses }}">
                    @error('credentials')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Position</label>
                    <input type="text" name="position" value="{{ old('position', $staff->position) }}"
                        class="{{ $inputClasses }}">
                    @error('position')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bio - Full Width -->
            <div class="mb-4">
                <label class="{{ $labelClasses }}">Bio</label>
                <textarea name="bio" rows="4" class="{{ $inputClasses }} resize-none">{{ old('bio', $staff->bio) }}</textarea>
                @error('bio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Info - Two Column Layout -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4">
                <div>
                    <label class="{{ $labelClasses }}">Email</label>
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                        class="{{ $inputClasses }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Phone Number</label>
                    <input type="text" name="phone_num" value="{{ old('phone_num', $staff->phone_num) }}"
                        class="{{ $inputClasses }}">
                    @error('phone_num')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Photo Upload - Full Width -->
            <div class="mb-4">
                <label class="{{ $labelClasses }}">Photo</label>
                <input type="file" name="photo_path" accept="image/*"
                    class="{{ $inputClasses }} file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:cursor-pointer hover:file:bg-blue-500">

                @if ($staff->photo_path)
                    <div class="flex items-center space-x-4 mt-4">
                        <img src="{{ Storage::url($staff->photo_path) }}" alt="Current photo"
                            class="w-24 h-24 object-cover rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-300">Current Photo</p>
                            <p class="text-xs text-gray-400">Upload a new photo to replace</p>
                        </div>
                    </div>
                @endif

                @error('photo_path')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button - Full Width -->
            <div>
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-2 rounded-md shadow-primary-hover hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
@endsection

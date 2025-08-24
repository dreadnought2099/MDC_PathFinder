@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-2xl mx-auto border-2 border-primary rounded-lg p-4">

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit <span class="text-primary">Staff</span></h1>
                <p class="text-gray-600">Update staff member information</p>
            </div>

            <!-- Floating Actions -->
            <div class="mb-8">
                <x-floating-actions />
            </div>

            <!-- Form Card -->
            <div class="bg-white overflow-hidden">
                <div class="p-8">
                    <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data"
                        class="space-y-6" data-upload>
                        @csrf
                        @method('PUT')

                        <!-- First Name Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">First Name</label>
                            <input type="text" name="first_name" value="{{ old('name', $staff->first_name) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('name', $staff->middle_name) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('name', $staff->last_name) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Suffix Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Suffix</label>
                            <select name="suffix"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 
                   focus:border-primary focus:ring-1 focus:ring-primary 
                   focus:outline-none transition-colors duration-300">
                                <option value="">None</option>
                                <option value="Jr." {{ old('suffix', $staff->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.
                                </option>
                                <option value="Sr." {{ old('suffix', $staff->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.
                                </option>
                                <option value="II" {{ old('suffix', $staff->suffix) == 'II' ? 'selected' : '' }}>II
                                </option>
                                <option value="III" {{ old('suffix', $staff->suffix) == 'III' ? 'selected' : '' }}>III
                                </option>
                                <option value="IV" {{ old('suffix', $staff->suffix) == 'IV' ? 'selected' : '' }}>IV
                                </option>
                                <option value="V" {{ old('suffix', $staff->suffix) == 'V' ? 'selected' : '' }}>V
                                </option>
                                <option value="VI" {{ old('suffix', $staff->suffix) == 'VI' ? 'selected' : '' }}>VI
                                </option>
                                 <option value="VII" {{ old('suffix', $staff->suffix) == 'VII' ? 'selected' : '' }}>VII
                                </option>
                                 <option value="VIII" {{ old('suffix', $staff->suffix) == 'VIII' ? 'selected' : '' }}>VIII
                                </option>
                                 <option value="IX" {{ old('suffix', $staff->suffix) == 'IX' ? 'selected' : '' }}>IX
                                </option>
                                 <option value="X" {{ old('suffix', $staff->suffix) == 'X' ? 'selected' : '' }}>X
                                </option>
                            </select>

                            @error('suffix')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Credentials Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Credentials</label>
                            <input type="text" name="credentials" value="{{ old('credentials', $staff->credentials) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Email Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Position</label>
                            <input type="text" name="position" value="{{ old('position', $staff->position) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                        </div>

                        <!-- Phone Number Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Phone Number</label>
                            <input type="text" name="phone_num" value="{{ old('phone_num', $staff->phone_num) }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300">
                            @error('phone_num')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Bio</label>
                            <textarea name="bio" rows="4"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300 resize-none">{{ old('bio', $staff->bio) }}</textarea>
                        </div>

                        <!-- Photo Upload Field -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Photo</label>
                            <div class="space-y-4">
                                <input type="file" name="photo_path" accept="image/*"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:cursor-pointer hover:file:bg-blue-500">

                                @if ($staff->photo_path)
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ Storage::url($staff->photo_path) }}" alt="Current photo"
                                            class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-sm">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Current Photo</p>
                                            <p class="text-xs text-gray-600">Upload a new photo to replace</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-primary text-white hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 px-6 py-3 rounded-xl shadow-md hover:shadow-lg cursor-pointer">
                                Update Staff Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

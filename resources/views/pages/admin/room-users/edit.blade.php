@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10">
        <!-- Page Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-white">
                <span class="text-primary">Edit</span> Office User
            </h1>
            <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300">
                Update user details and assign them to a room.
            </p>
        </div>

        <!-- Edit User Form -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
            <form action="{{ route('room-user.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm 
                           focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50
                           dark:bg-gray-700 dark:text-gray-200" />
                    @error('name')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Username
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm 
                           focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50
                           dark:bg-gray-700 dark:text-gray-200" />
                    @error('username')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input type="password" name="password" id="password" placeholder="Enter new password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm 
               focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50
               dark:bg-gray-700 dark:text-gray-200" />
                    <small class="text-gray-500">Leave blank if you do not want to change the password.</small>
                    @error('password')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Confirm password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm 
               focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50
               dark:bg-gray-700 dark:text-gray-200" />
                </div>

                <!-- Room Selection -->
                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Assign Room
                    </label>
                    <select name="room_id" id="room_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm 
               focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50
               dark:bg-gray-700 dark:text-gray-200">
                        <option value="" {{ $user->room_id === null ? 'selected' : '' }}>No Room Assigned</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ $user->room_id == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('room-user.index') }}"
                        class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-primary text-white hover:bg-blue-700 transition shadow">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

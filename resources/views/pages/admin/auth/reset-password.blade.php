@extends('layouts.admin-auth')

@section('title', 'Reset Password')
@section('header', 'Reset Password')

@section('content')
    <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Email Address
            </label>
            <input type="email" name="email" id="email" required
                class="font-sofia w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                New Password
            </label>
            <input type="password" name="password" id="password" required minlength="8"
                class="font-sofia w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Confirm Password
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                class="font-sofia w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit"
            class="w-full bg-primary text-white py-2 sm:py-2.5 text-sm sm:text-base rounded-md hover:bg-white hover:text-primary border border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
            Reset Password
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-primary hover:underline text-sm">
                Back to Login
            </a>
        </div>
    </form>
@endsection
@extends('layouts.admin-auth')

@section('title', 'Forgot Password')
@section('header', 'Forgot Password')

@section('content')
    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

        <p class="text-gray-600 dark:text-gray-400 text-sm text-center mb-10">
            Enter your registered email address and we’ll send you a link to reset your password.
        </p>

        @php
            $inputClasses =
                'peer py-3 w-full placeholder-transparent rounded-md font-sofia text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none';
            $labelClasses =
                'absolute cursor-text left-0 -top-3 text-sm text-gray-600 font-sofia bg-white mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white peer-focus:px-2 peer-focus:rounded-md';
        @endphp

        <div class="relative">
            <input type="email" name="email" id="email" required autofocus placeholder="Email Address"
                class="{{ $inputClasses }}">
            <label for="email" class="{{ $labelClasses }}">
                Email Address
            </label>

        </div>

        <button type="submit"
            class="w-full bg-primary text-white py-2 sm:py-2.5 text-sm sm:text-base rounded-md hover:bg-white hover:text-primary border border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
            Send Password Reset Link
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-primary hover-underline text-sm">
                Back to Login
            </a>
        </div>
    </form>
@endsection

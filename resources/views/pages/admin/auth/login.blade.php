@extends('layouts.guest')

@section('body-class', 'bg-gradient-to-tr from-blue-400 to-white min-h-screen flex items-center justify-center')
@section('main-class', 'w-full flex items-center justify-center')

@section('content')
    <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-xl shadow-lg overflow-hidden bg-white">

        {{-- Left - Login --}}
        <div class="w-full md:w-1/2 p-8 space-y-6 flex flex-col justify-center">
            <h2 class="text-2xl text-center font-semibold">Log <span class="text-primary">In</span></h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                @php
                    $inputClasses =
                        'peer py-3 w-full placeholder-transparent rounded-md text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none';
                    $labelClasses =
                        'absolute cursor-text left-0 -top-3 text-sm text-gray-600 bg-white mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white peer-focus:px-2 peer-focus:rounded-md';
                @endphp

                <div class="relative">
                    <input type="email" id="email" name="email" placeholder="Email" required
                        class="{{ $inputClasses }}">
                    <label for="email" class="{{ $labelClasses }}">Email</label>
                </div>

                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Password" required
                        class="{{ $inputClasses }}">
                    <label for="password" class="{{ $labelClasses }}">Password</label>
                </div>

                <button type="submit"
                    class="w-full bg-primary text-white py-2 rounded-md hover:bg-white hover:text-primary border border-primary transition-all duration-300 cursor-pointer">
                    Log In
                </button>
            </form>
        </div>

        {{-- Right - Logo --}}
        <div
            class="w-full md:w-1/2 bg-gradient-to-tr from-blue-400 to-white text-white p-6 flex flex-col items-center justify-center">
            <img src="{{ asset('images/mdc-logo.png') }}" class="w-36 h-36 rounded-full" />
            <h3 class="text-lg text-center mt-4">{{ config('app.name') }} <br>Management System</h3>
        </div>
    </div>
@endsection
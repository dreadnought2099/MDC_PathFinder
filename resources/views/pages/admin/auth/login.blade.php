@extends('layouts.guest')

{{-- This tells the layout: "Hey! Instead of plain white, use my blue-to-white gradient." --}}
@section('body-class', 'bg-gradient-to-tr from-blue-400 to-white')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div
            class="w-full max-w-4xl flex flex-col md:flex-row rounded-xl shadow-lg overflow-hidden bg-white border border-primary">

            {{-- Left - Login --}}
            <div class="w-full md:w-1/2 p-8 space-y-6 flex flex-col justify-center">
                <h2 class="text-2xl text-center font-semibold">Log <span class="text-primary">In</span></h2>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    @php
                        $inputClasses =
                            'peer py-3 w-full placeholder-transparent rounded-md font-sofia text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none';
                        $labelClasses =
                            'absolute cursor-text left-0 -top-3 text-sm text-gray-600 font-sofia bg-white mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white peer-focus:px-2 peer-focus:rounded-md';
                    @endphp

                    <div class="relative">
                        <input type="text" id="login" name="login" placeholder="Email or Username" required
                            class="{{ $inputClasses }}">
                        <label for="login" class="{{ $labelClasses }}">Email or Username</label>
                    </div>

                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" type="password" id="password" name="password"
                            placeholder="Password" required class="{{ $inputClasses }}">
                        <label for="password" class="{{ $labelClasses }}">Password</label>

                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 hover:text-primary focus:primary focus:outline-none focus:ring-0">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.05 10.05 0 011.67-2.71M6.26 6.26A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.953 9.953 0 01-3.568 4.368M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white py-2 rounded-md hover:bg-white hover:text-primary border border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
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
    </div>
@endsection

@extends('layouts.guest')

@section('content')
    <div class="min-h-screen w-full flex justify-center items-center px-4 py-6">
        <form action="{{ route('login') }}" method="POST"
            class="bg-white w-[90%] max-w-xs p-4 rounded-lg shadow-lg border border-primary space-y-4">
            @csrf

            <h2 class="text-xl font-bold text-dark text-center">Log <span class="text-primary">in</span></h2>

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent rounded-md text-gray-700  ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none';
                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm text-gray-600 bg-white mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white peer-focus:px-2 peer-focus:rounded-md';
            @endphp


            <div class="relative">
                <input type="email" id="email" name="email" placeholder="Email" required class="{{ $inputClasses }}">
                <label for="email" class="{{ $labelClasses }}">Email</label>
            </div>

            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Password" required
                    class="{{ $inputClasses }}">
                <label for="password" class="{{ $labelClasses }}">Password</label>
            </div>

            {{-- <div class="text-right">
                <a href="{{ route('password.request') }}"
                    class="text-xs text-dark hover:text-primary hover-underline-hyperlink">Forgot your password?</a>
            </div> --}}

            <button type="submit"
                class="w-full bg-primary text-white font-medium py-2 rounded-lg transition hover:bg-white hover:text-primary border hover:border-primary cursor-pointer">
                Log in
            </button>
        </form>
    </div>
@endsection
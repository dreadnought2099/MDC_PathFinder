@extends('layouts.guest')

@section('body-class', 'bg-gradient-to-tr from-blue-400 to-white min-h-screen flex items-center justify-center')
@section('main-class', 'w-full flex items-center justify-center')

@section('content')
    <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-xl shadow-lg overflow-hidden bg-white">

        {{-- Left - Login --}}
        <div class="w-full md:w-1/2 p-8 space-y-4">
            <h2 class="text-2xl text-center">Log In</h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="email" placeholder="Email"
                    class="w-full px-4 py-2 border border-primary rounded-md focus:outline-none" required>
                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-2 border border-primary rounded-md focus:outline-none" required>
                <button type="submit"
                    class="w-full bg-primary text-white py-2 rounded-md hover:bg-white hover:text-primary border border-primary transition-all duration-300 cursor-pointer">Log In</button>
            </form>
        </div>

        {{-- Right - Logo --}}
        <div class="w-full md:w-1/2 bg-gradient-to-tr from-blue-400 to-white text-white p-6 flex flex-col items-center justify-center">
            <img src="{{ asset('images/mdc-logo.png') }}" class="w-36 h-36 rounded-full" />
            <h3 class="text-lg text-center">MDC CampusLens<br>Management System</h3>
        </div>
    </div>
@endsection

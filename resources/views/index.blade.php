@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col justify-between px-4 py-6 dark:bg-gray-900">
        <!-- Top bar -->
        <div class="relative w-full">
            <!-- Toggle button (top-right) -->
            <div class="absolute top-0 right-0">
                <x-dark-mode-toggle />
            </div>

            <!-- Welcome message (top-center) -->
            <div class="text-center mt-18">
                <h3 class="text-3xl font-semibold dark:text-gray-300">Welcome to</h3>
                <h1 class="text-5xl font-bold mt-2">
                    <span class="text-primary">{{ config('app.name') }}</span>
                </h1>
            </div>
        </div>

        <!-- Bottom button -->
        <div class="flex justify-center pb-6">
            <a href="{{ route('scan.index') }}">
                <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                    class="w-10 h-10 hover:scale-110 transition-all duration-300 ease-in-out" title="Scan Office">
            </a>
        </div>
    </div>
@endsection

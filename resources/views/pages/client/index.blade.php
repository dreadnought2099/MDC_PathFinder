@extends('layouts.guest')

@section('content')
    <div class="min-h-screen bg-white dark:bg-gray-900 flex flex-col">

        <!-- Sticky Top Bar -->
        <div
            class="w-full flex justify-between items-center p-4 dark:border-primary bg-white dark:bg-gray-900 sticky top-0 z-50">
            <!-- Left (empty for index) -->
            <div></div>

            <!-- Right: About + Dark Mode -->
            <div class="flex items-center space-x-4">
                <x-about-page />
                <x-dark-mode-toggle />
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-grow justify-between items-center px-4 sm:px-6 lg:px-8">
            <!-- Top: Welcome Message -->
            <div class="text-center mt-16 sm:mt-20 lg:mt-24 xl:mt-28 px-4 sm:px-6 lg:px-8">
                <h3
                    class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-semibold dark:text-gray-300 mb-3 sm:mb-4 lg:mb-6">
                    Welcome to
                </h3>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight">
                    <span class="text-primary">{{ config('app.name') }}</span>
                </h1>
            </div>

            <!-- Floating QR Code -->
            <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                title="Scan office to know more" />

            <!-- Bottom: Navigation Button -->
            <div class="flex justify-center mb-12 sm:mb-16 lg:mb-20">
                <a href="{{ route('paths.select') }}"
                    class="relative group inline-flex items-center justify-center 
                      p-4 sm:p-5 lg:p-6 rounded-full bg-primary text-white 
                      border-2 border-primary shadow-lg hover:shadow-xl
                      hover:bg-white hover:text-primary dark:bg-gray-800 dark:hover:bg-gray-800
                      transition-all duration-300 ease-in-out shadow-primary-hover">
                    <span class="text-lg sm:text-xl">Start Navigation</span>
                    <span
                        class="hidden lg:flex absolute right-full mr-3 px-3 py-1.5 rounded-md 
                 bg-gray-800 text-white text-sm shadow-md opacity-0 group-hover:opacity-100 
                 transition-opacity duration-300 whitespace-nowrap">
                        Click to start navigation inside the MDC Campus
                    </span>
                </a>
            </div>
        </div>

    </div>
@endsection
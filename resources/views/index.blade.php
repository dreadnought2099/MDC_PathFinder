@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col justify-between px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 dark:bg-gray-900">
        <!-- Top bar -->
        <div class="relative w-full flex-shrink-0">
            <!-- Toggle button (top-right) -->
            <div class="absolute top-0 right-0 z-10">
                <x-dark-mode-toggle />
            </div>

            <!-- Welcome message (top-center) -->
            <div class="text-center mt-12 sm:mt-16 lg:mt-20 xl:mt-24 px-4 sm:px-6 lg:px-8">
                <h3 class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-semibold dark:text-gray-300 mb-3 sm:mb-4 lg:mb-6">
                    Welcome to
                </h3>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight">
                    <span class="text-primary">{{ config('app.name') }}</span>
                </h1>
            </div>
        </div>

        <!-- Center spacer for perfect vertical centering -->
        <div class="flex-grow flex items-center justify-center min-h-0">
            <div class="w-full max-w-md text-center px-4">
                <!-- This space can be used for additional content if needed -->
                <!-- Currently empty to maintain the original minimal design -->
            </div>
        </div>

        <!-- Bottom button -->
        <div class="flex justify-center pb-4 sm:pb-6 lg:pb-8 xl:pb-12 flex-shrink-0">
            <a href="{{ route('scan.index') }}" 
               class="group inline-flex items-center shadow-primary-hover justify-center p-3 sm:p-4 lg:p-5 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out border-2 border-primary hover:border-primary">
                <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                    class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 xl:w-14 xl:h-14 group-hover:scale-110 transition-all duration-300 ease-in-out filter group-hover:brightness-110" 
                    title="Scan Office">
            </a>
        </div>
    </div>
@endsection
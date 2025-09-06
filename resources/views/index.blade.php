@extends('layouts.guest')

@section('content')
    <div class="absolute top-6 right-6 z-10">
        <x-dark-mode-toggle />
    </div>

    <div class="min-h-screen flex flex-col justify-between px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 dark:bg-gray-900">
        <!-- Welcome message (top-center) -->
        <div class="text-center mt-12 sm:mt-16 lg:mt-20 xl:mt-24 px-4 sm:px-6 lg:px-8">
            <h3 class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-semibold dark:text-gray-300 mb-3 sm:mb-4 lg:mb-6">
                Welcome to
            </h3>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight">
                <span class="text-primary">{{ config('app.name') }}</span>
            </h1>
        </div>

        <!-- Center spacer for perfect vertical centering -->
        <div class="flex-grow flex items-center justify-center min-h-0">
            <div class="w-full max-w-md text-center px-4">
                <!-- This space can be used for additional content if needed -->
                <!-- Currently empty to maintain the original minimal design -->
            </div>
        </div>

        <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
            title="Scan office to know more" />

        <div class="flex justify-center pb-2 sm:pb-6 lg:pb-8 xl:pb-12 flex-shrink-0">
            <a href="{{ route('paths.select') }}" title="Click to start navigation inside the MDC Campus"
                class="group inline-flex items-center shadow-primary-hover justify-center 
              p-3 sm:p-4 lg:p-5 rounded-full bg-primary text-white hover:bg-white hover:text-primary dark:hover:bg-gray-800 dark:bg-gray-800 
              shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out 
              border-2 border-primary hover:border-primary">
                <span class="text-base sm:text-lg md:text-lg lg:text-base xl:text-lg">
                    Start Navigation
                </span>
            </a>
        </div>
    </div>
@endsection

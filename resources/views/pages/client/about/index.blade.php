@extends('layouts.guest')

@section('content')
    @php
        // Fetch and decode the JSON content from the file
        $jsonContent = file_get_contents(public_path('data/about.json'));
        $aboutData = json_decode($jsonContent, true);
    @endphp

    <div class="min-h-screen dark:bg-gray-900 flex flex-col">

        <!-- Top Bar -->
        <div
            class="w-full flex items-center p-4 dark:border-b border-b-primary dark:border-b-primary bg-white dark:bg-gray-900 sticky top-0 z-50">

            <!-- Left: fixed width container for back button -->
            <div class="w-48 flex items-center">
                <!-- Back button always present on About page -->
                <a href="{{ route('index') }}"
                    class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                    <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="font-medium">Back to Home</span>
                </a>
            </div>

            <!-- Center spacer -->
            <div class="flex-1"></div>

            <!-- Right: fixed width container for About + Dark Mode -->
            <div class="w-48 flex items-center">
                <!-- Slot 1: About Page -->
                <div class="flex-1 flex justify-end">
                    <x-about-page />
                </div>

                <!-- Slot 2: Dark Mode Toggle -->
                <div class="flex-1 flex justify-end">
                    <x-dark-mode-toggle />
                </div>
            </div>
        </div>


        <x-floating-q-r href="{{ route('scan.index') }}" icon="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
            title="Scan office to know more" />

        <div class="min-h-[70vh] flex flex-col items-center justify-center space-y-12 px-6 md:px-16 py-12">
            @foreach (['about' => 'What is <span class="text-primary">' . config('app.name') . '</span>?', 'overview' => '<span class="text-primary">Overview</span> Content'] as $key => $title)
                <div class="w-full max-w-3xl mx-auto md:px-8">
                    <h1
                        class="text-3xl md:text-4xl font-bold text-dark leading-snug dark:text-gray-300 text-center md:text-left">
                        {!! $title !!}
                    </h1>
                    <div class="mt-6 md:mt-8 space-y-6 text-lg text-gray-700 md:text-left text-center">
                        @foreach ($aboutData[$key]['content'] as $content)
                            <p class="italic font-sofia leading-relaxed md:leading-loose tracking-wide dark:text-gray-300">
                                {{ $content }}
                            </p>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

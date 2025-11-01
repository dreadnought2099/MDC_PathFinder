@extends('layouts.guest')

@section('title', 'MDC PathFinder')
@section('description', 'Welcome to MDC PathFinder, the interactive web-based navigation system for the MDC campus.')
@section('og_title', 'MDC PathFinder')
@section('og_description', 'Easily locate offices, scan QR codes, and navigate the MDC campus with MDC PathFinder.')
@section('twitter_title', 'MDC PathFinder')
@section('twitter_description', 'Navigate the MDC campus easily with MDC PathFinder.')

@section('content')
    <div
        class="min-h-screen dark:bg-gray-900 flex flex-col relative overflow-hidden bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">

        <!-- Animated SVG Background -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-30 dark:opacity-20"
            xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(59, 130, 246);stop-opacity:0.3" />
                    <stop offset="100%" style="stop-color:rgb(147, 51, 234);stop-opacity:0.3" />
                </linearGradient>
            </defs>
            <circle class="svg-circle-1" cx="10%" cy="20%" r="100" fill="url(#grad1)" />
            <circle class="svg-circle-2" cx="90%" cy="80%" r="150" fill="url(#grad1)" />
            <circle class="svg-circle-3" cx="50%" cy="50%" r="80" fill="url(#grad1)" />

            <!-- Animated paths -->
            <path class="svg-path-1" d="M 0 100 Q 250 50 500 100" stroke="rgba(59, 130, 246, 0.2)" stroke-width="2"
                fill="none" />
            <path class="svg-path-2" d="M 500 200 Q 750 150 1000 200" stroke="rgba(147, 51, 234, 0.2)" stroke-width="2"
                fill="none" />
        </svg>

        <!-- Top Bar -->
        <div
            class="w-full flex items-center justify-between p-6 lg:p-8 sticky top-0 z-50 nav-bar backdrop-blur-md bg-white/70 dark:bg-gray-900/70">
            <div class="flex-1"></div>
            <div class="flex items-center gap-4 lg:gap-6 nav-items">
                <x-team-modal />
                <x-about-page />
                <x-dark-mode-toggle />
            </div>
        </div>

        <!-- Main Content - Centered -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 sm:px-8 lg:px-12 py-12 relative z-10">

            <!-- Hero Section -->
            <div class="max-w-4xl mx-auto text-center space-y-8 lg:space-y-12">

                <!-- Welcome Badge -->
                <div
                    class="welcome-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 dark:bg-primary/20 border border-primary/20 opacity-0">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <span class="text-sm font-medium text-primary">Welcome to Campus Navigation</span>
                </div>

                <!-- Main Title -->
                <div class="space-y-4 lg:space-y-6">
                    <h1 class="main-title text-4xl sm:text-5xl lg:text-7xl font-bold leading-tight opacity-0">
                        <span class="block text-gray-800 dark:text-gray-100">Navigate</span>
                        <span class="block text-primary bg-clip-text">{{ config('app.name') }}</span>
                    </h1>
                    <p
                        class="subtitle text-lg sm:text-xl lg:text-2xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto opacity-0 font-light">
                        Find any office, department, or location on campus in seconds
                    </p>
                </div>

                <!-- CTA Button -->
                <div class="cta-button opacity-0 pt-4 lg:pt-8">
                    <a href="{{ route('paths.select') }}"
                        class="group inline-flex items-center gap-3 px-8 lg:px-10 py-4 lg:py-5 
                               bg-primary text-white rounded-full text-lg lg:text-xl font-semibold
                               shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/40
                               transition-all duration-300 hover:scale-105">
                        <span>Start Navigation</span>
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <!-- Feature Pills -->
                <div class="features-pills flex flex-wrap justify-center gap-3 lg:gap-4 pt-8 lg:pt-12 opacity-0">
                    <div
                        class="feature-pill flex items-center gap-2 px-4 lg:px-5 py-2 lg:py-3 rounded-full bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-700">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span class="text-sm lg:text-base font-medium text-gray-700 dark:text-gray-300">Interactive
                            Maps</span>
                    </div>
                    <div
                        class="feature-pill flex items-center gap-2 px-4 lg:px-5 py-2 lg:py-3 rounded-full bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-700">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span class="text-sm lg:text-base font-medium text-gray-700 dark:text-gray-300">QR Scanning</span>
                    </div>
                    <div
                        class="feature-pill flex items-center gap-2 px-4 lg:px-5 py-2 lg:py-3 rounded-full bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-700">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-sm lg:text-base font-medium text-gray-700 dark:text-gray-300">Instant
                            Access</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bottom Decoration -->
        <div
            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-primary via-purple-500 to-primary opacity-50">
        </div>
    </div>
@endsection

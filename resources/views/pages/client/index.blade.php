@extends('layouts.guest')

@section('title', 'MDC PathFinder')
@section('description', 'Welcome to MDC PathFinder, the interactive web-based navigation system for the MDC campus.')
@section('og_title', 'MDC PathFinder')
@section('og_description', 'Easily locate offices, scan QR codes, and navigate the MDC campus with MDC PathFinder.')
@section('twitter_title', 'MDC PathFinder')
@section('twitter_description', 'Navigate the MDC campus easily with MDC PathFinder.')

@section('content')
    <div
        class="min-h-screen moving-gradient flex flex-col relative overflow-hidden bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">

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

        <!-- Main Content - Centered -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 sm:px-8 lg:px-12 py-12 relative z-10">

            <!-- Hero Section -->
            <div class="max-w-4xl mx-auto text-center space-y-8 lg:space-y-12">
                <!-- Main Title -->
                <div class="space-y-4 lg:space-y-6">
                    <h1 class="main-title text-4xl sm:text-5xl lg:text-7xl font-bold leading-tight opacity-0">
                        <span class="block text-gray-800 dark:text-gray-100">Welcome to</span>
                        <span class="block text-primary bg-clip-text">{{ config('app.name') }}</span>
                    </h1>
                    <p
                        class="subtitle font-sofia text-lg sm:text-xl lg:text-2xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto opacity-0 font-light">
                        Navigate MDC campus with ease.
                    </p>
                </div>

                <!-- CTA Button -->
                <div class="cta-button opacity-0 pt-4 lg:pt-8 relative inline-block group">
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

                    <div
                        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                                whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                        Click to Start

                        <div
                            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                                    border-t-4 border-t-transparent 
                                    border-b-4 border-b-transparent">
                        </div>
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

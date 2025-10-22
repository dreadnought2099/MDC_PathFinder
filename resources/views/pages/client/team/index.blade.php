@extends('layouts.guest')

@section('title', 'Meet the Team - MDC PathFinder')
@section('description', 'Meet the amazing team behind MDC PathFinder and learn about their roles and contributions.')
@section('og_title', 'Meet the Team - MDC PathFinder')
@section('og_description', 'Discover the talented team behind MDC PathFinder and their roles in building this campus navigation system.')
@section('twitter_title', 'Meet the Team - MDC PathFinder')
@section('twitter_description', 'Learn about the team members who developed MDC PathFinder.')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div
            class="w-full flex items-center p-4 bg-white dark:bg-gray-900 sticky top-0 z-50">

            <div class="w-48 flex items-center">
                <!-- Left: Back button -->
                <a href="{{ route('index') }}"
                    class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                    <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="font-medium">Back to Home</span>
                </a>
            </div>

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

        <h1 class="text-4xl lg:text-5xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 text-center mt-8">
            Meet the <span class="text-primary">Team</span>
        </h1>

        <!-- Team Section -->
        <main class="flex-1 max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg" alt="Profile"
                        class="w-36 h-36 group  rounded-full object-cover border border-primary group-hover:scale-110 transition-transform duration-300 ease-in-out">

                    <h2 class="mt-6 text-lg font-semibold text-gray-600 dark:text-gray-300">
                        Joshua A. Salabe
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        BSIT 4
                    </p>

                    <div class="mt-4 flex space-x-5">
                        <a href="https://www.instagram.com/skerm_art/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg" alt="Profile"
                        class="w-36 h-36 group  rounded-full object-cover border border-primary group-hover:scale-110 transition-transform duration-300 ease-in-out">

                    <h2 class="mt-6 text-lg font-semibold text-gray-600 dark:text-gray-300">
                        Chris Marie Calesa
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        BSIT 4
                    </p>

                    <div class="mt-4 flex space-x-5">
                        <a href="https://www.instagram.com/skerm_art/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg" alt="Profile"
                        class="w-36 h-36 group  rounded-full object-cover border border-primary group-hover:scale-110 transition-transform duration-300 ease-in-out">

                    <h2 class="mt-6 text-lg font-semibold text-gray-600 dark:text-gray-300">
                        Joana Jean Astacaan
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        BSIT 4
                    </p>

                    <div class="mt-4 flex space-x-5">
                        <a href="https://www.instagram.com/skerm_art/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg" alt="Profile"
                        class="w-36 h-36 group  rounded-full object-cover border border-primary group-hover:scale-110 transition-transform duration-300 ease-in-out">

                    <h2 class="mt-6 text-lg font-semibold text-gray-600 dark:text-gray-300">
                        Raymart E. Magallanes
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        BSIT 4
                    </p>

                    <div class="mt-4 flex space-x-5">
                        <a href="https://www.instagram.com/skerm_art/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png" alt="Letterboxd Logo"
                                class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

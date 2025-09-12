@extends('layouts.guest')

@section('content')
    <div class="min-h-screen bg-white dark:bg-gray-900 flex flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-center items-center relative">
                <!-- Centered Title -->
                <h1 class="text-4xl lg:text-5xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 text-center mt-8">
                    Meet the <span class="text-primary">Team</span>
                </h1>

                <!-- Dark Mode Toggle (kept at top-right using absolute positioning) -->
                <div class="absolute right-6">
                    <x-dark-mode-toggle />
                </div>
            </div>
        </header>

        <!-- Team Section -->
        <main class="flex-1 max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="{{ asset('images/profile.jpeg') }}" alt="Profile"
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
                            <img src="{{ asset('icons/instagram.png') }}" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('icons/letterboxd.png') }}" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="{{ asset('images/profile.jpeg') }}" alt="Profile"
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
                            <img src="{{ asset('icons/instagram.png') }}" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('icons/letterboxd.png') }}" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="{{ asset('images/profile.jpeg') }}" alt="Profile"
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
                            <img src="{{ asset('icons/instagram.png') }}" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('icons/letterboxd.png') }}" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>

                <div
                    class="group card-shadow-hover bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-8 flex flex-col items-center text-center shadow-primary-hover cursor-pointer">
                    <img src="{{ asset('images/profile.jpeg') }}" alt="Profile"
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
                            <img src="{{ asset('icons/instagram.png') }}" alt="IG Logo" class="w-8 h-8 contain">
                        </a>
                        <a href="https://letterboxd.com/RMAGALLANEZ/" class="hover:scale-120 duration-300 transition-all"
                            target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('icons/letterboxd.png') }}" alt="Letterboxd Logo" class="w-8 h-8 contain">
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

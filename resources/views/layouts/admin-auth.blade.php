<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Auth') | {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function() {
            const theme = localStorage.getItem("theme");
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (theme === "dark" || (!theme && prefersDark)) {
                document.documentElement.classList.add("dark");
            } else {
                document.documentElement.classList.remove("dark");
            }
        })();
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #464c58;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9896a2;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png">
    <script src="https://unpkg.com/alpinejs@3.15.0/dist/cdn.min.js" defer></script>

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 sm:p-6 p-4">

    {{-- Container --}}
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border-2 border-primary">
        <div class="text-center mb-6">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png" alt="MDC Logo"
                class="w-16 h-16 mx-auto mb-3">
            <h1 class="text-2xl text-primary text-gray-800 dark:text-gray-100">
                @yield('header', 'Admin Portal')
            </h1>
        </div>

        {{-- Flash messages --}}
        @if (session('status'))
            <div
                class="mb-4 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-3 rounded-lg text-center">
                {{ session('status') }}
            </div>
        @elseif (session('error'))
            <div
                class="mb-4 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3 rounded-lg text-center">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>

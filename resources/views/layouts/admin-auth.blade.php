<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | @yield('title', 'Admin Auth')</title>
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
        <div id="success-message-container" class="fixed top-4 right-4 z-[9999] max-w-md">
            @if (session('success') || session('error') || session('info') || session('warning') || $errors->any())
                @php
                    $messageType = 'info';
                    $message = '';

                    if (session('success')) {
                        $messageType = 'success';
                        $message = session('success');
                    } elseif (session('error')) {
                        $messageType = 'error';
                        $message = session('error');
                    } elseif (session('warning')) {
                        $messageType = 'warning';
                        $message = session('warning');
                    } elseif (session('info')) {
                        $messageType = 'info';
                        $message = session('info');
                    } elseif ($errors->any()) {
                        $messageType = 'error';
                        $message = $errors->first();
                    }

                    $colors = [
                        'success' => 'bg-green-500 border-green-600',
                        'error' => 'bg-red-500 border-red-600',
                        'warning' => 'bg-yellow-500 border-yellow-600',
                        'info' => 'bg-blue-500 border-blue-600',
                    ];
                @endphp

                <div id="flash-message"
                    class="text-white px-6 py-4 rounded-lg shadow-2xl border-l-4 transform transition-all duration-300 ease-in-out {{ $colors[$messageType] }}"
                    style="opacity: 0; transform: translateX(100%);">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if ($messageType === 'success')
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/success.png"
                                    alt="Success Icon" class="w-8 h-8 object-contain">
                            @elseif ($messageType === 'error')
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                                    alt="Error Icon" class="w-8 h-8 object-contain">
                            @elseif ($messageType === 'warning')
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png"
                                    alt="Warning Icon" class="w-8 h-8 object-contain">
                            @else
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/information.png"
                                    alt="Information Icon" class="w-8 h-8 object-contain">
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-sm break-words">{{ $message }}</p>
                            @if ($errors->count() > 1)
                                <ul class="mt-2 text-xs space-y-1">
                                    @foreach ($errors->all() as $error)
                                        @if (!$loop->first)
                                            <li class="break-words">• {{ $error }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                            class="flex-shrink-0 ml-2 hover:bg-white/20 rounded p-1 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        // Animate in the flash message
                        setTimeout(() => {
                            const msg = document.getElementById('flash-message');
                            if (msg) {
                                msg.style.opacity = '1';
                                msg.style.transform = 'translateX(0)';
                            }
                        }, 10);

                        // Auto remove after 5 seconds
                        setTimeout(() => {
                            const msg = document.getElementById('flash-message');
                            if (msg) {
                                msg.style.opacity = '0';
                                msg.style.transform = 'translateX(100%)';
                                setTimeout(() => msg.remove(), 300);
                            }
                        }, 5000);
                    </script>
                @endpush
            @endif
        </div>

        @yield('content')
    </div>

    @stack('scripts')

    <div class="cursor-dot fixed pointer-events-none rounded-full z-[9999]"></div>
    <div class="cursor-outline fixed pointer-events-none rounded-full z-[9999]"></div>

    <div class="cursor-particles fixed pointer-events-none z-[9998]"></div>
</body>

</html>

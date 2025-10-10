<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <!-- Dark mode initialization -->
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

    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8"></script>
    <link rel="icon" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png">

    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    <style>
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #157ee1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #464c58;
            border-radius: 10px;
            transition: background-color 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9896a2;
        }

        ::-webkit-scrollbar-corner {
            background: #157ee1;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

{{-- If child page provides "body-class" section, use it; otherwise fallback --}}

<body class="@yield('body-class', 'bg-white dark:bg-gray-900')">
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
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        @elseif ($messageType === 'error')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @elseif ($messageType === 'warning')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
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

    <main>
        @yield('content')
    </main>

    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    @stack('scripts')

    {{-- showTemporaryMessage --}}
    <script>
        window.showTemporaryMessage = function(message, type = 'info') {
            // Remove any existing messages
            const existing = document.getElementById('temp-message');
            if (existing) {
                existing.remove();
            }

            // Define colors based on type
            const colors = {
                success: 'bg-green-500 border-green-600',
                error: 'bg-red-500 border-red-600',
                warning: 'bg-yellow-500 border-yellow-600',
                info: 'bg-blue-500 border-blue-600'
            };

            // Define icons based on type
            const icons = {
                success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                warning: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            // Create the message element
            const messageDiv = document.createElement('div');
            messageDiv.id = 'temp-message';
            messageDiv.className =
                `fixed top-4 right-4 z-[9999] ${colors[type] || colors.info} text-white px-6 py-4 rounded-lg shadow-2xl border-l-4 transform transition-all duration-300 ease-in-out max-w-md`;
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateX(100%)';

            messageDiv.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${icons[type] || icons.info}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-2 hover:bg-white/20 rounded p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(messageDiv);

            // Animate in
            setTimeout(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateX(0)';
            }, 10);

            // Auto remove after 5 seconds
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateX(100%)';
                setTimeout(() => messageDiv.remove(), 300);
            }, 5000);
        }
    </script>
</body>

</html>

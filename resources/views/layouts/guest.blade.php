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
    <div id="success-message-container" class="absolute top-24 right-4 z-49">
        @if (session('success') || session('error') || session('info') || $errors->any())
            <div id="message"
                class="p-3 rounded-md shadow-lg border-l-4
                    {{ session('success') ? 'bg-white border border-primary text-primary' : '' }}
                    {{ session('error') ? 'bg-red-100 text-red-700' : '' }}
                    {{ session('info') ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $errors->any() ? 'bg-red-100 text-red-700' : '' }}">

                {{-- Display session messages --}}
                @if (session('success'))
                    <p>{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @if (session('info'))
                    <p>{{ session('info') }}</p>
                @endif

                {{-- Display validation errors --}}
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>

            @push('scripts')
                <script>
                    setTimeout(() => {
                        const messageDiv = document.getElementById('message');
                        if (messageDiv) {
                            messageDiv.classList.add('opacity-0');
                            setTimeout(() => {
                                messageDiv.style.display = 'none';
                            }, 500);
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
</body>

</html>

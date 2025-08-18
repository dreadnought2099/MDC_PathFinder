<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('images/mdc-logo.png') }}">
</head>
<body class="@yield('body-class', 'bg-gray-100')">
    <div id="success-message-container" class="absolute top-24 right-4 z-50">
        @if (session('success'))
            <div class="message p-3 rounded-md shadow-lg border-l-4 bg-green-100 text-green-700">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="message p-3 rounded-md shadow-lg border-l-4 bg-red-100 text-red-700">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if (session('info'))
            <div class="message p-3 rounded-md shadow-lg border-l-4 bg-yellow-100 text-yellow-700">
                <p>{{ session('info') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="message p-3 rounded-md shadow-lg border-l-4 bg-red-100 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <script>
            setTimeout(() => {
                const messages = document.querySelectorAll('.message');
                messages.forEach(message => {
                    message.classList.add('opacity-0');
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        </script>
    </div>

    <main class="@yield('main-class', 'container mx-auto p-4')">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8"></script>
</body>
</html>
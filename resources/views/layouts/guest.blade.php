<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('images/pathfinder-logo.png')}}">
</head>

<body class="@yield('body-class')">

    {{-- Flash messages --}}
    <div id="success-message-container" class="absolute top-24 right-4 z-100">
        @if (session('success') || session('error') || session('info') || $errors->any())
            <div id="message"
                class="p-3 rounded-md shadow-lg border-l-4
                {{ session('success') ? 'bg-green-100 text-green-700' : '' }}
                {{ session('error') ? 'bg-red-100 text-red-700' : '' }}
                {{ session('info') ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $errors->any() ? 'bg-red-100 text-red-700' : '' }}">

                @if (session('success'))
                    <p>{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @if (session('info'))
                    <p>{{ session('info') }}</p>
                @endif
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>

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
        @endif
    </div>

    {{-- Page Content --}}
    <main class="@yield('main-class')">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
</body>

</html>

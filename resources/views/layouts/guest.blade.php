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

            <!-- Auto-hide message after 5 seconds with fade-out animation -->
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
    <main class="@yield('main-class', 'container mx-auto')">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8"></script>
</body>

</html>

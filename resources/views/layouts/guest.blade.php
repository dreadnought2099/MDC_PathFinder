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

    <main class="@yield('main-class', 'container mx-auto')">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8"></script>
</body>

</html>

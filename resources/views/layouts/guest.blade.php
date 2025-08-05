<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')

</head>
<body>
     {{-- Main Content --}}
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>    

    @stack('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
</body>
</html>
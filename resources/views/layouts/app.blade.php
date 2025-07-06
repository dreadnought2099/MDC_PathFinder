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
    @include('components.navbar')

     {{-- Main Content --}}
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>  
    
    <script src="//unpkg.com/alpinejs" defer></script>

     @yield('scripts')
</body>
</html>
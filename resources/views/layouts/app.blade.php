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
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">

    <!-- FilePond JS -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <script>
        FilePond.registerPlugin(); // optional if you're using plugins
        FilePond.parse(document.body);
    </script>

    @yield('scripts')
</body>

</html>

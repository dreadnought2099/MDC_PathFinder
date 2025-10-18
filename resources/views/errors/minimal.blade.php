<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="text-center">
        <div class="text-4xl font-bold text-gray-800 dark:text-gray-200">
            @yield('message')
        </div>
    </div>
</body>

</html>

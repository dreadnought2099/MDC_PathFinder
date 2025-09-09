<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</head>

<body class="antialiased">
    <div
        class="relative flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center pt-8 sm:pt-0">
                <div class="px-4 text-6xl sm:text-7xl md:text-8xl lg:text-9xl text-primary tracking-wider font-bold">
                    @yield('code')
                </div>
                <div class="mt-2 text-2xl text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    @yield('message')
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>

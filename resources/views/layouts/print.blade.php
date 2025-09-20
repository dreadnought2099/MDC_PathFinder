<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <!-- Theme script runs before CSS -->
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
    <link rel="icon" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png">
</head>

<body>
    <main class="py-10">
        @yield('content')
    </main>
</body>

</html>

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

    <!-- Custom Scrollbar -->
    <style>
        /* Modern thin scrollbar with custom color */
        ::-webkit-scrollbar {
            width: 8px;
            /* thin vertical scrollbar */
            height: 8px;
            /* thin horizontal scrollbar */
        }

        ::-webkit-scrollbar-track {
            background: transparent;
            /* minimal track */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #157ee1;
            /* your custom color */
            border-radius: 9999px;
            /* fully rounded */
            border: 2px solid transparent;
            /* padding effect */
            background-clip: content-box;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #0f73b8;
            /* slightly darker on hover */
            transform: scale(1.1);
            /* subtle enlarge effect */
        }

        ::-webkit-scrollbar-corner {
            background: transparent;
            /* hide corner */
        }

        /* Dark mode adjustments if needed */
        @media (prefers-color-scheme: dark) {
            ::-webkit-scrollbar-thumb {
                background-color: #157ee1;
                /* same color for dark mode or adjust */
            }

            ::-webkit-scrollbar-thumb:hover {
                background-color: #0f73b8;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png">
</head>

<body>
    <main class="py-10">
        @yield('content')
    </main>

    <div class="cursor-dot fixed pointer-events-none rounded-full z-[9999]"></div>
    <div class="cursor-outline fixed pointer-events-none rounded-full z-[9999]"></div>

    <div class="cursor-particles fixed pointer-events-none z-[9998]"></div>
</body>

</html>

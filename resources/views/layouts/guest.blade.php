<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Language" content="en">
    <meta name="author" content="Raymart Magallanes">
    <meta name="keywords" content="MDC PathFinder, campus navigation, MDC Tubigon, Bohol, office locator">
    <meta name="application-name" content="MDC PathFinder">
    <meta property="og:site_name" content="MDC PathFinder">
    <meta name="theme-color" content="#157ee1">

    <link rel="preload" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png" as="image" type="image/png">
    <title>{{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'MDC PathFinder helps students, staff, and visitors locate offices across the MDC campus through clear, accessible web-based navigation.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'MDC PathFinder helps students, staff, and visitors locate offices across the MDC campus through clear, accessible web-based navigation.')">
    <meta property="og:image" content="@yield('og_image', asset('images/pathfinder-bannerv2.png'))">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'MDC PathFinder helps students, staff, and visitors locate offices across the MDC campus through clear, accessible web-based navigation.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/pathfinder-bannerv2.png'))">

    <!-- Fonts & Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('images/pathfinder-bannerv2.png') }}" as="image">

    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    <!-- Vite compiled assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js & HTML5 QR Code -->
    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/html5-qrcode@2.3.8"></script>

    <!-- Dark mode initialization -->
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

    <!-- JSON-LD Schema -->
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => url('/') . '#organization',
                    'name' => 'MDC PathFinder',
                    'url' => url('/'),
                    'logo' => asset('images/mdc.png'),
                    'description' =>
                        'MDC PathFinder is a web-based platform that helps students, staff, and visitors easily locate offices across the MDC campus.',
                    'sameAs' => ['https://www.facebook.com/mdctubigon', 'https://www.tiktok.com/@theexemplarmdc'],
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'email' => 'mdc1983tub@gmail.com',
                        'contactType' => 'Customer Support',
                        'areaServed' => 'PH',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    'url' => url('/'),
                    'name' => 'MDC PathFinder',
                    'description' =>
                        'An interactive web-based navigation system for the MDC campus, accessible from any device.',
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/') . '?search={query}',
                        'query-input' => 'required name=query',
                    ],
                ],
                [
                    '@type' => 'WebApplication',
                    'name' => 'MDC PathFinder',
                    'operatingSystem' => 'All',
                    'applicationCategory' => 'Navigation',
                    'url' => url('/'),
                    'browserRequirements' => 'Requires JavaScript and a modern web browser',
                    'featureList' => ['Office locator', 'QR code scanning'],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'About', 'item' => url('/about')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Team', 'item' => url('/meet-the-team')],
                    ],
                ],
                [
                    '@type' => 'LocalBusiness',
                    'name' => 'MDC Campus',
                    'image' => asset('images/mdc.png'),
                    'url' => url('/'),
                    'telephone' => '+63 38 123 4567',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => 'Cabulijan',
                        'addressLocality' => 'Tubigon',
                        'addressRegion' => 'Bohol',
                        'postalCode' => '6329',
                        'addressCountry' => 'PH',
                    ],
                    'openingHours' => 'Mo-Fr 08:00-17:00',
                    'priceRange' => 'Free',
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => [
                        [
                            '@type' => 'Question',
                            'name' => 'What is MDC PathFinder?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' =>
                                    'MDC PathFinder is a campus navigation system that helps students, staff, and visitors easily locate offices and departments across the MDC campus.',
                            ],
                        ],
                        [
                            '@type' => 'Question',
                            'name' => 'Do I need an account to use MDC PathFinder?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => 'No account is required to use MDC PathFinder.',
                            ],
                        ],
                        [
                            '@type' => 'Question',
                            'name' => 'Can I access MDC PathFinder on mobile?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' =>
                                    'Yes, MDC PathFinder is fully responsive and works on all modern mobile devices.',
                            ],
                        ],
                    ],
                ],
                [
                    '@type' => 'WebPage',
                    'name' => 'MDC PathFinder - Campus Navigation System',
                    'url' => url()->current(),
                    'description' => 'Navigate the MDC campus easily using MDC PathFinder.',
                    'isPartOf' => ['@id' => url('/') . '#organization'],
                    'primaryImageOfPage' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/pathfinder-bannerv2.png'),
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
</head>

{{-- If child page provides "body-class" section, use it; otherwise fallback --}}

<body class="@yield('body-class', 'bg-white dark:bg-gray-900')">
    <div id="success-message-container" class="fixed top-4 right-4 z-[9999] max-w-md">
        @if (session('success') || session('error') || session('info') || session('warning') || $errors->any())
            @php
                $messageType = 'info';
                $message = '';

                if (session('success')) {
                    $messageType = 'success';
                    $message = session('success');
                } elseif (session('error')) {
                    $messageType = 'error';
                    $message = session('error');
                } elseif (session('warning')) {
                    $messageType = 'warning';
                    $message = session('warning');
                } elseif (session('info')) {
                    $messageType = 'info';
                    $message = session('info');
                } elseif ($errors->any()) {
                    $messageType = 'error';
                    $message = $errors->first();
                }

                $colors = [
                    'success' => 'bg-green-500 border-green-600',
                    'error' => 'bg-red-500 border-red-600',
                    'warning' => 'bg-yellow-500 border-yellow-600',
                    'info' => 'bg-blue-500 border-blue-600',
                ];
            @endphp

            <div id="flash-message"
                class="text-white px-6 py-4 rounded-lg shadow-2xl border-l-4 transform transition-all duration-300 ease-in-out {{ $colors[$messageType] }}"
                style="opacity: 0; transform: translateX(100%);">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if ($messageType === 'success')
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/success.png"
                                alt="Success Icon" class="w-8 h-8 object-contain">
                        @elseif ($messageType === 'error')
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png"
                                alt="Error Icon" class="w-8 h-8 object-contain">
                        @elseif ($messageType === 'warning')
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png"
                                alt="Warning Icon" class="w-8 h-8 object-contain">
                        @else
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/information.png"
                                alt="Information Icon" class="w-8 h-8 object-contain">
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm break-words">{{ $message }}</p>
                        @if ($errors->count() > 1)
                            <ul class="mt-2 text-xs space-y-1">
                                @foreach ($errors->all() as $error)
                                    @if (!$loop->first)
                                        <li class="break-words">• {{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="flex-shrink-0 ml-2 hover:bg-white/20 rounded p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            @push('scripts')
                <script>
                    // Animate in the flash message
                    setTimeout(() => {
                        const msg = document.getElementById('flash-message');
                        if (msg) {
                            msg.style.opacity = '1';
                            msg.style.transform = 'translateX(0)';
                        }
                    }, 10);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        const msg = document.getElementById('flash-message');
                        if (msg) {
                            msg.style.opacity = '0';
                            msg.style.transform = 'translateX(100%)';
                            setTimeout(() => msg.remove(), 300);
                        }
                    }, 5000);
                </script>
            @endpush
        @endif
    </div>

    <main>
        @yield('content')
    </main>

    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    @stack('scripts')

    {{-- showTemporaryMessage --}}
    <script>
        window.showTemporaryMessage = function(message, type = 'info') {
            // Remove any existing messages
            const existing = document.getElementById('temp-message');
            if (existing) {
                existing.remove();
            }

            // Define colors based on type
            const colors = {
                success: 'bg-green-500 border-green-600',
                error: 'bg-red-500 border-red-600',
                warning: 'bg-yellow-500 border-yellow-600',
                info: 'bg-blue-500 border-blue-600'
            };

            // Define icons based on type
            const icons = {
                success: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/success.png" alt="Success Icon" class="w-8 h-8 object-contain">',
                error: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning-red.png" alt="Error Icon" class="w-8 h-8 object-contain">',
                warning: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png" alt="Warning Icon" class="w-8 h-8 object-contain">',
                info: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/information.png" alt="Information Icon" class="w-8 h-8 object-contain">'
            };

            // Create the message element
            const messageDiv = document.createElement('div');
            messageDiv.id = 'temp-message';
            messageDiv.className =
                `fixed top-4 right-4 z-[9999] ${colors[type] || colors.info} text-white px-6 py-4 rounded-lg shadow-2xl border-l-4 transform transition-all duration-300 ease-in-out max-w-md`;
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateX(100%)';

            messageDiv.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${icons[type] || icons.info}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-2 hover:bg-white/20 rounded p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(messageDiv);

            // Animate in
            setTimeout(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateX(0)';
            }, 10);

            // Auto remove after 5 seconds
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateX(100%)';
                setTimeout(() => messageDiv.remove(), 300);
            }, 5000);
        }
    </script>

    <div class="cursor-dot fixed pointer-events-none rounded-full z-[9999]"></div>
    <div class="cursor-outline fixed pointer-events-none rounded-full z-[9999]"></div>

    <div class="cursor-particles fixed pointer-events-none z-[9998]"></div>
</body>

</html>

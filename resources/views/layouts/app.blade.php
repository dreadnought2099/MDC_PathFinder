<!DOCTYPE html>
{{-- Added x-data x-cloak to hide Alpine.js flash/jitter --}}
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }}</title>

    <!-- Theme script: must run before Tailwind CSS -->
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

    <!-- Alpine.js (pinned v3.14.1) -->
    <script src="https://unpkg.com/alpinejs@3.15.0/dist/cdn.min.js" defer></script>

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond@4.31.1/dist/filepond.min.css" rel="stylesheet">

    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    {{-- Cropper CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body class="flex flex-col min-h-screen">
    <!-- Flash Messages -->
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

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4">
        @yield('content')
    </main>

    <!-- Upload Progress Modal -->
    <x-upload-progress-modal />

    <!-- FilePond -->
    <script src="https://unpkg.com/filepond@4.31.1/dist/filepond.min.js"></script>
    <script>
        document.querySelectorAll('input[type="file"].filepond').forEach(input => {
            FilePond.create(input, {
                allowMultiple: input.hasAttribute('multiple'),
                storeAsFile: true
            });
        });
    </script>

    <!-- Upload Form Handler -->
    <script>
        document.querySelectorAll('form[data-upload]').forEach(form => {
            let filesCache = {}; // persistent cache per form

            // Cache files immediately when selected
            form.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', () => {
                    if (input.files.length) {
                        filesCache[input.name] = Array.from(input.files);
                    }
                });
            });

            form.addEventListener('submit', e => {
                e.preventDefault();

                const formData = new FormData(form);

                // Always re-attach cached files into FormData
                Object.keys(filesCache).forEach(name => {
                    filesCache[name].forEach(file => {
                        formData.append(name, file);
                    });
                });

                const xhr = new XMLHttpRequest();
                xhr.open(form.method, form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')
                    .content);

                // Show modal
                window.dispatchEvent(new CustomEvent('upload-start'));

                // Update progress
                xhr.upload.addEventListener('progress', (event) => {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        window.dispatchEvent(new CustomEvent('upload-progress', {
                            detail: {
                                progress: percent
                            }
                        }));
                    }
                });

                xhr.onload = function() {
                    // Hide modal
                    window.dispatchEvent(new CustomEvent('upload-finish'));

                    if (xhr.status === 422) {
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.errors) {
                                showErrors(form, json.errors);

                                // Restore previews from cached files
                                Object.keys(filesCache).forEach(name => {
                                    const preview = form.querySelector(`#${name}-preview`);
                                    if (preview) {
                                        preview.innerHTML = '';
                                        filesCache[name].forEach(file => {
                                            const reader = new FileReader();
                                            reader.onload = e => {
                                                const img = document.createElement(
                                                    'img');
                                                img.src = e.target.result;
                                                img.className =
                                                    "h-24 w-24 object-cover rounded-lg border-2 border-gray-300";
                                                preview.appendChild(img);
                                            };
                                            reader.readAsDataURL(file);
                                        });
                                    }
                                });

                                alert(
                                    "Validation failed. Please fix inputs — files are still attached."
                                );
                                return;
                            }
                        } catch (err) {
                            console.error("Validation parse error", err);
                        }
                    } else if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.redirect) {
                                window.location.href = json.redirect; // go to show page
                                return;
                            }
                        } catch (err) {
                            console.error("Success response parse error", err);
                        }
                        window.location.reload(); // fallback if no redirect returned
                    } else {
                        alert("Unexpected error, please try again.");
                    }
                };

                xhr.onerror = function() {
                    // Hide modal
                    window.dispatchEvent(new CustomEvent('upload-finish'));
                    alert("Network error, please try again.");
                };

                xhr.send(formData);
            });
        });

        // Helper: Show errors beside inputs
        function showErrors(form, errors) {
            // Clear old errors
            form.querySelectorAll('.error-message').forEach(el => el.remove());

            Object.keys(errors).forEach(name => {
                const input = form.querySelector(`[name="${name}"]`);
                if (input) {
                    const msg = document.createElement('div');
                    msg.className = 'error-message text-red-600 text-sm mt-1';
                    msg.innerText = errors[name][0];
                    input.closest('div').appendChild(msg);
                }
            });
        }
    </script>

    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    {{-- Cropper JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    {{-- Day.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.10/dayjs.min.js"></script>

    @stack('scripts')

    <!-- Spinner -->
    <script>
        // Show overlay spinner
        window.showSpinner = function() {
            if (!document.getElementById("loading")) {
                document.body.insertAdjacentHTML("beforeend",
                    '<div id="loading" class="fixed inset-0 flex items-center justify-center z-50">' +
                    '<div class="animate-spin border-4 border-blue-200 border-t-blue-600 rounded-full w-10 h-10"></div>' +
                    '</div>');
            }
        };

        // Hide overlay spinner
        window.hideSpinner = function() {
            const el = document.getElementById("loading");
            if (el) el.remove();
        };

        // Existing bindings (optional, keep if needed)
        document.querySelectorAll('#sort-form select').forEach(sel =>
            sel.addEventListener('change', window.showSpinner)
        );

        const assignRoomForm = document.getElementById("assign-staff-form");
        if (assignRoomForm) {
            assignRoomForm.querySelector("select")?.addEventListener("change", () => {
                window.showSpinner();
                assignRoomForm.submit();
            });
        }

        document.getElementById("assignForm")?.addEventListener("submit", window.showSpinner);
        document.getElementById("unassignForm")?.addEventListener("submit", window.showSpinner);
    </script>

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

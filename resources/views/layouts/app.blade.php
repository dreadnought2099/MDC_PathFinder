<!DOCTYPE html>
{{-- Added x-data x-cloak to hide Alpine.js flash/jitter --}}
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Custom Scrollbar -->
    <style>
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #157ee1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #464c58;
            border-radius: 10px;
            transition: background-color 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9896a2;
        }

        ::-webkit-scrollbar-corner {
            background: #157ee1;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

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
    <div id="success-message-container" class="absolute top-24 right-4 z-50">
        @if (session('success') || session('error') || session('info') || session('warning') || $errors->any())
            <div id="message"
                class="p-3 rounded-md shadow-lg border-l-4
                    {{ session('success') ? 'bg-green-100 text-green-700 border-green-300 dark:bg-green-800 dark:text-green-200 dark:border-green-600' : '' }}
                    {{ session('error') ? 'bg-red-100 text-red-700 border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600' : '' }}
                    {{ session('info') ? 'bg-yellow-100 text-yellow-700 border-yellow-300 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-500' : '' }}
                    {{ session('warning') ? 'bg-orange-100 text-orange-700 border-orange-300 dark:bg-orange-800 dark:text-orange-200 dark:border-orange-600' : '' }}
                    {{ $errors->any() ? 'bg-red-100 text-red-700 border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600' : '' }}">
                @if (session('success'))
                    <p>{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @if (session('info'))
                    <p>{{ session('info') }}</p>
                @endif
                @if (session('warning'))
                    <p>{{ session('warning') }}</p>
                @endif
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>

            @push('scripts')
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('message');
                        if (msg) {
                            msg.classList.add('opacity-0');
                            setTimeout(() => msg.remove(), 500);
                        }
                    }, 5000);
                </script>
            @endpush
        @endif
    </div>

    <!-- Simple 2FA Modal Container -->
    <div x-data="{
        showOtpModal: {{ session('show_2fa_modal') ? 'true' : 'false' }},
        showRecoveryModal: {{ session('show_recovery_modal') ? 'true' : 'false' }}
    }" x-cloak>

        <!-- OTP Modal -->
        <div x-show="showOtpModal"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-2xl z-[60]">

            <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-full max-w-md mx-4 shadow-xl">
                <h2 class="text-lg font-bold text-primary text-center mb-4">Two-Factor Authentication</h2>

                <form method="POST" action="{{ route('admin.2fa.verify') }}" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                            class="w-full py-4 text-center text-lg rounded-md dark:text-gray-300 text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-200"
                            placeholder="Enter 6-digit code" required autofocus x-ref="otpInput">
                    </div>

                    @error('otp')
                        <p class="text-red-600 text-sm text-center">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-white hover:text-primary dark:hover:bg-gray-800 border-2 border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
                        Verify Code
                    </button>

                    <div class="text-center">
                        <button type="button" @click="showOtpModal = false; showRecoveryModal = true"
                            class="text-primary hover-underline text-sm cursor-pointer">
                            Use a recovery code instead
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recovery Code Modal -->
        <div x-show="showRecoveryModal"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-2xl z-[60]">

            <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-full max-w-md mx-4 shadow-xl">
                <h2 class="text-lg font-bold text-primary text-center mb-4">Use Recovery Code</h2>

                <form method="POST" action="{{ route('admin.2fa.recovery.verify') }}" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="recovery_code"
                            class="w-full py-4 text-center text-lg rounded-md dark:text-gray-300 text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-200"
                            placeholder="Enter recovery code" required x-ref="recoveryInput">
                    </div>

                    @error('recovery_code')
                        <p class="text-red-600 text-sm text-center">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-white hover:text-primary dark:hover:bg-gray-800 border-2 border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
                        Verify Recovery Code
                    </button>

                    <div class="text-center">
                        <button type="button" @click="showRecoveryModal = false; showOtpModal = true"
                            class="text-primary hover-underline text-sm cursor-pointer">
                            Back to OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                                    "Validation failed. Please fix inputs — files are still attached.");
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
</body>

</html>

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
    <link rel="icon" href="{{ asset('images/mdc-logo.png') }}">

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

    <div x-data="{ open: {{ session('show_2fa_modal') ? 'true' : 'false' }} }" x-show="open" @close-2fa-modal.window="open = false" x-cloak
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-51 dark:text-gray-300 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none">


        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md">
            <h2 class="text-lg font-bold text-center">Two-Factor Authentication</h2>

            <form id="twofa-form" method="POST" action="{{ route('admin.profile.2fa.verify') }}"
                class="mt-4 space-y-3">
                @csrf
                <input type="text" name="otp" maxlength="6"
                    class="w-full text-center px-4 py-3 border rounded-lg text-2xl tracking-widest
                      focus:ring focus:ring-primary focus:outline-none"
                    placeholder="123456" required autofocus>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-lg font-semibold">
                    Verify
                </button>
            </form>

            <div id="twofa-message" class="mt-3 text-center"></div>
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
        if (document.querySelector('.filepond')) FilePond.parse(document.body);
    </script>

    <!-- Upload Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function clearErrors(form) {
                form.querySelectorAll('.field-error').forEach(el => el.remove());
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            }

            function showErrors(form, errors) {
                for (let key in errors) {
                    let input = form.querySelector(`[name="${key}"], [name="${key}[]"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const small = document.createElement('small');
                        small.className = 'field-error text-red-600';
                        small.innerText = errors[key][0];
                        input.insertAdjacentElement('afterend', small);
                    }
                }
            }
            document.querySelectorAll('form[data-upload]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    clearErrors(form);
                    const xhr = new XMLHttpRequest();
                    const data = new FormData(form);
                    window.dispatchEvent(new CustomEvent('upload-start'));
                    const token = document.head.querySelector('meta[name="csrf-token"]')?.content;
                    xhr.open(form.method || 'POST', form.action);
                    if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable) {
                            window.dispatchEvent(new CustomEvent('upload-progress', {
                                detail: {
                                    progress: Math.round((e.loaded / e.total) *
                                        100)
                                }
                            }));
                        }
                    });
                    xhr.onload = () => {
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (json.redirect) return window.location.href = json.redirect;
                            } catch {}
                            return window.location.reload();
                        }
                        if (xhr.status === 422) {
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (json.errors) return showErrors(form, json.errors);
                            } catch {}
                        }
                        alert('Upload failed. Please try again.');
                    };
                    xhr.onerror = () => {
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        alert('Network error.');
                    };
                    xhr.send(data);
                });
            });
        });
    </script>

    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    {{-- Cropper JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    @stack('scripts')

    <!-- Spinner -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('twofa-form');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async res => {
                        let contentType = res.headers.get("content-type");
                        if (contentType && contentType.includes("application/json")) {
                            return res.json();
                        } else {
                            return {
                                success: false,
                                message: "Unexpected response (HTML instead of JSON)"
                            };
                        }
                    })
                    .then(data => {
                        let msg = document.getElementById('twofa-message');
                        if (data.success) {
                            msg.innerHTML = `<span class="text-tertiary">${data.message}</span>`;

                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('close-2fa-modal'));
                            }, 1000);
                        } else {
                            msg.innerHTML = `<span class="text-secondary">${data.message}</span>`;
                        }
                    })
                    .catch(err => console.error(err));
            });
        });

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

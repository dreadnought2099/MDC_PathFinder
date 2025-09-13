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
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond@4.31.1/dist/filepond.min.css" rel="stylesheet">

    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

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

    @stack('scripts')

    <!-- Spinner -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function showSpinner() {
                if (!document.getElementById("loading")) {
                    document.body.insertAdjacentHTML("beforeend",
                        '<div id="loading" class="fixed inset-0 flex items-center justify-center z-50">' +
                        '<div class="animate-spin border-4 border-blue-200 border-t-blue-600 rounded-full w-10 h-10"></div>' +
                        '</div>');
                }
            }
            document.querySelectorAll('#sort-form select').forEach(sel => sel.addEventListener('change',
                showSpinner));
            const assignRoomForm = document.getElementById("assign-staff-form");
            if (assignRoomForm) assignRoomForm.querySelector("select")?.addEventListener("change", () => {
                showSpinner();
                assignRoomForm.submit();
            });
            document.getElementById("assignForm")?.addEventListener("submit", showSpinner);
            document.getElementById("unassignForm")?.addEventListener("submit", showSpinner);
        });
    </script>
</body>

</html>
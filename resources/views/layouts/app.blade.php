<!DOCTYPE html>
{{-- Added x-data x-cloak to hide the jitter --}}
<html lang="en" class="bg-white dark:bg-gray-900" x-data x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
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
    <link rel="icon" href="{{ asset('images/mdc-logo.png') }}">

    <!-- Alpine.js CDN: Pinned to v3.14.1 for stability, loaded with defer to avoid render-blocking -->
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    <!-- FilePond CSS: Moved to <head> for proper rendering, pinned to v4.31.1 for stability -->
    <link href="https://unpkg.com/filepond@4.31.1/dist/filepond.min.css" rel="stylesheet">

    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    <!-- x-cloak CSS: Ensures Alpine.js components (e.g., navbar dropdown) are hidden until initialized -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Success/Error Message Container: Displays session messages or validation errors -->
    <div id="success-message-container" class="absolute top-24 right-4 z-49">
        @if (session('success') || session('error') || session('info') || session('warning') || $errors->any())
            <div id="message"
                class="p-3 rounded-md shadow-lg border-l-4
                    {{ session('success') ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-800 dark:text-green-200 dark:border-green-600' : '' }}
                    {{ session('error') ? 'bg-red-100 text-red-700 border border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600' : '' }}
                    {{ session('info') ? 'bg-yellow-100 text-yellow-700 border border-yellow-300 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-500' : '' }}
                    {{ session('warning') ? 'bg-orange-100 text-orange-700 border border-orange-300 dark:bg-orange-800 dark:text-orange-200 dark:border-orange-600' : '' }}
                    {{ $errors->any() ? 'bg-red-100 text-red-700 border border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600' : '' }}">

                {{-- Display session messages --}}
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
                
                {{-- Display validation errors --}}
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>

            <!-- Auto-hide message after 5 seconds with fade-out animation -->
            <script>
                setTimeout(() => {
                    const messageDiv = document.getElementById('message');
                    if (messageDiv) {
                        messageDiv.classList.add('opacity-0');
                        setTimeout(() => {
                            messageDiv.style.display = 'none';
                        }, 500);
                    }
                }, 5000);
            </script>
        @endif
    </div>

    <!-- Navbar: Includes dropdown with x-cloak to prevent flash on page load -->
    @include('components.navbar')

    <!-- Main Content: Yields content from child views (e.g., profile.blade.php) -->
    <main class="flex-grow container mx-auto">
        @yield('content')
    </main>

    <!-- FilePond JS: Pinned to v4.31.1, loaded at bottom of <body> to avoid blocking rendering -->
    <script src="https://unpkg.com/filepond@4.31.1/dist/filepond.min.js"></script>

    <!-- FilePond Initialization: Only runs if FilePond inputs exist to avoid unnecessary DOM parsing -->
    <script>
        if (document.querySelector('.filepond')) {
            FilePond.parse(document.body);
        }
    </script>

    <!-- Form Submission Handler: Handles forms with data-upload attribute for file uploads -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clears validation error messages and styling from form
            function clearErrors(form) {
                form.querySelectorAll('.field-error').forEach(n => n.remove());
                form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));
            }

            // Displays validation errors next to form inputs or via alert
            function showErrors(form, errors) {
                const messages = [];
                for (let key in errors) {
                    messages.push(...errors[key]);
                    let base = key.split('.')[0]; // Handle nested fields (e.g., office_days.0)
                    let input = form.querySelector(`[name="${key}"], [name="${base}"], [name="${base}[]"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const small = document.createElement('small');
                        small.className = 'field-error text-red-600';
                        small.innerText = errors[key][0];
                        input.parentNode.insertBefore(small, input.nextSibling);
                    }
                }
                if (messages.length) alert(messages.join("\n"));
            }

            // Attach submit handler to forms with data-upload attribute
            document.querySelectorAll('form[data-upload]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    clearErrors(form);

                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();

                    // Dispatch upload-start event for progress modal
                    window.dispatchEvent(new CustomEvent('upload-start'));

                    // Set up XHR request with CSRF token and headers
                    const token = document.head.querySelector('meta[name="csrf-token"]')?.content;
                    xhr.open((form.method || 'POST').toUpperCase(), form.action);
                    if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');

                    // Update progress event for upload progress modal
                    xhr.upload.addEventListener('progress', function(ev) {
                        if (ev.lengthComputable) {
                            const percent = Math.round((ev.loaded / ev.total) * 100);
                            window.dispatchEvent(new CustomEvent('upload-progress', {
                                detail: {
                                    progress: percent
                                }
                            }));
                        }
                    });

                    // Handle successful response or redirect
                    xhr.addEventListener('load', function() {
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const json = JSON.parse(xhr.responseText || '{}');
                                if (json.redirect) {
                                    window.location.href = json.redirect;
                                    return;
                                }
                            } catch (err) {
                                /* not JSON; ignore */
                            }
                            window.location.reload();
                            return;
                        }

                        // Handle validation errors (422 status)
                        if (xhr.status === 422) {
                            let payload = {};
                            try {
                                payload = JSON.parse(xhr.responseText);
                            } catch (e) {}
                            if (payload.errors) {
                                showErrors(form, payload.errors);
                                return;
                            }
                        }

                        // Handle other errors
                        alert('Upload failed. Please try again.');
                    });

                    // Handle network errors
                    xhr.addEventListener('error', function() {
                        window.dispatchEvent(new CustomEvent('upload-finish'));
                        alert('Network error. Upload failed.');
                    });

                    xhr.send(formData);
                });
            });

            // Prevent navigation during uploads
            let isUploading = false;
            window.addEventListener('upload-start', () => {
                isUploading = true;
            });
            window.addEventListener('upload-finish', () => {
                isUploading = false;
            });
            window.addEventListener('beforeunload', (e) => {
                if (isUploading) {
                    e.preventDefault();
                    e.returnValue = ''; // Show confirm dialog in Chrome
                }
            });
        });
    </script>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Yield additional scripts from child views (e.g., Cropper.js in profile.blade.php) -->
    @stack('scripts')

    <!-- Upload Progress Modal: Used for forms with data-upload attribute -->
    <x-upload-progress-modal />

    {{-- Spinner --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Helper function to show spinner
            function showSpinner() {
                if (!document.getElementById("loading")) {
                    document.body.insertAdjacentHTML("beforeend",
                        '<div id="loading" class="fixed inset-0 flex items-center justify-center z-50">' +
                        '<div class="animate-spin border-4 border-blue-200 border-t-blue-600 rounded-full w-10 h-10"></div>' +
                        '</div>'
                    );
                }
            }

            // Sort dropdowns
            document.querySelectorAll('#sort-form select').forEach(select => {
                select.addEventListener('change', showSpinner);
            });

            // Assign staff room dropdown
            const assignRoomForm = document.getElementById("assign-staff-form");
            if (assignRoomForm) {
                const select = assignRoomForm.querySelector("select");
                if (select) {
                    select.addEventListener("change", function() {
                        showSpinner();
                        assignRoomForm.submit();
                    });
                }
            }

            // Assign staff update form
            const assignUpdateForm = document.getElementById("assignForm");
            if (assignUpdateForm) {
                assignUpdateForm.addEventListener("submit", showSpinner);
            }

            // Unassign confirmation form
            const unassignForm = document.getElementById("unassignForm");
            if (unassignForm) {
                unassignForm.addEventListener("submit", showSpinner);
            }
        });
    </script>
</body>

</html>

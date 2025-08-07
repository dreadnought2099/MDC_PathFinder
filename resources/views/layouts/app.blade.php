<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div id="success-message-container" class="absolute top-24 right-4 z-100">
        @if (session('success') || session('error') || session('info') || $errors->any())
            <div id="message"
                class="p-3 rounded-md shadow-lg border-l-4
                {{ session('success') ? 'bg-green-100 text-green-700' : '' }}
                {{ session('error') ? 'bg-red-100 text-red-700' : '' }}
                {{ session('info') ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $errors->any() ? 'bg-red-100 text-red-700' : '' }}">

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

                {{-- Display validation errors --}}
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>

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
    @include('components.navbar')

    {{-- Main Content --}}
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>

    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">

    <!-- FilePond JS -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <script>
        FilePond.registerPlugin(); // optional if you're using plugins
        FilePond.parse(document.body);
    </script>

    @yield('scripts')
</body>

</html>

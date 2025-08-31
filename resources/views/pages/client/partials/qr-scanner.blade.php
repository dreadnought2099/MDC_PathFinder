<!-- QR Scanner Partial -->
<div class="w-full min-h-screen flex flex-col justify-center items-center text-center px-4 py-6">
    <!-- Main content -->
    <div class="flex-grow flex flex-col justify-center items-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-primary dark:text-gray-100">{{ config('app.name') }}</h1>
        <p class="text-gray-600 mb-6 dark:text-gray-300">Point your camera at a QR code to start exploring campus rooms.
        </p>

        <!-- Camera scanner -->
        <div class="max-w-lg w-full mx-auto">
            <div id="qr-reader" class="mx-auto mb-4 border border-gray-300 rounded-lg overflow-hidden"
                style="max-width: 350px;"></div>
            <div id="qr-reader-results" class="text-sm text-gray-600 text-center dark:text-gray-300"></div>

            <!-- Manual restart button -->
            <button onclick="restartScanner()"
                class="mt-2 w-full bg-gray-500 hover:bg-white text-white hover:text-gray-500 border border-gray-500 py-2 px-4 rounded-lg transition-all duration-300 cursor-pointer">
                Restart Camera
            </button>
        </div>
    </div>
</div>

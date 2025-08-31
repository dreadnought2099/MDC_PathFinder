<div class="w-full min-h-screen flex flex-col justify-center items-center text-center px-4 py-6">
    <!-- Main content -->
    <div class="flex-grow flex flex-col justify-center items-center border-2 border-primary p-6 rounded-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-primary dark:text-gray-100">{{ config('app.name') }}</h1>
        <p class="text-gray-600 mb-6 dark:text-gray-300">
            Point your camera at a QR code to start exploring campus rooms.
        </p>

        <!-- Camera scanner -->
        <div class="max-w-lg w-full mx-auto">
            <div id="qr-reader" class="relative mx-auto mb-4 rounded-lg overflow-hidden border- border-primary"
                style="max-width: 350px;">
            </div>  

            <!-- Stop button -->
            <button id="stopBtn"
                class="mb-8 w-2xs bg-secondary hover:text-secondary hover:bg-white text-white border-2 border-secondary dark:hover:bg-gray-800 text-sm py-2 px-4 rounded-lg transition-all duration-300 ease-in-out cursor-pointer">
                Stop Scanning
            </button>

            <!-- Results -->
            <div id="qr-reader-results" class="text-sm text-gray-600 text-center dark:text-gray-300"></div>

            <!-- Manual restart button -->
            <button onclick="restartScanner()"
                class="mt-2 w-full bg-primary hover:bg-white hover:text-primary border-2 border-primary text-white dark:hover:bg-gray-800 py-2 px-4 rounded-lg transition-all duration-300 cursor-pointer">
                Restart Camera
            </button>
        </div>
    </div>
</div>
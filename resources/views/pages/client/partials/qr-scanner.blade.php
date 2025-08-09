<div class="text-center">
    <div>
        <a href="{{ route('index') }}"
            class="flex items-center text-black hover:text-[#157ee1] focus:outline-none cursor-pointer">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="ml-1">Back Home</span>
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 mb-4">CampusLens Scanner</h1>
    <p class="text-gray-600 mb-6">Point your camera at a QR code to start exploring campus rooms.</p>

    <!-- Camera scanner -->
    <div class="max-w-lg mx-auto">
        <div id="qr-reader" class="mx-auto mb-4 border border-gray-300 rounded-lg overflow-hidden"
            style="max-width: 350px;"></div>
        <div id="qr-reader-results" class="text-sm text-gray-600 text-center"></div>


        {{-- <!-- Manual input fallback -->
        <div class="mt-4">
            <p class="text-sm text-gray-500 mb-2">Or enter room ID manually:</p>
            <input type="number" id="manual-room-id" placeholder="Enter room ID"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            <button onclick="goToRoom()"
                class="mt-2 w-full bg-primary hover:bg-white hover:text-primary text-white py-2 px-4 rounded-lg border border-primary transition-all duration-300 cursor-pointer">
                View Room
            </button>

            <!-- Manual restart button -->
            <button onclick="restartScanner()"
                class="mt-2 w-full bg-gray-500 hover:bg-white text-white hover:text-gray-500 border border-gray-500 py-2 px-4 rounded-lg transition-all duration-300 cursor-pointer">
                Restart Camera
            </button>
        </div> --}}
    </div>

</div>

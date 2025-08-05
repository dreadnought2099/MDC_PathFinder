<div class="text-center">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Campus Tour Scanner</h1>
    <p class="text-gray-600 mb-6">Point your camera at a QR code to start exploring campus rooms.</p>
    
    <!-- Camera scanner -->
    <div class="max-w-lg mx-auto">
        <div id="qr-reader" class="w-full h-96 bg-gray-100 rounded-lg overflow-hidden mb-4"></div>
        <div id="qr-reader-results" class="text-sm text-gray-600"></div>
        
        <!-- Manual input fallback -->
        <div class="mt-4">
            <p class="text-sm text-gray-500 mb-2">Or enter room ID manually:</p>
            <input type="number" id="manual-room-id" placeholder="Enter room ID" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            <button onclick="goToRoom()" 
                    class="mt-2 w-full bg-primary hover:bg-white hover:text-primary text-white py-2 px-4 rounded-lg border border-primary transition-all duration-300">
                View Room
            </button>
            
            <!-- Manual restart button -->
            <button onclick="restartScanner()" 
                    class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-all duration-300">
                Restart Camera
            </button>
        </div>
    </div>
</div> 
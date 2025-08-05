@extends('layouts.guest')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        @if ($room)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $room->name }}</h1>
                
                @if ($room->description)
                    <p class="text-gray-600 mb-4">{{ $room->description }}</p>
                @endif

                @if ($room->image_path)
                    <div class="mb-4">
                        <img src="{{ asset($room->image_path) }}" alt="{{ $room->name }}" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                @if ($room->office_hours)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Office Hours:</h3>
                        <p class="text-gray-600">{{ $room->office_hours }}</p>
                    </div>
                @endif

                @if ($room->staff->count() > 0)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Staff in this Room:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($room->staff as $member)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    @if ($member->photo_path)
                                        <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}"
                                             class="w-16 h-16 object-cover rounded-full mb-2">
                                    @endif
                                    <h4 class="font-semibold text-gray-800">{{ $member->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $member->position }}</p>
                                    @if ($member->email)
                                        <p class="text-sm text-blue-600">{{ $member->email }}</p>
                                    @endif
                                    @if ($member->bio)
                                        <p class="text-sm text-gray-500 mt-1">{{ $member->bio }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($room->video_path)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Room Video:</h3>
                        <video controls class="w-full rounded-lg">
                            <source src="{{ asset($room->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif

                <!-- Back to scanner button -->
                <div class="mt-6 text-center">
                    <a href="{{ route('ar.view') }}" 
                       class="bg-primary hover:bg-white hover:text-primary text-white py-3 px-6 rounded-lg shadow-lg border border-primary transition-all duration-300">
                        Scan Another QR Code
                    </a>
                </div>
            </div>
        @else
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
        @endif
         </div>

     <!-- QR Scanner Script -->
     <script src="https://unpkg.com/html5-qrcode"></script>
     <script>
         let html5QrcodeScanner = null;
         let scanAttempts = 0;
         const maxScanAttempts = 3;
 
                   function onScanSuccess(decodedText, decodedResult) {
              console.log('QR Code scanned:', decodedText);
              
              // Stop scanning immediately to prevent multiple scans
              if (html5QrcodeScanner) {
                  html5QrcodeScanner.clear();
                  html5QrcodeScanner = null;
              }
              
                             let roomId = null;
               
               // Handle different QR code formats
               if (decodedText.startsWith('room_')) {
                   // Old format: room_13
                   roomId = decodedText.replace('room_', '');
                   console.log('Room ID from old format:', roomId);
               } else if (decodedText.match(/^\d+$/)) {
                   // New format: just the number (13)
                   roomId = decodedText;
                   console.log('Room ID from new format:', roomId);
               } else {
                   // Try URL format as fallback
                   try {
                       const url = new URL(decodedText);
                       roomId = url.searchParams.get('room');
                       console.log('Room ID from URL:', roomId);
                   } catch (error) {
                       console.log('Invalid QR code format:', decodedText);
                   }
               }
              
              if (roomId) {
                  // Show success message
                  document.getElementById('qr-reader-results').innerHTML = 
                      '<span class="text-green-600">✓ QR Code detected! Redirecting to room ' + roomId + '...</span>';
                  
                  // Redirect to room page after a short delay
                  setTimeout(() => {
                      window.location.href = `{{ route('ar.view') }}?room=${roomId}`;
                  }, 1500);
              } else {
                  // Invalid QR code format
                  document.getElementById('qr-reader-results').innerHTML = 
                      '<span class="text-red-600">✗ Invalid QR code format: ' + decodedText + '</span>';
                  
                  // Reinitialize scanner after delay
                  setTimeout(() => {
                      const qrReader = document.querySelector('#qr-reader');
                      if (qrReader) {
                          qrReader.removeAttribute('data-initialized');
                          initializeScanner();
                      }
                  }, 3000);
              }
          }
 
                   function onScanFailure(error) {
              // Don't count every failure, only log for debugging
              console.log('Scan attempt failed:', error);
          }
 
                   function restartScanner() {
              // Clear existing scanner
              if (html5QrcodeScanner) {
                  html5QrcodeScanner.clear();
                  html5QrcodeScanner = null;
              }
              
              // Reset initialization flag
              const qrReader = document.querySelector('#qr-reader');
              if (qrReader) {
                  qrReader.removeAttribute('data-initialized');
              }
              
              // Clear results
              document.getElementById('qr-reader-results').innerHTML = 
                  '<span class="text-blue-600">🔄 Restarting camera...</span>';
              
              // Restart after a short delay
              setTimeout(() => {
                  initializeScanner();
              }, 1000);
          }
 
                   function initializeScanner() {
              const qrReader = document.querySelector('#qr-reader');
              if (qrReader && !qrReader.hasAttribute('data-initialized')) {
                  try {
                      // Clear any previous results
                      document.getElementById('qr-reader-results').innerHTML = 
                          '<span class="text-blue-600">📷 Camera ready. Point at QR code...</span>';
                      
                                             html5QrcodeScanner = new Html5QrcodeScanner(
                           "qr-reader",
                           { 
                               fps: 5, 
                               qrbox: { width: 300, height: 300 },
                               aspectRatio: 1.0,
                               rememberLastUsedCamera: true,
                               showTorchButtonIfSupported: true,
                               disableFlip: false,
                               verbose: false
                           },
                           false
                       );
                      html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                      qrReader.setAttribute('data-initialized', 'true');
                  } catch (error) {
                      console.error('Scanner initialization error:', error);
                      document.getElementById('qr-reader-results').innerHTML = 
                          '<span class="text-red-600">❌ Camera initialization failed. Please refresh the page.</span>';
                  }
              }
          }
 
         // Initialize scanner when page loads
         document.addEventListener('DOMContentLoaded', function() {
             initializeScanner();
         });
 
         function goToRoom() {
             const roomId = document.getElementById('manual-room-id').value;
             if (roomId) {
                 window.location.href = `{{ route('ar.view') }}?room=${roomId}`;
             }
         }
     </script>
@endsection
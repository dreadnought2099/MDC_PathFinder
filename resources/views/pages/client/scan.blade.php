@extends('layouts.guest')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        @if ($room)
            @include('pages.client.partials.room-details', ['room' => $room])
        @else
            @include('pages.client.partials.qr-scanner')
        @endif
    </div>

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
                        "qr-reader", {
                            fps: 10, // faster scan attempts
                            qrbox: {
                                width: 350,
                                height: 350
                            }, // slightly bigger scan area
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

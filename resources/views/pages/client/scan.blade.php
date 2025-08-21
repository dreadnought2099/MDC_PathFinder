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

        function showMessage(message, type = 'info') {
            const resultsDiv = document.getElementById('qr-reader-results');
            const icons = {
                success: '✓',
                error: '✗',
                info: 'ℹ️',
                warning: '⚠️'
            };

            const colors = {
                success: 'text-green-600',
                error: 'text-red-600',
                info: 'text-blue-600',
                warning: 'text-yellow-600'
            };

            resultsDiv.innerHTML = `<span class="${colors[type]}">${icons[type]} ${message}</span>`;
        }

        function checkRoomExists(roomId) {
            // Show checking message
            showMessage(`Checking if room ${roomId} exists...`, 'info');

            // Make API call to check room existence
            fetch(`/api/rooms/${roomId}/exists`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.exists) {
                        // Room exists, proceed with redirect
                        showMessage(`Room ${roomId} found! Redirecting...`, 'success');
                        setTimeout(() => {
                            window.location.href = `{{ route('ar.view') }}?room=${roomId}`;
                        }, 1000);
                    } else {
                        // Room doesn't exist - show error and restart scanner
                        showMessage(`Room ${roomId} does not exist. Please try another QR code.`, 'error');
                        setTimeout(() => {
                            restartScanner();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error checking room existence:', error);

                    // If API fails, show error and restart scanner
                    showMessage(`Unable to verify room ${roomId}. Connection error. Please try again.`, 'error');
                    setTimeout(() => {
                        restartScanner();
                    }, 1000);
                });
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('QR Code scanned:', decodedText);

            // Stop scanning immediately to prevent multiple scans
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }

            let roomId = null;
            let errorMessage = null;

            // Validate and extract room ID
            if (!decodedText || decodedText.trim() === '') {
                errorMessage = 'Empty QR code detected';
            } else if (decodedText.startsWith('room_')) {
                // Old format: room_13
                const extractedId = decodedText.replace('room_', '');
                if (extractedId.match(/^\d+$/)) {
                    roomId = extractedId;
                    console.log('Room ID from old format:', roomId);
                } else {
                    errorMessage = 'Invalid room format. Expected: room_[number]';
                }
            } else if (decodedText.match(/^\d+$/)) {
                // New format: just the number (13)
                roomId = decodedText;
                console.log('Room ID from new format:', roomId);
            } else if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                // Try URL format
                try {
                    const url = new URL(decodedText);
                    const extractedId = url.searchParams.get('room');
                    if (extractedId && extractedId.match(/^\d+$/)) {
                        roomId = extractedId;
                        console.log('Room ID from URL:', roomId);
                    } else {
                        errorMessage = 'URL does not contain valid room parameter';
                    }
                } catch (error) {
                    errorMessage = 'Invalid URL format in QR code';
                }
            } else {
                // Unknown format
                errorMessage =
                    `Unrecognized QR code format. Expected room number, room_[number], or valid URL. Got: "${decodedText.substring(0, 50)}${decodedText.length > 50 ? '...' : ''}"`;
            }

            // Validate room ID if extracted
            if (roomId && !isValidRoomId(roomId)) {
                errorMessage = `Invalid room ID: ${roomId}. Room ID must be a positive number.`;
                roomId = null;
            }

            if (roomId) {
                // Success - valid room ID found, now check if room exists
                showMessage(`QR Code detected! Checking room ${roomId}...`, 'info');
                checkRoomExists(roomId);
            } else {
                // Error - invalid QR code format
                showMessage(errorMessage || 'Invalid QR code format', 'error');

                // Restart scanner after showing error
                setTimeout(() => {
                    restartScanner();
                }, 1000);
            }
        }

        function isValidRoomId(roomId) {
            const num = parseInt(roomId, 10);
            return !isNaN(num) && num > 0 && num.toString() === roomId;
        }

        function showRetryOptions() {
            const resultsDiv = document.getElementById('qr-reader-results');
            resultsDiv.innerHTML = `
        <div class="space-y-2">
            <span class="text-gray-600">Scan failed. What would you like to do?</span>
            <div class="flex gap-2">
                <button onclick="restartScanner()" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                    Try Again
                </button>
                <button onclick="showManualInput()" class="px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">
                    Enter Room ID
                </button>
            </div>
        </div>
    `;
        }

        function showManualInput() {
            const resultsDiv = document.getElementById('qr-reader-results');
            resultsDiv.innerHTML = `
        <div class="space-y-2">
            <span class="text-gray-600">Enter room ID manually:</span>
            <div class="flex gap-2">
                <input type="number" id="manual-room-id" placeholder="Room ID" class="px-2 py-1 border rounded text-sm" min="1">
                <button onclick="goToRoom()" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                    Go
                </button>
                <button onclick="restartScanner()" class="px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">
                    Scan QR
                </button>
            </div>
        </div>
    `;

            // Focus on input field
            setTimeout(() => {
                document.getElementById('manual-room-id')?.focus();
            }, 100);
        }

        function onScanFailure(error) {
            // Don't count every failure, only log for debugging
            console.log('Scan attempt failed:', error);

            // Increment scan attempts for persistent failures
            scanAttempts++;

            // If too many failures, show helpful message
            if (scanAttempts >= maxScanAttempts) {
                showMessage('Having trouble scanning? Make sure QR code is clear and well-lit', 'warning');
                scanAttempts = 0; // Reset counter
            }
        }

        function restartScanner() {
            // Clear existing scanner
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }

            // Reset initialization flag and attempts
            const qrReader = document.querySelector('#qr-reader');
            if (qrReader) {
                qrReader.removeAttribute('data-initialized');
            }
            scanAttempts = 0;

            // Show restarting message
            showMessage('Restarting camera...', 'info');

            // Restart after a short delay
            setTimeout(() => {
                initializeScanner();
            }, 1000);
        }

        function initializeScanner() {
            const qrReader = document.querySelector('#qr-reader');
            if (qrReader && !qrReader.hasAttribute('data-initialized')) {
                try {
                    // Show ready message
                    showMessage('Camera ready. Point at QR code...', 'info');

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
                    showMessage('Camera initialization failed. Please refresh the page or check camera permissions.',
                        'error');
                }
            }
        }

        // Initialize scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeScanner();
        });

        function goToRoom() {
            const roomId = document.getElementById('manual-room-id')?.value?.trim();
            if (roomId && isValidRoomId(roomId)) {
                // Also check room existence for manual input
                checkRoomExists(roomId);
            } else if (roomId) {
                showMessage('Please enter a valid room ID (positive number)', 'error');
            } else {
                showMessage('Please enter a room ID', 'warning');
            }
        }

        // Add Enter key support for manual input
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && document.getElementById('manual-room-id')) {
                goToRoom();
            }
        });
    </script>
@endsection

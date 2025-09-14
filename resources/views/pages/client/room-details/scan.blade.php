@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 mb-8">
        <!-- Top navigation bar with back button and dark mode toggle -->
        <div
            class="bg-white flex justify-between items-center p-4 mb-2 sticky top-0 z-50
           dark:bg-gray-900 dark:border-b border-b-primary dark:border-b-primary">

            <!-- Left: Back button -->
            <div>
                @if ($room)
                    @php
                        $returnRoute = request('return');
                        $backUrl = route('scan.index');
                        $backText = 'Back to Scanner';

                        if ($returnRoute) {
                            $backUrl = route('scan.index', ['return' => $returnRoute]);
                        }
                    @endphp

                    <a href="{{ $backUrl }}"
                        class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                        <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-medium">{{ $backText }}</span>
                    </a>
                @else
                    @php
                        $returnRoute = request('return');
                        $backUrl = route('index');
                        $backText = 'Back to Home';

                        if ($returnRoute && Route::has($returnRoute)) {
                            switch ($returnRoute) {
                                case 'paths.select':
                                    $backUrl = route('paths.select');
                                    $backText = 'Back to Path Selection';
                                    break;
                                case 'paths.results':
                                    $backUrl = route('paths.return-to-results');
                                    $backText = 'Back to Results';
                                    break;
                                default:
                                    $backUrl = route('index');
                                    $backText = 'Back to Home';
                            }
                        }
                    @endphp

                    <a href="{{ $backUrl }}"
                        class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                        <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-medium">{{ $backText }}</span>
                    </a>
                @endif
            </div>

            <!-- Center spacer -->
            <div class="flex-1"></div>

            <!-- Right: fixed width container for About + Dark Mode -->
            <div class="w-48 flex items-center">
                <!-- Slot 1: About Page -->
                <div class="flex-1 flex justify-end">
                    <x-about-page />
                </div>

                <!-- Slot 2: Dark Mode Toggle -->
                <div class="flex-1 flex justify-end">
                    <x-dark-mode-toggle />
                </div>
            </div>
        </div>

        <!-- Main content area -->
        @if ($room)
            @include('pages.client.room-details.details', ['room' => $room])
        @else
            <!-- QR Scanner Content -->
            <div class="w-full min-h-screen flex flex-col justify-center items-center text-center px-4 py-6">
                <!-- Main content -->
                <div
                    class="flex-grow flex flex-col justify-center items-center border-2 border-primary p-6 rounded-2xl max-w-2xl w-full">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 text-primary dark:text-gray-100">
                        {{ config('app.name') }}
                    </h1>
                    <p class="text-gray-600 mb-6 dark:text-gray-300">
                        Point your camera at a QR code to start exploring campus rooms.
                    </p>

                    <!-- Camera scanner -->
                    <div class="max-w-lg w-full mx-auto">
                        <div id="qr-reader" class="relative mx-auto mb-4 rounded-lg overflow-hidden"
                            style="max-width: 350px;">
                        </div>

                        <!-- Control buttons -->
                        <div class="flex flex-col gap-3 items-center">
                            <!-- Stop button -->
                            <button id="stopBtn"
                                class="w-full max-w-xs bg-secondary hover:text-secondary hover:bg-white text-white border-2 border-secondary dark:hover:bg-gray-800 py-2 px-4 rounded-md transition-all duration-300 ease-in-out cursor-pointer shadow-secondary-hover">
                                Stop Scanning
                            </button>

                            <!-- Manual restart button -->
                            <button id="restartBtn"
                                class="w-full max-w-xs bg-primary hover:bg-white hover:text-primary border-2 border-primary text-white dark:hover:bg-gray-800 py-2 px-4 rounded-md transition-all duration-300 cursor-pointer shadow-primary-hover">
                                Restart Camera
                            </button>
                        </div>

                        <!-- Results and status messages -->
                        <div id="qr-reader-results"
                            class="mt-4 text-sm text-gray-600 text-center dark:text-gray-300 min-h-[24px]"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- QR Scanner JavaScript (only load when needed) -->
    @if (!$room)
        <script>
            class QRScanner {
                constructor() {
                    this.html5QrCode = null;
                    this.scanAttempts = 0;
                    this.maxScanAttempts = 3;
                    this.isScanning = false;
                    this.init();
                }

                init() {
                    document.addEventListener("DOMContentLoaded", () => {
                        this.bindEvents();
                        this.startScanner();
                    });
                }

                bindEvents() {
                    const stopBtn = document.getElementById("stopBtn");
                    const restartBtn = document.getElementById("restartBtn");

                    if (stopBtn) {
                        stopBtn.addEventListener("click", () => this.stopScanner());
                    }

                    if (restartBtn) {
                        restartBtn.addEventListener("click", () => this.restartScanner());
                    }
                }

                showMessage(message, type = 'info') {
                    const resultsDiv = document.getElementById('qr-reader-results');
                    if (!resultsDiv) return;

                    const colors = {
                        success: 'text-green-600 dark:text-green-400',
                        error: 'text-red-600 dark:text-red-400',
                        info: 'text-blue-600 dark:text-blue-400',
                        warning: 'text-yellow-600 dark:text-yellow-400'
                    };

                    const icons = {
                        success: '/icons/success.png',
                        error: '/icons/error.png',
                        info: '/icons/information.png',
                        warning: '/icons/warning.png'
                    };

                    resultsDiv.innerHTML = `
                    <div class="${colors[type]} flex items-center justify-center gap-2">
                        <img src="${icons[type]}" alt="${type}" class="h-4 w-4" />
                        <span>${message}</span>
                    </div>
                `;
                }

                async checkRoomExists(roomToken) {
                    try {
                        this.showMessage(`Checking if room ${roomToken} exists...`, 'info');

                        const url = `{{ route('rooms.exists', ['token' => 'TOKEN_PLACEHOLDER']) }}`
                            .replace('TOKEN_PLACEHOLDER', roomToken);

                        const response = await fetch(url, {
                            headers: {
                                "Accept": "application/json"
                            }
                        });

                        const data = await response.json();

                        if (!response.ok || data.error) {
                            throw new Error(data.error || `HTTP ${response.status}`);
                        }

                        if (data.exists) {
                            this.showMessage(`Room found! Redirecting...`, 'success');

                            const returnParam = new URLSearchParams(window.location.search).get('return');
                            let roomUrl = `{{ route('scan.room', ['token' => 'TOKEN_PLACEHOLDER']) }}`
                                .replace('TOKEN_PLACEHOLDER', roomToken);

                            if (returnParam) {
                                roomUrl += `?return=${returnParam}`;
                            }

                            setTimeout(() => {
                                window.location.href = roomUrl;
                            }, 1000);
                        } else {
                            this.showMessage(`Room does not exist.`, 'error');
                            setTimeout(() => this.restartScanner(), 2000);
                        }
                    } catch (error) {
                        console.error('Room check error:', error);
                        this.showMessage(error.message || `Connection error. Please try again.`, 'error');
                        setTimeout(() => this.restartScanner(), 2000);
                    }
                }

                onScanSuccess(decodedText) {
                    if (!this.isScanning) return;

                    this.stopScanner();

                    // Just trim and pass to backend — regex is handled by route constraint
                    const roomToken = decodedText.trim();

                    this.checkRoomExists(roomToken);
                }

                onScanFailure(error) {
                    if (this.isScanning && error.includes('NotFoundException')) {
                        this.scanAttempts++;

                        if (this.scanAttempts >= this.maxScanAttempts) {
                            this.showMessage("Having trouble scanning? Make sure the QR code is clear and well-lit.",
                                "warning");
                            this.scanAttempts = 0;
                        }
                    }
                }

                async startScanner() {
                    try {
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("qr-reader");
                        }

                        this.showMessage("Initializing camera...", 'info');

                        await this.html5QrCode.start({
                                facingMode: "environment"
                            }, {
                                fps: 10,
                                qrbox: undefined,
                                aspectRatio: 1.0
                            },
                            (decodedText) => this.onScanSuccess(decodedText),
                            (error) => this.onScanFailure(error)
                        );

                        this.isScanning = true;
                        this.scanAttempts = 0;
                        this.showMessage("Point your camera at a QR code", 'info');

                    } catch (error) {
                        console.error("Failed to start scanner:", error);

                        let errorMessage = "Camera initialization failed.";
                        if (error.name === 'NotAllowedError') {
                            errorMessage = "Camera permission denied. Please enable camera access.";
                        } else if (error.name === 'NotFoundError') {
                            errorMessage = "No camera found. Please check your device.";
                        }

                        this.showMessage(errorMessage, "error");
                        this.isScanning = false;
                    }
                }

                async stopScanner() {
                    if (this.html5QrCode && this.isScanning) {
                        try {
                            await this.html5QrCode.stop();
                            this.html5QrCode.clear();
                            this.isScanning = false;
                            this.showMessage("Scanner stopped.", "warning");
                        } catch (error) {
                            console.error("Error stopping scanner:", error);
                        }
                    }
                }

                async restartScanner() {
                    await this.stopScanner();
                    setTimeout(() => {
                        this.startScanner();
                    }, 1000);
                }
            }

            new QRScanner();
        </script>
    @endif
    @if ($room)
        <script>
            console.log('Room details loaded for room:', @json($room->id ?? null));
        </script>
    @endif
@endsection

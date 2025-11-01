@extends('layouts.guest')

@section('content')
    <div class="min-h-screen moving-gradient">
        <!-- Main content area -->
        @if ($room)
            @include('pages.client.room-details.details', ['room' => $room])
        @else
            <!-- QR Scanner Content -->
            <div
                class="w-full min-h-[calc(100vh-64px)] flex flex-col justify-center items-center px-4 py-6 sm:py-8 lg:py-12">
                <!-- Main content -->
                <div
                    class="flex-grow flex flex-col justify-center items-center dark:bg-gray-800 border-2 border-primary p-4 sm:p-6 lg:p-8 rounded-2xl max-w-2xl w-full">
                    <h1
                        class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-3 sm:mb-4 text-primary dark:text-gray-100 text-center">
                        {{ config('app.name') }}
                    </h1>
                    <p
                        class="text-sm sm:text-base lg:text-lg text-gray-600 mb-6 sm:mb-8 dark:text-gray-300 text-center max-w-md">
                        Point your camera at a QR code to start exploring campus rooms.
                    </p>

                    <!-- Camera scanner -->
                    <div class="w-full max-w-lg mx-auto">
                        <div id="qr-reader" class="relative mx-auto mb-4 sm:mb-6 rounded-lg overflow-hidden w-full"
                            style="max-width: min(350px, 100%);">
                        </div>

                        <!-- Control buttons -->
                        <div class="flex flex-col gap-2 items-center w-full">
                            <!-- Stop button -->
                            <button id="stopBtn"
                                class="w-full max-w-xs bg-secondary hover:text-secondary hover:bg-white text-white border-2 border-secondary dark:hover:bg-gray-800 py-2 px-4 rounded-md transition-all duration-300 ease-in-out cursor-pointer shadow-secondary-hover text-sm font-medium">
                                Stop Scanning
                            </button>

                            <!-- Manual restart button -->
                            <button id="restartBtn"
                                class="w-full max-w-xs bg-primary hover:bg-white hover:text-primary border-2 border-primary text-white dark:hover:bg-gray-800 py-2 px-4 rounded-md transition-all duration-300 cursor-pointer shadow-primary-hover text-sm font-medium">
                                Restart Camera
                            </button>
                        </div>

                        <!-- Results and status messages -->
                        <div id="qr-reader-results"
                            class="mt-4 sm:mt-6 text-xs sm:text-sm text-gray-600 text-center dark:text-gray-300 min-h-[24px] px-2">
                        </div>
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
                        success: 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/success.png',
                        error: 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/error.png',
                        info: 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/information.png',
                        warning: 'https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/warning.png'
                    };

                    resultsDiv.innerHTML = `
                    <div class="${colors[type]} flex items-center justify-center gap-2">
                        <img src="${icons[type]}" alt="${type}" class="h-4 w-4 flex-shrink-0" />
                        <span class="break-words">${message}</span>
                    </div>
                `;
                }

                async checkRoomExists(roomToken) {
                    try {
                        console.log('Scanned room token:', roomToken);
                        this.showMessage(`Verifying scanned office...`, 'info');

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
                            this.showMessage(`Office found! Redirecting...`, 'success');

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
                            this.showMessage(`Office does not exist.`, 'error');
                            setTimeout(() => this.restartScanner(), 2000);
                        }
                    } catch (error) {
                        console.error('Office check error:', error);
                        this.showMessage(error.message || `Connection error. Please try again.`, 'error');
                        setTimeout(() => this.restartScanner(), 2000);
                    }
                }

                onScanSuccess(decodedText) {
                    if (!this.isScanning) return;

                    this.stopScanner(false);

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

                async stopScanner(showMessage = true) {
                    if (this.html5QrCode && this.isScanning) {
                        try {
                            await this.html5QrCode.stop();
                            this.html5QrCode.clear();
                            this.isScanning = false;
                            if (showMessage) {
                                this.showMessage("Scanner stopped.", "warning");
                            }
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
@endsection

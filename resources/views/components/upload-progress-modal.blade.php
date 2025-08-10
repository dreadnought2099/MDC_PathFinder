<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
    <div x-data="{ uploading: false, progress: 0 }"
         x-on:upload-start.window="uploading = true; progress = 0"
         x-on:upload-progress.window="progress = $event.detail.progress"
         x-on:upload-finish.window="uploading = false">

        {{-- Slot contains the form --}}
        {{ $slot }}

        <!-- Modal -->
        <div x-show="uploading" 
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded p-6 w-96 shadow-lg">
                <h2 class="text-lg mb-4">{{ $title ?? 'Uploading...' }}</h2>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                    <div class="h-4 rounded-full progress-bar transition-all duration-300 ease-out"
                         :style="'width:' + progress + '%'">
                    </div>
                </div>

                <p class="mt-2 text-sm text-gray-600" x-text="progress + '%'"></p>
            </div>
        </div>
    </div>

    <style>
        /* Glow animation */
        @keyframes glowPulse {
            0% { box-shadow: 0 0 5px #157ee1, 0 0 10px #157ee1; }
            50% { box-shadow: 0 0 15px #157ee1, 0 0 25px #157ee1; }
            100% { box-shadow: 0 0 5px #157ee1, 0 0 10px #157ee1; }
        }

        /* Light sweep animation */
        @keyframes sweep {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        /* Base progress bar */
        .progress-bar {
            background-color: #157ee1;
            position: relative;
            animation: glowPulse 2s infinite ease-in-out;
        }

        /* Shiny light sweep overlay */
        .progress-bar::after {
            content: "";
            position: absolute;
            top: 0;
            left: -50%;
            height: 100%;
            width: 50%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
            animation: sweep 1.5s infinite linear;
        }
    </style>
</div>

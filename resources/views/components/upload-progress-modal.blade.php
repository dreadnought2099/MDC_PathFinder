<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
    <div x-data="{
        uploading: false,
        progress: 0,
        init() {
            window.addEventListener('beforeunload', (e) => {
                if (this.uploading) {
                    e.preventDefault();
                    e.returnValue = 'Upload in progress. Leaving will cancel the upload.';
                    return 'Upload in progress. Leaving will cancel the upload.';
                }
            });
        }
    }" x-on:upload-start.window="uploading = true; progress = 0"
        x-on:upload-progress.window="progress = $event.detail.progress" x-on:upload-finish.window="uploading = false">

        {{-- Slot contains the form --}}
        {{ $slot }}

        <!-- Modal -->
        <div x-show="uploading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
            style="display: none;">
            <div
                class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-sm sm:max-w-md lg:max-w-lg shadow-lg dark:bg-gray-800 border border-primary">
                <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 dark:text-gray-300">
                    {{ $title ?? 'Uploading...' }}</h2>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-3 sm:h-4 overflow-hidden relative">
                    <div class="h-3 sm:h-4 rounded-full progress-bar transition-all duration-300 ease-out"
                        :style="'width:' + progress + '%'">
                    </div>
                </div>

                <p class="mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300 font-medium" x-text="progress + '%'">
                </p>
            </div>
        </div>
    </div>
</div>
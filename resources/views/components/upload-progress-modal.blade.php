<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->

    <div x-data="{ uploading: false, progress: 0 }" x-on:upload-start.window="uploading = true; progress = 0"
        x-on:upload-progress.window="progress = $event.detail.progress" x-on:upload-finish.window="uploading = false">
        {{-- slot contains the form --}}
        {{ $slot }}

        <!-- Modal -->
        <div x-show="uploading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            style="display: none;">
            <div class="bg-white rounded p-6 w-96">
                <h2 class="text-lg font-bold mb-4">{{ $title ?? 'Uploading...' }}</h2>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-200" style="background:#157ee1"
                        :style="'width:' + progress + '%'"></div>
                </div>
                <p class="mt-2 text-sm text-gray-600" x-text="progress + '%'"></p>
            </div>
        </div>
    </div>
</div>

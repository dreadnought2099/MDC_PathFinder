@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <x-floating-actions />

        <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-2 border-primary relative">
            <h2 class="text-2xl text-center mb-6 text-primary">Profile</h2>

            <!-- Profile Image Wrapper -->
            <div class="relative w-40 h-40 mx-auto mb-16 flex flex-col items-center gap-2">

                <!-- Profile Image (click to view) -->
                <img id="profile-page-image"
                    src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) . '?v=' . time() : asset('images/mdc.png') }}"
                    alt="Profile"
                    class="w-40 h-40 rounded-full object-cover border-2 border-primary cursor-pointer hover:scale-105 duration-300 ease-in-out transition-all"
                    onclick="openViewModal()">

                <!-- Separate Crop Button -->
                <button onclick="openModal()"
                    class="bg-primary text-white text-xs px-2 py-1 rounded-md hover:bg-white dark:hover:bg-gray-800 hover:text-primary border border-primary transition-all duration-300 cursor-pointer">
                    Update Profile
                </button>
            </div>

            <!-- User Info -->
            <div class="space-y-2 text-center">
                <p>
                    <span class="text-primary">{{ $user->name ? 'Name:' : 'Username:' }}</span>
                    <span class="dark:text-gray-300">{{ $user->name ?? $user->username }}</span>
                </p>
                <p>
                    <span class="text-primary">Email:</span>
                    <span class="dark:text-gray-300">{{ $user->email ?? 'N/A' }}</span>
                </p>
            </div>

        </div>
    </div>
    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-2 border-primary relative">
        <div class="border-2 border-primary mt-4 text-primary text-2xl text-center font-semibold">
            Two Factor Authentication
        </div>

        {{-- If 2FA already enabled --}}
        @if ($user->google2fa_secret)
            <div class="mt-4 text-center">
                <div
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Two-Factor Authentication Enabled
                </div>
            </div>

            <div class="mt-4 space-y-2">
                {{-- Regenerate Recovery Codes --}}
                <form method="POST" action="{{ route('admin.profile.2fa.recovery.regenerate') }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-tertiary border-2 border-tertiary rounded-md hover:bg-white hover:text-tertiary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-tertiary-hover">
                        Generate New Recovery Codes
                    </button>
                </form>

                {{-- Disable button --}}
                <form method="POST" action="{{ route('admin.profile.2fa.disable') }}"
                    onsubmit="return confirm('Are you sure you want to disable Two-Factor Authentication? This will make your account less secure.')">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                        Disable 2FA
                    </button>
                </form>
            </div>

            {{-- Show QR if regenerate triggered --}}
            @if (session('qrCode'))
                <div class="mt-4 border rounded-lg p-4 bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <p class="text-center font-semibold">Scan this new QR code with your Authenticator app:</p>
                    <div class="my-3 flex justify-center">{!! session('qrCode') !!}</div>
                    <p class="text-center"><strong>Manual Key:</strong> <code
                            class="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded text-sm">{{ session('secret') }}</code>
                    </p>

                    <form method="POST" action="{{ route('admin.profile.2fa.enable') }}" class="mt-4">
                        @csrf
                        <label class="block text-center font-medium mb-2">Enter the 6-digit code from your app:</label>
                        <input type="text" name="otp" maxlength="6"
                            class="w-full px-3 py-3 border rounded-lg text-center text-lg font-mono tracking-widest"
                            placeholder="000000" required>
                        <button type="submit"
                            class="w-full mt-3 px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                            Confirm & Save Changes
                        </button>
                    </form>
                </div>
            @endif

            {{-- If 2FA not enabled --}}
        @else
            <div x-data="{ showSetup: false }" class="mt-4">
                <button @click="showSetup = !showSetup"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    <span x-show="!showSetup">Enable Two-Factor Authentication</span>
                    <span x-show="showSetup">Cancel Setup</span>
                </button>

                {{-- Setup section hidden until clicked --}}
                <div x-show="showSetup" x-cloak
                    class="mt-4 border rounded-lg p-4 bg-white dark:bg-gray-800 dark:text-gray-300">
                    <p class="text-center font-semibold mb-2">Step 1: Scan QR Code</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 text-center">
                        Use an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator
                    </p>
                    <div class="my-4 flex justify-center py-4 bg-white rounded">{!! $qrCode !!}</div>

                    <p class="text-center mb-4"><strong>Manual Entry Key:</strong></p>
                    <p class="text-center mb-4">
                        <code
                            class="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded text-sm break-all">{{ $secret }}</code>
                    </p>

                    <form method="POST" action="{{ route('admin.profile.2fa.enable') }}" class="mt-4">
                        @csrf
                        <label class="block text-center font-semibold mb-2">Step 2: Enter Verification Code</label>
                        <input type="text" name="otp" maxlength="6"
                            class="w-full px-3 py-3 border rounded-lg text-center text-lg font-mono tracking-widest mb-4"
                            placeholder="000000" required>
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                            Enable Two-Factor Authentication
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Recovery codes display (shown once after enabling/regenerating) --}}
        {{-- Recovery codes display (shown until page reload or download) --}}
        @if (session('recovery_codes'))
            <div class="mt-6 border-2 border-amber-300 rounded-lg p-4 bg-amber-50 dark:bg-amber-900/30">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    IMPORTANT: Save Your Recovery Codes
                </h3>

                <p class="text-sm text-amber-700 dark:text-amber-300 mt-2 mb-4">
                    These codes can be used to access your account if you lose your authenticator device.
                    <strong>Each code can only be used once.</strong> Store them in a secure location.
                </p>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    @foreach (session('recovery_codes') as $index => $code)
                        <div class="px-3 py-2 bg-white dark:bg-gray-700 rounded border text-center">
                            <span class="text-xs text-gray-500">{{ $index + 1 }}.</span>
                            <span class="font-mono text-sm">{{ $code }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.2fa.recovery.download') }}"
                        class="flex-1 text-center px-4 py-2 bg-primary text-white rounded border-2 border-primary hover:bg-white hover:text-primary transition-all duration-300 dark:hover:bg-gray-800 shadow-primary-hover">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Download Codes
                    </a>

                    <form method="POST" action="{{ route('admin.profile.2fa.recovery.regenerate') }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full text-center px-4 py-2 bg-tertiary text-white rounded border-2 border-tertiary hover:bg-white hover:text-tertiary transition-all duration-300 dark:hover:bg-gray-800 cursor-pointer shadow-tertiary-hover"
                            title="Generate and display new recovery codes">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            New Codes
                        </button>
                    </form>
                </div>

                <p class="text-xs text-amber-600 dark:text-amber-400 mt-3 text-center">
                    ⚠️ These codes will disappear if you reload this page or after you download them.<br>
                    Save them now in a secure location.
                </p>
            </div>
        @endif
    </div>

    <!-- Cropper Modal -->
    <div id="cropperModal"
        class="modal fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex justify-center items-center cursor-pointer"
        onclick="handleOutsideClick(event)">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative dark:bg-gray-800 border-2 border-primary">
            <button type="button" onclick="closeModal()"
                class="absolute top-2 right-4 text-gray-500 hover:text-red-600">
                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png" alt="Close"
                    class="w-6 h-6">
            </button>

            <h3 class="text-xl mb-4 text-primary">Crop Image</h3>

            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <img id="image-to-crop" class="max-w-full max-h-[400px] mx-auto rounded">
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-2 dark:text-gray-300">Preview</p>
                    <div id="preview" class="w-32 h-32 border overflow-hidden rounded-full"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.profile.updateImage') }}" enctype="multipart/form-data"
                id="upload-form" class="mt-4">
                @csrf
                <input type="hidden" name="cropped_image" id="cropped_image">
                <input type="file" id="profile_image" accept="image/*" class="hidden">
                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-md border-2 border-primary hover:bg-white hover:text-primary transition-all duration-300 dark:hover:bg-gray-800 cursor-pointer shadow-primary-hover">
                        Crop & Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Image Modal -->
    <div id="viewModal"
        class="modal fixed inset-0 hidden bg-black/50 flex justify-center items-center p-4 cursor-pointer z-50"
        onclick="handleViewOutsideClick(event)">
        <div class="relative max-w-[95vw] max-h-[95vh] flex justify-center items-center">
            <!-- Image wrapper (scales independently) -->
            <div id="imageWrapper" class="relative transform transition-transform duration-300"
                style="transform-origin: center center;">
                <img id="viewImage" class="rounded shadow-lg max-h-full max-w-full cursor-zoom-in" alt="Profile View">

                <!-- Close button positioned relative to image wrapper -->
                <button type="button" onclick="closeViewModal(); event.stopPropagation();"
                    class="absolute top-4 right-4 z-50 focus:outline-none">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                        alt="Exit Button"
                        class="w-6 h-6 object-contain hover:scale-120 duration-300 transition-transform ease-in-out cursor-pointer">
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Crop Modal
        let cropper;
        const input = document.getElementById('profile_image');
        const image = document.getElementById('image-to-crop');
        const preview = document.getElementById('preview');
        const cropModal = document.getElementById('cropperModal');

        window.openModal = function() {
            input.click();
        };
        window.handleOutsideClick = function(e) {
            if (e.target === cropModal) closeModal();
        };
        window.closeModal = function() {
            cropModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            image.src = '';
            preview.src = '';
            input.value = '';
        };

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = () => {
                image.src = reader.result;
                cropModal.classList.remove('hidden');
                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    preview: '#preview'
                });
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!cropper) return;
            cropper.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingQuality: 'high'
            }).toBlob(blob => {
                const formData = new FormData();
                formData.append('cropped_image', blob);
                formData.append('_token', '{{ csrf_token() }}');
                fetch('{{ route('admin.profile.updateImage') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.imageUrl) {
                            document.querySelector('[x-ref="navbarProfile"]').src = data.imageUrl +
                                '?v=' + new Date().getTime();
                            document.getElementById('profile-page-image').src = data.imageUrl + '?v=' +
                                new Date().getTime();
                        }
                        closeModal();
                    })
                    .catch(() => closeModal());
            }, 'image/jpeg', 0.92);
        });

        const viewModal = document.getElementById('viewModal');
        const viewImage = document.getElementById('viewImage');
        const imageWrapper = document.getElementById('imageWrapper'); // Add this line
        let zoomed = false;

        window.openViewModal = function() {
            viewImage.src = document.getElementById('profile-page-image').src;
            viewModal.classList.remove('hidden');
            imageWrapper.style.transform = "scale(1)"; // Change from viewImage to imageWrapper
            viewImage.style.cursor = "zoom-in";
            zoomed = false;
        };

        window.handleViewOutsideClick = function(e) {
            if (e.target === viewModal) closeViewModal();
        };

        window.closeViewModal = function() {
            viewModal.classList.add('hidden');
            viewImage.src = '';
            imageWrapper.style.transform = "scale(1)"; // Change from viewImage to imageWrapper
        };

        // Zoom toggle
        viewImage.addEventListener('click', function(e) {
            e.stopPropagation();
            zoomed = !zoomed;
            imageWrapper.style.transform = zoomed ? "scale(2)" :
                "scale(1)"; // Change from viewImage to imageWrapper
            viewImage.style.cursor = zoomed ? "zoom-out" : "zoom-in";
        });
    </script>
@endpush

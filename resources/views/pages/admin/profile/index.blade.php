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
                    src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) . '?v=' . time() : asset('images/mdc-logo.png') }}"
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
            <p class="mt-4 text-green-600">Two-Factor Authentication is enabled.</p>

            {{-- Disable button --}}
            <form method="POST" action="{{ route('admin.profile.2fa.disable') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-secondary border-2 border-secondary rounded-md hover:bg-white hover:text-secondary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-secondary-hover">
                    Disable 2FA
                </button>
            </form>

            {{-- Optional: Regenerate QR button --}}
            <form method="POST" action="{{ route('admin.profile.2fa.regenerate') }}" class="mt-2">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-2 text-sm font-medium bg-tertiary text-white hover:text-tertiary hover:bg-white cursor-pointer border-2 border-tertiary rounded-md transition-all duration-300 ease-in-out dark:hover:bg-gray-800 shadow-tertiary-hover">
                    Regenerate QR Code
                </button>
            </form>

            {{-- Show QR if regenerate triggered --}}
            @if (session('qrCode'))
                <div class="mt-4 border rounded-lg p-4 bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <p>Scan this new QR code with your Authenticator app:</p>
                    <div class="my-3 flex justify-center">{!! session('qrCode') !!}</div>
                    <p><strong>Manual Key:</strong> {{ session('secret') }}</p>

                    <form method="POST" action="{{ route('admin.profile.2fa.enable') }}" class="mt-3">
                        @csrf
                        <label class="block">Enter the 6-digit code:</label>
                        <input type="text" name="otp" maxlength="6"
                            class="w-full px-3 py-2 border rounded-lg mt-4 mb-4  text-center text-lg" required>
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                            Confirm & Save
                        </button>
                    </form>
                </div>
            @endif

            {{-- If 2FA not enabled --}}
        @else
            <div x-data="{ showSetup: false }" class="mt-4">
                <button @click="showSetup = !showSetup"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Enable 2FA
                </button>

                {{-- Setup section hidden until clicked --}}
                <div x-show="showSetup" x-cloak class="mt-4 border rounded-lg p-4 bg-white dark:bg-gray-800">
                    <p>Scan this QR code with your Authenticator app:</p>
                    <div class="my-3 flex justify-center bg-white">{!! $qrCode !!}</div>
                    <p><strong>Manual Key:</strong> {{ $secret }}</p>

                    <form method="POST" action="{{ route('admin.profile.2fa.enable') }}" class="mt-3">
                        @csrf
                        <label class="block">Enter the 6-digit code:</label>
                        <input type="text" name="otp" maxlength="6"
                            class="w-full px-3 py-2 border rounded-lg mt-4 mb-4 text-center text-lg" required>
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-primary border-2 border-primary rounded-md hover:bg-white hover:text-primary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                            Confirm & Enable
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>


    <!-- Cropper Modal -->
    <div id="cropperModal"
        class="modal fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex justify-center items-center cursor-pointer"
        onclick="handleOutsideClick(event)">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative dark:bg-gray-800 border-2 border-primary">
            <button type="button" onclick="closeModal()" class="absolute top-2 right-4 text-gray-500 hover:text-red-600">
                <img src="{{ asset('icons/exit.png') }}" alt="Close" class="w-6 h-6">
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
                        class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-md border-2 border-primary hover:bg-white hover:text-primary transition-all duration-300 dark:hover:bg-gray-800">
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
                    <img src="{{ asset('icons/exit.png') }}" alt="Exit Button"
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

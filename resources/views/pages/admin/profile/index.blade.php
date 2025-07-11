@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg border-2 border-primary">
            <h2 class="text-2xl text-center mb-6">Profile</h2>

            <!-- Profile Image Wrapper -->
            <div class="relative w-40 h-40 mx-auto mb-4">
                <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) . '?v=' . time() : asset('images/profile.jpeg') }}"
                    alt="Profile" class="w-full h-full rounded-full object-cover border">

                <!-- Overlay Button -->
                <button onclick="openModal()"
                    class="absolute inset-0 bg-primary bg-opacity-50 text-white text-xs rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300 cursor-pointer">
                    Change Profile Image
                </button>
            </div>

            <!-- User Info -->
            <div class="space-y-2 text-center">
                <p><span class="text-primary">Name:</span> {{ $user->name }}</p>
                <p><span class="text-primary">Email:</span> {{ $user->email }}</p>
            </div>
        </div>
    </div>

    <style>
        .modal {
            background-color: rgba(0, 0, 0, 0.4);
        }
    </style>

    <!-- Modal -->
    <div id="cropperModal"
        class="modal fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center cursor-pointer"
        onclick="handleOutsideClick(event)">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">

            <button type="button" onclick="closeModal()"
                class="absolute top-2 right-4 text-6xl text-gray-500 hover:text-red-600 focus:outline-none cursor-pointer"
                aria-label="Close">
                &times;
            </button>

            <h3 class="text-xl mb-4">Crop Image</h3>

            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <img id="image-to-crop" class="max-w-full max-h-[400px] mx-auto rounded">
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-2">Preview</p>
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
                        class="px-4 py-2 bg-primary hover:bg-white hover:text-primary border border-primary duration-300 ease-in-out text-white rounded cursor-pointer">
                        Crop & Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        let cropper;
        const input = document.getElementById('profile_image');
        const image = document.getElementById('image-to-crop');
        const preview = document.getElementById('preview');
        const modal = document.getElementById('cropperModal');

        window.openModal = function() {
            input.click();
        };

        window.handleOutsideClick = function(event) {
            if (event.target === modal) closeModal();
        };

        window.closeModal = function() {
            modal.classList.add('hidden');
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
                modal.classList.remove('hidden');

                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    preview: '#preview',
                });
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!cropper) return;

            cropper.getCroppedCanvas({
                width: 300,
                height: 300
            }).toBlob((blob) => {
                const formData = new FormData();
                formData.append('cropped_image', blob);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('admin.profile.updateImage') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.imageUrl) {
                            // ✅ This is where you paste it
                            document.querySelector('[x-ref="navbarProfile"]').src = data.imageUrl +
                                '?v=' + new Date().getTime();
                        }

                        closeModal();
                    })
                    .catch(error => {
                        console.error('Upload failed:', error);
                        closeModal();
                    });
            });
        });
    </script>
@endsection

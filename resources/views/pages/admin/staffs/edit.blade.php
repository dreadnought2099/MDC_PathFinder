@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div
        class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-4 sm:p-6 md:p-8 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-8"><span class="text-primary">Edit</span> <span class="dark:text-gray-300">Staff
                Member</span></h2>

        <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data" data-upload>
            @csrf
            @method('PUT')

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';
                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="first_name" placeholder="First Name" class="{{ $inputClasses }}"
                        value="{{ old('first_name', $staff->first_name) }}" required>
                    <label class="{{ $labelClasses }}">First Name</label>
                    @error('first_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <input type="text" name="last_name" placeholder="Last Name" class="{{ $inputClasses }}"
                        value="{{ old('last_name', $staff->last_name) }}" required>
                    <label class="{{ $labelClasses }}">Last Name</label>
                    @error('last_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="middle_name" placeholder="Middle Name" class="{{ $inputClasses }}"
                        value="{{ old('middle_name', $staff->middle_name) }}">
                    <label class="{{ $labelClasses }}">Middle Name</label>
                    @error('middle_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <select name="suffix"
                        class="peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800 appearance-none">
                        <option value="" hidden></option>
                        <option value="">None</option>
                        <option value="Jr." {{ old('suffix', $staff->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                        <option value="Sr." {{ old('suffix', $staff->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                        <option value="II" {{ old('suffix', $staff->suffix) == 'II' ? 'selected' : '' }}>II</option>
                        <option value="III" {{ old('suffix', $staff->suffix) == 'III' ? 'selected' : '' }}>III</option>
                        <option value="IV" {{ old('suffix', $staff->suffix) == 'IV' ? 'selected' : '' }}>IV</option>
                        <option value="V" {{ old('suffix', $staff->suffix) == 'V' ? 'selected' : '' }}>V</option>
                        <option value="VI" {{ old('suffix', $staff->suffix) == 'VI' ? 'selected' : '' }}>VI</option>
                        <option value="VII" {{ old('suffix', $staff->suffix) == 'VII' ? 'selected' : '' }}>VII</option>
                        <option value="VIII" {{ old('suffix', $staff->suffix) == 'VIII' ? 'selected' : '' }}>VIII
                        </option>
                        <option value="IX" {{ old('suffix', $staff->suffix) == 'IX' ? 'selected' : '' }}>IX</option>
                        <option value="X" {{ old('suffix', $staff->suffix) == 'X' ? 'selected' : '' }}>X</option>
                    </select>
                    <label class="{{ $labelClasses }}">Suffix (Optional)</label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    @error('suffix')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Professional Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="credentials" placeholder="Professional Credentials (Optional)"
                        class="{{ $inputClasses }}" value="{{ old('credentials', $staff->credentials) }}">
                    <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
                    @error('credentials')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <input type="text" name="position" placeholder="Position" class="{{ $inputClasses }}"
                        value="{{ old('position', $staff->position) }}">
                    <label class="{{ $labelClasses }}">Position</label>
                    @error('position')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bio -->
            <div class="relative mb-4">
                <textarea name="bio" id="bio" placeholder="Bio" rows="4" class="{{ $inputClasses }}">{{ old('bio', $staff->bio) }}</textarea>
                <label class="{{ $labelClasses }}">Bio</label>
                @error('bio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="email" placeholder="Email" name="email" id="email" class="{{ $inputClasses }}"
                        value="{{ old('email', $staff->email) }}">
                    <label class="{{ $labelClasses }}">Email</label>
                    <p id="email_error" class="text-red-500 text-sm mt-1 invisible">The email has already been taken.</p>
                </div>
                <div class="relative">
                    <input type="tel" name="phone_num" placeholder="Phone Number" class="{{ $inputClasses }}"
                        value="{{ old('phone_num', $staff->phone_num) }}" pattern="[0-9]{11}" maxlength="11"
                        oninput="this.value = this.value.replace(/\D/g,'').slice(0,11);">
                    <label class="{{ $labelClasses }}">Phone Number</label>
                </div>
            </div>

            <!-- Photo Upload -->
            <div class="mb-8">
                <label class="block mb-2 dark:text-gray-300">Staff Photo (optional, max 5MB)</label>
                <div id="staffUploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded cursor-pointer hover:border-primary hover:bg-gray-50 dark:hover:border-primary dark:hover:bg-gray-800 transition-colors relative">
                    <div id="staffPlaceholder" class="text-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                            alt="Image Icon" class="w-8 h-8 mx-auto" onerror="this.style.display='none'">
                        <span class="text-gray-500 dark:text-gray-300 block mt-2">Click to upload staff photo</span>
                    </div>
                    <div id="staffPreviewContainer" class="w-full h-full hidden"></div>
                </div>
                <input type="file" name="photo_path" id="photo_path" class="hidden"
                    accept="image/jpeg,image/jpg,image/png" />
                @error('photo_path')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button id="submitBtn" type="submit"
                    class="w-full bg-primary text-white px-4 py-2 rounded-md shadow-primary-hover hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email_error');
            const submitBtn = document.getElementById("submitBtn");

            const photoInput = document.getElementById("photo_path");
            const uploadBox = document.getElementById("staffUploadBox");
            const placeholder = document.getElementById("staffPlaceholder");
            const previewContainer = document.getElementById("staffPreviewContainer");
            const maxSize = 5 * 1024 * 1024; // 5MB
            let compressedFile = null;

            // Staff ID for edit page
            const staffId = "{{ $staff->id ?? '0' }}";

            // ===== Email Live Check =====
            let debounceTimer;

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            emailInput?.addEventListener('input', function() {
                const email = emailInput.value.trim();

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('cursor-pointer');
                emailError.classList.add('invisible');

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(async () => {
                    if (!email || !isValidEmail(email)) {
                        emailError.textContent = !email ? '' : 'Invalid email format';
                        if (email) emailError.classList.remove('invisible');
                        return;
                    }

                    try {
                        const response = await fetch("{{ route('staff.checkEmail') }}?email=" +
                            encodeURIComponent(email) + "&ignore_id=" + staffId);
                        const data = await response.json();

                        if (data.exists) {
                            emailError.textContent = 'Email already exists';
                            emailError.classList.remove('invisible');
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            submitBtn.classList.remove('cursor-pointer');
                        } else {
                            emailError.classList.add('invisible');
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            submitBtn.classList.add('cursor-pointer');
                        }
                    } catch (err) {
                        console.error('Email check failed:', err);
                        submitBtn.disabled = true;
                    }
                }, 500);
            });

            // ===== Photo Upload & Compression =====
            function showTemporaryMessage(message, type = "info") {
                const existing = document.getElementById("temp-message");
                if (existing) existing.remove();
                const div = document.createElement("div");
                div.id = "temp-message";
                div.textContent = message;
                const base =
                    "fixed top-24 right-4 p-3 rounded shadow-lg z-50 transition-opacity duration-500 border-l-4";
                const colors = {
                    success: "bg-green-100 text-green-700 border border-green-300 dark:bg-green-800 dark:text-green-200 dark:border-green-600",
                    error: "bg-red-100 text-red-700 border border-red-300 dark:bg-red-800 dark:text-red-200 dark:border-red-600",
                    info: "bg-yellow-100 text-yellow-700 border border-yellow-300 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-500"
                };
                div.className = `${base} ${colors[type] || colors.info}`;
                document.body.appendChild(div);
                setTimeout(() => {
                    div.style.opacity = "0";
                    setTimeout(() => div.remove(), 500);
                }, 3000);
            }

            async function compressImageCanvas(file, maxDimension = 1500, quality = 0.55) {
                return new Promise((resolve, reject) => {
                    if (!file.type.startsWith('image/')) return reject("Not an image file");
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = new Image();
                        img.onload = () => {
                            let width = img.width;
                            let height = img.height;
                            const scale = Math.min(maxDimension / width, maxDimension / height,
                                1);
                            width = Math.round(width * scale);
                            height = Math.round(height * scale);

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob(blob => {
                                if (!blob) return reject("Compression failed");
                                const originalName = file.name.replace(/\.[^/.]+$/, '');
                                const compressedFile = new File([blob],
                                    `${originalName}.jpg`, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                resolve(compressedFile);
                            }, 'image/jpeg', quality);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            function validateImageFile(file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showTemporaryMessage('Invalid image type. Only JPEG, JPG, PNG allowed.', 'error');
                    return false;
                }
                if (file.size > maxSize) {
                    showTemporaryMessage(`Image too large. Maximum ${maxSize/1024/1024}MB`, 'error');
                    return false;
                }
                return true;
            }

            function formatFileSize(bytes) {
                return (bytes / (1024 * 1024)).toFixed(2) + 'MB';
            }

            function createPreview(file, originalSize = 0, compressedSize = 0, url = null) {
                placeholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');

                const reader = new FileReader();
                const displayPreview = (src) => {
                    previewContainer.innerHTML = `
        <div class="relative w-full h-full">
            <img src="${src}" class="w-full h-full object-cover rounded" alt="Staff photo preview">
            ${file ? `<button type="button" id="removePhotoBtn" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors font-bold">×</button>` : ''}
            <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs p-2 font-medium">
                ${file 
                    ? `1 IMAGE(S) COMPRESSED: ${formatFileSize(originalSize)} → ${formatFileSize(compressedSize)}` 
                    : '1 IMAGE LOADED'}
            </div>
        </div>
        `;

                    if (file) {
                        document.getElementById('removePhotoBtn').addEventListener('click', e => {
                            e.stopPropagation();
                            photoInput.value = '';
                            compressedFile = null;
                            previewContainer.classList.add('hidden');
                            previewContainer.innerHTML = '';
                            placeholder.classList.remove('hidden');
                        });
                    }
                };

                if (file) {
                    reader.onload = e => displayPreview(e.target.result);
                    reader.readAsDataURL(file);
                } else if (url) {
                    displayPreview(url);
                }
            }

            async function compressAndPreviewImage(file) {
                if (!validateImageFile(file)) return;
                showTemporaryMessage('Compressing image...', 'info');
                const compressed = await compressImageCanvas(file);
                compressedFile = compressed;
                createPreview(compressed, file.size, compressed.size);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressed);
                photoInput.files = dataTransfer.files;
                showTemporaryMessage(
                    `Image compressed: ${formatFileSize(file.size)} → ${formatFileSize(compressed.size)}`,
                    'success');
            }

            // Preload existing photo for edit
            @if (isset($staff) && $staff->photo_path)
                createPreview(null, 0, 0, "{{ Storage::url($staff->photo_path) }}");
            @endif

            uploadBox?.addEventListener('click', () => photoInput.click());
            photoInput?.addEventListener('change', async () => {
                if (photoInput.files.length > 0) await compressAndPreviewImage(photoInput.files[0]);
            });
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => uploadBox?.addEventListener(ev, e => {
                e.preventDefault();
                e.stopPropagation();
            }));
            ['dragenter', 'dragover'].forEach(ev => uploadBox?.addEventListener(ev, () => uploadBox.classList.add(
                'border-primary', 'bg-gray-50')));
            ['dragleave', 'drop'].forEach(ev => uploadBox?.addEventListener(ev, () => uploadBox.classList.remove(
                'border-primary', 'bg-gray-50')));
            uploadBox?.addEventListener('drop', async e => {
                if (e.dataTransfer.files.length > 0) await compressAndPreviewImage(e.dataTransfer.files[
                    0]);
            });

            const bioTextArea = document.getElementById('bio');
            if (bioTextArea) {
                function autoResize() {
                    bioTextArea.style.height = 'auto';
                    bioTextArea.style.height = bioTextArea.scrollHeight + 'px';
                }

                // Resize on page load if there's existing content
                if (bioTextArea.value) {
                    autoResize();
                }

                // Resize on input
                bioTextArea.addEventListener('input', autoResize);
            }
        });
    </script>
@endpush

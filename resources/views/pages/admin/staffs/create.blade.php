@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div
        class="max-w-4xl mx-auto mt-10 mb-10 rounded-lg border-2 shadow-2xl border-primary p-4 sm:p-6 md:p-8 dark:bg-gray-800">
        <h2 class="text-2xl text-center mb-8"><span class="text-primary">Add</span> <span class="dark:text-gray-300">Staff
                Member</span></h2>

        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" data-upload>
            @csrf

            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';

                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <!-- Name Fields - Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="first_name" placeholder="First Name" class="{{ $inputClasses }}" required>
                    <label class="{{ $labelClasses }}">First Name</label>
                </div>

                <div class="relative">
                    <input type="text" name="last_name" placeholder="Last Name" class="{{ $inputClasses }}" required>
                    <label class="{{ $labelClasses }}">Last Name</label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="middle_name" placeholder="Middle Name" class="{{ $inputClasses }}">
                    <label class="{{ $labelClasses }}">Middle Name</label>
                </div>

                <div class="relative">
                    <select name="suffix"
                        class="peer py-3 w-full rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-400 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800 appearance-none">
                        <option value="" hidden></option>
                        <option value="">None</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                        <option value="VII">VII</option>
                        <option value="VIII">VIII</option>
                        <option value="IX">IX</option>
                        <option value="X">X</option>
                    </select>
                    <label
                        class="absolute cursor-text left-0 -top-3 text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md">
                        Suffix (Optional)
                    </label>

                    <!-- Custom dropdown arrow -->
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Professional Info - Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="text" name="credentials" placeholder="Professional Credentials (Optional)"
                        class="{{ $inputClasses }}">
                    <label class="{{ $labelClasses }}">Professional Credentials (Optional)</label>
                </div>

                <div class="relative">
                    <input type="text" name="position" placeholder="Position" class="{{ $inputClasses }}">
                    <label class="{{ $labelClasses }}">Position</label>
                </div>
            </div>

            <!-- Bio - Full Width -->
            <div class="relative mb-4">
                <textarea name="bio" placeholder="Bio" rows="4" class="{{ $inputClasses }}"></textarea>
                <label class="{{ $labelClasses }}">Bio</label>
            </div>

            <!-- Contact Info - Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="relative">
                    <input type="email" placeholder="Email" name="email" id="email" class="{{ $inputClasses }}">
                    <label class="{{ $labelClasses }}">Email</label>
                    <p id="email_error" class="text-red-500 text-sm mt-1 invisible">This email is already taken.</p>
                </div>

                <div class="relative">
                    <input type="tel" name="phone_num" placeholder="Phone Number" class="{{ $inputClasses }}"
                        pattern="[0-9]{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g,'').slice(0,11);">
                    <label class="{{ $labelClasses }}">Phone Number</label>
                </div>

            </div>

            <!-- Photo Upload - Full width in grid -->
            <div class="mb-8">
                <label class="block mb-2 dark:text-gray-300">Staff Photo (optional, max 5MB)</label>
                <div id="staffUploadBox"
                    class="flex flex-col items-center justify-center w-full h-40 p-4
                            border-2 border-dashed border-gray-300 dark:border-gray-600 
                            rounded cursor-pointer hover:border-primary hover:bg-gray-50 
                            dark:hover:border-primary dark:hover:bg-gray-800 transition-colors relative">
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

            <!-- Submit Button - Full width -->
            <div>
                <button id="submitBtn" type="submit"
                    class="w-full bg-primary text-white px-4 py-2 bg-primary rounded-md shadow-primary-hover hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover">
                    Save Staff
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

            // Staff photo upload elements
            const photoInput = document.getElementById("photo_path");
            const uploadBox = document.getElementById("staffUploadBox");
            const placeholder = document.getElementById("staffPlaceholder");
            const previewContainer = document.getElementById("staffPreviewContainer");

            const maxSize = 5 * 1024 * 1024; // 5 MB
            let compressedFile = null;

            // ===== Live email check =====
            emailInput?.addEventListener('input', function() {
                const email = emailInput.value.trim();

                if (email.length > 0) {
                    fetch(`/admin/staff/check-email?email=${encodeURIComponent(email)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.exists) {
                                emailError.classList.remove('invisible');
                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                emailError.classList.add('invisible');
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        })
                        .catch(() => emailError.classList.add('invisible'));
                } else {
                    emailError.classList.add('invisible');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            // ===== Notification System =====
            function showTemporaryMessage(message, type = "info") {
                let msgDiv = document.getElementById('temp-message');

                if (!msgDiv) {
                    msgDiv = document.createElement('div');
                    msgDiv.id = 'temp-message';
                    document.body.appendChild(msgDiv);
                }

                msgDiv.textContent = message;

                const baseClasses =
                    'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4 py-2 rounded shadow-lg transition-opacity duration-500';
                const typeClasses = {
                    success: 'bg-green-500 text-white',
                    error: 'bg-red-500 text-white',
                    info: 'bg-blue-500 text-white'
                };

                msgDiv.className = `${baseClasses} ${typeClasses[type] || typeClasses.info}`;
                msgDiv.style.display = 'block';
                msgDiv.style.opacity = '1';

                setTimeout(() => {
                    msgDiv.style.opacity = '0';
                    setTimeout(() => {
                        msgDiv.style.display = 'none';
                    }, 500);
                }, 3500);
            }

            // ===== Canvas-based Image Compression (matching Room controller) =====
            async function compressImageCanvas(file, maxDimension = 2000, quality = 0.85) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            let width = img.width;
                            let height = img.height;

                            if (width > maxDimension || height > maxDimension) {
                                if (width > height) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                } else {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');

                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            ctx.drawImage(img, 0, 0, width, height);

                            let targetQuality = quality;
                            const sizeMB = file.size / 1024 / 1024;

                            if (sizeMB > 8) targetQuality = 0.75;
                            else if (sizeMB > 5) targetQuality = 0.80;

                            canvas.toBlob(
                                (blob) => {
                                    if (blob && blob.size < file.size) {
                                        const originalName = file.name.replace(/\.[^/.]+$/,
                                            '');
                                        const compressedFile = new File([blob],
                                            `${originalName}.jpg`, {
                                                type: 'image/jpeg',
                                                lastModified: Date.now()
                                            });
                                        resolve(compressedFile);
                                    } else {
                                        resolve(file);
                                    }
                                },
                                'image/jpeg',
                                targetQuality
                            );
                        };
                        img.onerror = () => {
                            console.error('Image load failed');
                            resolve(file);
                        };
                        img.src = e.target.result;
                    };
                    reader.onerror = () => {
                        console.error('FileReader error');
                        resolve(file);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function validateImageFile(file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showTemporaryMessage('Invalid image type. Only JPEG, JPG, or PNG allowed.', 'error');
                    return false;
                }
                if (file.size > maxSize) {
                    showTemporaryMessage(`Image too large. Maximum size is ${maxSize / 1024 / 1024}MB.`, 'error');
                    return false;
                }
                return true;
            }

            function formatFileSize(bytes) {
                return (bytes / (1024 * 1024)).toFixed(2) + 'MB';
            }

            function createPreview(file, originalSize, compressedSize) {
                placeholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                <div class="relative w-full h-full">
                    <img src="${e.target.result}" 
                         class="w-full h-full object-cover rounded" 
                         alt="Staff photo preview">
                    <button type="button" 
                            id="removePhotoBtn"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors font-bold">
                        ×
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs p-2 font-medium">
                        1 IMAGE(S) COMPRESSED: ${formatFileSize(originalSize)} → ${formatFileSize(compressedSize)}
                    </div>
                </div>
            `;

                    document.getElementById('removePhotoBtn').addEventListener('click', (e) => {
                        e.stopPropagation();
                        photoInput.value = '';
                        compressedFile = null;
                        previewContainer.classList.add('hidden');
                        previewContainer.innerHTML = '';
                        placeholder.classList.remove('hidden');
                    });
                };
                reader.readAsDataURL(file);
            }

            async function compressAndPreviewImage(file) {
                if (!validateImageFile(file)) return;

                try {
                    showTemporaryMessage('Compressing staff photo...', 'info');
                    const originalSizeMB = file.size;

                    const compressed = await compressImageCanvas(file, 2000, 0.85);
                    const compressedSizeMB = compressed.size;

                    compressedFile = compressed;

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressed);
                    photoInput.files = dataTransfer.files;

                    createPreview(compressed, originalSizeMB, compressedSizeMB);

                    showTemporaryMessage(
                        `Staff photo compressed: ${formatFileSize(originalSizeMB)} → ${formatFileSize(compressedSizeMB)}`,
                        'success'
                    );
                } catch (error) {
                    console.error('Compression failed:', error);
                    showTemporaryMessage('Compression failed, using original image', 'error');
                    compressedFile = file;
                    createPreview(file, file.size, file.size);
                }
            }

            // Click to upload
            uploadBox?.addEventListener('click', (e) => {
                if (!previewContainer.contains(e.target) || previewContainer.classList.contains('hidden')) {
                    photoInput.click();
                }
            });

            // Handle file selection
            photoInput?.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) return;

                await compressAndPreviewImage(file);
            });

            // Drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadBox?.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadBox?.addEventListener(eventName, () => {
                    uploadBox.classList.add('border-primary', 'bg-gray-50');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadBox?.addEventListener(eventName, () => {
                    uploadBox.classList.remove('border-primary', 'bg-gray-50');
                });
            });

            uploadBox?.addEventListener('drop', async (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    await compressAndPreviewImage(files[0]);
                }
            });
        });
    </script>
@endpush

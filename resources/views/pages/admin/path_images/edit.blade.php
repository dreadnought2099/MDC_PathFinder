@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-7xl mx-auto space-y-8">

            <x-floating-actions />

            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2 dark:text-gray-300">
                    @if (isset($pathImages) && $pathImages->count() > 1)
                        {{-- Edit <span class="text-primary">Path Images</span> --}}
                        <span class="text-primary">Edit</span> Path Images
                    @else
                        {{-- Edit <span class="text-primary">Path Image</span> --}}
                        <span class="text-primary">Edit</span> Path Image
                    @endif
                </h1>
                <p class="text-gray-600">
                    @if (isset($pathImages) && $pathImages->count() > 1)
                        Manage multiple images for this path
                    @else
                        Update path image details or replace the file
                    @endif
                </p>
            </div>

            <!-- Path Information -->
            <div class="bg-white border-2 border-primary rounded-xl shadow-sm p-6 dark:bg-gray-800">
                <div class="flex items-center justify-center space-x-6">
                    <div class="text-center p-3 bg-primary text-white rounded-xl">
                        <i class="fas fa-door-open fa-lg mb-1"></i>
                        <p class="text-sm font-bold">
                            {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                        </p>
                    </div>
                    <i class="fas fa-arrow-right fa-lg text-gray-500"></i>
                    <div class="text-center p-3 bg-green-600 text-white rounded-xl">
                        <i class="fas fa-door-open fa-lg mb-1"></i>
                        <p class="text-sm font-bold">
                            {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4">
                    <p class="text-gray-500 text-sm">
                        Path ID: {{ $path->id }} |
                        @if (isset($pathImages) && $pathImages->count() > 1)
                            {{ $pathImages->count() }} Images
                        @else
                            Image Order: {{ $pathImages->first()->image_order ?? 'N/A' }}
                        @endif
                    </p>
                    <div class="flex space-x-2">
                        @if (isset($pathImages) && $pathImages->count() === 1)
                            <a href="{{ route('path-image.edit-multiple', $path) }}"
                                class="bg-primary hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition">
                                <i class="fas fa-images mr-1"></i> Edit All Images
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if (isset($pathImages) && $pathImages->count() > 1)
                <!-- Multiple Images Edit -->
                <form action="{{ route('path-image.update-multiple', $path) }}" method="POST" enctype="multipart/form-data"
                    id="multipleImagesForm">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="path_id" value="{{ $path->id }}">

                    <!-- Bulk Actions -->
                    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6 dark:bg-gray-800 border-2 border-primary">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="text-sm font-medium text-gray-700">Select All Images</span>
                                </label>
                                <span class="text-sm text-gray-500">Selected: <span id="selectedCount">0</span>
                                    image(s)</span>
                            </div>
                            <div class="flex space-x-3">
                                <button type="submit"
                                    class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                                <button type="button" id="bulkDeleteBtn"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition hidden">
                                    <i class="fas fa-trash mr-2"></i>Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Images Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="imagesContainer">
                        @foreach ($pathImages as $index => $image)
                            <div class="bg-white rounded-xl shadow-sm p-4 image-card transition-all duration-300 dark:text-gray-300 dark:bg-gray-800"
                                data-image-id="{{ $image->id }}">
                                <div class="relative">
                                    <!-- Selection Checkbox -->
                                    <div class="absolute top-2 right-2 z-10">
                                        <input type="checkbox" name="images[{{ $index }}][delete]" value="1"
                                            class="delete-image-checkbox rounded border-gray-300 text-red-500 focus:ring-red-500"
                                            id="delete_{{ $image->id }}">
                                    </div>

                                    <!-- Order Badge -->
                                    <div
                                        class="absolute top-2 left-2 bg-primary text-white px-2 py-1 rounded-lg text-xs font-bold z-10">
                                        {{ $image->image_order }}
                                    </div>

                                    <!-- Current Image -->
                                    <img src="{{ asset('storage/' . $image->image_file) }}"
                                        alt="Path Image {{ $image->image_order }}"
                                        class="w-full h-48 object-cover rounded-lg cursor-pointer"
                                        onclick="showImageModal('{{ asset('storage/' . $image->image_file) }}', '{{ $image->image_order }}', '{{ basename($image->image_file) }}', '{{ $image->created_at->format('M d, Y H:i') }}')">

                                    <!-- Hidden ID Field -->
                                    <input type="hidden" name="images[{{ $index }}][id]"
                                        value="{{ $image->id }}">
                                </div>

                                <div class="mt-4 space-y-4">
                                    <!-- Image Order Input -->
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1 dark:text-gray-300">Display Order</label>
                                        <input type="number" name="images[{{ $index }}][image_order]"
                                            value="{{ $image->image_order }}" min="1"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring focus:ring-primary focus:outline-none">
                                    </div>

                                    <!-- Replace Image File -->
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1 dark:text-gray-300">Replace Image</label>
                                        <input type="file" name="images[{{ $index }}][image_file]"
                                            accept="image/*" onchange="previewImageChange(this, {{ $index }})"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary file:text-white file:text-xs">

                                        <!-- New Image Preview -->
                                        <div id="newPreview_{{ $index }}" class="hidden mt-2">
                                            <img id="previewImg_{{ $index }}"
                                                class="w-full h-24 object-cover rounded border-2 border-green-400">
                                            <button type="button" onclick="clearPreview({{ $index }})"
                                                class="mt-1 text-xs text-red-500 hover:text-red-700">
                                                <i class="fas fa-times mr-1"></i>Clear
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Image Info -->
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span>ID: {{ $image->id }}</span>
                                        <span>{{ $image->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Actions -->
                    <div class="text-center mt-8">
                        <button type="submit"
                            class="bg-primary hover:bg-blue-600 text-white px-8 py-3 rounded-xl text-lg font-medium transition">
                            <i class="fas fa-save mr-2"></i>Save All Changes
                        </button>
                        <a href="{{ route('path.show', $path) }}"
                            class="ml-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl text-lg font-medium transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </form>

                <!-- Bulk Delete Form -->
                <form id="bulkDeleteForm" action="{{ route('path-image.destroy-multiple', $path) }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="path_id" value="{{ $path->id }}">
                    <div id="bulkDeleteIds"></div>
                </form>
            @else
                <!-- Single Image Edit (Original functionality) -->
                @php $pathImage = $pathImages->first(); @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Current Image -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">
                            <i class="fas fa-image text-primary"></i> Current Image
                        </h2>
                        <div class="bg-gray-50 border-2 border-dashed rounded-xl p-4 text-center">
                            <img src="{{ asset('storage/' . $pathImage->image_file) }}"
                                alt="Path Image {{ $pathImage->image_order }}"
                                class="mx-auto rounded-lg shadow max-h-80 cursor-pointer"
                                onclick="showImageModal('{{ asset('storage/' . $pathImage->image_file) }}', '{{ $pathImage->image_order }}', '{{ basename($pathImage->image_file) }}', '{{ $pathImage->created_at->format('M d, Y H:i') }}')">
                            <p class="mt-3 inline-block bg-primary text-white text-xs px-3 py-1 rounded-full">
                                Order: {{ $pathImage->image_order }}
                            </p>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <strong>File:</strong><br>
                                {{ basename($pathImage->image_file) }}
                            </div>
                            <div>
                                <strong>Uploaded:</strong><br>
                                {{ $pathImage->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-6 flex space-x-4">
                            <button type="button" onclick="showDeleteModal()"
                                class="flex-1 border border-red-500 text-red-500 rounded-xl px-4 py-2 hover:bg-red-50 transition">
                                <i class="fas fa-trash"></i> Delete Image
                            </button>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">
                            <i class="fas fa-edit text-primary"></i> Edit Image
                        </h2>

                        @if ($errors->any())
                            <div class="mb-4 p-4 border border-red-300 bg-red-50 text-red-700 rounded-xl">
                                <p class="font-semibold"><i class="fas fa-exclamation-triangle"></i> Please fix the
                                    following:</p>
                                <ul class="list-disc pl-5 mt-2 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('path-image.update', $pathImage->id) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Image Order -->
                            <div>
                                <label class="block text-sm text-gray-800 mb-2">Image Order</label>
                                <input type="number" name="image_order"
                                    value="{{ old('image_order', $pathImage->image_order) }}" min="1"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:border-primary focus:ring focus:ring-primary focus:outline-none transition">
                                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first. Leave empty to keep
                                    current order.</p>
                            </div>

                            <!-- Replace Image File -->
                            <div>
                                <label class="block text-sm text-gray-800 mb-2">Replace Image File</label>
                                <input type="file" name="image_file" accept="image/*"
                                    onchange="previewNewImage(this)"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:cursor-pointer hover:file:bg-blue-500">

                                <p class="text-xs text-gray-500 mt-2">
                                    Leave empty to keep current image. Max 50MB. Supported: JPG, PNG, GIF, SVG, WEBP.
                                </p>

                                <!-- New Image Preview -->
                                <div id="newImagePreview" class="hidden mt-4 text-center">
                                    <p class="text-green-600 text-sm mb-2"><i class="fas fa-eye"></i> New Image Preview:
                                    </p>
                                    <img id="previewImage" class="mx-auto rounded-xl border border-green-400 max-h-52">
                                    <button type="button" onclick="clearImagePreview()"
                                        class="mt-2 border border-gray-300 rounded-lg px-3 py-1 text-sm hover:bg-gray-100">
                                        <i class="fas fa-times"></i> Clear Selection
                                    </button>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div>
                                <button type="submit"
                                    class="w-full bg-primary text-white rounded-xl px-6 py-3 shadow hover:bg-white hover:text-primary border-2 border-primary transition">
                                    <i class="fas fa-save"></i> Update Image
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Additional Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6 dark:bg-gray-800">
                <h3 class="text-md text-center text-gray-700 mb-3 dark:text-gray-300">
                    <i class="fas fa-info-circle text-primary"></i> Update Information
                </h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 dark:text-gray-300">
                    @if (isset($pathImages) && $pathImages->count() > 1)
                        <li>Select multiple images to delete them in bulk</li>
                        <li>You can update order numbers and replace files simultaneously</li>
                        <li>Changes are saved when you click "Save All Changes"</li>
                        <li class="text-secondary"><i class="fas fa-exclamation-triangle"></i> Deleted and replaced images
                            cannot be recovered</li>
                    @else
                        <li>You can update the image order without replacing the file</li>
                        <li>You can replace the image file without changing the order</li>
                        <li>Both fields are optional - update only what you need</li>
                        <li class="text-secondary"><i class="fas fa-exclamation-triangle"></i> Replacing the image will
                            delete the old file permanently</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <dialog id="imageModal" class="p-0 rounded-xl w-full max-w-3xl backdrop:bg-black backdrop:bg-opacity-50">
        <div class="bg-white rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Path Image</h2>
            <div class="text-center">
                <img id="modalImage" class="mx-auto rounded-lg max-h-[600px]">
                <div id="modalInfo" class="mt-3 text-sm text-gray-600"></div>
            </div>
            <div class="mt-6 text-right">
                <button onclick="closeImageModal()" class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100">
                    Close
                </button>
            </div>
        </div>
    </dialog>

    <!-- Delete Modal -->
    <dialog id="deleteModal" class="p-0 rounded-xl w-full max-w-lg backdrop:bg-black backdrop:bg-opacity-50">
        <div class="bg-white rounded-xl p-6">
            <h2 class="text-lg font-semibold text-red-600 mb-3">
                <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
            </h2>
            <div id="deleteContent">
                @if (isset($pathImages) && $pathImages->count() === 1)
                    @php $pathImage = $pathImages->first(); @endphp
                    <p class="text-sm mb-3">Are you sure you want to delete this image?</p>
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 text-sm mb-3">
                        <p><strong>Image:</strong> {{ basename($pathImage->image_file) }}</p>
                        <p><strong>Order:</strong> {{ $pathImage->image_order }}</p>
                        <p><strong>Path:</strong>
                            {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                            →
                            {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                        </p>
                    </div>
                @endif
            </div>
            <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>

            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeDeleteModal()"
                    class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <div id="deleteAction">
                    @if (isset($pathImages) && $pathImages->count() === 1)
                        @php $pathImage = $pathImages->first(); @endphp
                        <form action="{{ route('path-image.destroy', $pathImage) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                <i class="fas fa-trash"></i> Delete Image
                            </button>
                        </form>
                    @else
                        <button type="button" id="confirmBulkDelete"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </dialog>

    <script>
        // Single image preview (original functionality)
        function previewNewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('newImagePreview').classList.remove('hidden');
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function clearImagePreview() {
            document.querySelector('input[name="image_file"]').value = '';
            document.getElementById('newImagePreview').classList.add('hidden');
            document.getElementById('previewImage').src = '';
        }

        // Multiple images functionality
        function previewImageChange(input, index) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById(`newPreview_${index}`).classList.remove('hidden');
                    document.getElementById(`previewImg_${index}`).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function clearPreview(index) {
            document.querySelector(`input[name="images[${index}][image_file]"]`).value = '';
            document.getElementById(`newPreview_${index}`).classList.add('hidden');
            document.getElementById(`previewImg_${index}`).src = '';
        }

        // Modal functions
        function showImageModal(src, order, filename, date) {
            document.getElementById('modalTitle').textContent = `Path Image - Order ${order}`;
            document.getElementById('modalImage').src = src;
            document.getElementById('modalInfo').innerHTML = `
                <p><strong>File:</strong> ${filename}</p>
                <p><strong>Order:</strong> ${order}</p>
                <p><strong>Uploaded:</strong> ${date}</p>
            `;
            document.getElementById('imageModal').showModal();
        }

        function closeImageModal() {
            document.getElementById('imageModal').close();
        }

        function showDeleteModal() {
            document.getElementById('deleteModal').showModal();
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').close();
        }

        // Multiple images selection logic
        @if (isset($pathImages) && $pathImages->count() > 1)
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const deleteCheckboxes = document.querySelectorAll('.delete-image-checkbox');
                const selectedCountSpan = document.getElementById('selectedCount');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const imageCards = document.querySelectorAll('.image-card');

                function updateSelectionUI() {
                    const selectedCheckboxes = document.querySelectorAll('.delete-image-checkbox:checked');
                    const count = selectedCheckboxes.length;

                    selectedCountSpan.textContent = count;

                    if (count > 0) {
                        bulkDeleteBtn.classList.remove('hidden');
                    } else {
                        bulkDeleteBtn.classList.add('hidden');
                    }

                    // Update select all checkbox state
                    if (count === 0) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = false;
                    } else if (count === deleteCheckboxes.length) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.indeterminate = true;
                        selectAllCheckbox.checked = false;
                    }

                    // Update card appearances
                    imageCards.forEach(card => {
                        const checkbox = card.querySelector('.delete-image-checkbox');
                        if (checkbox && checkbox.checked) {
                            card.classList.add('opacity-60', 'border-2', 'border-red-300', 'bg-red-50');
                        } else {
                            card.classList.remove('opacity-60', 'border-2', 'border-red-300', 'bg-red-50');
                        }
                    });
                }

                selectAllCheckbox.addEventListener('change', function() {
                    deleteCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectionUI();
                });

                deleteCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectionUI);
                });

                bulkDeleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const selectedCount = document.querySelectorAll('.delete-image-checkbox:checked')
                        .length;

                    document.getElementById('deleteContent').innerHTML = `
                    <p class="text-sm mb-3">Are you sure you want to delete <strong>${selectedCount}</strong> selected image(s)?</p>
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 text-sm mb-3">
                        <p>This will permanently delete ${selectedCount} image(s) from the path.</p>
                    </div>
                `;

                    showDeleteModal();
                });

                document.getElementById('confirmBulkDelete').addEventListener('click', function() {
                    const selectedCheckboxes = document.querySelectorAll('.delete-image-checkbox:checked');
                    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                    const bulkDeleteIds = document.getElementById('bulkDeleteIds');

                    bulkDeleteIds.innerHTML = '';

                    selectedCheckboxes.forEach((checkbox, index) => {
                        const imageCard = checkbox.closest('[data-image-id]');
                        const imageId = imageCard.getAttribute('data-image-id');

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `image_ids[${index}]`;
                        hiddenInput.value = imageId;
                        bulkDeleteIds.appendChild(hiddenInput);
                    });

                    bulkDeleteForm.submit();
                });

                updateSelectionUI();
            });
        @endif
    </script>
@endsection

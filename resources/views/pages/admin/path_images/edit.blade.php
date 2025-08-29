@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Edit <span class="text-primary">Path Image</span>
                </h1>
                <p class="text-gray-600">Update path image details or replace the file</p>
            </div>

            <!-- Path Information -->
            <div class="bg-white border-2 border-primary rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-center space-x-6">
                    <div class="text-center p-3 bg-primary text-white rounded-xl">
                        <i class="fas fa-door-open fa-lg mb-1"></i>
                        <p class="text-sm font-bold">
                            {{ $pathImage->path->fromRoom->name ?? 'Room #' . $pathImage->path->from_room_id }}
                        </p>
                    </div>
                    <i class="fas fa-arrow-right fa-lg text-gray-500"></i>
                    <div class="text-center p-3 bg-green-600 text-white rounded-xl">
                        <i class="fas fa-door-open fa-lg mb-1"></i>
                        <p class="text-sm font-bold">
                            {{ $pathImage->path->toRoom->name ?? 'Room #' . $pathImage->path->to_room_id }}
                        </p>
                    </div>
                </div>
                <p class="text-gray-500 text-sm mt-4 text-center">
                    Path ID: {{ $pathImage->path->id }} | Image Order: {{ $pathImage->image_order }}
                </p>
            </div>

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
                            onclick="document.getElementById('imageModal').showModal()">
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
                        <a href="{{ route('path.show', $pathImage->path) }}"
                            class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-center hover:bg-gray-100 transition">
                            <i class="fas fa-arrow-left"></i> Back to Path
                        </a>
                        <button type="button" onclick="document.getElementById('deleteModal').showModal()"
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
                            <p class="font-semibold"><i class="fas fa-exclamation-triangle"></i> Please fix the following:
                            </p>
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
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first. Leave empty to keep current
                                order.</p>
                        </div>

                        <!-- Replace Image File -->
                        <div>
                            <label class="block text-sm text-gray-800 mb-2">Replace Image File</label>
                            <input type="file" name="image_file" accept="image/*" onchange="previewNewImage(this)"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:cursor-pointer hover:file:bg-blue-500">

                            <p class="text-xs text-gray-500 mt-2">
                                Leave empty to keep current image. Max 50MB. Supported: JPG, PNG, GIF, SVG, WEBP.
                            </p>

                            <!-- New Image Preview -->
                            <div id="newImagePreview" class="hidden mt-4 text-center">
                                <p class="text-green-600 text-sm mb-2"><i class="fas fa-eye"></i> New Image Preview:</p>
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

            <!-- Additional Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-700 mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Update Information
                </h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>You can update the image order without replacing the file</li>
                    <li>You can replace the image file without changing the order</li>
                    <li>Both fields are optional - update only what you need</li>
                    <li class="text-red-600"><i class="fas fa-exclamation-triangle"></i> Replacing the image will delete the
                        old file permanently</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <dialog id="imageModal" class="p-0 rounded-xl w-full max-w-3xl">
        <div class="bg-white rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-4">Path Image - Order {{ $pathImage->image_order }}</h2>
            <div class="text-center">
                <img src="{{ asset('storage/' . $pathImage->image_file) }}" class="mx-auto rounded-lg max-h-[600px]">
                <div class="mt-3 text-sm text-gray-600">
                    <p><strong>File:</strong> {{ basename($pathImage->image_file) }}</p>
                    <p><strong>Order:</strong> {{ $pathImage->image_order }}</p>
                    <p><strong>Uploaded:</strong> {{ $pathImage->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-6 text-right">
                <button onclick="document.getElementById('imageModal').close()"
                    class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100">
                    Close
                </button>
            </div>
        </div>
    </dialog>

    <!-- Delete Modal -->
    <dialog id="deleteModal" class="p-0 rounded-xl w-full max-w-lg">
        <div class="bg-white rounded-xl p-6">
            <h2 class="text-lg font-semibold text-red-600 mb-3">
                <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
            </h2>
            <p class="text-sm mb-3">Are you sure you want to delete this image?</p>
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 text-sm mb-3">
                <p><strong>Image:</strong> {{ basename($pathImage->image_file) }}</p>
                <p><strong>Order:</strong> {{ $pathImage->image_order }}</p>
                <p><strong>Path:</strong>
                    {{ $pathImage->path->fromRoom->name ?? 'Room #' . $pathImage->path->from_room_id }}
                    →
                    {{ $pathImage->path->toRoom->name ?? 'Room #' . $pathImage->path->to_room_id }}
                </p>
            </div>
            <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>

            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="document.getElementById('deleteModal').close()"
                    class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <form action="{{ route('path-image.destroy', $pathImage) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash"></i> Delete Image
                    </button>
                </form>
            </div>
        </div>
    </dialog>

    <script>
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
            document.getElementById('image_file').value = '';
            document.getElementById('newImagePreview').classList.add('hidden');
            document.getElementById('previewImage').src = '';
        }
    </script>
@endsection

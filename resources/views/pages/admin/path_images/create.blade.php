@extends('layouts.app')

@section('title', 'Upload Path Images')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6">

        <h2 class="text-2xl text-center mb-6"><span class="text-primary">Upload</span> Path Images</h2>

        {{-- Upload Form + Alpine Modal --}}
        <div x-data="{ uploading: false, progress: 0 }" x-on:upload-start.window="uploading = true; progress = 0"
            x-on:upload-progress.window="progress = $event.detail.progress" x-on:upload-finish.window="uploading = false">

            <form id="uploadForm" action="{{ route('path-image.store', $defaultPath) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Path Selector --}}
                <div class="mb-4">
                    <label for="path_id" class="block text-gray-700 font-semibold mb-2">Select Path</label>
                    <select name="path_id" id="path_id" required class="w-full border rounded px-3 py-2">
                        <option value="" disabled selected>-- Choose a path --</option>
                        @foreach ($paths as $path)
                            <option value="{{ $path->id }}">
                                {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }} →
                                {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropzone --}}
                <label for="fileInput"
                    class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary hover:bg-gray-50 transition-colors p-4 overflow-auto relative">
                    <i class="fas fa-cloud-upload-alt fa-4x text-gray-400 mb-2"></i>
                    <span class="text-gray-500 mb-2">Drop images here or click to browse</span>
                    <span class="text-xs text-gray-400">
                        JPG, JPEG, PNG, GIF, BMP, SVG, WEBP | max 50MB each | multiple allowed
                    </span>
                    <input type="file" name="files[]" id="fileInput" multiple accept="image/*" class="hidden">
                </label>

                {{-- Selected Files Preview --}}
                <div id="selectedFiles" class="grid grid-cols-2 md:grid-cols-3 gap-4" style="display:none;"></div>

                {{-- Submit Button --}}
                <button type="submit" id="submitBtn"
                    class="w-full bg-primary text-white px-4 py-2 rounded hover:bg-white hover:text-primary border-2 border-primary disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    <i class="fas fa-upload mr-2"></i> Upload Images
                </button>
            </form>

            {{-- Upload Modal --}}
            <div x-show="uploading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                style="display:none;">
                <div class="bg-white rounded p-6 w-96 shadow-lg">
                    <h2 class="text-lg mb-4">{{ $title ?? 'Uploading...' }}</h2>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                        <div class="h-4 rounded-full bg-primary progress-bar transition-all duration-300 ease-out"
                            :style="'width:' + progress + '%'"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600" x-text="progress + '%'"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const submitBtn = document.getElementById('submitBtn');
            const uploadForm = document.getElementById('uploadForm');
            const pathSelect = document.getElementById('path_id');
            const uploadEventTarget = document;

            let files = [];

            function updateSubmitButton() {
                const pathSelected = pathSelect.value !== '';
                submitBtn.disabled = files.length === 0 || !pathSelected;
            }

            fileInput.addEventListener('change', () => {
                addFiles(Array.from(fileInput.files));
                fileInput.value = '';
                updateSubmitButton();
            });

            pathSelect.addEventListener('change', updateSubmitButton);

            function addFiles(newFiles) {
                newFiles.forEach(file => {
                    if (file.type.startsWith('image/') && file.size <= 50 * 1024 * 1024) { // 50MB
                        files.push(file);
                    } else {
                        console.warn(`File ${file.name} ignored: Invalid type or size exceeds 50MB`);
                    }
                });
                renderPreviews();
                updateSubmitButton();
            }

            function removeFile(index) {
                files.splice(index, 1);
                renderPreviews();
                updateSubmitButton();
            }

            function renderPreviews() {
                selectedFilesContainer.innerHTML = '';
                if (!files.length) {
                    selectedFilesContainer.style.display = 'none';
                    return;
                }
                selectedFilesContainer.style.display = 'grid';
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden border shadow-sm';
                    reader.onload = e => {
                        div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs" onclick="removeFile(${index})">&times;</button>
                `;
                    };
                    reader.readAsDataURL(file);
                    selectedFilesContainer.appendChild(div);
                });
            }

            window.removeFile = removeFile;

            uploadForm.addEventListener('submit', async e => {
                e.preventDefault();
                if (!files.length || !pathSelect.value) return;

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('path_id', pathSelect.value);
                files.forEach(file => formData.append('files[]', file));

                uploadEventTarget.dispatchEvent(new CustomEvent('upload-start'));

                try {
                    const response = await fetch(uploadForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    console.log('Response:', data);

                    if (response.ok) {
                        if (data.redirect) window.location.href = data.redirect;
                        else location.reload();
                    } else if (response.status === 422) {
                        alert('Validation error: ' + (data.errors ? Object.values(data.errors).join(
                            ', ') : data.message));
                    } else {
                        alert('Upload failed. Status: ' + response.status);
                    }
                } catch (err) {
                    console.error('Upload error:', err);
                    alert('Upload failed. Check the console for details.');
                } finally {
                    uploadEventTarget.dispatchEvent(new CustomEvent('upload-finish'));
                }
            });
        });
    </script>
@endpush

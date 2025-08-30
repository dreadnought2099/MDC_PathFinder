@extends('layouts.app')

@section('title', 'Upload Path Images')

@section('content')
    <x-floating-actions />

    <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6">
        <h2 class="text-2xl text-center mb-6">
            <span class="text-primary">Upload</span> Path Images
        </h2>

        <form id="uploadForm" action="{{ route('path-image.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6" data-upload onsubmit="return false;">
            @csrf

            {{-- Path Selector --}}
            <div class="mb-4">
                <label for="path_id" class="block text-gray-700 font-semibold mb-2">Select Path</label>
                <select name="path_id" id="path_id" required class="w-full border rounded px-3 py-2">
                    @foreach ($paths as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $defaultPath->id ? 'selected' : '' }}>
                            {{ $p->fromRoom->name ?? 'Room #' . $p->from_room_id }} →
                            {{ $p->toRoom->name ?? 'Room #' . $p->to_room_id }}
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

            <div id="fileError" class="text-red-500 text-sm mt-2 hidden"></div>

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
        <div id="uploadModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded p-6 w-96 shadow-lg">
                <h2 class="text-lg mb-4">Uploading...</h2>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                    <div id="progressBar" class="h-4 rounded-full bg-primary transition-all duration-300 ease-out"
                        style="width: 0%"></div>
                </div>
                <p id="progressText" class="mt-2 text-sm text-gray-600">0%</p>
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
            const uploadModal = document.getElementById('uploadModal');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            let files = [];

            function updateSubmitButton() {
                submitBtn.disabled = files.length === 0 || pathSelect.value === '';
            }

            fileInput.addEventListener('change', () => {
                addFiles(Array.from(fileInput.files));
                fileInput.value = '';
                updateSubmitButton();
            });

            // Change dropdown → reload with new default path
            pathSelect.addEventListener('change', function() {
                const selectedId = this.value;
                if (selectedId) {
                    let baseUrl = "{{ route('path-image.create', ':id') }}".replace(':id', selectedId);
                    window.location.href = baseUrl;
                }
            });

            function addFiles(newFiles) {
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');
                let errorMessages = [];

                newFiles.forEach(file => {
                    if (file.type.startsWith('image/') && file.size <= 50 * 1024 * 1024) {
                        files.push(file);
                    } else {
                        let error = `File ${file.name}: `;
                        if (!file.type.startsWith('image/')) error += 'Must be an image.';
                        else error += 'Size exceeds 50MB.';
                        errorMessages.push(error);
                    }
                });

                if (errorMessages.length > 0) {
                    fileError.textContent = errorMessages.join(' ');
                    fileError.classList.remove('hidden');
                }

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
                    <button type="button"
                        class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full
                        flex items-center justify-center text-xs"
                        onclick="removeFile(${index})">&times;</button>`;
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

                // Show modal
                uploadModal.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = '0%';

                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', e => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressText.textContent = `${percent}%`;
                    }
                });

                xhr.addEventListener('load', () => {
                    uploadModal.classList.add('hidden');
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (xhr.status === 200) {
                            if (data.redirect) window.location.href = data.redirect;
                            else location.reload();
                        } else if (xhr.status === 422) {
                            alert('Validation error: ' + (data.errors ? Object.values(data
                                .errors).join(', ') : data.message));
                        } else {
                            alert('Upload failed. Status: ' + xhr.status);
                        }
                    } catch (err) {
                        console.error('Response parse error:', err);
                        alert('Upload failed. Check console for details.');
                    }
                });

                xhr.addEventListener('error', () => {
                    uploadModal.classList.add('hidden');
                    alert('Upload failed. Check console for details.');
                });

                xhr.open('POST', uploadForm.action);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
        });
    </script>
@endpush

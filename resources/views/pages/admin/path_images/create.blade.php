@extends('layouts.app')

@section('title', 'Upload Path Images')

@section('content')
    @if (auth()->user()->hasRole('Admin'))
        <x-floating-actions />

        <div class="max-w-xl mx-auto mt-10 rounded-lg border-2 shadow-2xl border-primary p-6">
            <h2 class="text-2xl text-center mb-6 dark:text-gray-300">
                <span class="text-primary">Upload</span> Path Images
            </h2>

            <form id="uploadForm" action="{{ route('path-image.store') }}" method="POST" enctype="multipart/form-data"
                data-upload>
                @csrf
                {{-- Path Selector --}}
                <div class="mb-4 dark:text-gray-300">
                    <label for="path_id" class="block text-gray-700 mb-2 dark:text-gray-300">Select Path</label>
                    <select name="path_id" id="path_id" required
                        class="w-full border border-primary rounded px-3 py-2 dark:bg-gray-800">
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
                    class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary dark:hover:bg-gray-800 transition-colors p-4 overflow-auto relative">
                    <span class="text-gray-600 dark:text-gray-300 mb-2">Drop images here or click to browse</span>
                    <span class="text-xs text-gray-400">
                        JPG, JPEG, PNG, GIF, BMP, SVG, WEBP | max 50MB each | multiple allowed
                    </span>
                    <input type="file" name="files[]" id="fileInput" multiple accept="image/*" class="hidden">
                </label>

                <div id="fileError" class="text-red-500 text-sm mt-2 hidden"></div>
                <div id="selectedFiles" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-primary text-white px-4 py-2 rounded-md border-2 border-primary duration-300 transition-all ease-in-out mt-4 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:bg-white hover:text-primary dark:hover:bg-gray-800 shadow-primary-hover"
                    disabled>
                    Upload Images
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
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const submitBtn = document.getElementById('submitBtn');
            const uploadForm = document.getElementById('uploadForm');
            const pathSelect = document.getElementById('path_id');

            let files = [];
            let isSubmitting = false;

            // Session storage key
            const STORAGE_KEY = 'selected_path_id';

            // Get the base URL from Laravel (more robust than hardcoding)
            const createRouteBase = "{{ url('/admin/path-images/create') }}";

            // Load saved path selection from sessionStorage
            function loadSavedPathSelection() {
                const savedPathId = sessionStorage.getItem(STORAGE_KEY);
                if (savedPathId) {
                    const option = pathSelect.querySelector(`option[value="${savedPathId}"]`);
                    if (option) {
                        pathSelect.value = savedPathId;
                    }
                }
            }

            // Save path selection to sessionStorage
            function savePathSelection() {
                sessionStorage.setItem(STORAGE_KEY, pathSelect.value);
            }

            // Handle path selection change - redirect to maintain URL consistency
            function handlePathChange() {
                const selectedPathId = pathSelect.value;
                console.log('Selected Path ID:', selectedPathId);
                console.log('Create Route Base:', createRouteBase);

                if (selectedPathId) {
                    savePathSelection();
                    const newUrl = `${createRouteBase}/${selectedPathId}`;
                    console.log('Redirecting to:', newUrl);
                    // Redirect to the create route with the selected path
                    window.location.href = newUrl;
                }
            }

            // Handle path selection change
            pathSelect.addEventListener('change', function() {
                console.log('Dropdown changed!');
                handlePathChange();
            });

            function updateSubmitButton() {
                submitBtn.disabled = files.length === 0 || isSubmitting;
            }

            function addFiles(newFiles) {
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');
                let errorMessages = [];

                newFiles.forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        errorMessages.push(`File ${file.name} is not an image.`);
                    } else if (file.size > 50 * 1024 * 1024) {
                        errorMessages.push(`File ${file.name} exceeds 50MB.`);
                    } else {
                        files.push(file);
                    }
                });

                if (errorMessages.length) {
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
                if (!files.length) return;

                files.forEach((file, index) => {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden border shadow-sm';
                    reader.onload = e => {
                        div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full
                    flex items-center justify-center text-xs hover:bg-red-600"
                    onclick="removeFile(${index})">&times;</button>
                `;
                    };
                    reader.readAsDataURL(file);
                    selectedFilesContainer.appendChild(div);
                });
            }

            // Make removeFile function global
            window.removeFile = removeFile;

            // File input change handler
            fileInput.addEventListener('change', function() {
                addFiles(Array.from(this.files));
            });

            // Initialize on page load
            loadSavedPathSelection();
        });
    </script>
@endpush

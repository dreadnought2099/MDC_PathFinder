@extends('layouts.app')

@section('title', 'Upload Path Images')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Path Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-route"></i> Path Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center py-3">
                            <div class="text-center p-3 bg-primary text-white rounded mr-3">
                                <i class="fas fa-door-open fa-2x mb-2"></i>
                                <br>
                                <strong>{{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}</strong>
                            </div>
                            <div class="mx-4">
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                            </div>
                            <div class="text-center p-3 bg-success text-white rounded ml-3">
                                <i class="fas fa-door-open fa-2x mb-2"></i>
                                <br>
                                <strong>{{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}</strong>
                            </div>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Path ID: {{ $path->id }}</small>
                        </div>
                    </div>
                </div>

                <!-- Upload Images Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Path Images
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('path.show', $path) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Path
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="uploadForm" action="{{ route('path_images.store', $path) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- File Upload Area -->
                            <div class="upload-area mb-4">
                                <div class="upload-drop-zone" id="dropZone">
                                    <div class="upload-content text-center p-5">
                                        <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Drop images here or click to browse</h5>
                                        <p class="text-muted">
                                            You can upload multiple images at once.<br>
                                            Maximum file size: 50MB per image<br>
                                            Supported formats: JPG, JPEG, PNG, GIF, BMP, SVG, WEBP
                                        </p>
                                        <input type="file" id="fileInput" name="files[]" multiple accept="image/*"
                                            style="display: none;">
                                        <button type="button" class="btn btn-primary"
                                            onclick="document.getElementById('fileInput').click()">
                                            <i class="fas fa-folder-open"></i> Choose Images
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Files Preview -->
                            <div id="selectedFiles" class="mb-4" style="display: none;">
                                <h5 class="mb-3">
                                    <i class="fas fa-images"></i> Selected Images
                                    <span id="fileCount" class="badge badge-primary">0</span>
                                </h5>
                                <div id="fileList" class="row"></div>
                            </div>

                            <!-- Upload Progress -->
                            <div id="uploadProgress" class="mb-4" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="fas fa-upload"></i> Uploading images...</span>
                                    <span id="progressText">0%</span>
                                </div>
                                <div class="progress">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" id="submitBtn" class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-upload"></i> Upload Images
                                </button>
                                <a href="{{ route('path.show', $path) }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Upload Tips Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb"></i> Upload Tips
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-sort-numeric-down fa-2x text-primary mr-3 mt-1"></i>
                                    <div>
                                        <h6>Automatic Ordering</h6>
                                        <small class="text-muted">Images will be automatically ordered based on upload
                                            sequence. You can reorder them later.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-file-image fa-2x text-success mr-3 mt-1"></i>
                                    <div>
                                        <h6>Supported Formats</h6>
                                        <small class="text-muted">JPG, JPEG, PNG, GIF, BMP, SVG, and WEBP files are
                                            supported.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-weight-hanging fa-2x text-warning mr-3 mt-1"></i>
                                    <div>
                                        <h6>File Size Limit</h6>
                                        <small class="text-muted">Each image can be up to 50MB in size for high-quality
                                            uploads.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .upload-drop-zone {
            border: 2px dashed #ddd;
            border-radius: 10px;
            background-color: #fafafa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-drop-zone:hover,
        .upload-drop-zone.dragover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .file-preview {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            margin-bottom: 15px;
        }

        .file-preview img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .file-preview-info {
            padding: 10px;
            font-size: 0.85em;
        }

        .file-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-remove:hover {
            background: rgba(220, 53, 69, 1);
        }

        .progress {
            height: 20px;
        }

        .file-size {
            color: #666;
            font-size: 0.8em;
        }

        .file-name {
            word-break: break-word;
            margin-bottom: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const selectedFiles = document.getElementById('selectedFiles');
            const fileList = document.getElementById('fileList');
            const fileCount = document.getElementById('fileCount');
            const submitBtn = document.getElementById('submitBtn');
            const uploadForm = document.getElementById('uploadForm');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            let files = [];

            // Drag and drop functionality
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                const droppedFiles = Array.from(e.dataTransfer.files);
                addFiles(droppedFiles);
            });

            // Click to upload
            dropZone.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                const selectedFilesArray = Array.from(this.files);
                addFiles(selectedFilesArray);
            });

            function addFiles(newFiles) {
                newFiles.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        files.push(file);
                    }
                });
                updateFileList();
                updateSubmitButton();
            }

            function removeFile(index) {
                files.splice(index, 1);
                updateFileList();
                updateSubmitButton();
            }

            function updateFileList() {
                fileList.innerHTML = '';
                fileCount.textContent = files.length;

                if (files.length > 0) {
                    selectedFiles.style.display = 'block';

                    files.forEach((file, index) => {
                        const filePreview = document.createElement('div');
                        filePreview.className = 'col-lg-3 col-md-4 col-sm-6';

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            filePreview.innerHTML = `
                        <div class="file-preview">
                            <button type="button" class="file-remove" onclick="removeFile(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="${e.target.result}" alt="Preview">
                            <div class="file-preview-info">
                                <div class="file-name text-truncate" title="${file.name}">
                                    <strong>${file.name}</strong>
                                </div>
                                <div class="file-size text-muted">
                                    ${formatFileSize(file.size)}
                                </div>
                            </div>
                        </div>
                    `;
                        };
                        reader.readAsDataURL(file);

                        fileList.appendChild(filePreview);
                    });
                } else {
                    selectedFiles.style.display = 'none';
                }
            }

            function updateSubmitButton() {
                submitBtn.disabled = files.length === 0;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Make removeFile function globally accessible
            window.removeFile = removeFile;

            // Handle form submission with progress
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (files.length === 0) return;

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                files.forEach(file => {
                    formData.append('files[]', file);
                });

                // Show progress
                uploadProgress.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

                // Use XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = Math.round(percentComplete) + '%';
                    }
                });

                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        alert('Upload failed. Please try again.');
                        resetUploadState();
                    }
                });

                xhr.addEventListener('error', function() {
                    alert('Upload failed. Please try again.');
                    resetUploadState();
                });

                xhr.open('POST', uploadForm.action);
                xhr.send(formData);
            });

            function resetUploadState() {
                uploadProgress.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Images';
                progressBar.style.width = '0%';
                progressText.textContent = '0%';
            }
        });
    </script>
@endpush

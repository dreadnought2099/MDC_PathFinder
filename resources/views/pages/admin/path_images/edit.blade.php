@extends('layouts.app')

@section('title', 'Edit Path Image')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Path Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-route"></i> Path Information
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center py-2">
                            <div class="text-center p-2 bg-primary text-white rounded mr-3">
                                <i class="fas fa-door-open fa-lg mb-1"></i>
                                <br>
                                <small><strong>{{ $pathImage->path->fromRoom->name ?? 'Room #' . $pathImage->path->from_room_id }}</strong></small>
                            </div>
                            <div class="mx-3">
                                <i class="fas fa-arrow-right fa-lg text-muted"></i>
                            </div>
                            <div class="text-center p-2 bg-success text-white rounded ml-3">
                                <i class="fas fa-door-open fa-lg mb-1"></i>
                                <br>
                                <small><strong>{{ $pathImage->path->toRoom->name ?? 'Room #' . $pathImage->path->to_room_id }}</strong></small>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Path ID: {{ $pathImage->path->id }} | Image Order:
                                {{ $pathImage->image_order }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Current Image Display -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-image"></i> Current Image
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="current-image-container mb-3">
                                    <img src="{{ asset('storage/' . $pathImage->image_file) }}"
                                        alt="Path Image {{ $pathImage->image_order }}" class="img-fluid rounded shadow"
                                        style="max-height: 400px; cursor: pointer;" data-bs-toggle="modal"
                                        data-bs-target="#imageModal">

                                    <div class="mt-2">
                                        <span class="badge badge-primary">Order: {{ $pathImage->image_order }}</span>
                                    </div>
                                </div>

                                <div class="image-info">
                                    <div class="row text-left">
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <strong>File:</strong><br>
                                                {{ basename($pathImage->image_file) }}
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <strong>Uploaded:</strong><br>
                                                {{ $pathImage->created_at->format('M d, Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ route('path.show', $pathImage->path) }}"
                                            class="btn btn-outline-secondary btn-block">
                                            <i class="fas fa-arrow-left"></i> Back to Path
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-danger btn-block"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash"></i> Delete Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-edit"></i> Edit Image
                                </h5>
                            </div>

                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:
                                        </h6>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('path_images.update', $pathImage) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Image Order -->
                                    <div class="form-group mb-4">
                                        <label for="image_order" class="form-label">
                                            <i class="fas fa-sort-numeric-down"></i> Image Order
                                        </label>
                                        <input type="number"
                                            class="form-control @error('image_order') is-invalid @enderror" id="image_order"
                                            name="image_order" value="{{ old('image_order', $pathImage->image_order) }}"
                                            min="1" placeholder="Enter display order">
                                        <small class="form-text text-muted">
                                            Lower numbers appear first. Leave empty to keep current order.
                                        </small>
                                        @error('image_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Replace Image File -->
                                    <div class="form-group mb-4">
                                        <label for="image_file" class="form-label">
                                            <i class="fas fa-image"></i> Replace Image File
                                        </label>
                                        <div class="upload-area">
                                            <input type="file"
                                                class="form-control @error('image_file') is-invalid @enderror"
                                                id="image_file" name="image_file" accept="image/*"
                                                onchange="previewNewImage(this)">
                                            <small class="form-text text-muted">
                                                Leave empty to keep current image. Max size: 50MB<br>
                                                Supported formats: JPG, JPEG, PNG, GIF, BMP, SVG, WEBP
                                            </small>
                                            @error('image_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- New Image Preview -->
                                        <div id="newImagePreview" class="mt-3" style="display: none;">
                                            <label class="form-label text-success">
                                                <i class="fas fa-eye"></i> New Image Preview:
                                            </label>
                                            <div class="text-center">
                                                <img id="previewImage" src="" alt="New Image Preview"
                                                    class="img-fluid rounded border border-success"
                                                    style="max-height: 200px;">
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="clearImagePreview()">
                                                        <i class="fas fa-times"></i> Clear Selection
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Update Image
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle"></i> Update Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small text-muted">
                                    <li><i class="fas fa-check text-success"></i> You can update the image order without
                                        replacing the file</li>
                                    <li><i class="fas fa-check text-success"></i> You can replace the image file without
                                        changing the order</li>
                                    <li><i class="fas fa-check text-success"></i> Both fields are optional - update only
                                        what you need</li>
                                    <li><i class="fas fa-exclamation text-warning"></i> Replacing the image will
                                        permanently delete the old file</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Size Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Path Image - Order {{ $pathImage->image_order }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('storage/' . $pathImage->image_file) }}"
                        alt="Path Image {{ $pathImage->image_order }}" class="img-fluid rounded">

                    <div class="mt-3 text-muted">
                        <small>
                            <strong>File:</strong> {{ basename($pathImage->image_file) }}<br>
                            <strong>Order:</strong> {{ $pathImage->image_order }}<br>
                            <strong>Uploaded:</strong> {{ $pathImage->created_at->format('M d, Y H:i:s') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this image?</p>
                    <div class="alert alert-warning">
                        <strong>Image:</strong> {{ basename($pathImage->image_file) }}<br>
                        <strong>Order:</strong> {{ $pathImage->image_order }}<br>
                        <strong>Path:</strong>
                        {{ $pathImage->path->fromRoom->name ?? 'Room #' . $pathImage->path->from_room_id }}
                        →
                        {{ $pathImage->path->toRoom->name ?? 'Room #' . $pathImage->path->to_room_id }}
                    </div>
                    <small class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        This action cannot be undone and will permanently delete the image file.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('path_images.destroy', $pathImage) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Image
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .current-image-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }

        .current-image-container img {
            transition: transform 0.3s ease;
        }

        .current-image-container img:hover {
            transform: scale(1.05);
        }

        .upload-area {
            position: relative;
        }

        .upload-area input[type="file"] {
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background-color: #fafafa;
            transition: border-color 0.3s ease;
        }

        .upload-area input[type="file"]:focus,
        .upload-area input[type="file"]:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
            outline: none;
        }

        .image-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .modal-lg {
            max-width: 800px;
        }

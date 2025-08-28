@extends('layouts.app')

@section('title', 'Path Details')

@section('content')
    <div class="container mx-auto max-w-6xl px-4 py-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                <i class="fas fa-route mr-2"></i> Path Details
            </h1>
        </div>

        <!-- Path Info & Visualization -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Path Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h2 class="text-lg font-semibold mb-4"><i class="fas fa-info-circle mr-2"></i> Path Info</h2>
                <dl class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <!-- Path ID -->
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-500">Path ID:</dt>
                        <dd>{{ $path->id }}</dd>
                    </div>
                    <!-- From Room -->
                    <div class="flex justify-between items-center">
                        <dt class="font-medium text-gray-500">From Room:</dt>
                        <dd>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                            </span>
                            @if ($path->fromRoom?->description)
                                <br><small class="text-gray-400">{{ Str::limit($path->fromRoom->description, 50) }}</small>
                            @endif
                        </dd>
                    </div>
                    <!-- To Room -->
                    <div class="flex justify-between items-center">
                        <dt class="font-medium text-gray-500">To Room:</dt>
                        <dd>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                            </span>
                            @if ($path->toRoom?->description)
                                <br><small class="text-gray-400">{{ Str::limit($path->toRoom->description, 50) }}</small>
                            @endif
                        </dd>
                    </div>
                    <!-- Created At -->
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-500">Created:</dt>
                        <dd>{{ $path->created_at?->format('M d, Y H:i') }}</dd>
                    </div>
                    <!-- Number of Images -->
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-500">Images:</dt>
                        <dd>
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                                {{ $path->images->count() }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Path Visualization Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 flex items-center justify-around">
                <div class="text-center p-4 bg-blue-500 text-white rounded shadow">
                    <i class="fas fa-door-open fa-2x mb-2"></i>
                    <div>{{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}</div>
                </div>
                <i class="fas fa-arrow-right fa-2x text-gray-400 mx-4"></i>
                <div class="text-center p-4 bg-green-500 text-white rounded shadow">
                    <i class="fas fa-door-open fa-2x mb-2"></i>
                    <div>{{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}</div>
                </div>
            </div>
        </div>

        <!-- Path Images Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <h2 class="text-lg font-semibold mb-4"><i class="fas fa-images mr-2"></i> Path Images
                ({{ $path->images->count() }})</h2>
            @if ($path->images->count() > 0)
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($path->images as $image)
                        <!-- GLightbox Wrapper -->
                        <a href="{{ asset('storage/' . $image->image_file) }}" class="glightbox"
                            data-gallery="path-{{ $path->id }}"
                            data-title="{{ $image->description ?? 'Order ' . $image->image_order }}">
                            <div class="relative group overflow-hidden rounded shadow hover:shadow-lg transition">
                                <img src="{{ asset('storage/' . $image->image_file) }}"
                                    class="w-full h-48 object-cover transform group-hover:scale-105 transition"
                                    alt="Path Image {{ $image->image_order }}">
                                @if ($image->description)
                                    <div
                                        class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-sm p-2 truncate">
                                        {{ Str::limit($image->description, 40) }}
                                    </div>
                                @else
                                    <div
                                        class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-sm p-2 text-center">
                                        Order {{ $image->image_order }}
                                    </div>
                                @endif
                            </div>
                        </a>
                        <!-- End GLightbox Wrapper -->
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-image fa-3x mb-4"></i>
                    <h4 class="text-lg font-semibold">No Images Found</h4>
                    <p class="text-sm">This path doesn't have any images yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.glightboxInstance = window.glightboxInstance || GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: true,
            autoplayVideos: false,
            moreText: 'View Image'
        });
    </script>
@endpush

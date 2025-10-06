@extends('layouts.app')

@section('title', 'Path Details')

@section('content')
    <x-floating-actions />

    <div class="container mx-auto max-w-6xl px-4 py-6">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                <span class="text-primary">Path</span> Details
            </h1>
            <p class="text-sm sm:text-base lg:text-base text-gray-600 dark:text-gray-300 mt-1 sm:mt-2">
                Overview of the route from the starting office to the destination office.
            </p>

        </div>

        <div class="container mx-auto max-w-3xl px-4 py-6">
            <!-- Room Visualization -->
            <div
                class="bg-white dark:bg-gray-800 border border-primary rounded-md shadow-lg px-4 py-3 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 mx-auto">
                <!-- From Room -->
                <div class="text-center flex-1">
                    <div class="text-gray-700 dark:text-gray-300">
                        <div class="text-md font-semibold truncate">
                            {{ $path->fromRoom->name ?? 'Room #' . $path->from_room_id }}
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 my-2 sm:my-0">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/arrow.png"
                        alt="Path Arrow" class="w-6 h-6 sm:w-8 sm:h-8 object-contain">
                </div>

                <!-- To Room -->
                <div class="text-center flex-1">
                    <div class="text-gray-700 dark:text-gray-300">
                        <div class="text-md font-semibold truncate">
                            {{ $path->toRoom->name ?? 'Room #' . $path->to_room_id }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Path Images -->
        <div class="bg-white dark:bg-gray-800 border-2 border-primary rounded-lg shadow p-5 text-center">
            <h2 class="text-xl font-semibold mb-4 dark:text-gray-300">
                <i class="fas fa-images mr-2"></i> Path Images ({{ $path->images->count() }})
            </h2>

            @if ($path->images->count())
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($path->images as $image)
                        <a href="{{ asset('storage/' . $image->image_file) }}" class="glightbox"
                            data-gallery="path-{{ $path->id }}"
                            data-title="{{ $image->description ?? 'Path ' . $image->image_order }}">
                            <div class="relative group overflow-hidden rounded shadow hover:shadow-lg transition">
                                <img src="{{ asset('storage/' . $image->image_file) }}"
                                    class="w-full h-48 object-cover transform group-hover:scale-105 transition"
                                    alt="Path Image {{ $image->image_order }}">
                                <div
                                    class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-sm p-2 truncate">
                                    {{ $image->description ?? 'Path ' . $image->image_order }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-image fa-3x mb-4"></i>
                    <h4 class="text-lg">No Images Found</h4>
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
            moreText: 'View Image',
            svg: {
                close: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"/>',
                next: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/next.png"/>',
                prev: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/prev.png"/>'
            }
        });
    </script>
@endpush

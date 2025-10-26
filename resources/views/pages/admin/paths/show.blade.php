@extends('layouts.app')

@section('title', 'Path Details')

@section('content')
    <x-floating-actions />

    <div class="container mx-auto max-w-6xl px-4 py-6">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-1 dark:text-gray-100">
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

        <div class="relative">
            <div class="absolute -top-3 -right-3 z-10 inline-block group">
                <a href="{{ route('path-image.edit', $path->id) }}"
                    class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-md hover:scale-125 transition duration-200 bg-white dark:bg-gray-800 shadow-md border border-edit">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                        alt="Edit Icon" class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                </a>
                <div
                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                    Edit
                    <div
                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                    </div>
                </div>
            </div>

            <x-filter-header :route="route('path.show', $path->id)" :fields="['image_order' => 'Image Order', 'image_file' => 'Filename']" placeholder="image number" />

            <div id="records-table">
                @include('pages.admin.paths.partials.path-images-table', ['images' => $images])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.glightboxInstance) {
                window.glightboxInstance.destroy();
            }

            window.glightboxInstance = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true,
                autoplayVideos: false,
                moreText: 'View Image',
                svg: {
                    close: '<img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png" alt="Close"/>',
                    next: '<svg width="25" height="40" viewBox="0 0 25 40" xmlns="http://www.w3.org/2000/svg"><path d="M5 0 L25 20 L5 40" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    prev: '<svg width="25" height="40" viewBox="0 0 25 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 0 L0 20 L20 40" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                }
            });
        });
    </script>
@endpush

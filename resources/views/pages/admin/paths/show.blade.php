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

            <div class="bg-white dark:bg-gray-800 border-2 border-primary rounded-lg shadow p-5 text-center">
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
                    <tr class="dark:bg-gray-800">
                        <td colspan="3" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4 mt-12">
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 bg-primary-10 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                        alt="Group icon" class="w-9 h-8 sm:w-11 sm:h-10">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-base sm:text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">
                                        No Images found
                                    </h3>
                                    <p class="text-gray-500 text-xs sm:text-sm dark:text-gray-400">
                                        This path doesn't have any images yet.
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
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

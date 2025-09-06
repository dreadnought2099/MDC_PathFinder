@extends('layouts.guest')

@section('content')
    <div class="min-h-screen dark:bg-gray-900 flex flex-col">
        <!-- Top bar -->
        <div
            class="w-full flex justify-between items-center p-4 border-b-2 border-primary dark:border-primary bg-white dark:bg-gray-900 sticky top-0 z-50">
            <!-- Back button -->
            <a href="{{ route('index') }}"
                class="flex items-center text-gray-700 hover:text-primary transition-colors duration-200 dark:text-gray-300">
                <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="font-medium">Back to Scanner</span>
            </a>

            <!-- Dark mode toggle -->
            <x-dark-mode-toggle />
        </div>

        <!-- Main content -->
        <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-5xl bg-white border-2 border-primary dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-6 text-center dark:text-gray-200">
                    Select Starting Point & Destination
                </h2>

                @if ($paths->isEmpty())
                    <p class="text-center text-gray-600 dark:text-gray-300">
                        No navigation path available between these rooms.
                    </p>
                @else
                    @foreach ($paths as $path)
                        <!-- Path Images Card -->
                        <div
                            class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 mb-8 text-center border-2 border-primary">
                            <h2 class="text-xl font-semibold mb-4 dark:text-gray-300">
                                <i class="fas fa-images mr-2"></i> Path Images ({{ $path->images->count() }})
                            </h2>

                            @if ($path->images->count() > 0)
                                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach ($path->images as $image)
                                        <!-- GLightbox Wrapper -->
                                        <a href="{{ asset('storage/' . $image->image_file) }}" class="glightbox"
                                            data-gallery="path-{{ $path->id }}"
                                            data-title="{{ $image->description ?? 'Order ' . $image->image_order }}">
                                            <div
                                                class="relative group overflow-hidden rounded shadow hover:shadow-lg transition">
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
                                    <h4 class="text-lg">No Images Found</h4>
                                    <p class="text-sm">This path doesn't have any images yet.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <div class="mt-8 flex justify-center">
                    <a href="{{ route('paths.select') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary/90">
                        Start New Navigation
                    </a>
                </div>
            </div>
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
                close: '<img src="/icons/exit.png"/>',
                next: '<img src="/icons/next.png"/>',
                prev: '<img src="/icons/prev.png"/>'
            }
        });
    </script>
@endpush

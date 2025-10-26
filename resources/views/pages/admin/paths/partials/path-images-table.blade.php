<div class="bg-white dark:bg-gray-800 border-2 border-primary rounded-lg shadow p-5 text-center">
    @if ($images->count())
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($images as $image)
                <a href="{{ asset('storage/' . $image->image_file) }}" class="glightbox"
                    data-gallery="path-{{ $image->path_id }}" data-title="Path {{ $image->image_order }}">
                    <div class="relative group overflow-hidden rounded shadow hover:shadow-lg transition">
                        <img src="{{ asset('storage/' . $image->image_file) }}"
                            class="w-full h-48 object-cover transform group-hover:scale-105 transition"
                            alt="Path Image {{ $image->image_order }}">
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-sm p-2 truncate">
                            Path {{ $image->image_order }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="dark:bg-gray-800 py-12 sm:py-16 text-center">
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
        </div>
    @endif
</div>

<!-- Pagination -->
@if (auth()->user()->hasRole('Admin') && method_exists($images, 'links'))
    <div class="mt-3 flex justify-center">
        {{ $images->appends(request()->query())->links('pagination::tailwind') }}
    </div>
@endif

@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-6 text-center sticky top-0 z-48 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                <span class="text-primary">Path</span> Management
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-600 dark:text-gray-300 mb-3">
                Manage paths between offices
            </p>

            <!-- Pagination -->
            <div class="mt-2 flex justify-center">
                {{ $paths->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            <!-- Sort -->
            <div class="py-2 sm:py-4">
                <x-sort-by :route="route('path.index')" :fields="[
                    'id' => 'ID',
                    'from_room_id' => 'From Room',
                    'to_room_id' => 'To Room',
                    'created_at' => 'Created At',
                ]" :current-sort="$sort" :current-direction="$direction" />
            </div>
        </div>

        <!-- Floating Actions -->
        <div class="mb-6 px-4 sm:px-6 lg:px-0 flex justify-end flex-wrap gap-2">
            <x-floating-actions />
        </div>

        <!-- Paths Table -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm md:text-base dark:bg-gray-800">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 text-left uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                ID</th>
                            <th
                                class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 text-left uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                From Room</th>
                            <th
                                class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 text-left uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                To Room</th>
                            <th
                                class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 text-left uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs sm:text-xs md:text-xs">
                                Path Direction</th>
                            <th
                                class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 text-right uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($paths as $path)
                            @if ($path->fromRoom && $path->toRoom)
                                @php $hasImage = $path->images->isNotEmpty(); @endphp
                                <tr
                                    class="hover:bg-gray-50 transition-colors duration-200 dark:hover:bg-gray-800 {{ $hasImage ? 'bg-primary-10' : '' }}">

                                    <!-- ID -->
                                    <td class="px-3 sm:px-4 md:px-6 py-3 text-gray-500 text-xs sm:text-sm">
                                        {{ $path->id }}
                                    </td>

                                    <!-- From Room -->
                                    <td class="px-3 sm:px-4 md:px-6 py-3 text-primary text-xs sm:text-sm">
                                        {{ $path->fromRoom->name }}
                                    </td>

                                    <!-- To Room -->
                                    <td class="px-3 sm:px-4 md:px-6 py-3 text-primary text-xs sm:text-sm">
                                        {{ $path->toRoom->name }}
                                    </td>

                                    <!-- Path Direction -->
                                    <td class="px-3 sm:px-4 md:px-6 py-3 text-[11px] sm:text-xs md:text-xs text-gray-500">
                                        {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                                        @if ($hasImage)
                                            <span
                                                class="ml-2 px-2 py-0.5 text-[9px] sm:text-xs md:text-xs text-white bg-primary rounded-full whitespace-nowrap">
                                                Image Uploaded
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-3 sm:px-4 md:px-6 py-3">
                                        <div class="flex flex-wrap justify-end gap-2 sm:gap-3">

                                            {{-- View --}}
                                            <div class="relative inline-block group">
                                                <a href="{{ route('path.show', $path->id) }}"
                                                    class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                        alt="View Icon"
                                                        class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                </a>
                                                <div
                                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                    View
                                                    <div
                                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Add Path Images --}}
                                            <div class="relative inline-block group">
                                                <a href="{{ route('path-image.create', $path->id) }}"
                                                    class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                                        alt="Add Image"
                                                        class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                </a>
                                                <div
                                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                    Add Path Image
                                                    <div
                                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Edit --}}
                                            <div class="relative inline-block group">
                                                <a href="{{ route('path-image.edit', $path->id) }}"
                                                    class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                        alt="Edit Icon"
                                                        class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                                </a>
                                                <div
                                                    class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                                    Edit
                                                    <div
                                                        class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-600 dark:text-gray-300 text-sm">
                                    No paths found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

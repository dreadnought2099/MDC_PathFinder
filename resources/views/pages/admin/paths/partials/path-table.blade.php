<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                    <th
                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300">
                        ID
                    </th>
                    <th
                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300">
                        From Room
                    </th>
                    <th
                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300">
                        To Room
                    </th>
                    <th
                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300">
                        Status
                    </th>
                    <th
                        class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($paths as $path)
                    @if ($path->fromRoom && $path->toRoom)
                        @php $hasImage = $path->images->isNotEmpty(); @endphp
                        <tr
                            class="hover:bg-gray-50 transition-colors duration-150 dark:hover:bg-gray-800 {{ $hasImage ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">

                            <!-- ID -->
                            <td class="px-6 py-5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $path->id }}
                            </td>

                            <!-- From Room -->
                            <td class="px-6 py-5 text-sm text-gray-900 dark:text-gray-300">
                                {{ $path->fromRoom->name }}
                            </td>

                            <!-- To Room -->
                            <td class="px-6 py-5 text-sm text-gray-900 dark:text-gray-300">
                                {{ $path->toRoom->name }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5">
                                @if ($hasImage)
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs text-white bg-primary rounded-full whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Image Uploaded
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 rounded-full whitespace-nowrap">
                                        No Image
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-3">

                                    {{-- View --}}
                                    <div class="relative inline-block group">
                                        <a href="{{ route('path.show', $path->id) }}"
                                            class="hover-underline inline-flex items-center justify-center w-11 h-11 transition-all duration-200 hover:scale-125">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                alt="View"
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
                                        <a id="pathRowImageLink_{{ $path->id }}"
                                            href="{{ route('path-image.create', $path->id) }}"
                                            class="hover-underline inline-flex items-center justify-center w-11 h-11 transition-all duration-200 hover:scale-125"
                                            onclick="savePathSelection({{ $path->id }})">
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
                                            class="hover-underline-edit inline-flex items-center justify-center w-11 h-11 transition-all duration-200 hover:scale-125">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                alt="Edit"
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
                        <td colspan="5" class="px-6 py-16 text-center dark:bg-gray-800">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div
                                    class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/navigation.png"
                                        alt="No paths" class="w-12 h-12 opacity-40">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        No paths found
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        There are no paths available.
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-2 flex justify-center">
    {{ $paths->appends(request()->query())->links('pagination::tailwind') }}
</div>

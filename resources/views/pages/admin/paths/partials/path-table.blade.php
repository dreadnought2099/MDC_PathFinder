<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                    <th
                        class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300 lg:px-6">
                        ID
                    </th>
                    <th
                        class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300 lg:px-6">
                        From Room
                    </th>
                    <th
                        class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300 lg:px-6">
                        To Room
                    </th>
                    <th
                        class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300 lg:px-6">
                        Path Direction
                    </th>
                    <th
                        class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider dark:text-gray-300 lg:px-6">
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
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400 lg:px-6">
                                {{ $path->id }}
                            </td>

                            <!-- From Room -->
                            <td class="px-4 py-4 text-sm font-medium text-primary dark:text-primary-light lg:px-6">
                                {{ $path->fromRoom->name }}
                            </td>

                            <!-- To Room -->
                            <td class="px-4 py-4 text-sm font-medium text-primary dark:text-primary-light lg:px-6">
                                {{ $path->toRoom->name }}
                            </td>

                            <!-- Path Direction -->
                            <td class="px-4 py-4 lg:px-6">
                                @if ($hasImage)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-white bg-primary rounded-full whitespace-nowrap">
                                        Image Uploaded
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4 lg:px-6">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- View --}}
                                    <div class="relative inline-block group">
                                        <a href="{{ route('path.show', $path->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-110">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                alt="View" class="w-5 h-5 object-contain">
                                        </a>
                                        <div
                                            class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block z-10">
                                            View
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-[5px] border-l-gray-900 dark:border-l-gray-700 border-t-[5px] border-t-transparent border-b-[5px] border-b-transparent">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Add Path Images --}}
                                    <div class="relative inline-block group">
                                        <a id="pathRowImageLink_{{ $path->id }}"
                                            href="{{ route('path-image.create', $path->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-110"
                                            onclick="savePathSelection({{ $path->id }})">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/image.png"
                                                alt="Add Image" class="w-5 h-5 object-contain">
                                        </a>
                                        <div
                                            class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block z-10">
                                            Add Path Image
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-[5px] border-l-gray-900 dark:border-l-gray-700 border-t-[5px] border-t-transparent border-b-[5px] border-b-transparent">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit --}}
                                    <div class="relative inline-block group">
                                        <a href="{{ route('path-image.edit', $path->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-110">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                alt="Edit" class="w-5 h-5 object-contain">
                                        </a>
                                        <div
                                            class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block z-10">
                                            Edit
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-[5px] border-l-gray-900 dark:border-l-gray-700 border-t-[5px] border-t-transparent border-b-[5px] border-b-transparent">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center dark:bg-gray-800">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/navigation.png"
                                        alt="No paths" class="w-10 h-10 opacity-40">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">
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

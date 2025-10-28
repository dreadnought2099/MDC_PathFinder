<div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[500px]">
            <thead>
                <tr
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                    <th
                        class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Staff ID
                    </th>
                    <th
                        class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Full Name
                    </th>
                    <th
                        class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Office Assigned
                    </th>
                    <th
                        class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Office ID
                    </th>
                    <th
                        class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs sm:text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($staffs as $staff)
                    <tr class="transition-all duration-300 ease-[var(--ease-in-out-expo-smooth)] dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-200">
                        <!-- Staff ID -->
                        <td
                            class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $staff->id }}
                        </td>

                        <!-- Staff Name -->
                        <td
                            class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $staff->full_name }}
                        </td>

                        <!-- Office Name -->
                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-400">
                            {{ $staff->room->name ?? 'N/A' }}
                        </td>

                        <!-- Room ID -->
                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-400">
                            {{ $staff->room_id ?? 'N/A' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="flex flex-wrap justify-end gap-2 sm:gap-3 items-center">
                                {{-- View --}}
                                @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view staff'))
                                    <div class="relative inline-block group">
                                        <a href="{{ route('staff.show', $staff->id) }}"
                                            class="hover-underline inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                                alt="View Icon"
                                                class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                        </a>
                                        <div
                                            class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                            View Staff
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Edit --}}
                                @if (auth()->user()->hasRole('Admin') || auth()->user()->can('edit staff'))
                                    <div class="relative inline-block group">
                                        <a href="{{ route('staff.edit', $staff->id) }}"
                                            class="hover-underline-edit inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/edit.png"
                                                alt="Edit Icon"
                                                class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                        </a>
                                        <div
                                            class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                            Edit Staff
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Delete --}}
                                @if (auth()->user()->hasRole('Admin'))
                                    <div class="relative inline-block group">
                                        <button type="button"
                                            onclick="openModal('{{ $staff->id }}', '{{ addslashes($staff->first_name . ' ' . $staff->last_name) }}')"
                                            class="hover-underline-delete inline-flex items-center justify-center p-2 rounded-lg hover:scale-125 transition duration-200 appearance-none bg-transparent border-0 cursor-pointer">
                                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/trash.png"
                                                alt="Delete Icon"
                                                class="block w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 object-contain">
                                        </button>
                                        <div
                                            class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
                                            Delete Staff
                                            <div
                                                class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 dark:border-l-gray-700 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($staffs->isEmpty())
                    <tr class="dark:bg-gray-800">
                        <td colspan="5" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 bg-primary-10 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder@cac2411/public/icons/staff.png"
                                        alt="Group icon" class="w-9 h-8 sm:w-11 sm:h-10">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-base sm:text-lg font-medium dark:text-gray-300 text-gray-700 mb-2">
                                        No staff found
                                    </h3>
                                    @if (auth()->user()->hasRole('Admin'))
                                        <p class="text-gray-500 text-xs sm:text-sm dark:text-gray-400">
                                            Get started by adding your first team member.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-3 flex justify-center">
    {{ $staffs->appends(request()->query())->links('pagination::tailwind') }}
</div>
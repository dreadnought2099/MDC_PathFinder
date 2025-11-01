<div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative group">
    <!-- Trigger Button -->
    <button @click="open = true"
        class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 cursor-pointer">
        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/team.png" alt="Team Icon"
            class="w-8 h-8 sm:w-10 sm:h-10 hover:scale-110 transition-transform duration-300">
    </button>

    <!-- Tooltip -->
    <div
        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none">
        Meet the Team
        
        <!-- Arrow -->
        <div
            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                    border-t-4 border-t-transparent 
                    border-b-4 border-b-transparent">
        </div>
    </div>

    <!-- Modal Overlay -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click="open = false" style="display: none;">

        <!-- Modal Content -->
        <div @click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden relative z-10 border-2 border-primary">

            <!-- Modal Header -->
            <div
                class="sticky top-0 bg-white dark:bg-gray-900 px-4 sm:px-6 py-4 flex items-center justify-between z-10">
                <div class="w-6"></div> <!-- Spacer to balance the close button -->
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 flex-1 text-center">
                    Meet the <span class="text-primary">Team</span>
                </h2>
                <button @click="open = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors w-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="overflow-y-auto max-h-[calc(90vh-80px)] px-4 sm:px-6 py-6 sm:py-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Team Member 1 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-2xl border-2 border-primary transition-all duration-300 p-6 flex flex-col items-center text-center shadow-primary-hover">
                        <div class="relative mb-4">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png"
                                alt="Joshua A. Salabe"
                                class="w-28 h-28 rounded-full object-cover group-hover:scale-105 transition-transform duration-300 shadow-primary-hover">
                        </div>

                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">
                            Joshua A. Salabe
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            BSIT 4
                        </p>

                        <div class="flex gap-3 mt-auto">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-6 h-6">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-6 h-6">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-2xl border-2 border-primary transition-all duration-300 p-6 flex flex-col items-center text-center shadow-primary-hover">
                        <div class="relative mb-4">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png"
                                alt="Chris Marie Calesa"
                                class="w-28 h-28 rounded-full object-cover group-hover:scale-105 transition-transform duration-300 shadow-primary-hover">
                        </div>

                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">
                            Chris Marie Calesa
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            BSIT 4
                        </p>

                        <div class="flex gap-3 mt-auto">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-6 h-6">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-6 h-6">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-2xl border-2 border-primary transition-all duration-300 p-6 flex flex-col items-center text-center shadow-primary-hover">
                        <div class="relative mb-4">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/mdc.png"
                                alt="Joana Jean Astacaan"
                                class="w-28 h-28 rounded-full object-cover group-hover:scale-105 transition-transform duration-300 shadow-primary-hover">
                        </div>

                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">
                            Joana Jean Astacaan
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            BSIT 4
                        </p>

                        <div class="flex gap-3 mt-auto">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-6 h-6">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-6 h-6">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-2xl border-2 border-primary transition-all duration-300 p-6 flex flex-col items-center text-center shadow-primary-hover">
                        <div class="relative mb-4">
                            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg"
                                alt="Raymart E. Magallanes"
                                class="w-28 h-28 rounded-full object-cover group-hover:scale-105 transition-transform duration-300 shadow-primary-hover">
                        </div>

                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">
                            Raymart E. Magallanes
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            BSIT 4
                        </p>

                        <div class="flex gap-3 mt-auto">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-6 h-6">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-120 transition-transform duration-300 p-2 rounded-md hover-underline"
                                target="_blank" rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-6 h-6">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

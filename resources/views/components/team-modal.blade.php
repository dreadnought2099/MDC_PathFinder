<!-- Team Modal Component - Save as: resources/views/components/team-modal.blade.php -->

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
                text-white bg-gray-900 rounded-lg shadow-lg opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none z-50">
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
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" @click="open = false"
        style="display: none;">

        <!-- Modal Content -->
        <div @click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">

            <!-- Modal Header -->
            <div
                class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Meet the <span class="text-primary">Team</span>
                </h2>
                <button @click="open = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="overflow-y-auto max-h-[calc(90vh-80px)] px-6 py-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Team Member 1 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-6 flex flex-col items-center text-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg"
                            alt="Joshua A. Salabe"
                            class="w-32 h-32 rounded-full object-cover border-2 border-primary group-hover:scale-110 transition-transform duration-300">

                        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-300">
                            Joshua A. Salabe
                        </h3>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            BSIT 4
                        </p>

                        <div class="mt-4 flex space-x-4">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-7 h-7">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-7 h-7">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-6 flex flex-col items-center text-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg"
                            alt="Chris Marie Calesa"
                            class="w-32 h-32 rounded-full object-cover border-2 border-primary group-hover:scale-110 transition-transform duration-300">

                        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-300">
                            Chris Marie Calesa
                        </h3>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            BSIT 4
                        </p>

                        <div class="mt-4 flex space-x-4">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-7 h-7">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-7 h-7">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-6 flex flex-col items-center text-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg"
                            alt="Joana Jean Astacaan"
                            class="w-32 h-32 rounded-full object-cover border-2 border-primary group-hover:scale-110 transition-transform duration-300">

                        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-300">
                            Joana Jean Astacaan
                        </h3>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            BSIT 4
                        </p>

                        <div class="mt-4 flex space-x-4">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-7 h-7">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-7 h-7">
                            </a>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-primary transition-all p-6 flex flex-col items-center text-center">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/images/profile.jpeg"
                            alt="Raymart E. Magallanes"
                            class="w-32 h-32 rounded-full object-cover border-2 border-primary group-hover:scale-110 transition-transform duration-300">

                        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-300">
                            Raymart E. Magallanes
                        </h3>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            BSIT 4
                        </p>

                        <div class="mt-4 flex space-x-4">
                            <a href="https://www.instagram.com/skerm_art/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/instagram.png"
                                    alt="Instagram" class="w-7 h-7">
                            </a>
                            <a href="https://letterboxd.com/RMAGALLANEZ/"
                                class="hover:scale-110 transition-transform duration-300" target="_blank"
                                rel="noopener noreferrer">
                                <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/letterboxd.png"
                                    alt="Letterboxd" class="w-7 h-7">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
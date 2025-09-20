<div class="dark-mode-toggle-component relative inline-block group">
    <!-- Nothing worth having comes easy. - Theodore Roosevelt -->
    <button id="darkModeToggle" class="toggle-switch" aria-label="Toggle dark mode">
        <div class="toggle-slider">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/sun.png" alt="Light mode"
                class="sun-icon">
            <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/moon.png" alt="Dark mode"
                class="moon-icon">
        </div>
    </button>

    <!-- Pure CSS Tooltip -->
    <div
        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
        Toggle dark mode
        <!-- Arrow -->
        <div
            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                    border-t-4 border-t-transparent 
                    border-b-4 border-b-transparent">
        </div>
    </div>
</div>

<style>
    /* Toggle switch styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        background-color: #e5e7eb;
        border-radius: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        border: 1px solid #d1d5db;
    }

    .dark .toggle-switch {
        background-color: #374151;
        border-color: #4b5563;
    }

    .toggle-switch.active {
        background-color: #3b82f6;
        border-color: #2563eb;
    }

    .dark .toggle-switch.active {
        background-color: #60a5fa;
        border-color: #3b82f6;
    }

    .toggle-slider {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 22px;
        height: 22px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .toggle-switch.active .toggle-slider {
        transform: translateX(24px);
    }

    .sun-icon {
        background-color: #EFB31D;
        border-radius: 20px;
        opacity: 1;
        transition: opacity 0.2s ease;
    }

    .moon-icon {
        opacity: 0;
        position: absolute;
        transition: opacity 0.2s ease;
    }

    .toggle-switch.active .sun-icon {
        opacity: 0;
    }

    .toggle-switch.active .moon-icon {
        opacity: 1;
    }
</style>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggle = document.getElementById("darkModeToggle");
            const root = document.documentElement;

            if (!toggle) return;

            // Function to sync the toggle UI with current dark mode
            function updateToggleState() {
                toggle.classList.toggle("active", root.classList.contains("dark"));
            }

            // Set initial toggle state
            updateToggleState();

            // Handle toggle click
            toggle.addEventListener("click", () => {
                root.classList.toggle("dark");
                updateToggleState();

                localStorage.setItem(
                    "theme",
                    root.classList.contains("dark") ? "dark" : "light"
                );
            });

            // Listen for system theme changes (when no manual preference is set)
            window.matchMedia("(prefers-color-scheme: dark)").addEventListener('change', (e) => {
                if (!localStorage.getItem("theme")) {
                    if (e.matches) {
                        root.classList.add("dark");
                    } else {
                        root.classList.remove("dark");
                    }
                    updateToggleState();
                }
            });
        });
    </script>
@endpush

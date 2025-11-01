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

    <div
        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
                text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
                group-hover:opacity-100 group-hover:visible transition-all duration-300 
                whitespace-nowrap dark:bg-gray-700 pointer-events-none hidden lg:block">
        Toggle dark mode
        <div
            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                    border-l-4 border-l-gray-900 dark:border-l-gray-700
                    border-t-4 border-t-transparent 
                    border-b-4 border-b-transparent">
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Run immediately when script loads (no DOMContentLoaded wait)
        (function() {
            const toggle = document.getElementById("darkModeToggle");
            if (!toggle) return;

            const root = document.documentElement;
            const themeKey = root.dataset.themeScope || "theme";

            function updateToggleState() {
                toggle.classList.toggle("active", root.classList.contains("dark"));
            }

            // Just update the toggle visual state based on current dark class
            // (which was already set by the head script)
            updateToggleState();

            // Toggle handler
            toggle.addEventListener("click", () => {
                root.classList.toggle("dark");
                updateToggleState();
                localStorage.setItem(themeKey, root.classList.contains("dark") ? "dark" : "light");
            });

            // React to system theme only if user hasn't set a preference
            window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", e => {
                if (!localStorage.getItem(themeKey)) {
                    root.classList.toggle("dark", e.matches);
                    updateToggleState();
                }
            });
        })();
    </script>
@endpush

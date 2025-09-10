<div class="dark-mode-toggle-component">
    <!-- Nothing worth having comes easy. - Theodore Roosevelt -->
    <button id="darkModeToggle" class="relative group toggle-switch" aria-label="Toggle dark mode">
        <div class="toggle-slider">
            <img src="{{ asset('icons/sun.png') }}" alt="Light mode" class="sun-icon">
            <img src="{{ asset('icons/moon.png') }}" alt="Dark mode" class="moon-icon">
        </div>
        <span
            class="hidden lg:flex absolute right-full mr-3 px-3 py-1.5 rounded-md 
                 bg-gray-800 text-white text-sm shadow-md opacity-0 group-hover:opacity-100 
                 transition-opacity duration-300 whitespace-nowrap">
            Click to toggle dark mode
        </span>
    </button>
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

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toggle = document.getElementById("darkModeToggle");
        const root = document.documentElement;

        if (!toggle) return;

        // Function to update toggle appearance
        function updateToggleState() {
            if (root.classList.contains("dark")) {
                toggle.classList.add("active");
            } else {
                toggle.classList.remove("active");
            }
        }

        // Set initial toggle state based on current theme
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

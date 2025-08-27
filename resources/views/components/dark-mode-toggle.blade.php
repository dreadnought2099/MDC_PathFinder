<div>
    <!-- Nothing worth having comes easy. - Theodore Roosevelt -->

    <button id="darkModeToggle" class="p-2 rounded border bg-white dark:bg-gray-800 dark:text-white transition">
        🌞 / 🌙
    </button>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggle = document.getElementById("darkModeToggle");
            const root = document.documentElement; // <html>

            // Check localStorage first
            let theme = localStorage.getItem("theme");

            if (theme === "dark") {
                root.classList.add("dark");
            } else if (theme === "light") {
                root.classList.remove("dark");
            } else {
                // No preference saved → follow system
                if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
                    root.classList.add("dark");
                }
            }

            toggle.addEventListener("click", () => {
                root.classList.toggle("dark");

                localStorage.setItem(
                    "theme",
                    root.classList.contains("dark") ? "dark" : "light"
                );
            });
        });
    </script>

</div>

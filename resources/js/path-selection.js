export function savePathSelection(pathId) {
    sessionStorage.setItem("selectedPathId", pathId);

    // Also update the floating link if it exists on the current page
    const link = document.getElementById("floatingPathImageLink");
    if (link) {
        const routeBase = link.dataset.routeBase;
        if (routeBase) {
            link.href = routeBase.replace(":pathId", pathId);
            link.classList.remove(
                "opacity-50",
                "cursor-not-allowed",
                "pointer-events-none"
            );
        }
    }

    // Dispatch event for other components (like create.blade.php)
    window.dispatchEvent(
        new CustomEvent("path-changed", {
            detail: { pathId: pathId },
        })
    );
}

export function initializeFloatingLink() {
    const link = document.getElementById("floatingPathImageLink");
    if (!link) return;

    // Just ensure the link is updated with current session storage
    const storedPathId = sessionStorage.getItem("selectedPathId");
    if (storedPathId) {
        const routeBase = link.dataset.routeBase;
        if (routeBase) {
            link.href = routeBase.replace(":pathId", storedPathId);
            link.classList.remove(
                "opacity-50",
                "cursor-not-allowed",
                "pointer-events-none"
            );
        }
    }
}
export function savePathSelection(pathId) {
    sessionStorage.setItem("selectedPathId", pathId);
}

export function updatePathLinkBeforeNavigate(createRouteBase) {
    const storedPathId = sessionStorage.getItem("selectedPathId");
    if (storedPathId) {
        const newUrl = createRouteBase.replace(":pathId", storedPathId);
        window.location.href = newUrl;
        return true;
    }
    return false;
}

export function initializeFloatingLink() {
    const link = document.getElementById("floatingPathImageLink");
    if (!link) return;

    const routeBase = link.dataset.routeBase;
    if (!routeBase) return;

    // Update href if sessionStorage already has path
    const storedPathId = sessionStorage.getItem("selectedPathId");
    if (storedPathId) {
        link.href = routeBase.replace(":pathId", storedPathId);
    }

    // Attach click handler
    link.addEventListener("click", (event) => {
        const storedPathId = sessionStorage.getItem("selectedPathId");
        if (storedPathId) {
            event.preventDefault();
            link.href = routeBase.replace(":pathId", storedPathId);
            window.location.href = link.href;
        }
    });
}
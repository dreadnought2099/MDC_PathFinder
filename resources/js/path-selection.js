export function savePathSelection(pathId) {
    sessionStorage.setItem("selectedPathId", pathId);
}

// Navigate with remembered path
export function updatePathLinkBeforeNavigate(event, createRouteBase) {
    const storedPathId = sessionStorage.getItem("selectedPathId");
    if (storedPathId) {
        event.preventDefault();
        const newUrl = createRouteBase.replace(":pathId", storedPathId);
        window.location.href = newUrl;
        return false;
    }
    return true;
}

// Update links on page load
export function initializePathLinks(createRouteBase) {
    const storedPathId = sessionStorage.getItem("selectedPathId");

    if (storedPathId) {
        const floatingLink = document.getElementById("floatingPathImageLink");
        if (floatingLink) {
            floatingLink.href = createRouteBase.replace(
                ":pathId",
                storedPathId
            );
        }
    }
}
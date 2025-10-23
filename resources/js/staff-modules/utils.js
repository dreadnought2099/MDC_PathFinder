export function formatFileSize(bytes) {
    if (!Number.isFinite(bytes)) return "";
    const mb = bytes / (1024 * 1024);
    if (mb >= 1) return mb.toFixed(2) + " MB";
    return (bytes / 1024).toFixed(1) + " KB";
}

/**
 * showTemporaryMessage(message, type = "info")
 * Relies on the global function defined in your layout.
 */
export function showTemporaryMessage(message, type = "info") {
    if (typeof window.showTemporaryMessage === "function") {
        window.showTemporaryMessage(message, type);
    } else {
        // fallback if layout script missing
        console.warn("Global showTemporaryMessage() not found");
        alert(message);
    }
}
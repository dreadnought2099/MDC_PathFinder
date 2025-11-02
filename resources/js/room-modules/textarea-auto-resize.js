export function initializeTextareaAutoResize() {
    const textareas = document.querySelectorAll("textarea");
    if (!textareas.length) return;

    textareas.forEach((textarea) => {
        const resize = () => {
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";
        };
        textarea.addEventListener("input", resize);
        resize(); // Run once on page load (for edit pages)
    });
}
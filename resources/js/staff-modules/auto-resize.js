export function setupAutoResize(textarea) {
    if (!textarea) return;
    const resize = () => {
        textarea.style.height = "auto";
        textarea.style.height = `${textarea.scrollHeight}px`;
    };

    // Listen to input and also resize once on init
    textarea.addEventListener("input", resize);
    // small safety: also resize on window load/resize to handle CSS changes
    window.addEventListener("load", resize);
    window.addEventListener("resize", resize);
    resize();
}
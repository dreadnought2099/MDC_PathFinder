export function setupAutoResize(textarea) {
    const resize = () => {
        textarea.style.height = "auto";
        textarea.style.height = `${textarea.scrollHeight}px`;
    };

    textarea.addEventListener("input", resize);
    resize();
}

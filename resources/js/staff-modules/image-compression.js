/**
 * Validate selected image file before compression
 * - Ensures correct file type and size limit
 */
export function validateImageFile(file, inputEl = null) {
    const validTypes = ["image/jpeg", "image/png", "image/webp"];

    // Default: 5 MB, can be overridden by <input data-max-size="5120">
    let maxSizeMB = 5;
    if (inputEl && inputEl.dataset.maxSize) {
        const kb = parseFloat(inputEl.dataset.maxSize);
        if (!isNaN(kb)) maxSizeMB = kb / 1024; // convert KB to MB
    }

    if (!file) return false;

    // Type validation
    if (!validTypes.includes(file.type)) {
        window.showTemporaryMessage?.(
            "Only JPG, PNG, or WEBP images are allowed.",
            "error"
        );
        return false;
    }

    // Size validation
    const sizeMB = file.size / (1024 * 1024);
    if (sizeMB > maxSizeMB) {
        window.showTemporaryMessage?.(
            `Image exceeds ${maxSizeMB} MB limit. Please choose a smaller file.`,
            "error"
        );
        return false;
    }

    return true;
}

/**
 * Compress image using Canvas API
 */
export async function compressImageCanvas(
    file,
    quality = 0.7,
    maxWidth = 1200,
    maxHeight = 1200
) {
    const imageBitmap = await createImageBitmap(file);
    const canvas = document.createElement("canvas");
    let { width, height } = imageBitmap;

    // Resize if too large
    if (width > maxWidth || height > maxHeight) {
        const ratio = Math.min(maxWidth / width, maxHeight / height);
        width = Math.round(width * ratio);
        height = Math.round(height * ratio);
    }

    canvas.width = width;
    canvas.height = height;

    const ctx = canvas.getContext("2d");
    ctx.drawImage(imageBitmap, 0, 0, width, height);

    return new Promise((resolve) => {
        canvas.toBlob(
            (blob) => {
                resolve(new File([blob], file.name, { type: blob.type }));
            },
            "image/jpeg",
            quality
        );
    });
}
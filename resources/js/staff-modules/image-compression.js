export function validateImageFile(file) {
    const validTypes = ["image/jpeg", "image/png", "image/webp"];
    const maxSizeMB = 10;

    if (!validTypes.includes(file.type)) {
        alert("Only JPG, PNG, or WEBP images are allowed.");
        return false;
    }

    if (file.size / (1024 * 1024) > maxSizeMB) {
        alert("Image exceeds 10MB limit.");
        return false;
    }

    return true;
}

// Compress image using Canvas API
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
        width *= ratio;
        height *= ratio;
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
/**
 * Validate selected image file before compression
 * - Ensures correct file type and size limit
 */
export function validateImageFile(file, inputEl = null) {
    const validTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp"];

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

    // Size validation (before compression)
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
 * FIXED: Properly handles large images by resizing BEFORE canvas creation
 * and validates actual dimensions after compression
 */
export async function compressImageCanvas(
    file,
    maxWidth = 1200,
    maxHeight = 1200,
    quality = 0.85
) {

    // Create image bitmap from file
    const imageBitmap = await createImageBitmap(file);
    let { width, height } = imageBitmap;

    // Calculate new dimensions maintaining aspect ratio
    let targetWidth = width;
    let targetHeight = height;

    if (width > maxWidth || height > maxHeight) {
        const ratio = Math.min(maxWidth / width, maxHeight / height);
        targetWidth = Math.round(width * ratio);
        targetHeight = Math.round(height * ratio);
    }

    // Create canvas with target dimensions
    const canvas = document.createElement("canvas");
    canvas.width = targetWidth;
    canvas.height = targetHeight;

    // Draw resized image
    const ctx = canvas.getContext("2d");
    ctx.drawImage(imageBitmap, 0, 0, targetWidth, targetHeight);

    // Clean up bitmap
    imageBitmap.close();

    return new Promise((resolve, reject) => {
        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    reject(new Error("Failed to create compressed image blob"));
                    return;
                }

                // Create File object with sanitized name (no original metadata)
                const sanitizedName = `compressed_${Date.now()}.jpg`;
                const compressedFile = new File([blob], sanitizedName, {
                    type: "image/jpeg",
                    lastModified: Date.now(),
                });

                // Verify actual dimensions of compressed file
                verifyImageDimensions(compressedFile, targetWidth, targetHeight)
                    .then(() => resolve(compressedFile))
                    .catch(reject);
            },
            "image/jpeg",
            quality
        );
    });
}

/**
 * Verify the actual dimensions of the compressed image
 * This ensures the backend won't reject it due to dimension limits
 */
async function verifyImageDimensions(file, expectedWidth, expectedHeight) {
    return new Promise((resolve, reject) => {
        const img = new Image();

        img.onload = () => {
            
            // Allow small tolerance for rounding
            const widthMatch = Math.abs(img.width - expectedWidth) <= 1;
            const heightMatch = Math.abs(img.height - expectedHeight) <= 1;

            URL.revokeObjectURL(img.src);

            if (widthMatch && heightMatch) {
                resolve();
            } else {
                reject(
                    new Error(
                        `Dimension mismatch: expected ${expectedWidth}x${expectedHeight}, ` +
                            `got ${img.width}x${img.height}`
                    )
                );
            }
        };

        img.onerror = () => {
            URL.revokeObjectURL(img.src);
            reject(new Error("Failed to verify image dimensions"));
        };

        img.src = URL.createObjectURL(file);
    });
}
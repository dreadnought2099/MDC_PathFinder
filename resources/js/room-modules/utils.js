/**
 * utils.js - Utility Functions for Image Processing
 *
 * UPDATED TO MATCH RoomController.php:
 * - Max file size: 5MB (reduced from 10MB)
 * - Max dimensions: 3000x3000px (NEW - matches Laravel validation)
 * - Compression target: 2000px (matches backend processing)
 */

export function showTemporaryMessage(text, type = "info") {
    if (typeof window.showTemporaryMessage === "function") {
        window.showTemporaryMessage(text, type);
    } else {
        console.log(`[${type}] ${text}`);
    }
}

export function showTemporaryFeedback(button, text, duration = 2000) {
    const old = button.textContent;
    button.textContent = text;
    setTimeout(() => (button.textContent = old), duration);
}

export function showError(row, msg) {
    if (!row) return;
    row.classList.add("bg-red-50", "border", "border-red-400");
    if (!row.querySelector(".error-msg")) {
        const p = document.createElement("p");
        p.className = "error-msg text-red-600 text-xs mt-1";
        p.textContent = msg;
        row.appendChild(p);
    }
}

export function clearError(row) {
    if (!row) return;
    row.classList.remove("bg-red-50", "border", "border-red-400");
    const msg = row.querySelector(".error-msg");
    if (msg) msg.remove();
}

/**
 * Compress image using canvas - Frontend compression layer
 *
 * MATCHES CONTROLLER PIPELINE:
 * - Compresses to 2000px max dimension (same as backend target)
 * - Converts to JPEG for consistent format
 * - Quality: 85% base, 80% for 5-8MB, 75% for >8MB
 * - This is the FIRST step before Laravel validation
 *
 * @param {File} file - Original image file
 * @param {number} maxDimension - Max width/height (default: 2000px)
 * @param {number} quality - JPEG quality 0-1 (default: 0.85)
 * @returns {Promise<File>} Compressed JPEG file
 */
export async function compressImageCanvas(
    file,
    maxDimension = 2000,
    quality = 0.85
) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                // Resize if exceeds max dimension
                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement("canvas");
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext("2d");
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = "high";
                ctx.drawImage(img, 0, 0, width, height);

                // Adjust quality based on original file size
                let targetQuality = quality;
                const sizeMB = file.size / 1024 / 1024;
                if (sizeMB > 8) targetQuality = 0.75;
                else if (sizeMB > 5) targetQuality = 0.8;

                canvas.toBlob(
                    (blob) => {
                        if (blob && blob.size < file.size) {
                            const originalName = file.name.replace(
                                /\.[^/.]+$/,
                                ""
                            );
                            const compressedFile = new File(
                                [blob],
                                `${originalName}.jpg`,
                                {
                                    type: "image/jpeg",
                                    lastModified: Date.now(),
                                }
                            );
                            resolve(compressedFile);
                        } else {
                            resolve(file); // Use original if compression didn't help
                        }
                    },
                    "image/jpeg",
                    targetQuality
                );
            };
            img.onerror = () => resolve(file);
            img.src = e.target.result;
        };
        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
    });
}

/**
 * Validate image file - Frontend validation layer
 *
 * ⚠️ UPDATED: NOW ASYNC AND CHECKS DIMENSIONS
 *
 * MATCHES CONTROLLER VALIDATION:
 * - Max size: 5MB (reduced from 10MB to match controller)
 * - Max dimensions: 3000x3000px (NEW - matches Laravel validation)
 * - Allowed types: JPEG, PNG only
 * - This validation happens AFTER compression
 *
 * @param {File} file - Image file to validate
 * @param {number} maxSizeMB - Max file size in MB (default: 5MB)
 * @param {boolean} showError - Show error message (default: true)
 * @returns {Promise<boolean>} True if valid, false otherwise
 */
export async function validateImageFile(file, maxSizeMB = 5, showError = true) {
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];

    // Check file type
    if (!allowedTypes.includes(file.type)) {
        if (showError) {
            showTemporaryMessage(
                "Invalid image type. Only JPEG and PNG are allowed.",
                "error"
            );
        }
        return false;
    }

    // Check file size (5MB limit matches controller)
    if (file.size > maxSizeMB * 1024 * 1024) {
        if (showError) {
            showTemporaryMessage(
                `Image too large. Maximum ${maxSizeMB}MB allowed.`,
                "error"
            );
        }
        return false;
    }

    // Check dimensions (3000x3000px limit matches controller)
    try {
        const dimensions = await getImageDimensions(file);
        const maxDimension = 3000;

        if (
            dimensions.width > maxDimension ||
            dimensions.height > maxDimension
        ) {
            if (showError) {
                showTemporaryMessage(
                    `Image dimensions too large. Maximum ${maxDimension}px on either side. Current: ${dimensions.width}x${dimensions.height}px`,
                    "error"
                );
            }
            return false;
        }
    } catch (error) {
        console.error("Failed to check image dimensions:", error);
        // Allow file if dimension check fails (backend will catch it)
    }

    return true;
}

/**
 * Get image dimensions from file
 * Helper function for dimension validation
 *
 * @param {File} file - Image file
 * @returns {Promise<{width: number, height: number}>}
 */
function getImageDimensions(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(url);
            resolve({
                width: img.width,
                height: img.height,
            });
        };

        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error("Failed to load image"));
        };

        img.src = url;
    });
}

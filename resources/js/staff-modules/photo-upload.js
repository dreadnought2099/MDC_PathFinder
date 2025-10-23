// photo-upload.js
import { compressImageCanvas, validateImageFile } from "./image-compression.js";
import { createPreview } from "./image-preview.js";
import { formatFileSize } from "./utils.js";

/**
 * setupPhotoUpload(options)
 * Handles image selection, validation, compression, and preview display.
 *
 * options: {
 *   photoInput, uploadBox, placeholder, previewContainer,
 *   onCompressStart?, onCompressEnd?, onError?
 * }
 */
export function setupPhotoUpload(options = {}) {
    const {
        photoInput,
        uploadBox,
        placeholder,
        previewContainer,
        onCompressStart,
        onCompressEnd,
        onError,
    } = options;

    if (!photoInput || !uploadBox || !previewContainer) return;

    // Open file selector when user clicks upload box
    uploadBox.addEventListener("click", () => photoInput.click());

    // Handle file selection
    photoInput.addEventListener("change", async () => {
        const file = photoInput.files?.[0];

        // If user canceled file selection
        if (!file) {
            window.showTemporaryMessage?.("No image selected.", "warning");
            return;
        }

        // Validate before compression
        if (!validateImageFile(file, photoInput)) {
            onError?.("Invalid or oversized image file.");
            return;
        }

        try {
            onCompressStart?.(file.size);
            window.showTemporaryMessage?.("Compressing image...", "info");

            const compressedFile = await compressImageCanvas(file);

            // Replace the input's FileList with the compressed file
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            photoInput.files = dt.files;

            // Create live preview
            createPreview({
                input: photoInput,
                container: previewContainer,
                placeholder,
                file: compressedFile,
            });

            onCompressEnd?.(file.size, compressedFile.size);

            // Show success message
            window.showTemporaryMessage?.(
                `Image compressed: ${formatFileSize(
                    file.size
                )} → ${formatFileSize(compressedFile.size)}`,
                "success"
            );
        } catch (err) {
            console.error("Compression error:", err);
            const msg = "Image compression failed. Try another file.";
            onError?.(msg);
            window.showTemporaryMessage?.(msg, "error");
        }
    });
}
import { compressImageCanvas, validateImageFile } from "./image-compression.js";
import { createPreview } from "./image-preview.js";
import { formatFileSize } from "./utils.js";

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

    // --- CLICK UPLOAD ---
    uploadBox.addEventListener("click", () => photoInput.click());

    // --- DRAG AND DROP EVENTS ---
    uploadBox.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadBox.classList.add("border-blue-500", "bg-blue-50");
    });

    uploadBox.addEventListener("dragleave", () => {
        uploadBox.classList.remove("border-blue-500", "bg-blue-50");
    });

    uploadBox.addEventListener("drop", async (e) => {
        e.preventDefault();
        uploadBox.classList.remove("border-blue-500", "bg-blue-50");
        const file = e.dataTransfer.files?.[0];
        if (file) await handleFile(file);
    });

    // --- NORMAL FILE INPUT CHANGE ---
    photoInput.addEventListener(
        "change",
        async (e) => {
            e.preventDefault(); // Prevent default handling
            e.stopImmediatePropagation(); // Stop other listeners

            const file = photoInput.files?.[0];
            if (file) {
                // Clear the input immediately to prevent form submission of original
                const originalFile = file;
                photoInput.value = "";
                await handleFile(originalFile);
            }
        },
        { capture: true }
    ); // Capture phase to run before other handlers

    // --- CORE FILE HANDLER ---
    async function handleFile(file) {
        if (!file) {
            window.showTemporaryMessage?.("No image selected.", "warning");
            return;
        }

        // Validate before compression
        if (!validateImageFile(file, photoInput)) {
            const msg = "Invalid or oversized image file.";
            onError?.(msg);
            window.showTemporaryMessage?.(msg, "error");
            return;
        }

        try {
            onCompressStart?.(file.size);
            window.showTemporaryMessage?.("Compressing image...", "info");

            // Compress
            const compressedFile = await compressImageCanvas(
                file,
                1200,
                1200,
                0.85
            );

            // CRITICAL FIX: Create a fresh Blob then File to strip ALL metadata
            const cleanBlob = await fetch(
                URL.createObjectURL(compressedFile)
            ).then((r) => r.blob());

            const cleanFile = new File(
                [cleanBlob],
                `staff_photo_${Date.now()}.jpg`,
                {
                    type: "image/jpeg",
                    lastModified: Date.now(),
                }
            );

            // Replace original FileList with clean file
            const dt = new DataTransfer();
            dt.items.add(cleanFile);
            photoInput.files = dt.files;

            // Verify the input now has correct file
            if (photoInput.files[0]) {
            }

            // Preview
            createPreview({
                input: photoInput,
                container: previewContainer,
                placeholder,
                file: cleanFile,
            });

            onCompressEnd?.(file.size, cleanFile.size);

            window.showTemporaryMessage?.(
                `Image compressed: ${formatFileSize(
                    file.size
                )} → ${formatFileSize(cleanFile.size)}`,
                "success"
            );
        } catch (err) {
            console.error("Compression error:", err);
            const msg = "Image compression failed. Try another file.";
            onError?.(msg);
            window.showTemporaryMessage?.(msg, "error");
        } finally {
            // Always reset drag styles
            uploadBox.classList.remove("border-blue-500", "bg-blue-50");
        }
    }
}
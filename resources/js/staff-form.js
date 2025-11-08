import { setupPhotoUpload } from "./staff-modules/photo-upload.js";
import { createPreview } from "./staff-modules/image-preview.js";
import { setupAutoResize } from "./staff-modules/auto-resize.js";
import { showTemporaryMessage, formatFileSize } from "./staff-modules/utils.js";
import { initializeFormSubmission } from "./staff-modules/form-submission.js";

document.addEventListener("DOMContentLoaded", () => {
    const photoInput = document.getElementById("photo_path");
    const uploadBox = document.getElementById("staffUploadBox");
    const placeholder = document.getElementById("staffPlaceholder");
    const previewContainer = document.getElementById("staffPreviewContainer");
    const bioTextarea = document.getElementById("bio");

    // Show existing image (edit mode)
    const existingPhotoUrl = photoInput?.dataset?.existingPhoto || null;
    if (existingPhotoUrl && previewContainer && placeholder && photoInput) {
        createPreview({
            input: photoInput,
            container: previewContainer,
            placeholder,
            existingUrl: existingPhotoUrl,
        });
    }

    // Setup centralized photo upload flow
    if (photoInput && uploadBox && placeholder && previewContainer) {
        setupPhotoUpload({
            photoInput,
            uploadBox,
            placeholder,
            previewContainer,
            onCompressStart: () =>
                showTemporaryMessage("Compressing image...", "info"),
            onCompressEnd: (originalBytes, compressedBytes) =>
                showTemporaryMessage(
                    `Image compressed: ${formatFileSize(
                        originalBytes
                    )} → ${formatFileSize(compressedBytes)}`,
                    "success"
                ),
            onError: (msg) => showTemporaryMessage(msg, "error"),
        });
    }

    // Auto-resize textarea
    if (bioTextarea) setupAutoResize(bioTextarea);

    // Initialize form submission
    initializeFormSubmission();
});

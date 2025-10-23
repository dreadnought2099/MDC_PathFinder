import { setupPhotoUpload } from "./staff-modules/photo-upload.js";
import { createPreview } from "./staff-modules/image-preview.js";
import { setupEmailValidation } from "./staff-modules/email-validation.js";
import { setupAutoResize } from "./staff-modules/auto-resize.js";
import { showTemporaryMessage, formatFileSize } from "./staff-modules/utils.js";

document.addEventListener("DOMContentLoaded", () => {
    const photoInput = document.getElementById("photo_path");
    const uploadBox = document.getElementById("staffUploadBox");
    const placeholder = document.getElementById("staffPlaceholder");
    const previewContainer = document.getElementById("staffPreviewContainer");
    const bioTextarea = document.getElementById("bio");
    const emailInput = document.getElementById("email");

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

    // Email validation (single source of truth)
    if (emailInput) {
        const existingEmail = emailInput.dataset.existingEmail || null;
        setupEmailValidation(emailInput, existingEmail);
    }

    // Auto-resize textarea
    if (bioTextarea) setupAutoResize(bioTextarea);
});
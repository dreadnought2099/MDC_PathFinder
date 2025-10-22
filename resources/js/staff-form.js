import { createPreview } from "./staff-modules/image-preview.js";
import {
    compressImageCanvas,
    validateImageFile,
} from "./staff-modules/image-compression.js";
import { setupPhotoUpload } from "./staff-modules/photo-upload.js";
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

    // Show existing image if editing
    const existingPhotoUrl = photoInput?.dataset.existingPhoto;
    if (existingPhotoUrl) {
        createPreview(
            photoInput,
            previewContainer,
            placeholder,
            null,
            existingPhotoUrl
        );
    }

    // Handle new uploads
    photoInput.addEventListener("change", async () => {
        if (!photoInput.files.length) return;

        const file = photoInput.files[0];
        if (!validateImageFile(file)) return;

        showTemporaryMessage("Compressing image...", "info");

        try {
            const compressed = await compressImageCanvas(file);

            createPreview(
                photoInput,
                previewContainer,
                placeholder,
                compressed
            );

            showTemporaryMessage(
                `Image compressed: ${formatFileSize(
                    file.size
                )} → ${formatFileSize(compressed.size)}`,
                "success"
            );

            // Update input files
            const dt = new DataTransfer();
            dt.items.add(compressed);
            photoInput.files = dt.files;
        } catch (err) {
            showTemporaryMessage("Image compression failed.", "error");
            console.error(err);
        }
    });

    // Email validation
    if (emailInput) {
        const existingEmail = emailInput.dataset.existingEmail || null;
        setupEmailValidation(emailInput, existingEmail);
    }

    // Auto-resize bio
    if (bioTextarea) setupAutoResize(bioTextarea);

    // Optional: Setup click-to-upload area
    if (uploadBox && photoInput && previewContainer && placeholder) {
        setupPhotoUpload({
            photoInput,
            uploadBox,
            placeholder,
            previewContainer,
            onCompressStart: () =>
                showTemporaryMessage("Compressing image...", "info"),
            onCompressEnd: (originalSize, compressedSize) =>
                showTemporaryMessage(
                    `Image compressed: ${formatFileSize(
                        originalSize
                    )} → ${formatFileSize(compressedSize)}`,
                    "success"
                ),
            onError: (msg) => showTemporaryMessage(msg, "error"),
        });
    }
});
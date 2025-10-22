import { createPreview } from "./staff-modules/image-preview.js";
import { compressImageCanvas, validateImageFile } from "./staff-modules/image-compression.js";
import { setupPhotoUpload } from "./staff-modules/photo-upload.js";
import { setupEmailValidation } from "./staff-modules/email-validation.js";
import { setupAutoResize } from "./staff-modules/auto-resize.js";
import { showTemporaryMessage } from "./staff-modules/utils.js";

document.addEventListener("DOMContentLoaded", () => {
    const photoInput = document.getElementById("photo_path");
    const uploadBox = document.getElementById("staffUploadBox");
    const placeholder = document.getElementById("staffPlaceholder");
    const previewContainer = document.getElementById("staffPreviewContainer");
    const bioTextarea = document.getElementById("bio");
    const emailInput = document.getElementById("email");

    // Handle existing image for edit form
    const existingPhotoUrl = photoInput?.dataset.existingPhoto;
    if (existingPhotoUrl) {
        createPreview(
            photoInput,
            previewContainer,
            placeholder,
            null,
            0,
            0,
            existingPhotoUrl
        );
    }

    // Handle new uploads
    photoInput.addEventListener("change", async () => {
        if (photoInput.files.length > 0) {
            const file = photoInput.files[0];

            if (!validateImageFile(file)) return;

            showTemporaryMessage("Compressing image...", "info");
            const compressed = await compressImageCanvas(file);
            createPreview(
                photoInput,
                previewContainer,
                placeholder,
                compressed,
                file.size,
                compressed.size
            );
            showTemporaryMessage(
                `Image compressed: ${file.size / 1024 / 1024} → ${
                    compressed.size / 1024 / 1024
                } MB`,
                "success"
            );

            // Update input files
            const dt = new DataTransfer();
            dt.items.add(compressed);
            photoInput.files = dt.files;
        }
    });

    // Email validation setup
    if (emailInput) {
        const existingEmail = emailInput.dataset.existingEmail || null;
        setupEmailValidation(emailInput, existingEmail);
    }

    // Auto-resize bio field
    if (bioTextarea) setupAutoResize(bioTextarea);

    // Photo upload + compression
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
                    `Image compressed: ${originalSize} → ${compressedSize}`,
                    "success"
                ),
            onError: (msg) => showTemporaryMessage(msg, "error"),
        });
    }
});

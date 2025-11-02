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

    // Form submission handler with Alpine.js modal support
    const form = document.querySelector("form[action*='staff']");
    if (form) {

        let isSubmitting = false;

        form.addEventListener(
            "submit",
            async (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                if (isSubmitting) {
                    console.warn("⚠️ Form already submitting");
                    return;
                }
                isSubmitting = true;

                const submitBtn = document.getElementById("submitBtn");
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.textContent;
                    submitBtn.textContent = "Saving...";
                    submitBtn.dataset.originalText = originalText;
                }

                try {
                    // Build FormData
                    const formData = new FormData();
                    const formElements = form.elements;

                    for (let i = 0; i < formElements.length; i++) {
                        const element = formElements[i];
                        if (element.type === "file") continue;
                        if (!element.name || element.type === "submit")
                            continue;
                        if (
                            (element.type === "checkbox" ||
                                element.type === "radio") &&
                            !element.checked
                        )
                            continue;
                        formData.append(element.name, element.value);
                    }

                    // Add compressed file
                    if (photoInput?.files?.length > 0) {
                        const compressedFile = photoInput.files[0];
                        formData.append(
                            "photo_path",
                            compressedFile,
                            compressedFile.name
                        );
                    }

                    const requestId = `req_${Date.now()}`;

                    // Use XMLHttpRequest for upload progress tracking
                    const response = await submitWithProgress(
                        form.action,
                        formData,
                        requestId
                    );
                    const data = await response.json();


                    if (response.ok) {
                        showTemporaryMessage(
                            "Staff saved successfully!",
                            "success"
                        );
                        setTimeout(() => {
                            window.location.href =
                                data.redirect || "/admin/staff";
                        }, 500);
                    } else if (data.errors) {
                        Object.values(data.errors)
                            .flat()
                            .forEach((msg) => {
                                showTemporaryMessage(msg, "error");
                            });
                    }
                } catch (error) {
                    console.error("Submit error:", error);
                    showTemporaryMessage("Network error occurred", "error");
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent =
                            submitBtn.dataset.originalText || "Save Staff";
                    }
                    isSubmitting = false;
                }
            },
            { once: false }
        );
    }
});

/**
 * Submit form with progress tracking using XMLHttpRequest
 * Dispatches Alpine.js compatible events: upload-start, upload-progress, upload-finish
 */
function submitWithProgress(url, formData, requestId) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();

        // Dispatch upload-start event for Alpine.js modal
        window.dispatchEvent(
            new CustomEvent("upload-start", {
                bubbles: true,
                detail: { message: "Uploading staff data..." },
            })
        );

        // Track upload progress
        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                const progress = Math.round((e.loaded / e.total) * 100);

                // Dispatch upload-progress event for Alpine.js modal
                window.dispatchEvent(
                    new CustomEvent("upload-progress", {
                        bubbles: true,
                        detail: {
                            progress: progress,
                            loaded: e.loaded,
                            total: e.total,
                        },
                    })
                );
            }
        });

        // Handle completion
        xhr.addEventListener("load", () => {

            // Dispatch upload-finish event for Alpine.js modal
            window.dispatchEvent(
                new CustomEvent("upload-finish", {
                    bubbles: true,
                    detail: { status: xhr.status },
                })
            );

            // Create Response object for consistent API
            const response = {
                ok: xhr.status >= 200 && xhr.status < 300,
                status: xhr.status,
                json: async () => JSON.parse(xhr.responseText),
            };

            resolve(response);
        });

        // Handle errors
        xhr.addEventListener("error", () => {
            console.error("❌ Network error");

            // Dispatch upload-finish to close modal on error
            window.dispatchEvent(
                new CustomEvent("upload-finish", {
                    bubbles: true,
                    detail: { error: true },
                })
            );

            reject(new Error("Network error occurred"));
        });

        // Handle abort
        xhr.addEventListener("abort", () => {
            console.warn("⚠️ Upload aborted");

            window.dispatchEvent(
                new CustomEvent("upload-finish", {
                    bubbles: true,
                    detail: { aborted: true },
                })
            );

            reject(new Error("Upload aborted"));
        });

        // Open and send request
        xhr.open("POST", url);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("X-Request-ID", requestId);

        // Get CSRF token
        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) {
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
        }

        xhr.send(formData);
    });
}
import { showTemporaryMessage } from "./utils.js";

export function initializeFormSubmission() {
    const form = document.getElementById("staff-form");
    if (!form) return;

    let isSubmitting = false;

    form.addEventListener("submit", async (e) => {
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
            const formData = new FormData();
            const formElements = form.elements;
            const photoInput = document.getElementById("photo_path");

            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.type === "file") continue;
                if (!element.name || element.type === "submit") continue;
                if (
                    (element.type === "checkbox" || element.type === "radio") &&
                    !element.checked
                )
                    continue;
                formData.append(element.name, element.value);
            }

            // Add compressed image if available
            if (photoInput?.files?.length > 0) {
                const compressedFile = photoInput.files[0];
                formData.append(
                    "photo_path",
                    compressedFile,
                    compressedFile.name
                );
            }

            const requestId = `req_${Date.now()}`;
            const response = await submitWithProgress(
                form.action,
                formData,
                requestId
            );
            const data = await response.json();

            if (response.ok) {
                showTemporaryMessage("Staff saved successfully!", "success");
                setTimeout(() => {
                    window.location.href = data.redirect || "/admin/staff";
                }, 500);
            } else if (data.errors) {
                Object.values(data.errors)
                    .flat()
                    .forEach((msg) => showTemporaryMessage(msg, "error"));
            }
        } catch (error) {
            console.error("Submit error:", error);
            showTemporaryMessage("Network error occurred", "error");
        } finally {
            const submitBtn = document.getElementById("submitBtn");
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent =
                    submitBtn.dataset.originalText || "Save Staff";
            }
            isSubmitting = false;
        }
    });
}

/**
 * Handles form submission with progress tracking and Alpine.js event dispatching.
 */
function submitWithProgress(url, formData, requestId) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();

        // Start upload event
        window.dispatchEvent(
            new CustomEvent("upload-start", {
                bubbles: true,
                detail: { message: "Uploading staff data..." },
            })
        );

        // Progress tracking
        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                const progress = Math.round((e.loaded / e.total) * 100);
                window.dispatchEvent(
                    new CustomEvent("upload-progress", {
                        bubbles: true,
                        detail: { progress, loaded: e.loaded, total: e.total },
                    })
                );
            }
        });

        // On complete
        xhr.addEventListener("load", () => {
            window.dispatchEvent(
                new CustomEvent("upload-finish", {
                    bubbles: true,
                    detail: { status: xhr.status },
                })
            );

            const response = {
                ok: xhr.status >= 200 && xhr.status < 300,
                status: xhr.status,
                json: async () => JSON.parse(xhr.responseText),
            };

            resolve(response);
        });

        // On error
        xhr.addEventListener("error", () => {
            console.error("❌ Network error");
            window.dispatchEvent(
                new CustomEvent("upload-finish", {
                    bubbles: true,
                    detail: { error: true },
                })
            );
            reject(new Error("Network error occurred"));
        });

        // On abort
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

        // Open and send
        xhr.open("POST", url);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("X-Request-ID", requestId);

        // CSRF token
        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) {
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
        }

        xhr.send(formData);
    });
}
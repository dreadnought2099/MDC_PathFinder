import { showTemporaryMessage } from "./utils";
import { prepareCarouselForSubmit } from "./carousel-images";

export function initializeFormSubmission(setIsUploadingCallback = () => {}) {
    const form = document.getElementById("room-form");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        submitFormWithProgress(setIsUploadingCallback);
    });
}

async function submitFormWithProgress(setIsUploading) {
    const form = document.getElementById("room-form");
    const submitBtn = document.querySelector("#submit-btn");
    if (!form) return;

    // Give modules a chance to add hidden inputs (e.g. removed image ids)
    try {
        prepareCarouselForSubmit();
    } catch (err) {
        console.warn("prepareCarouselForSubmit error", err);
    }

    setIsUploading(true);
    window.dispatchEvent(new CustomEvent("upload-start"));
    if (submitBtn) submitBtn.disabled = true;

    try {
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open(form.method || "POST", form.action, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                window.dispatchEvent(
                    new CustomEvent("upload-progress", {
                        detail: { progress: percent },
                    })
                );
            }
        };

        xhr.onload = function () {
            setIsUploading(false);
            window.dispatchEvent(new CustomEvent("upload-finish"));
            if (submitBtn) submitBtn.disabled = false;

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.redirect) {
                        window.onbeforeunload = null;
                        window.location.href = data.redirect;
                        return;
                    }
                } catch (err) {
                    /* ignore */
                }
                window.onbeforeunload = null;
                window.location.href = "/admin/rooms";
            } else {
                let msg = "Submission failed.";
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.message) msg = data.message;
                } catch {}
                showTemporaryMessage(msg, "error");
            }
        };

        xhr.onerror = function () {
            setIsUploading(false);
            window.dispatchEvent(new CustomEvent("upload-finish"));
            if (submitBtn) submitBtn.disabled = false;
            showTemporaryMessage("Upload failed. Please try again.", "error");
        };

        xhr.send(formData);
    } catch (error) {
        setIsUploading(false);
        window.dispatchEvent(new CustomEvent("upload-finish"));
        if (submitBtn) submitBtn.disabled = false;
        showTemporaryMessage(
            "Error submitting form. Please try again.",
            "error"
        );
        console.error("Form submission error:", error);
    }
}
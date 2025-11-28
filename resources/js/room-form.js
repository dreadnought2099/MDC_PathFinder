import { initializeCoverImage } from "./room-modules/cover-image";
import { initializeCarouselImages } from "./room-modules/carousel-images";
import { initializeVideoUpload } from "./room-modules/video-upload";
import { initializeOfficeHours } from "./room-modules/office-hours";
import { initializeConsultationTimes } from "./room-modules/consultation-times";
import { initializeScheduleTabs } from "./room-modules/schedule-tabs";
import { initializeFormSubmission } from "./room-modules/form-submission";
import { initializeConditionalFields } from "./room-modules/conditional-fields";
import { initializeTextareaAutoResize } from "./room-modules/textarea-auto-resize";

function initRoomForm() {
    window.existingOfficeHours = window.existingOfficeHours || {};
    window.existingConsultationTimes = window.existingConsultationTimes || {};
    let isUploading = false;

    // Prevent accidental navigation during upload
    window.onbeforeunload = function (e) {
        if (isUploading) {
            e.preventDefault();
            e.returnValue = "";
            return "";
        }
    };

    initializeCoverImage();
    initializeCarouselImages();
    initializeVideoUpload();
    initializeScheduleTabs();
    initializeOfficeHours();
    initializeConsultationTimes();
    initializeFormSubmission((uploading) => {
        isUploading = uploading;
    });
    initializeConditionalFields();
    initializeTextareaAutoResize();
}

// Make removeFile globally available for inline Blade onclick handlers
window.removeFile = function (type) {
    if (type === "image") {
        const removeImageFlag = document.getElementById("remove_image_path");
        const imagePreview = document.querySelector(
            '#cover-image-section img[src*="storage/"]'
        );
        if (removeImageFlag) removeImageFlag.value = "1";
        if (imagePreview) {
            imagePreview.classList.add("opacity-50", "grayscale");
            imagePreview.style.pointerEvents = "none";
        }
    }

    if (type === "video") {
        const removeVideoFlag = document.getElementById("remove_video_path");
        const videoPreview = document.querySelector(
            '#video-section video[src*="storage/"]'
        );
        if (removeVideoFlag) removeVideoFlag.value = "1";
        if (videoPreview) {
            videoPreview.classList.add("opacity-50", "grayscale");
            videoPreview.style.pointerEvents = "none";
        }
    }
};

// Run immediately if DOM is already loaded, otherwise wait
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initRoomForm);
} else {
    // DOM is already loaded (common with dynamic imports)
    initRoomForm();
}
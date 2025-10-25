import { initializeCoverImage } from "./room-modules/cover-image";
import { initializeCarouselImages } from "./room-modules/carousel-images";
import { initializeVideoUpload } from "./room-modules/video-upload";
import { initializeOfficeHours } from "./room-modules/office-hours";
import { initializeFormSubmission } from "./room-modules/form-submission";
import { initializeConditionalFields } from "./room-modules/conditional-fields";

document.addEventListener("DOMContentLoaded", function () {
    window.existingOfficeHours = window.existingOfficeHours || {};
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
    initializeOfficeHours();
    initializeFormSubmission((uploading) => {
        isUploading = uploading;
    });
    initializeConditionalFields();
});

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

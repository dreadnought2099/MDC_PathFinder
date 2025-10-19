import { initializeCoverImage } from "./room-modules/cover-image";
import { initializeCarouselImages } from "./room-modules/carousel-images";
import { initializeVideoUpload } from "./room-modules/video-upload";
import { initializeOfficeHours } from "./room-modules/office-hours";
import { initializeFormSubmission } from "./room-modules/form-submission";
import { initializeConditionalFields } from "./room-modules/conditional-fields";

document.addEventListener("DOMContentLoaded", function () {
    let isUploading = false;

    // Prevent accidental navigation during upload
    window.onbeforeunload = function (e) {
        if (isUploading) {
            e.preventDefault();
            e.returnValue = "";
            return "";
        }
    };

    // Initialize all form modules
    initializeCoverImage();
    initializeCarouselImages();
    initializeVideoUpload();
    initializeOfficeHours();
    initializeFormSubmission((uploading) => {
        isUploading = uploading;
    });
    initializeConditionalFields();
});
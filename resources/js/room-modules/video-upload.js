import { showTemporaryMessage } from "./utils";

const MAX_VIDEO_SIZE_MB = 50;
const ALLOWED_VIDEO_TYPES = ["video/mp4", "video/avi", "video/mpeg"];

export function initializeVideoUpload() {
    const videoDropZone = document.getElementById("videoDropZone");
    const videoInput = document.getElementById("video_path");
    const removeVideoThumbnailBtn = document.getElementById(
        "removeVideoThumbnailBtn"
    );
    if (!videoDropZone || !videoInput) return;

    document
        .querySelectorAll('.group label input[type="checkbox"]')
        .forEach((cb) => {
            cb.addEventListener("change", function () {
                const parent = this.closest(".group");
                if (parent) {
                    parent.classList.toggle("opacity-50", this.checked);
                    parent.classList.toggle("grayscale", this.checked);
                }
            });
        });

    // If there's already a server-rendered video above the upload box (edit), keep it visible.
    // Your Blade already prints a <video src="..."> above the drop zone if room->video_path exists,
    // so no work is required here; our preview area (#videoThumbnailPreview) will be used when user selects a new file.

    videoDropZone.addEventListener("click", (e) => {
        if (e.target.closest("#removeVideoThumbnailBtn")) {
            e.stopPropagation();
            e.preventDefault();
            return;
        }
        videoInput.click();
    });

    // When a user selects a new video file
    videoInput.addEventListener("change", () => {
        if (videoInput.files && videoInput.files[0]) {
            showVideoThumbnailPreview(videoInput.files[0]);
        }

        // Reset deletion flag — user uploaded new video
        const removeVideoFlag = document.getElementById("remove_video_path");
        if (removeVideoFlag) removeVideoFlag.value = "0";
    });

    ["dragover", "dragleave", "drop"].forEach((eventName) => {
        videoDropZone.addEventListener(eventName, handleVideoDrag);
    });

    if (removeVideoThumbnailBtn) {
        removeVideoThumbnailBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            e.preventDefault();
            clearVideoThumbnail();
            const vi = document.getElementById("video_path");
            if (vi) vi.value = "";
            const removeVideoFlag =
                document.getElementById("remove_video_path");
            if (removeVideoFlag) removeVideoFlag.value = "1"; // mark for deletion
        });
    }
}

function handleVideoDrag(e) {
    const videoDropZone = document.getElementById("videoDropZone");
    if (!videoDropZone) return;
    e.preventDefault();
    if (e.type === "dragover")
        videoDropZone.classList.add("border-primary", "bg-gray-50");
    else if (e.type === "dragleave")
        videoDropZone.classList.remove("border-primary", "bg-gray-50");
    else if (e.type === "drop") {
        videoDropZone.classList.remove("border-primary", "bg-gray-50");
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const videoInput = document.getElementById("video_path");
            if (videoInput) videoInput.files = e.dataTransfer.files;
            showVideoThumbnailPreview(e.dataTransfer.files[0]);

            // Reset deletion flag when dropping a new file
            const removeVideoFlag =
                document.getElementById("remove_video_path");
            if (removeVideoFlag) removeVideoFlag.value = "0";
        }
    }
}

function showVideoThumbnailPreview(file) {
    const videoInput = document.getElementById("video_path");
    if (!file || !videoInput) return;

    if (file.size / 1024 / 1024 > MAX_VIDEO_SIZE_MB) {
        showTemporaryMessage(
            `"${file.name}" is too large. Max size is ${MAX_VIDEO_SIZE_MB} MB.`,
            "error"
        );
        clearVideoThumbnail();
        videoInput.value = "";
        return;
    }
    if (!ALLOWED_VIDEO_TYPES.includes(file.type)) {
        showTemporaryMessage(
            `"${file.name}" is not a valid format. Only MP4, AVI, or MPEG allowed.`,
            "error"
        );
        clearVideoThumbnail();
        videoInput.value = "";
        return;
    }

    const url = URL.createObjectURL(file);
    const videoThumbnail = document.getElementById("videoThumbnail");
    const videoThumbnailPreview = document.getElementById(
        "videoThumbnailPreview"
    );
    const videoIcon = document.getElementById("videoIcon");
    const videoUploadText = document.getElementById("videoUploadText");
    const videoFormatText = document
        .getElementById("videoDropZone")
        ?.querySelector("p.text-xs");

    if (videoThumbnail) videoThumbnail.src = url;
    if (videoThumbnailPreview) videoThumbnailPreview.classList.remove("hidden");
    if (videoIcon) videoIcon.style.display = "none";
    if (videoUploadText) videoUploadText.style.display = "none";
    if (videoFormatText) videoFormatText.style.display = "none";
}

function clearVideoThumbnail() {
    const videoThumbnail = document.getElementById("videoThumbnail");
    const videoThumbnailPreview = document.getElementById(
        "videoThumbnailPreview"
    );
    const videoIcon = document.getElementById("videoIcon");
    const videoUploadText = document.getElementById("videoUploadText");
    const videoFormatText = document
        .getElementById("videoDropZone")
        ?.querySelector("p.text-xs");

    if (videoThumbnail) videoThumbnail.src = "";
    if (videoThumbnailPreview) videoThumbnailPreview.classList.add("hidden");
    if (videoIcon) videoIcon.style.display = "";
    if (videoUploadText) videoUploadText.style.display = "";
    if (videoFormatText) videoFormatText.style.display = "";
}

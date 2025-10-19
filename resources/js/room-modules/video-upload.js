import { showTemporaryMessage } from "./utils";

const MAX_VIDEO_SIZE_MB = 50;
const ALLOWED_VIDEO_TYPES = ["video/mp4", "video/avi", "video/mpeg"];

export function initializeVideoUpload() {
    const videoDropZone = document.getElementById("videoDropZone");
    const videoInput = document.getElementById("video_path");
    const removeVideoThumbnailBtn = document.getElementById(
        "removeVideoThumbnailBtn"
    );

    videoDropZone.addEventListener("click", (e) => {
        if (e.target.closest("#removeVideoThumbnailBtn")) {
            e.stopPropagation();
            e.preventDefault();
            return;
        }
        videoInput.click();
    });

    videoInput.addEventListener("change", () => {
        if (videoInput.files && videoInput.files[0]) {
            showVideoThumbnailPreview(videoInput.files[0]);
        }
    });

    ["dragover", "dragleave", "drop"].forEach((eventName) => {
        videoDropZone.addEventListener(eventName, handleVideoDrag);
    });

    removeVideoThumbnailBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        e.preventDefault();
        clearVideoThumbnail();
        videoInput.value = "";
    });
}

function handleVideoDrag(e) {
    const videoDropZone = document.getElementById("videoDropZone");
    e.preventDefault();
    if (e.type === "dragover") {
        videoDropZone.classList.add("border-primary", "bg-gray-50");
    } else if (e.type === "dragleave") {
        videoDropZone.classList.remove("border-primary", "bg-gray-50");
    } else if (e.type === "drop") {
        videoDropZone.classList.remove("border-primary", "bg-gray-50");
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const videoInput = document.getElementById("video_path");
            videoInput.files = e.dataTransfer.files;
            showVideoThumbnailPreview(e.dataTransfer.files[0]);
        }
    }
}

function showVideoThumbnailPreview(file) {
    const videoInput = document.getElementById("video_path");

    if (file.size / 1024 / 1024 > MAX_VIDEO_SIZE_MB) {
        showTemporaryMessage(
            `Video is too large. Max size is ${MAX_VIDEO_SIZE_MB}MB.`,
            "error"
        );
        clearVideoThumbnail();
        videoInput.value = "";
        return;
    }

    if (!ALLOWED_VIDEO_TYPES.includes(file.type)) {
        showTemporaryMessage(
            "Invalid video format. Only MP4, AVI, or MPEG allowed.",
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
        .querySelector("p.text-xs");

    videoThumbnail.src = url;
    videoThumbnailPreview.classList.remove("hidden");

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
        .querySelector("p.text-xs");

    videoThumbnail.src = "";
    videoThumbnailPreview.classList.add("hidden");

    if (videoIcon) videoIcon.style.display = "";
    if (videoUploadText) videoUploadText.style.display = "";
    if (videoFormatText) videoFormatText.style.display = "";
}
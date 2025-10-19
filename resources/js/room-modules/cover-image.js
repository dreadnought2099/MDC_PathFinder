import {
    compressImageCanvas,
    validateImageFile,
    showTemporaryMessage,
} from "./utils";

const MAX_IMAGE_SIZE_MB = 10;
let compressedCoverFile = null;

export function initializeCoverImage() {
    const coverInput = document.getElementById("image_path");
    const coverUploadBox = document.getElementById("uploadBox");
    let coverPreview = document.getElementById("previewImage");

    if (!coverPreview) {
        coverPreview = document.createElement("img");
        coverPreview.id = "previewImage";
        coverPreview.className =
            "absolute inset-0 object-cover w-full h-full hidden";
        coverUploadBox.appendChild(coverPreview);
    }

    coverUploadBox.addEventListener("click", () => coverInput.click());

    coverInput.addEventListener("change", async () => {
        if (coverInput.files && coverInput.files[0]) {
            await compressAndPreviewCoverImage(coverInput.files[0]);
        }
    });

    ["dragover", "dragleave", "drop"].forEach((eventName) => {
        coverUploadBox.addEventListener(eventName, handleDragEvent);
    });
}

function handleDragEvent(e) {
    const coverUploadBox = document.getElementById("uploadBox");
    e.preventDefault();
    if (e.type === "dragover") {
        coverUploadBox.classList.add("border-primary", "bg-gray-50");
    } else if (e.type === "dragleave") {
        coverUploadBox.classList.remove("border-primary", "bg-gray-50");
    } else if (e.type === "drop") {
        coverUploadBox.classList.remove("border-primary", "bg-gray-50");
        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            compressAndPreviewCoverImage(files[0]);
        }
    }
}

async function compressAndPreviewCoverImage(file) {
    if (!validateImageFile(file, MAX_IMAGE_SIZE_MB, true)) return;

    try {
        showTemporaryMessage("Compressing cover image...", "info");
        const originalSizeMB = (file.size / 1024 / 1024).toFixed(2);

        const compressedFile = await compressImageCanvas(file, 2000, 0.85);
        const compressedSizeMB = (compressedFile.size / 1024 / 1024).toFixed(2);

        compressedCoverFile = compressedFile;

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(compressedFile);
        document.getElementById("image_path").files = dataTransfer.files;

        showCoverPreview(compressedFile);

        showTemporaryMessage(
            `Cover image compressed: ${originalSizeMB}MB → ${compressedSizeMB}MB`,
            "success"
        );
    } catch (error) {
        console.error("Compression failed:", error);
        showTemporaryMessage(
            "Compression failed, using original image",
            "error"
        );
        compressedCoverFile = file;
        showCoverPreview(file);
    }
}

function showCoverPreview(file) {
    const reader = new FileReader();
    const coverUploadBox = document.getElementById("uploadBox");
    const coverPreview = document.getElementById("previewImage");

    reader.onload = (e) => {
        coverPreview.src = e.target.result;
        coverPreview.classList.remove("hidden");

        const icon = coverUploadBox.querySelector("img:not(#previewImage)");
        const text = coverUploadBox.querySelector("span");
        if (icon) icon.style.display = "none";
        if (text) text.style.display = "none";
    };
    reader.readAsDataURL(file);
}

export function getCompressedCoverFile() {
    return compressedCoverFile;
}
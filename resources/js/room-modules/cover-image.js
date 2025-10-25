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
    if (!coverInput || !coverUploadBox) return;

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

    // Create preview image if not exists
    let coverPreview = document.getElementById("previewImage");
    if (!coverPreview) {
        coverPreview = document.createElement("img");
        coverPreview.id = "previewImage";
        coverPreview.className =
            "absolute inset-0 object-cover w-full h-full hidden";
        coverUploadBox.appendChild(coverPreview);
    }

    const placeholderIcon = coverUploadBox.querySelector(
        "img:not(#previewImage)"
    );
    const placeholderText = coverUploadBox.querySelector("span");

    // Show existing image on edit
    if (coverPreview.src && coverPreview.src.trim() !== "") {
        coverPreview.classList.remove("hidden");
        if (placeholderIcon) placeholderIcon.style.display = "none";
        if (placeholderText) placeholderText.style.display = "none";
    }

    // Handle click to open file input
    coverUploadBox.addEventListener("click", () => coverInput.click());

    // Handle file selection
    coverInput.addEventListener("change", async () => {
        if (coverInput.files && coverInput.files[0]) {
            await compressAndPreviewCoverImage(coverInput.files[0]);
            // Uncheck remove checkbox if user uploads a new file
            const removeCheckbox = document.querySelector(
                'input[name="remove_cover_image"]'
            );
            if (removeCheckbox) removeCheckbox.checked = false;
        }
        const removeImageFlag = document.getElementById("remove_image_path");
        if (removeImageFlag) removeImageFlag.value = "0"; //  ensure not marked for deletion
    });

    // Drag & Drop
    ["dragover", "dragleave", "drop"].forEach((eventName) =>
        coverUploadBox.addEventListener(eventName, handleDragEvent)
    );

    // Handle remove current image checkbox
    const removeCheckbox = document.querySelector(
        'input[name="remove_cover_image"]'
    );
    if (removeCheckbox) {
        removeCheckbox.addEventListener("change", (e) => {
            const removeImageFlag =
                document.getElementById("remove_image_path");
            if (e.target.checked) {
                coverPreview.classList.add("hidden");
                if (placeholderIcon) placeholderIcon.style.display = "";
                if (placeholderText) placeholderText.style.display = "";
                coverInput.value = "";
                compressedCoverFile = null;
                if (removeImageFlag) removeImageFlag.value = "1"; // mark for deletion
            } else {
                if (removeImageFlag) removeImageFlag.value = "0"; // reset if unchecked
            }
        });
    }
}

// Drag & Drop helper
function handleDragEvent(e) {
    const coverUploadBox = document.getElementById("uploadBox");
    if (!coverUploadBox) return;
    e.preventDefault();
    if (e.type === "dragover") {
        coverUploadBox.classList.add("border-primary", "bg-gray-50");
    } else if (e.type === "dragleave" || e.type === "drop") {
        coverUploadBox.classList.remove("border-primary", "bg-gray-50");
    }
    if (e.type === "drop") {
        const files = Array.from(e.dataTransfer.files || []);
        if (files.length > 0) {
            compressAndPreviewCoverImage(files[0]);
            showTemporaryMessage(
                "Cover image added from drag & drop",
                "success"
            );
            const removeImageFlag =
                document.getElementById("remove_image_path");
            if (removeImageFlag) removeImageFlag.value = "0"; // reset flag
        }
    }
}

// Compress and preview cover image
async function compressAndPreviewCoverImage(file) {
    if (!validateImageFile(file, MAX_IMAGE_SIZE_MB, true)) return;

    try {
        showTemporaryMessage("Compressing cover image...", "info");
        const originalSizeMB = (file.size / 1024 / 1024).toFixed(2);
        const compressedFile = await compressImageCanvas(file, 2000, 0.85);
        const compressedSizeMB = (compressedFile.size / 1024 / 1024).toFixed(2);
        compressedCoverFile = compressedFile;

        // Set input file to compressed version
        const dt = new DataTransfer();
        dt.items.add(compressedFile);
        const input = document.getElementById("image_path");
        if (input) input.files = dt.files;

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

// Display preview
function showCoverPreview(file) {
    const reader = new FileReader();
    const coverPreview = document.getElementById("previewImage");
    const coverUploadBox = document.getElementById("uploadBox");
    const placeholderIcon = coverUploadBox.querySelector(
        "img:not(#previewImage)"
    );
    const placeholderText = coverUploadBox.querySelector("span");

    if (!coverPreview) return;

    reader.onload = (e) => {
        coverPreview.src = e.target.result;
        coverPreview.classList.remove("hidden");
        if (placeholderIcon) placeholderIcon.style.display = "none";
        if (placeholderText) placeholderText.style.display = "none";
    };
    reader.readAsDataURL(file);
}

export function getCompressedCoverFile() {
    return compressedCoverFile;
}

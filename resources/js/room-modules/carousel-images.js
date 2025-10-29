/**
 * carouselImages.js - Carousel Images Upload Handler
 *
 * UPDATED VALIDATION FLOW (MATCHES RoomController.php):
 * 1. Check total count (existing + new ≤ 15)
 * 2. Pre-validate files (type check only)
 * 3. Compress each file (2000px, 85% quality)
 * 4. Post-validate compressed files (5MB, 3000px)
 * 5. Add valid files to selectedFiles array
 * 6. Skip invalid files (don't block entire batch)
 */

import {
    compressImageCanvas,
    validateImageFile,
    showTemporaryMessage,
} from "./utils";

const MAX_CAROUSEL_FILES = 15;
// UPDATED: Reduced to 5MB to match controller validation
const MAX_IMAGE_SIZE_MB = 5;

let selectedFiles = []; // NEW files (File objects)
let existingImageData = []; // { id, src, filename } from DOM (edit)
let removedExistingImageIds = []; // IDs user marked to remove
let compressedCarouselFiles = new Map(); // map originalName -> compressed File

export function initializeCarouselImages() {
    const carouselInput = document.getElementById("carousel_images");
    const carouselUploadBox = document.getElementById("carouselUploadBox");
    const carouselPreviewContainer = document.getElementById(
        "carouselPreviewContainer"
    );
    if (!carouselInput || !carouselUploadBox || !carouselPreviewContainer)
        return;

    // Load existing from DOM if any
    loadExistingCarouselImagesFromDOM();

    carouselUploadBox.addEventListener("click", function (e) {
        const clickedPreviewItem = e.target.closest("[data-carousel-index]");
        const clickedRemoveBtn = e.target.closest(".remove-carousel-btn");
        if (clickedPreviewItem || clickedRemoveBtn) return;
        carouselInput.click();
    });

    carouselInput.addEventListener("change", () => {
        handleCarouselFiles(Array.from(carouselInput.files || []));
    });

    ["dragover", "dragleave", "drop"].forEach((eventName) =>
        carouselUploadBox.addEventListener(eventName, handleCarouselDrag)
    );

    carouselPreviewContainer.addEventListener(
        "click",
        handlePreviewClick,
        true
    );

    // Also wire up any existing checkboxes in the existing container (edit)
    document
        .querySelectorAll('input[name="remove_carousel_images[]"]')
        .forEach((cb) => {
            cb.addEventListener("change", function () {
                const label = this.closest("label");
                if (label) {
                    if (this.checked)
                        label.classList.add("bg-red-100", "border-red-500");
                    else label.classList.remove("bg-red-100", "border-red-500");
                }
            });
        });
}

function loadExistingCarouselImagesFromDOM() {
    const existingImages = document.querySelectorAll(
        "[data-existing-carousel-id]"
    );
    if (!existingImages || existingImages.length === 0) return;
    existingImageData = Array.from(existingImages).map((img) => ({
        id: img.dataset.existingCarouselId,
        src: img.src,
        filename: img.dataset.filename || img.getAttribute("alt") || "image",
    }));

    // hide the server-rendered container (we render a combined UI)
    const existingContainer = document.querySelector(
        "[data-existing-carousel-container]"
    );
    if (existingContainer) existingContainer.style.display = "none";

    renderCarouselPreviews();
    updateCarouselPlaceholderVisibility();
}

export function loadExistingCarouselImages(dataArray) {
    // Optional programmatic loader (if you pass data from server as JSON)
    if (!Array.isArray(dataArray) || !dataArray.length) return;
    existingImageData = dataArray.map((d) => ({
        id: d.id,
        src: d.src,
        filename: d.filename,
    }));
    renderCarouselPreviews();
    updateCarouselPlaceholderVisibility();
}

function handleCarouselDrag(e) {
    const carouselUploadBox = document.getElementById("carouselUploadBox");
    if (!carouselUploadBox) return;
    e.preventDefault();
    if (e.type === "dragover")
        carouselUploadBox.classList.add("border-primary", "bg-gray-50");
    else if (e.type === "dragleave")
        carouselUploadBox.classList.remove("border-primary", "bg-gray-50");
    else if (e.type === "drop") {
        carouselUploadBox.classList.remove("border-primary", "bg-gray-50");
        const files = Array.from(e.dataTransfer.files || []);
        handleCarouselFiles(files);
    }
}

function handlePreviewClick(e) {
    const removeBtn = e.target.closest(".remove-carousel-btn");
    if (!removeBtn) return;
    e.stopPropagation();
    e.preventDefault();

    const carouselItem = removeBtn.closest("[data-carousel-index]");
    if (!carouselItem) return;

    const type = carouselItem.dataset.carouselType;
    const index = parseInt(carouselItem.dataset.carouselIndex, 10);

    if (type === "existing") {
        const removedImg = existingImageData[index];
        if (removedImg) {
            removedExistingImageIds.push(removedImg.id);
            existingImageData.splice(index, 1);
            // Also mark the checkbox in old server-rendered area if present
            const checkbox = document.querySelector(
                `input[name="remove_carousel_images[]"][value="${removedImg.id}"]`
            );
            if (checkbox) checkbox.checked = true;
            addRemovedImageHiddenInputs();
        }
    } else if (type === "new") {
        const removedFile = selectedFiles[index];
        if (removedFile) {
            compressedCarouselFiles.delete(removedFile.name);
            selectedFiles.splice(index, 1);
            updateCarouselInputFiles();
        }
    }
    renderCarouselPreviews();
}

function handleCarouselFiles(newFiles) {
    const carouselInput = document.getElementById("carousel_images");
    if (!carouselInput) return;
    carouselInput.value = "";

    const totalActiveExisting = existingImageData.length; // only images currently displayed
    const totalNewFiles = selectedFiles.length + newFiles.length;
    const totalImages = totalActiveExisting + totalNewFiles;

    if (totalImages > MAX_CAROUSEL_FILES) {
        const available =
            MAX_CAROUSEL_FILES - totalActiveExisting - selectedFiles.length;
        showTemporaryMessage(
            `You can only add ${available} more image(s). Maximum is ${MAX_CAROUSEL_FILES} total.`,
            "error"
        );
        return;
    }

    // Pre-validate file types (before compression)
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
    const invalidFiles = newFiles.filter(
        (file) => !allowedTypes.includes(file.type)
    );

    if (invalidFiles.length) {
        showTemporaryMessage(
            "Some files have invalid types. Only JPEG and PNG are allowed.",
            "error"
        );
        return;
    }

    compressCarouselImages(newFiles);
}

async function compressCarouselImages(newFiles) {
    try {
        const progressElement = createProgressMessage(`Compressing 0%`, "info");
        const compressedFiles = [];
        let totalOriginalSize = 0;
        let totalCompressedSize = 0;
        let failedCount = 0;

        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];
            totalOriginalSize += file.size;
            try {
                const compressed = await compressImageCanvas(file, 2000, 0.85);

                // Post-compression validation (5MB, 3000px limits)
                const isValid = await validateImageFile(
                    compressed,
                    MAX_IMAGE_SIZE_MB,
                    false
                );

                if (!isValid) {
                    failedCount++;
                    console.warn(
                        `Skipped ${file.name}: Validation failed after compression`
                    );
                    continue;
                }

                const finalFile = new File(
                    [compressed],
                    file.name.replace(/\.[^/.]+$/, ".jpg"),
                    { type: "image/jpeg", lastModified: Date.now() }
                );
                compressedFiles.push(finalFile);
                compressedCarouselFiles.set(file.name, finalFile);
                totalCompressedSize += finalFile.size;
            } catch (err) {
                failedCount++;
                console.error(`Failed to compress ${file.name}`, err);
            }

            const progress = Math.round(((i + 1) / newFiles.length) * 100);
            updateProgressMessage(progressElement, `Compressing ${progress}%`);
        }

        if (progressElement) {
            progressElement.style.opacity = "0";
            progressElement.style.transform = "translateX(100%)";
            setTimeout(() => progressElement.remove(), 300);
        }

        selectedFiles = selectedFiles.concat(compressedFiles);
        renderCarouselPreviews();
        updateCarouselInputFiles();

        const originalMB = (totalOriginalSize / 1024 / 1024).toFixed(2);
        const compressedMB = (totalCompressedSize / 1024 / 1024).toFixed(2);

        let message = `${compressedFiles.length} image(s) compressed: ${originalMB}MB → ${compressedMB}MB`;
        if (failedCount > 0) {
            message += `. ${failedCount} image(s) were too large and skipped.`;
        }

        setTimeout(
            () =>
                showTemporaryMessage(
                    message,
                    failedCount > 0 ? "warning" : "success"
                ),
            350
        );
    } catch (error) {
        console.error("Batch compression failed:", error);
        showTemporaryMessage("Some images could not be compressed", "error");
    }
}

function renderCarouselPreviews() {
    const container = document.getElementById("carouselPreviewContainer");
    if (!container) return;
    container.innerHTML = "";

    // Existing images first
    existingImageData.forEach((imgData, index) => {
        const div = document.createElement("div");
        div.className =
            "relative rounded overflow-hidden border shadow-sm group aspect-square";
        div.dataset.carouselType = "existing";
        div.dataset.carouselIndex = index;

        div.innerHTML = `
            <img src="${imgData.src}" class="w-full h-full object-cover">
            <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs p-1 truncate">${imgData.filename}</div>
            <div class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 rounded">Existing</div>
            <button type="button" class="remove-carousel-btn absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-lg hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100" title="Remove">×</button>
        `;
        container.appendChild(div);
    });

    // New files
    selectedFiles.forEach((file, index) => {
        const div = document.createElement("div");
        div.className =
            "relative rounded overflow-hidden border shadow-sm group aspect-square";
        div.dataset.carouselType = "new";
        div.dataset.carouselIndex = index;

        const reader = new FileReader();
        reader.onload = (e) => {
            div.innerHTML = `
                <img src="${
                    e.target.result
                }" class="w-full h-full object-cover">
                <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs p-1 truncate">${
                    file.name
                }</div>
                <div class="absolute top-1 left-1 bg-green-500 text-white text-xs px-1 rounded">${(
                    file.size /
                    1024 /
                    1024
                ).toFixed(2)}MB</div>
                <button type="button" class="remove-carousel-btn absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-lg hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100" title="Remove">×</button>
            `;
        };
        reader.readAsDataURL(file);
        container.appendChild(div);
    });

    updateCarouselPlaceholderVisibility();
}

function updateCarouselInputFiles() {
    const carouselInput = document.getElementById("carousel_images");
    if (!carouselInput) return;
    const dt = new DataTransfer();
    selectedFiles.forEach((file) => dt.items.add(file));
    carouselInput.files = dt.files;
}

function updateCarouselPlaceholderVisibility() {
    const carouselPlaceholder = document.getElementById("carouselPlaceholder");
    if (!carouselPlaceholder) return;
    const totalImages = existingImageData.length + selectedFiles.length;
    carouselPlaceholder.style.display = totalImages > 0 ? "none" : "";
}

function addRemovedImageHiddenInputs() {
    const form = document.getElementById("room-form");
    if (!form) return;
    document
        .querySelectorAll('input[data-dynamic-removal="true"]')
        .forEach((i) => i.remove());

    removedExistingImageIds.forEach((id) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "remove_images[]";
        input.value = id;
        input.dataset.dynamicRemoval = "true";
        form.appendChild(input);
    });
}

// Prepare before form submit (adds hidden removal inputs)
export function prepareCarouselForSubmit() {
    addRemovedImageHiddenInputs();
}

// Helpers for progress UI
function createProgressMessage(text, type = "info") {
    if (typeof window.showTemporaryMessage === "function") {
        window.showTemporaryMessage(text, type);
    }
    return document.getElementById("temp-message");
}
function updateProgressMessage(element, text) {
    if (element && element.querySelector("p"))
        element.querySelector("p").textContent = text;
}

// API helpers
export function getSelectedFiles() {
    return selectedFiles;
}
export function getRemovedExistingImageIds() {
    return removedExistingImageIds;
}
export function loadExistingImagesFromArray(arr) {
    loadExistingCarouselImages(arr);
}
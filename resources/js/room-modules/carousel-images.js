import {
    compressImageCanvas,
    validateImageFile,
    showTemporaryMessage,
} from "./utils";

const MAX_CAROUSEL_FILES = 50;
const MAX_IMAGE_SIZE_MB = 10;

let selectedFiles = [];
let compressedCarouselFiles = new Map();

export function initializeCarouselImages() {
    const carouselInput = document.getElementById("carousel_images");
    const carouselUploadBox = document.getElementById("carouselUploadBox");
    const carouselPreviewContainer = document.getElementById(
        "carouselPreviewContainer"
    );

    carouselUploadBox.addEventListener("click", function (e) {
        const clickedPreviewItem = e.target.closest("[data-carousel-index]");
        const clickedRemoveBtn = e.target.closest(".remove-carousel-btn");

        if (clickedPreviewItem || clickedRemoveBtn) return;
        carouselInput.click();
    });

    carouselInput.addEventListener("change", () => {
        handleCarouselFiles(Array.from(carouselInput.files || []));
    });

    ["dragover", "dragleave", "drop"].forEach((eventName) => {
        carouselUploadBox.addEventListener(eventName, handleCarouselDrag);
    });

    carouselPreviewContainer.addEventListener(
        "click",
        function (e) {
            const removeBtn = e.target.closest(".remove-carousel-btn");
            if (removeBtn) {
                e.stopPropagation();
                e.preventDefault();

                const carouselItem = removeBtn.closest("[data-carousel-index]");
                if (carouselItem) {
                    const index = parseInt(carouselItem.dataset.carouselIndex);
                    const removedFile = selectedFiles[index];

                    compressedCarouselFiles.delete(removedFile.name);
                    selectedFiles.splice(index, 1);
                    renderCarouselPreviews();
                    updateCarouselInputFiles();
                }
            }
        },
        true
    );
}

function handleCarouselDrag(e) {
    const carouselUploadBox = document.getElementById("carouselUploadBox");
    e.preventDefault();
    if (e.type === "dragover") {
        carouselUploadBox.classList.add("border-primary", "bg-gray-50");
    } else if (e.type === "dragleave") {
        carouselUploadBox.classList.remove("border-primary", "bg-gray-50");
    } else if (e.type === "drop") {
        carouselUploadBox.classList.remove("border-primary", "bg-gray-50");
        const files = Array.from(e.dataTransfer.files);
        handleCarouselFiles(files);
    }
}

function handleCarouselFiles(newFiles) {
    const carouselInput = document.getElementById("carousel_images");
    carouselInput.value = "";

    if (selectedFiles.length + newFiles.length > MAX_CAROUSEL_FILES) {
        showTemporaryMessage(
            `Maximum ${MAX_CAROUSEL_FILES} images allowed.`,
            "error"
        );
        return;
    }

    const invalidFiles = newFiles.filter(
        (file) => !validateImageFile(file, MAX_IMAGE_SIZE_MB, false)
    );
    if (invalidFiles.length > 0) {
        showTemporaryMessage("Some files are invalid or too large.", "error");
        return;
    }

    compressCarouselImages(newFiles);
}

async function compressCarouselImages(newFiles) {
    showTemporaryMessage(`Compressing ${newFiles.length} image(s)...`, "info");

    try {
        const batchSize = 3;
        const compressedFiles = [];
        let totalOriginalSize = 0;
        let totalCompressedSize = 0;

        for (let i = 0; i < newFiles.length; i += batchSize) {
            const batch = newFiles.slice(i, i + batchSize);

            const batchResults = await Promise.all(
                batch.map((file) => {
                    totalOriginalSize += file.size;
                    return compressImageCanvas(file, 2000, 0.85);
                })
            );

            batchResults.forEach((compressed, idx) => {
                const originalFile = batch[idx];
                const compressedFile = new File(
                    [compressed],
                    originalFile.name.replace(/\.[^/.]+$/, ".jpg"),
                    {
                        type: "image/jpeg",
                        lastModified: Date.now(),
                    }
                );

                totalCompressedSize += compressedFile.size;
                compressedFiles.push(compressedFile);
                compressedCarouselFiles.set(originalFile.name, compressedFile);
            });

            const progress = Math.round(
                ((i + batch.length) / newFiles.length) * 100
            );
            showTemporaryMessage(`Compressing: ${progress}%`, "info");
        }

        selectedFiles = selectedFiles.concat(compressedFiles);
        renderCarouselPreviews();
        updateCarouselInputFiles();

        const originalMB = (totalOriginalSize / 1024 / 1024).toFixed(2);
        const compressedMB = (totalCompressedSize / 1024 / 1024).toFixed(2);

        showTemporaryMessage(
            `${newFiles.length} image(s) compressed: ${originalMB}MB → ${compressedMB}MB`,
            "success"
        );
    } catch (error) {
        console.error("Batch compression failed:", error);
        showTemporaryMessage("Some images could not be compressed", "error");
    }
}

function renderCarouselPreviews() {
    const carouselPreviewContainer = document.getElementById(
        "carouselPreviewContainer"
    );
    carouselPreviewContainer.innerHTML = "";

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        const div = document.createElement("div");
        div.className =
            "relative rounded overflow-hidden border shadow-sm group aspect-square";
        div.dataset.carouselIndex = index;

        reader.onload = (e) => {
            div.innerHTML = `
        <img src="${e.target.result}" class="w-full h-full object-cover">
        <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs p-1 truncate">
          ${file.name}
        </div>
        <div class="absolute top-1 left-1 bg-black/60 text-white text-xs px-1 rounded">
          ${(file.size / 1024 / 1024).toFixed(2)}MB
        </div>
        <button type="button" 
          class="remove-carousel-btn absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full
          flex items-center justify-center text-lg hover:bg-red-600 transition-colors
          opacity-0 group-hover:opacity-100"
          title="Remove">×</button>
      `;
        };

        reader.readAsDataURL(file);
        carouselPreviewContainer.appendChild(div);
    });

    updateCarouselPlaceholderVisibility();
}

function updateCarouselInputFiles() {
    const carouselInput = document.getElementById("carousel_images");
    const dt = new DataTransfer();
    selectedFiles.forEach((file) => dt.items.add(file));
    carouselInput.files = dt.files;
}

function updateCarouselPlaceholderVisibility() {
    const carouselPlaceholder = document.getElementById("carouselPlaceholder");
    if (carouselPlaceholder) {
        carouselPlaceholder.style.display =
            selectedFiles.length > 0 ? "none" : "";
    }
}

export function getSelectedFiles() {
    return selectedFiles;
}
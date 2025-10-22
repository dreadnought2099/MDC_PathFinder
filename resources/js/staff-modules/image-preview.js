import { formatFileSize } from "./utils.js";

export function createPreview(
    photoInput,
    previewContainer,
    placeholder,
    file = null,
    originalSize = 0,
    compressedSize = 0,
    existingUrl = null
) {
    placeholder.classList.add("hidden");
    previewContainer.classList.remove("hidden");

    const displayPreview = (src, isCompressed = false) => {
        previewContainer.innerHTML = `
            <div class="relative w-full h-full">
                <img src="${src}" class="w-full h-full object-cover rounded" alt="Staff photo preview">
                <button type="button" id="removePhotoBtn"
                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors font-bold">×</button>
               <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs p-2 font-medium">
                    ${
                        isCompressed
                            ? `1 IMAGE(S) COMPRESSED: ${formatFileSize(originalSize)} → ${formatFileSize(compressedSize)}`
                            : "1 IMAGE LOADED"
                    }
                </div>
            </div>`;

        document
            .getElementById("removePhotoBtn")
            .addEventListener("click", (e) => {
                e.stopPropagation();
                photoInput.value = "";
                previewContainer.classList.add("hidden");
                previewContainer.innerHTML = "";
                placeholder.classList.remove("hidden");
            });
    };

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => displayPreview(e.target.result, true);
        reader.readAsDataURL(file);
    } else if (existingUrl) {
        displayPreview(existingUrl, false);
    }
}
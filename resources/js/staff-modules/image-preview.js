import { formatFileSize } from "./utils.js";

export function createPreview(
    photoInput,
    previewContainer,
    placeholder,
    file = null,
    existingUrl = null
) {
    placeholder.classList.add("hidden");
    previewContainer.classList.remove("hidden");

    const displayPreview = (src, sizeText = "1 IMAGE LOADED") => {
        previewContainer.innerHTML = `
            <div class="relative w-full h-full">
                <img src="${src}" class="w-full h-full object-cover rounded" alt="Staff photo preview">
                <button type="button" id="removePhotoBtn"
                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors font-bold">×</button>
                <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs p-2 font-medium">
                    ${sizeText}
                </div>
            </div>`;

        const removeBtn = document.getElementById("removePhotoBtn");
        if (removeBtn) {
            removeBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                photoInput.value = "";
                previewContainer.classList.add("hidden");
                previewContainer.innerHTML = "";
                placeholder.classList.remove("hidden");

                // mark image for deletion
                const deleteInput = document.getElementById("delete_photo");
                if (deleteInput) deleteInput.value = "1";
            });
        }
    };

    if (file) {
        const reader = new FileReader();
        reader.onload = () =>
            displayPreview(
                reader.result,
                `1 IMAGE(S) COMPRESSED: ${formatFileSize(file.size)}`
            );
        reader.readAsDataURL(file);
    } else if (existingUrl) {
        displayPreview(existingUrl); // just show "1 IMAGE LOADED"
    }
}

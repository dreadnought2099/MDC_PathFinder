import { formatFileSize } from "./utils.js";

/**
 * createPreview({ input, container, placeholder, file = null, existingUrl = null })
 * - Renders a single-image preview with remove button.
 * - When removed: clears input, hides preview, shows placeholder, sets #delete_photo = "1" (if exists).
 */
export function createPreview({
    input,
    container,
    placeholder,
    file = null,
    existingUrl = null,
}) {
    if (placeholder) placeholder.classList.add("hidden");
    if (container) container.classList.remove("hidden");

    const displayPreview = (src, sizeText = "") => {
        container.innerHTML = `
      <div class="relative w-full h-full">
        <img src="${src}" class="w-full h-full object-cover rounded" alt="Staff photo preview" />
        <button type="button" id="removePhotoBtn"
          class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors font-bold">×</button>
        ${
            sizeText
                ? `<div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs p-2 font-medium">${sizeText}</div>`
                : ""
        }
      </div>
    `;

        const removeBtn = container.querySelector("#removePhotoBtn");
        if (removeBtn) {
            removeBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                // Clear input value and files
                if (input) {
                    input.value = "";
                    try {
                        input.files = new DataTransfer().files;
                    } catch (err) {
                        // some older browsers may not allow setting files
                    }
                }
                // Hide/reset preview + show placeholder
                container.classList.add("hidden");
                container.innerHTML = "";
                if (placeholder) placeholder.classList.remove("hidden");

                // Mark for deletion (server-side)
                const deleteInput = document.getElementById("delete_photo");
                if (deleteInput) deleteInput.value = "1";
            });
        }
    };

    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            const sizeText = file.size
                ? `1 IMAGE(S) COMPRESSED: ${formatFileSize(file.size)}`
                : "1 IMAGE LOADED";
            displayPreview(reader.result, sizeText);
        };
        reader.readAsDataURL(file);
    } else if (existingUrl) {
        displayPreview(existingUrl, "1 IMAGE LOADED");
    }
}
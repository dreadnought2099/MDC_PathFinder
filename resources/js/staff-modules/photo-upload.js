import { compressImageCanvas, validateImageFile } from "./image-compression.js";
import { formatFileSize } from "./utils.js";
import { createPreview } from "./image-preview.js";

export function setupPhotoUpload({
    photoInput,
    uploadBox,
    placeholder,
    previewContainer,
    onCompressStart,
    onCompressEnd,
    onError,
}) {
    uploadBox.addEventListener("click", () => photoInput.click());

    photoInput.addEventListener("change", async () => {
        const file = photoInput.files[0];
        if (!file) return;

        if (!validateImageFile(file)) return;

        try {
            onCompressStart?.();
            const compressed = await compressImageCanvas(file);
            createPreview(
                photoInput,
                previewContainer,
                placeholder,
                compressed,
                file.size,
                compressed.size
            );
            onCompressEnd?.(
                formatFileSize(file.size),
                formatFileSize(compressed.size)
            );
        } catch (err) {
            console.error(err);
            onError?.("Image compression failed.");
        }
    });
}
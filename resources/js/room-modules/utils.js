export function showTemporaryMessage(text, type = "info") {
    if (typeof window.showTemporaryMessage === "function") {
        window.showTemporaryMessage(text, type);
    } else {
        console.log(`[${type}] ${text}`);
    }
}

export function showTemporaryFeedback(button, text, duration = 2000) {
    const old = button.textContent;
    button.textContent = text;
    setTimeout(() => (button.textContent = old), duration);
}

export function showError(row, msg) {
    if (!row) return;
    row.classList.add("bg-red-50", "border", "border-red-400");
    if (!row.querySelector(".error-msg")) {
        const p = document.createElement("p");
        p.className = "error-msg text-red-600 text-xs mt-1";
        p.textContent = msg;
        row.appendChild(p);
    }
}

export function clearError(row) {
    if (!row) return;
    row.classList.remove("bg-red-50", "border", "border-red-400");
    const msg = row.querySelector(".error-msg");
    if (msg) msg.remove();
}

export async function compressImageCanvas(
    file,
    maxDimension = 2000,
    quality = 0.85
) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement("canvas");
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext("2d");
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = "high";
                ctx.drawImage(img, 0, 0, width, height);

                let targetQuality = quality;
                const sizeMB = file.size / 1024 / 1024;
                if (sizeMB > 8) targetQuality = 0.75;
                else if (sizeMB > 5) targetQuality = 0.8;

                canvas.toBlob(
                    (blob) => {
                        if (blob && blob.size < file.size) {
                            const originalName = file.name.replace(
                                /\.[^/.]+$/,
                                ""
                            );
                            const compressedFile = new File(
                                [blob],
                                `${originalName}.jpg`,
                                {
                                    type: "image/jpeg",
                                    lastModified: Date.now(),
                                }
                            );
                            resolve(compressedFile);
                        } else {
                            resolve(file);
                        }
                    },
                    "image/jpeg",
                    targetQuality
                );
            };
            img.onerror = () => resolve(file);
            img.src = e.target.result;
        };
        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
    });
}

export function validateImageFile(file, maxSizeMB = 10, showError = true) {
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
    if (!allowedTypes.includes(file.type)) {
        if (showError) showTemporaryMessage("Invalid image type.", "error");
        return false;
    }
    if (file.size > maxSizeMB * 1024 * 1024) {
        if (showError) showTemporaryMessage("Image too large.", "error");
        return false;
    }
    return true;
}
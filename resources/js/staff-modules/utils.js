export function setupEmailValidation(emailInput, existingEmail = null) {
    if (!emailInput) return;

    const emailError = document.getElementById("email_error");
    const submitBtn = document.getElementById("submitBtn");
    let debounceTimer;

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    emailInput.addEventListener("input", function () {
        const email = emailInput.value.trim();

        // Reset state
        emailError.classList.add("invisible");
        submitBtn.disabled = true;
        submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        submitBtn.classList.remove("cursor-pointer");

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            if (!email) {
                // Empty email: enable submit (nullable)
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                submitBtn.classList.add("cursor-pointer");
                return;
            }

            if (!isValidEmail(email)) {
                emailError.textContent = "Invalid email format";
                emailError.classList.remove("invisible");
                return;
            }

            try {
                const url = emailInput.dataset.checkEmailUrl;
                const params = new URLSearchParams();
                params.append("email", email);
                if (existingEmail) params.append("ignore_id", existingEmail);

                const response = await fetch(`${url}?${params.toString()}`);
                const data = await response.json();

                if (data.exists) {
                    emailError.textContent = "Email already exists";
                    emailError.classList.remove("invisible");
                    submitBtn.disabled = true;
                    submitBtn.classList.add("opacity-50", "cursor-not-allowed");
                    submitBtn.classList.remove("cursor-pointer");
                } else {
                    emailError.classList.add("invisible");
                    submitBtn.disabled = false;
                    submitBtn.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );
                    submitBtn.classList.add("cursor-pointer");
                }
            } catch (err) {
                console.error("Error checking email:", err);
                showTemporaryMessage(
                    "Could not validate email. Try again later.",
                    "error"
                );
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                submitBtn.classList.add("cursor-pointer");
            }
        }, 500);
    });
}

// Convert bytes to KB or MB string
export function formatFileSize(bytes) {
    const mb = bytes / (1024 * 1024);
    if (mb >= 1) {
        return mb.toFixed(2) + " MB";
    } else {
        const kb = bytes / 1024;
        return kb.toFixed(1) + " KB"; // you can use toFixed(1) if you want decimals
    }
}

// Optional: use global temporary message if you want
export function showTemporaryMessage(message, type = "info") {
    window.showTemporaryMessage(message, type);
}

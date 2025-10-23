/**
 * setupEmailValidation(emailInput, existingEmail = null)
 * Live email validation (frontend + backend)
 * - Shows "Invalid format" immediately
 * - Checks backend for duplicates after debounce
 * - Disables Save/Submit button when invalid
 */
export function setupEmailValidation(emailInput, existingEmail = null) {
    if (!emailInput) return;

    const checkUrl = emailInput.dataset.checkEmailUrl;
    const emailError = document.getElementById("email_error");
    const submitBtn = document.getElementById("submitBtn");
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || null;

    let debounceTimer;

    /** Basic regex for valid email structure */
    const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    /** UI helpers */
    const enableSubmit = () => {
        if (!submitBtn) return;
        submitBtn.disabled = false;
        submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        submitBtn.classList.add("cursor-pointer");
    };

    const disableSubmit = () => {
        if (!submitBtn) return;
        submitBtn.disabled = true;
        submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        submitBtn.classList.remove("cursor-pointer");
    };

    /** --- MAIN LISTENER --- */
    emailInput.addEventListener("input", () => {
        const email = emailInput.value.trim();

        clearTimeout(debounceTimer);

        // Reset state
        if (emailError) {
            emailError.textContent = "The email has already been taken.";
            emailError.classList.add("invisible");
        }

        // If empty → allow submission (nullable)
        if (!email) {
            enableSubmit();
            return;
        }

        // Show "Invalid format" instantly if malformed
        if (!isValidEmail(email)) {
            if (emailError) {
                emailError.textContent = "Invalid email format";
                emailError.classList.remove("invisible");
            }
            disableSubmit();
            return;
        }

        // Valid format → temporarily enable (prevents button lag)
        enableSubmit();

        // Skip backend check if same as existing (edit form)
        if (
            existingEmail &&
            email.toLowerCase() === existingEmail.toLowerCase()
        ) {
            emailError.classList.add("invisible");
            return;
        }

        // Debounce backend check (500ms after typing stops)
        debounceTimer = setTimeout(async () => {
            try {
                disableSubmit(); // while checking backend

                const response = await fetch(
                    `${checkUrl}?email=${encodeURIComponent(email)}`,
                    {
                        headers: csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {},
                        credentials: "same-origin",
                    }
                );

                if (!response.ok) throw new Error("Network error");
                const data = await response.json();

                if (data.exists) {
                    // Email already taken
                    if (emailError) {
                        emailError.textContent =
                            "The email has already been taken.";
                        emailError.classList.remove("invisible");
                    }
                    disableSubmit();
                } else {
                    // Email available
                    if (emailError) emailError.classList.add("invisible");
                    enableSubmit();
                }
            } catch (err) {
                console.error("Email validation error:", err);
                window.showTemporaryMessage?.(
                    "Could not validate email. Try again later.",
                    "warning"
                );
                enableSubmit();
            }
        }, 500);
    });
}
export function setupEmailValidation(emailInput, existingEmail = null) {
    if (!emailInput) return;

    const checkUrl = emailInput.dataset.checkEmailUrl;
    const emailError = document.getElementById("email_error");
    const submitBtn = document.getElementById("submitBtn");
    let debounceTimer;
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || null;

    const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

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

    emailInput.addEventListener("input", () => {
        const email = emailInput.value.trim();

        clearTimeout(debounceTimer);
        disableSubmit();

        // Just hide error visually, don’t clear its text
        if (emailError) emailError.classList.add("invisible");

        debounceTimer = setTimeout(async () => {
            if (!email) {
                enableSubmit();
                return;
            }

            if (!isValidEmail(email)) {
                if (emailError) {
                    emailError.textContent = "Invalid email format";
                    emailError.classList.remove("invisible");
                }
                disableSubmit();
                return;
            }

            if (
                existingEmail &&
                email.toLowerCase() === existingEmail.toLowerCase()
            ) {
                enableSubmit();
                return;
            }

            try {
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
                    // Use your Blade text instead of replacing it
                    if (emailError) emailError.classList.remove("invisible");
                    disableSubmit();
                } else {
                    if (emailError) emailError.classList.add("invisible");
                    enableSubmit();
                }
            } catch (err) {
                console.error("Error checking email:", err);
                if (window.showTemporaryMessage)
                    window.showTemporaryMessage(
                        "Could not validate email. Try again later.",
                        "warning"
                    );
                enableSubmit();
            }
        }, 500);
    });
}
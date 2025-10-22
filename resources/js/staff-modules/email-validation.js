export function setupEmailValidation(emailInput, existingEmail = null) {
    if (!emailInput) return;

    const checkUrl = emailInput.dataset.checkEmailUrl; // pass from Blade
    const emailError = document.getElementById("email_error");
    const submitBtn = document.getElementById("submitBtn");
    let debounceTimer;

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    emailInput.addEventListener("input", function () {
        const email = emailInput.value.trim();

        // Reset error and submit button
        emailError.classList.add("invisible");
        submitBtn.disabled = false;
        submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        submitBtn.classList.add("cursor-pointer");

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            if (!email) return; // allow empty input

            if (!isValidEmail(email)) {
                emailError.textContent = "Invalid email format";
                emailError.classList.remove("invisible");
                submitBtn.disabled = true;
                submitBtn.classList.add("opacity-50", "cursor-not-allowed");
                submitBtn.classList.remove("cursor-pointer");
                return;
            }

            // If the email is same as existing email (edit page), skip check
            if (
                existingEmail &&
                email.toLowerCase() === existingEmail.toLowerCase()
            ) {
                emailError.classList.add("invisible");
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                submitBtn.classList.add("cursor-pointer");
                return;
            }

            // Check email availability
            try {
                const response = await fetch(
                    `${checkUrl}?email=${encodeURIComponent(email)}`
                );
                if (!response.ok)
                    throw new Error("Network response was not OK");
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
                // Optional: allow submission if network error occurs
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                submitBtn.classList.add("cursor-pointer");
            }
        }, 500); // debounce 500ms
    });
}
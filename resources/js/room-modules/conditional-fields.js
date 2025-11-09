export function initializeConditionalFields() {
    const roomTypeSelect = document.getElementById("room_type");
    const conditionalFields = document.querySelectorAll(".conditional-field");
    if (!roomTypeSelect) return;

    function toggleConditionalFields() {
        const isEntrancePoint = roomTypeSelect.value === "entrance_point";
        conditionalFields.forEach((field) => {
            field.style.display = isEntrancePoint ? "none" : "";

            // Disable all inputs when hiding to prevent submission
            if (isEntrancePoint) {
                field
                    .querySelectorAll("input, select, textarea")
                    .forEach((input) => {
                        input.disabled = true;
                    });

                // Clear the data when switching TO entrance point
                window.dispatchEvent(new CustomEvent("clear-office-hours"));
                window.dispatchEvent(
                    new CustomEvent("clear-consultation-times")
                );
            } else {
                field
                    .querySelectorAll("input, select, textarea")
                    .forEach((input) => {
                        input.disabled = false;
                    });
            }
        });
    }

    toggleConditionalFields();
    roomTypeSelect.addEventListener("change", toggleConditionalFields);
}
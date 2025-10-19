export function initializeConditionalFields() {
    const roomTypeSelect = document.getElementById("room_type");
    const conditionalFields = document.querySelectorAll(".conditional-field");

    function toggleConditionalFields() {
        const isEntrancePoint = roomTypeSelect.value === "entrance_point";
        conditionalFields.forEach((field) => {
            field.style.display = isEntrancePoint ? "none" : "";
        });
    }

    toggleConditionalFields();
    roomTypeSelect.addEventListener("change", toggleConditionalFields);
}
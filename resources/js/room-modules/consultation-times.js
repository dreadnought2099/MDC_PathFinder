import {
    showTemporaryMessage,
    showTemporaryFeedback,
    showError,
    clearError,
} from "./utils";

const DAYS_OF_WEEK = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
let consultationTimesData = {};

export function initializeConsultationTimes() {
    setupQuickSelectButtons();
    setupBulkApplyButton();
    setupClearTimeButtons();

    window.addEventListener("clear-consultation-times", () => {
        consultationTimesData = {};
        renderConsultationTimes();
    });

    // FIX: Load existing consultation times if available
    if (
        typeof window.existingConsultationTimes !== "undefined" &&
        window.existingConsultationTimes &&
        Object.keys(window.existingConsultationTimes).length > 0
    ) {
        try {
            loadExistingConsultationTimes(window.existingConsultationTimes);
        } catch (err) {
            console.error("Failed to load existing consultation times:", err);
            renderConsultationTimes(); // Render empty if loading fails
        }
    } else {
        renderConsultationTimes(); // Render empty display
    }
}

// Quick select / clear buttons
function setupQuickSelectButtons() {
    document.querySelectorAll(".consultation-quick-select").forEach((btn) => {
        btn.addEventListener("click", () => {
            const days = btn.dataset.days.split(",");
            document
                .querySelectorAll(".consultation-day-checkbox")
                .forEach((cb) => (cb.checked = false));
            days.forEach((day) => {
                const cb = document.querySelector(
                    `.consultation-day-checkbox[value="${day}"]`
                );
                if (cb) cb.checked = true;
            });
        });
    });

    document
        .querySelector(".consultation-clear-select")
        ?.addEventListener("click", () => {
            document
                .querySelectorAll(".consultation-day-checkbox")
                .forEach((cb) => (cb.checked = false));
        });
}

// Bulk apply
function setupBulkApplyButton() {
    document
        .querySelector(".consultation-apply-bulk")
        ?.addEventListener("click", function () {
            const selectedDays = Array.from(
                document.querySelectorAll(".consultation-day-checkbox:checked")
            ).map((cb) => cb.value);
            if (!selectedDays.length)
                return showTemporaryMessage("Please select at least one day.");

            const ranges = collectBulkRanges();
            if (!ranges) return;

            clearExistingScheduleForEdit(selectedDays, ranges);

            selectedDays.forEach(
                (day) => (consultationTimesData[day] = ranges)
            );
            renderConsultationTimes();
            showTemporaryFeedback(this, "Applied Successfully!");
            showTemporaryMessage(
                "Consultation times updated for selected days!",
                "success"
            );
        });
}

// Clear individual time input
function setupClearTimeButtons() {
    document.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("consultation-clear-time") ||
            e.target.closest(".consultation-clear-time")
        ) {
            const button = e.target.classList.contains(
                "consultation-clear-time"
            )
                ? e.target
                : e.target.closest(".consultation-clear-time");
            const input = button.previousElementSibling;
            if (input && input.type === "time") input.value = "";
        }
    });
}

// Format time to 12-hour format
function formatTime12Hour(time24) {
    try {
        if (!time24 || !time24.match(/^\d{2}:\d{2}$/)) return time24;
        const timeObj = dayjs(`2000-01-01 ${time24}:00`);
        return timeObj.isValid() ? timeObj.format("h:mm A") : time24;
    } catch (error) {
        return time24;
    }
}

// Validate time range
function validateTimeRange(startTime, endTime) {
    try {
        const start = dayjs(`2000-01-01 ${startTime}:00`);
        const end = dayjs(`2000-01-01 ${endTime}:00`);
        if (!start.isValid() || !end.isValid())
            return { valid: false, error: "Invalid time format" };
        if (start.isAfter(end) || start.isSame(end))
            return { valid: false, error: "End time must be after start time" };
        return { valid: true };
    } catch {
        return { valid: false, error: "Time validation failed" };
    }
}

// Check for overlapping ranges
function hasOverlapDayJs(ranges) {
    const sortedRanges = ranges
        .map((range) => ({
            start: dayjs(`2000-01-01 ${range.start}:00`),
            end: dayjs(`2000-01-01 ${range.end}:00`),
            original: range,
        }))
        .sort((a, b) => (a.start.isBefore(b.start) ? -1 : 1));
    for (let i = 0; i < sortedRanges.length - 1; i++) {
        const cur = sortedRanges[i],
            next = sortedRanges[i + 1];
        if (cur.end.isAfter(next.start))
            return {
                hasOverlap: true,
                conflictingRanges: [cur.original, next.original],
            };
    }
    return { hasOverlap: false };
}

// Format duration
function formatDuration(startTime, endTime) {
    const start = dayjs(`2000-01-01 ${startTime}:00`);
    const end = dayjs(`2000-01-01 ${endTime}:00`);
    const diffMinutes = end.diff(start, "minute");
    const hours = Math.floor(diffMinutes / 60);
    const minutes = diffMinutes % 60;
    if (hours === 0) return `${minutes}m`;
    if (minutes === 0) return `${hours}h`;
    return `${hours}h ${minutes}m`;
}

// Collect bulk ranges
function collectBulkRanges() {
    const ranges = [];
    let valid = true;
    document.querySelectorAll(".consultation-bulk-range-row").forEach((row) => {
        const start = row.querySelector(".consultation-bulk-start-time")?.value;
        const end = row.querySelector(".consultation-bulk-end-time")?.value;
        clearError(row);
        if (start && end) {
            const validation = validateTimeRange(start, end);
            if (!validation.valid) {
                showError(row, validation.error);
                valid = false;
                return;
            }
            ranges.push({ start, end });
        }
    });
    if (!valid) return null;
    if (!ranges.length) {
        showTemporaryMessage("Please enter at least one valid time range.");
        return null;
    }

    // Check overlap
    const overlapCheck = hasOverlapDayJs(ranges);
    if (overlapCheck.hasOverlap) {
        showTemporaryMessage("Time ranges overlap. Fix them first.");
        return null;
    }
    return ranges;
}

// Clear existing schedule for edit
function clearExistingScheduleForEdit(selectedDays, newRanges) {
    const newRangeKey = newRanges.map((r) => `${r.start}-${r.end}`).join(",");
    const existingGroups = {};
    DAYS_OF_WEEK.forEach((day) => {
        const ranges = consultationTimesData[day] || [];
        const rangeKey = ranges.length
            ? ranges.map((r) => `${r.start}-${r.end}`).join(",")
            : "closed";
        if (!existingGroups[rangeKey]) existingGroups[rangeKey] = [];
        existingGroups[rangeKey].push(day);
    });

    Object.entries(existingGroups).forEach(([rangeKey, groupDays]) => {
        if (rangeKey !== "closed") {
            const hasOverlap = groupDays.some((d) => selectedDays.includes(d));
            if (hasOverlap)
                groupDays.forEach((d) => {
                    if (!selectedDays.includes(d))
                        delete consultationTimesData[d];
                });
        }
    });
}

// Format days group
function formatDaysGroup(days) {
    if (!days || !days.length) return "";
    if (days.length === 1) return days[0];
    const sortedDays = days.sort(
        (a, b) => DAYS_OF_WEEK.indexOf(a) - DAYS_OF_WEEK.indexOf(b)
    );
    const isExactMatch = (pattern) =>
        sortedDays.length === pattern.length &&
        sortedDays.every((d, i) => d === pattern[i]);
    if (isExactMatch(["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]))
        return "Daily";
    if (isExactMatch(["Mon", "Tue", "Wed", "Thu", "Fri"])) return "Weekdays";
    if (isExactMatch(["Sat", "Sun"])) return "Weekends";

    const isConsecutive = () => {
        for (let i = 0; i < sortedDays.length - 1; i++) {
            if (
                DAYS_OF_WEEK.indexOf(sortedDays[i + 1]) !==
                DAYS_OF_WEEK.indexOf(sortedDays[i]) + 1
            )
                return false;
        }
        return true;
    };

    if (isConsecutive() && sortedDays.length > 2)
        return `${sortedDays[0]} - ${sortedDays[sortedDays.length - 1]}`;
    return sortedDays.join(", ");
}

// Load existing consultation times
export function loadExistingConsultationTimes(data) {
    if (!data) return;

    consultationTimesData = {}; // reset before loading

    // Handle object format (from controller)
    if (typeof data === "object" && !Array.isArray(data)) {
        // Data is already in the correct format: { Mon: [{start, end}], Tue: [...] }
        Object.keys(data).forEach((day) => {
            if (Array.isArray(data[day]) && data[day].length > 0) {
                consultationTimesData[day] = data[day].map((range) => ({
                    start: range.start,
                    end: range.end,
                }));
            }
        });
    }
    // Handle array format (legacy support)
    else if (Array.isArray(data)) {
        data.forEach((hour) => {
            const dayKey = hour.day || hour.day_of_week;
            if (!dayKey) return;
            if (!consultationTimesData[dayKey])
                consultationTimesData[dayKey] = [];
            const start = hour.start_time
                ? hour.start_time.substring(0, 5)
                : hour.start;
            const end = hour.end_time
                ? hour.end_time.substring(0, 5)
                : hour.end;
            consultationTimesData[dayKey].push({ start, end });
        });
    }
    renderConsultationTimes();
}

// Render consultation times display
function renderConsultationTimes() {
    const container = document.getElementById("consultationTimesDisplay");
    if (!container) return;
    container.innerHTML = "";

    const groupedSchedule = {};
    DAYS_OF_WEEK.forEach((day) => {
        const ranges = consultationTimesData[day] || [];
        const rangeKey = ranges.length
            ? ranges.map((r) => `${r.start}-${r.end}`).join(",")
            : "closed";
        if (!groupedSchedule[rangeKey])
            groupedSchedule[rangeKey] = { days: [], ranges: ranges };
        groupedSchedule[rangeKey].days.push(day);
    });

    Object.entries(groupedSchedule).forEach(([rangeKey, group]) => {
        const li = document.createElement("li");
        li.className =
            "mb-3 p-3 bg-white rounded border relative dark:bg-gray-800 border border-primary";
        const daysText = formatDaysGroup(group.days);
        let timeText =
            rangeKey === "closed"
                ? "Closed"
                : group.ranges
                      .map(
                          (r) =>
                              `${formatTime12Hour(
                                  r.start
                              )} - ${formatTime12Hour(
                                  r.end
                              )} <span class="text-gray-500 text-xs">(${formatDuration(
                                  r.start,
                                  r.end
                              )})</span>`
                      )
                      .join(", ");
        li.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium text-gray-800 dark:text-gray-300">${daysText}</div>
                    <div class="text-sm text-gray-600 mt-1 dark:text-gray-300">${timeText}</div>
                </div>
                ${
                    rangeKey !== "closed"
                        ? `
                    <div class="flex flex-col sm:flex-row gap-2 ml-0 sm:ml-4 mt-2 sm:mt-0">
                        <button type="button" class="edit-consultation-btn bg-edit text-white text-sm px-3 py-1.5 rounded-md border border-edit shadow-edit-hover"
                            data-days='${JSON.stringify(group.days)}'
                            data-ranges='${JSON.stringify(
                                group.ranges
                            )}'>Edit</button>
                        <button type="button" class="delete-consultation-btn bg-secondary text-white text-sm px-3 py-1.5 rounded-md border border-secondary shadow-secondary-hover"
                            data-days='${JSON.stringify(
                                group.days
                            )}'>Delete</button>
                    </div>`
                        : ""
                }
            </div>
        `;

        // Hidden inputs
        group.days.forEach((day) => {
            group.ranges.forEach((range, idx) => {
                const startInput = document.createElement("input");
                startInput.type = "hidden";
                startInput.name = `consultation_times[${day}][${idx}][start]`;
                startInput.value = range.start;

                const endInput = document.createElement("input");
                endInput.type = "hidden";
                endInput.name = `consultation_times[${day}][${idx}][end]`;
                endInput.value = range.end;

                li.appendChild(startInput);
                li.appendChild(endInput);
            });
        });

        container.appendChild(li);
    });

    attachScheduleActionListeners();
}

function attachScheduleActionListeners() {
    document.querySelectorAll(".delete-consultation-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const days = JSON.parse(this.dataset.days);
            const daysText = formatDaysGroup(days);
            if (
                confirm(
                    `Are you sure you want to remove consultation times for ${daysText}?`
                )
            ) {
                days.forEach((day) => delete consultationTimesData[day]);
                renderConsultationTimes();
                showTemporaryMessage(
                    "Consultation times deleted successfully!",
                    "success"
                );
            }
        });
    });

    document.querySelectorAll(".edit-consultation-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const days = JSON.parse(this.dataset.days);
            const ranges = JSON.parse(this.dataset.ranges);

            // Populate all time rows if multiple ranges exist
            document
                .querySelectorAll(".consultation-bulk-range-row")
                .forEach((row, index) => {
                    const startInput = row.querySelector(
                        ".consultation-bulk-start-time"
                    );
                    const endInput = row.querySelector(
                        ".consultation-bulk-end-time"
                    );
                    if (ranges[index]) {
                        startInput.value = ranges[index].start;
                        endInput.value = ranges[index].end;
                    } else {
                        startInput.value = "";
                        endInput.value = "";
                    }
                });

            document
                .querySelectorAll(".consultation-day-checkbox")
                .forEach((cb) => (cb.checked = days.includes(cb.value)));

            document
                .querySelector(".bulk-time-ranges")
                ?.scrollIntoView({ behavior: "smooth", block: "center" });
            const bulkSection = document.querySelector(
                ".mb-6.p-4.border.border-primary.rounded"
            );
            if (bulkSection) {
                bulkSection.classList.add("ring-2", "ring-blue-400");
                setTimeout(
                    () =>
                        bulkSection.classList.remove("ring-2", "ring-blue-400"),
                    2000
                );
            }
            showTemporaryMessage(
                "Schedule loaded for editing. Modify time and click 'Apply'.",
                "info"
            );
        });
    });
}

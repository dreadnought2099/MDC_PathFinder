import {
    showTemporaryMessage,
    showTemporaryFeedback,
    showError,
    clearError,
} from "./utils";

const DAYS_OF_WEEK = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
let officeHoursData = {};

export function initializeOfficeHours() {
    setupQuickSelectButtons();
    setupBulkApplyButton();
    setupClearTimeButtons();
    renderOfficeHours();
}

function setupQuickSelectButtons() {
    document.querySelectorAll(".quick-select").forEach((btn) => {
        btn.addEventListener("click", () => {
            const days = btn.dataset.days.split(",");
            document
                .querySelectorAll(".bulk-day-checkbox")
                .forEach((cb) => (cb.checked = false));
            days.forEach((day) => {
                const cb = document.querySelector(
                    `.bulk-day-checkbox[value="${day}"]`
                );
                if (cb) cb.checked = true;
            });
        });
    });

    document.querySelector(".clear-select")?.addEventListener("click", () => {
        document
            .querySelectorAll(".bulk-day-checkbox")
            .forEach((cb) => (cb.checked = false));
    });
}

function setupBulkApplyButton() {
    document
        .querySelector(".apply-bulk")
        ?.addEventListener("click", function () {
            const selectedDays = Array.from(
                document.querySelectorAll(".bulk-day-checkbox:checked")
            ).map((cb) => cb.value);

            if (!selectedDays.length) {
                showTemporaryMessage("Please select at least one day.");
                return;
            }

            const ranges = collectBulkRanges();
            if (!ranges) return;

            clearExistingScheduleForEdit(selectedDays, ranges);

            selectedDays.forEach((day) => {
                officeHoursData[day] = ranges;
            });

            renderOfficeHours();
            showTemporaryFeedback(this, "Applied Successfully!");
            showTemporaryMessage(
                "Office hours updated for selected days!",
                "success"
            );
        });
}

function setupClearTimeButtons() {
    document.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("clear-time") ||
            e.target.closest(".clear-time")
        ) {
            const button = e.target.classList.contains("clear-time")
                ? e.target
                : e.target.closest(".clear-time");
            const input = button.previousElementSibling;
            if (input && input.type === "time") {
                input.value = "";
            }
        }
    });
}

function formatTime12Hour(time24) {
    try {
        if (!time24 || !time24.match(/^\d{2}:\d{2}$/)) {
            return time24;
        }
        const timeObj = dayjs(`2000-01-01 ${time24}:00`);
        return timeObj.isValid() ? timeObj.format("h:mm A") : time24;
    } catch (error) {
        console.warn("Time formatting error:", error);
        return time24;
    }
}

function validateTimeRange(startTime, endTime) {
    try {
        const start = dayjs(`2000-01-01 ${startTime}:00`);
        const end = dayjs(`2000-01-01 ${endTime}:00`);

        if (!start.isValid() || !end.isValid()) {
            return { valid: false, error: "Invalid time format" };
        }

        if (start.isAfter(end) || start.isSame(end)) {
            return { valid: false, error: "End time must be after start time" };
        }

        return { valid: true };
    } catch (error) {
        return { valid: false, error: "Time validation failed" };
    }
}

function hasOverlapDayJs(ranges) {
    const sortedRanges = ranges
        .map((range) => ({
            start: dayjs(`2000-01-01 ${range.start}:00`),
            end: dayjs(`2000-01-01 ${range.end}:00`),
            original: range,
        }))
        .sort((a, b) => (a.start.isBefore(b.start) ? -1 : 1));

    for (let i = 0; i < sortedRanges.length - 1; i++) {
        const current = sortedRanges[i];
        const next = sortedRanges[i + 1];

        if (current.end.isAfter(next.start)) {
            return {
                hasOverlap: true,
                conflictingRanges: [current.original, next.original],
            };
        }
    }

    return { hasOverlap: false };
}

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

function collectBulkRanges() {
    const ranges = [];
    let valid = true;

    document.querySelectorAll(".bulk-range-row").forEach((row) => {
        const start = row.querySelector(".bulk-start-time").value;
        const end = row.querySelector(".bulk-end-time").value;
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

    const overlapCheck = hasOverlapDayJs(ranges);
    if (overlapCheck.hasOverlap) {
        showTemporaryMessage("Time ranges overlap. Fix them first.");
        return null;
    }

    return ranges;
}

function clearExistingScheduleForEdit(selectedDays, newRanges) {
    const newRangeKey = newRanges.map((r) => `${r.start}-${r.end}`).join(",");
    const existingGroups = {};

    DAYS_OF_WEEK.forEach((day) => {
        const ranges = officeHoursData[day] || [];
        const rangeKey = ranges.length
            ? ranges.map((r) => `${r.start}-${r.end}`).join(",")
            : "closed";

        if (!existingGroups[rangeKey]) {
            existingGroups[rangeKey] = [];
        }
        existingGroups[rangeKey].push(day);
    });

    Object.entries(existingGroups).forEach(([rangeKey, groupDays]) => {
        if (rangeKey !== "closed") {
            const hasOverlap = groupDays.some((day) =>
                selectedDays.includes(day)
            );

            if (hasOverlap) {
                groupDays.forEach((day) => {
                    if (!selectedDays.includes(day)) {
                        delete officeHoursData[day];
                    }
                });
            }
        }
    });
}

function formatDaysGroup(days) {
    if (days.length === 0) return "";
    if (days.length === 1) return days[0];

    const sortedDays = days.sort(
        (a, b) => DAYS_OF_WEEK.indexOf(a) - DAYS_OF_WEEK.indexOf(b)
    );

    const isExactMatch = (pattern) => {
        return (
            sortedDays.length === pattern.length &&
            sortedDays.every((day, index) => day === pattern[index])
        );
    };

    if (isExactMatch(["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"])) {
        return "Daily";
    }

    if (isExactMatch(["Mon", "Tue", "Wed", "Thu", "Fri"])) {
        return "Weekdays";
    }

    if (isExactMatch(["Sat", "Sun"])) {
        return "Weekends";
    }

    const isConsecutive = () => {
        for (let i = 0; i < sortedDays.length - 1; i++) {
            const currentIndex = DAYS_OF_WEEK.indexOf(sortedDays[i]);
            const nextIndex = DAYS_OF_WEEK.indexOf(sortedDays[i + 1]);
            if (nextIndex !== currentIndex + 1) return false;
        }
        return true;
    };

    if (isConsecutive() && sortedDays.length > 2) {
        return `${sortedDays[0]} - ${sortedDays[sortedDays.length - 1]}`;
    }

    return sortedDays.join(", ");
}

function renderOfficeHours() {
    const container = document.getElementById("officeHoursDisplay");
    container.innerHTML = "";

    const groupedSchedule = {};

    DAYS_OF_WEEK.forEach((day) => {
        const ranges = officeHoursData[day] || [];
        const rangeKey = ranges.length
            ? ranges.map((r) => `${r.start}-${r.end}`).join(",")
            : "closed";

        if (!groupedSchedule[rangeKey]) {
            groupedSchedule[rangeKey] = {
                days: [],
                ranges: ranges,
            };
        }
        groupedSchedule[rangeKey].days.push(day);
    });

    Object.entries(groupedSchedule).forEach(([rangeKey, group]) => {
        const li = document.createElement("li");
        li.className =
            "mb-3 p-3 bg-white rounded border relative dark:bg-gray-800 border border-primary";

        const daysText = formatDaysGroup(group.days);
        let timeText;

        if (rangeKey === "closed") {
            timeText = "Closed";
        } else {
            timeText = group.ranges
                .map((r) => {
                    const timeRange = `${formatTime12Hour(
                        r.start
                    )} - ${formatTime12Hour(r.end)}`;
                    const duration = formatDuration(r.start, r.end);
                    return `${timeRange} <span class="text-gray-500 text-xs">(${duration})</span>`;
                })
                .join(", ");
        }

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
            <button type="button" 
              class="edit-schedule-btn bg-edit text-white hover:text-edit hover:bg-white text-sm px-3 py-1.5 rounded-md border border-edit transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 w-full sm:w-auto shadow-edit-hover" 
              data-days='${JSON.stringify(group.days)}' 
              data-ranges='${JSON.stringify(group.ranges)}'>
              Edit
            </button>
            <button type="button" 
              class="delete-schedule-btn bg-secondary text-white hover:text-secondary hover:bg-white text-sm px-3 py-1.5 rounded-md border border-secondary transition-all duration-300 ease-in-out cursor-pointer dark:hover:bg-gray-800 w-full sm:w-auto shadow-secondary-hover" 
              data-days='${JSON.stringify(group.days)}'>
              Delete
            </button>
          </div>
        `
                : ""
        }
      </div>
    `;

        group.days.forEach((day) => {
            group.ranges.forEach((range, idx) => {
                const startInput = document.createElement("input");
                startInput.type = "hidden";
                startInput.name = `office_hours[${day}][${idx}][start]`;
                startInput.value = range.start;

                const endInput = document.createElement("input");
                endInput.type = "hidden";
                endInput.name = `office_hours[${day}][${idx}][end]`;
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
    document.querySelectorAll(".delete-schedule-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const days = JSON.parse(this.dataset.days);
            const daysText = formatDaysGroup(days);

            if (
                confirm(
                    `Are you sure you want to remove office hours for ${daysText}?`
                )
            ) {
                days.forEach((day) => {
                    delete officeHoursData[day];
                });
                renderOfficeHours();
                showTemporaryMessage(
                    "Office hours deleted successfully!",
                    "success"
                );
            }
        });
    });

    document.querySelectorAll(".edit-schedule-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const days = JSON.parse(this.dataset.days);
            const ranges = JSON.parse(this.dataset.ranges);

            document.querySelectorAll(".bulk-day-checkbox").forEach((cb) => {
                cb.checked = days.includes(cb.value);
            });

            if (ranges.length > 0) {
                document.querySelector(".bulk-start-time").value =
                    ranges[0].start;
                document.querySelector(".bulk-end-time").value = ranges[0].end;
            }

            document.querySelector(".bulk-time-ranges").scrollIntoView({
                behavior: "smooth",
                block: "center",
            });

            const bulkSection = document.querySelector(
                ".mb-6.p-4.border.border-primary.rounded"
            );
            if (bulkSection) {
                bulkSection.classList.add("ring-2", "ring-blue-400");
                setTimeout(() => {
                    bulkSection.classList.remove("ring-2", "ring-blue-400");
                }, 2000);
            }

            showTemporaryMessage(
                "Schedule loaded for editing. Modify time and click 'Apply'.",
                "info"
            );
        });
    });
}
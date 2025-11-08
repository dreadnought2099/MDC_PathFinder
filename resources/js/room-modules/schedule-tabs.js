/**
 * Schedule Tabs Module
 * Handles tab switching between Office Hours and Consultation Times
 */

export function initializeScheduleTabs() {
    const tabs = document.querySelectorAll(".schedule-tab");
    const contents = document.querySelectorAll(".schedule-tab-content");

    if (tabs.length === 0 || contents.length === 0) {
        return; // Exit if tabs don't exist on the page
    }

    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            const targetTab = this.dataset.tab;

            // Remove active class from all tabs
            tabs.forEach((t) => t.classList.remove("active"));

            // Hide all tab contents
            contents.forEach((c) => c.classList.add("hidden"));

            // Add active class to clicked tab
            this.classList.add("active");

            // Show corresponding content
            const targetContent = document.getElementById(targetTab + "-tab");
            if (targetContent) {
                targetContent.classList.remove("hidden");
            }
        });
    });
}

/**
 * Get currently active tab
 * @returns {string|null} - The data-tab value of active tab or null
 */
export function getActiveTab() {
    const activeTab = document.querySelector(".schedule-tab.active");
    return activeTab ? activeTab.dataset.tab : null;
}

/**
 * Switch to a specific tab programmatically
 * @param {string} tabName - The data-tab value to switch to
 */
export function switchToTab(tabName) {
    const tab = document.querySelector(`.schedule-tab[data-tab="${tabName}"]`);
    if (tab) {
        tab.click();
    }
}
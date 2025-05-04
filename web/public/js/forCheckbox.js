
document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".rowCheckbox");
    const exportButton = document.getElementById("exportDropdown");
    // Function to check if any checkbox is selected
    function updateExportButtonState() {
        const anyChecked = Array.from(rowCheckboxes).some((checkbox) => checkbox.checked);
        exportButton.disabled = !anyChecked; // Enable if any checkbox is checked
    }
    // Monitor the "Select All" checkbox
    selectAllCheckbox.addEventListener("change", function () {
        rowCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateExportButtonState();
    });
    // Monitor individual row checkboxes
    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            // Update the "Select All" checkbox state
            selectAllCheckbox.checked = Array.from(rowCheckboxes).every((cb) => cb.checked);
            updateExportButtonState();
        });
    });
    // Initialize the state of the Export button
    updateExportButtonState();
    });

    document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".rowCheckbox");
    const exportButton = document.getElementById("exportDropdown");
    // Function to check if any checkbox is selected
    function updateExportButtonState() {
        const anyChecked = Array.from(rowCheckboxes).some((checkbox) => checkbox.checked);
        exportButton.disabled = !anyChecked; // Enable if any checkbox is checked
    }
    // Monitor the "Select All" checkbox
    selectAllCheckbox.addEventListener("change", function () {
        rowCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateExportButtonState();
    });
    // Monitor individual row checkboxes
    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            // Update the "Select All" checkbox state
            selectAllCheckbox.checked = Array.from(rowCheckboxes).every((cb) => cb.checked);
            updateExportButtonState();
        });
    });
    // Initialize the state of the Export button
    updateExportButtonState();
    });
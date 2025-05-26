document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const exportPdfButton = document.getElementById('exportPdf');
    const exportExcelButton = document.getElementById('exportExcel');
    const exportForm = document.getElementById('exportForm');
    const selectedBeneficiaries = document.getElementById('selectedBeneficiaries');
    
    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }
    
    // Update select all checkbox when individual checkboxes change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
        });
    });
    
    // Function to update the "Select All" checkbox state
    function updateSelectAllCheckbox() {
        if (!selectAllCheckbox) return;
        
        const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
        selectAllCheckbox.checked = checkedBoxes.length === rowCheckboxes.length && rowCheckboxes.length > 0;
    }
    
    // Handle PDF export
    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            
            // Get all checked beneficiary IDs
            const selectedIds = getSelectedBeneficiaryIds();
            
            if (selectedIds.length === 0) {
                alert('Please select at least one beneficiary to export.');
                return;
            }
            
            // Show loading state
            exportPdfButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';
            
            // Set form data and submit
            selectedBeneficiaries.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-pdf-route');
            exportForm.method = 'POST'; // Ensure POST method is set
            exportForm.submit();
            
            // Reset button after a delay (for UX)
            setTimeout(() => {
                exportPdfButton.innerHTML = 'Export as PDF';
            }, 3000);
        });
    }
    
    // Handle Excel export
    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            
            // Get all checked beneficiary IDs
            const selectedIds = getSelectedBeneficiaryIds();
            
            if (selectedIds.length === 0) {
                alert('Please select at least one beneficiary to export.');
                return;
            }
            
            // Show loading state
            exportExcelButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';
            
            // Set form data and submit
            selectedBeneficiaries.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-excel-route');
            exportForm.method = 'POST'; // Ensure POST method is set
            exportForm.submit();
            
            // Reset button after a delay (for UX)
            setTimeout(() => {
                exportExcelButton.innerHTML = 'Export as Excel';
            }, 3000);
        });
    }
    
    // Helper function to get all selected beneficiary IDs
    function getSelectedBeneficiaryIds() {
        const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
        return Array.from(checkedBoxes).map(checkbox => checkbox.value);
    }
});
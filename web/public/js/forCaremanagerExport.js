// Function to handle checkbox selection and export
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const exportForm = document.getElementById('exportForm');
    const selectedCaremanagers = document.getElementById('selectedCaremanagers');
    const exportExcelButton = document.getElementById('exportExcel');
    const exportPdfButton = document.getElementById('exportPdf');

    // Get selected care manager IDs
    function getSelectedIds() {
        const selectedIds = [...rowCheckboxes]
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
            
        if (selectedIds.length === 0) {
            alert('Please select at least one care manager to export.');
            return null;
        }
        
        return selectedIds;
    }

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
            const allChecked = [...rowCheckboxes].every(c => c.checked);
            const someChecked = [...rowCheckboxes].some(c => c.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });
    
    // Handle PDF export
    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedIds = getSelectedIds();
            if (!selectedIds) return;
            
            // Show loading state
            exportPdfButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';
            
            // Set form data and submit
            selectedCaremanagers.value = JSON.stringify(selectedIds);
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
            e.preventDefault();
            
            const selectedIds = getSelectedIds();
            if (!selectedIds) return;
            
            // Show loading state
            exportExcelButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';
            
            // Set form data and submit
            selectedCaremanagers.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-excel-route');
            exportForm.method = 'POST'; // Ensure POST method is set
            exportForm.submit();
            
            // Reset button after a delay (for UX)
            setTimeout(() => {
                exportExcelButton.innerHTML = 'Export as Excel';
            }, 3000);
        });
    }
});
// Function to handle checkbox selection and export
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const exportForm = document.getElementById('exportForm');
    const selectedCareworkers = document.getElementById('selectedCareworkers');
    
    // Helper function to get selected IDs
    function getSelectedIds() {
        const selectedIds = [...rowCheckboxes]
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
            
        if (selectedIds.length === 0) {
            alert('Please select at least one care worker to export.');
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
    const exportPdfButton = document.getElementById('exportPdf');
    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedIds = [...rowCheckboxes]
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
                
            if (selectedIds.length === 0) {
                alert('Please select at least one care worker to export.');
                return;
            }
            
            document.getElementById('selectedCareworkers').value = JSON.stringify(selectedIds);
            document.getElementById('exportForm').submit();
        });
    }

    // Handle Excel export
    const exportExcelButton = document.getElementById('exportExcel');
    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedIds = getSelectedIds();
            if (!selectedIds) return;
            
            selectedCareworkers.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-excel-route');
            exportForm.submit();
        });
    }
});
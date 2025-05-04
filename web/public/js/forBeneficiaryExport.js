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
            const allChecked = [...rowCheckboxes].every(c => c.checked);
            const someChecked = [...rowCheckboxes].some(c => c.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });
    
    // Get selected IDs
    function getSelectedIds() {
        const selectedIds = [...rowCheckboxes]
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
            
        if (selectedIds.length === 0) {
            alert('Please select at least one beneficiary to export.');
            return null;
        }
        
        return selectedIds;
    }
    
    // Handle PDF export
    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedIds = getSelectedIds();
            if (!selectedIds) return;
            
            selectedBeneficiaries.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-pdf-route');
            exportForm.submit();
        });
    }
    
    // Handle Excel export
    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedIds = getSelectedIds();
            if (!selectedIds) return;
            
            selectedBeneficiaries.value = JSON.stringify(selectedIds);
            exportForm.action = exportForm.getAttribute('data-excel-route');
            exportForm.submit();
        });
    }
});
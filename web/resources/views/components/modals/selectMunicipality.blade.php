<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\selectMunicipality.blade.php -->
<div class="modal fade" id="selectMunicipalityModal" tabindex="-1" aria-labelledby="selectMunicipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectMunicipalityModalLabel">Select Municipality to Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($municipalities as $municipality)
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                onclick="openDeleteMunicipalityModal('{{ $municipality->municipality_id }}', '{{ $municipality->municipality_name }}')">
                            {{ $municipality->municipality_name }}
                            <span class="badge bg-primary rounded-pill">
                                {{ $barangays->where('municipality_id', $municipality->municipality_id)->count() }} barangays
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize the select municipality modal when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Button to open the select municipality modal
    document.getElementById('deleteMunicipalityButton').addEventListener('click', function() {
        const selectMunicipalityModal = new bootstrap.Modal(document.getElementById('selectMunicipalityModal'));
        selectMunicipalityModal.show();
    });

    // When a municipality is selected from the list, hide the selection modal
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function() {
            const selectMunicipalityModal = bootstrap.Modal.getInstance(document.getElementById('selectMunicipalityModal'));
            if (selectMunicipalityModal) {
                selectMunicipalityModal.hide();
            }
        });
    });
});
</script>
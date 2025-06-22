@php
use App\Helpers\TranslationHelper as T;
@endphp

<!-- Weekly Care Plan Delete Modal (Password Confirmation) -->
<div class="modal fade" id="deleteWeeklyCarePlanModal" tabindex="-1" aria-labelledby="deleteWeeklyCarePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteWeeklyCarePlanModalLabel">{{ T::translate('Confirm Deletion with Password', 'Kumpirmahin ang Pagtanggal gamit ang Password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="weeklyCarePlanModalBodyContent">
                <div id="weeklyCarePlanDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Final Warning!', 'Huling Babala!') }}</strong> {{ T::translate('This action cannot be undone.', 'Hindi na mababawi ang aksyon na ito.') }}
                    </p>
                    <p>{{ T::translate('You are about to permanently delete the weekly care plan for', 'Tatanggalin mo na ang weekly care plan para kay') }} <span id="beneficiaryNameToDelete" style="font-weight: bold;"></span>.</p>
                    <p><strong>{{ T::translate('This action cannot be undone and all data will be permanently lost.', 'Hindi na mababawi ang aksyon na ito at ang lahat ng datos ay permanenteng mawawala.') }}</strong></p>
                    <div class="mb-3">
                        <label for="weeklyCarePlanDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="weeklyCarePlanDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="weeklyCarePlanIdToDelete" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmWeeklyCarePlanDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Weekly Care Plan', 'Tanggalin ang Weekly Care Plan') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the password confirmation modal
window.openPasswordConfirmationModal = function(id, name) {
    // Reset modal state
    const modalBody = document.getElementById('weeklyCarePlanModalBodyContent');
    
    // Reset to original content
    modalBody.innerHTML = `
        <div id="weeklyCarePlanDeleteMessage" class="alert d-none" role="alert"></div>
        
        <div id="deleteConfirmation">
            <p class="text-danger">
                <i class="bi bi-exclamation-circle"></i> 
                <strong>{{ T::translate('Final Warning!', 'Huling Babala!') }}</strong> {{ T::translate('This action cannot be undone.', 'Hindi na mababawi ang aksyon na ito.') }}
            </p>
            <p>{{ T::translate('You are about to permanently delete the weekly care plan for', 'Tatanggalin mo na ang weekly care plan para kay') }} <span id="beneficiaryNameToDelete" style="font-weight: bold;"></span>.</p>
            <p><strong>{{ T::translate('This action cannot be undone and all data will be permanently lost.', 'Hindi na mababawi ang aksyon na ito at ang lahat ng datos ay permanenteng mawawala.') }}</strong></p>
            <div class="mb-3">
                <label for="weeklyCarePlanDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                <input type="password" class="form-control" id="weeklyCarePlanDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                <input type="hidden" id="weeklyCarePlanIdToDelete" value="">
            </div>
        </div>
    `;
    
    // Set values
    document.getElementById('weeklyCarePlanIdToDelete').value = id;
    document.getElementById('beneficiaryNameToDelete').textContent = name;
    
    // Reset buttons
    const confirmButton = document.getElementById('confirmWeeklyCarePlanDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Weekly Care Plan", "Tanggalin ang Weekly Care Plan") }}';
    confirmButton.style.display = 'inline-block';
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Cancel", "I-Kansela") }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteWeeklyCarePlanModal'));
    deleteModal.show();
    
    // Re-add event listener for password input
    document.getElementById('weeklyCarePlanDeletePasswordInput').addEventListener('input', function() {
        const messageElement = document.getElementById('weeklyCarePlanDeleteMessage');
        if (messageElement) {
            messageElement.classList.add('d-none');
            messageElement.style.display = 'none';
        }
    });
}

// Function to show error message
function showWeeklyCarePlanError(message) {
    const messageElement = document.getElementById('weeklyCarePlanDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
    messageElement.style.display = 'block';
}

// Function to completely replace modal content with success message
function showWeeklyCarePlanSuccess() {
    // Get modal body reference
    const modalBody = document.getElementById('weeklyCarePlanModalBodyContent');
    
    // Replace content with success message
    modalBody.innerHTML = `
        <div class="text-center mb-2">
            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
        </div>
        <p class="text-success text-center">
            <strong>{{ T::translate('Success!', 'Tagumpay!') }}</strong> {{ T::translate('The weekly care plan has been deleted successfully.', 'Matagumpay na natanggal ang weekly care plan.') }}
        </p>
        <p class="text-center">{{ T::translate('You will be redirected to the reports page shortly.', 'Ikaw ay maire-redirect sa pahina ng mga report sa ilang sandali.') }}</p>
    `;
    
    // Hide delete button and update cancel button
    document.getElementById('confirmWeeklyCarePlanDeleteButton').style.display = 'none';
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Set timeout to redirect to the reports page
    setTimeout(function() {
        // Use role-specific reports route
        @if(Auth::user()->role_id == 2)
            window.location.href = "{{ route('care-manager.reports') }}";
        @else
            window.location.href = "{{ route('admin.reports') }}";
        @endif
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation button click handler
    document.getElementById('confirmWeeklyCarePlanDeleteButton').addEventListener('click', function() {
        // Get password and ID from the form
        const passwordInput = document.getElementById('weeklyCarePlanDeletePasswordInput');
        const idInput = document.getElementById('weeklyCarePlanIdToDelete');
        
        if (!passwordInput || !idInput) {
            console.error('Form elements not found');
            return;
        }
        
        const password = passwordInput.value.trim();
        const weeklyCarePlanId = idInput.value;
        
        if (!password) {
            showWeeklyCarePlanError('{{ T::translate("Please enter your password to confirm deletion.", "Mangyaring ilagay ang iyong password upang kumpirmahin ang pagtanggal.") }}');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate("Deleting...", "Tinatanggal...") }}';
        
        // Send delete request with CSRF token and password
        fetch(`{{ Auth::user()->role_id == 2 ? '/care-manager' : '/admin' }}/weekly-care-plans/${weeklyCarePlanId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => {
            // Check if response is ok (status in 200-299 range)
            if (!response.ok) {
                // Check content type to handle differently
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    // If it's JSON, parse the error
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || '{{ T::translate("Server error", "Error sa server") }}');
                    });
                } else {
                    // Not JSON, likely HTML error page
                    throw new Error(`{{ T::translate("Server error", "Error sa server") }}: ${response.status}`);
                }
            }
            
            // If we got here, response is ok, parse as JSON
            return response.json();
        })
        .then(data => {
            console.log("Server response:", data);
            
            if (data.success) {
                showWeeklyCarePlanSuccess();
            } else {
                // Show the specific error message from the server
                showWeeklyCarePlanError(data.message || '{{ T::translate("Failed to delete weekly care plan.", "Nabigong tanggalin ang weekly care plan.") }}');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Weekly Care Plan", "Tanggalin ang Weekly Care Plan") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showWeeklyCarePlanError(error.message || '{{ T::translate("An unexpected error occurred. Please try again.", "Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.") }}');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Weekly Care Plan", "Tanggalin ang Weekly Care Plan") }}';
        });
    });
});
</script>
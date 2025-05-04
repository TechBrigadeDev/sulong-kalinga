document.addEventListener("DOMContentLoaded", function () {
    let deleteTarget = null;

    // Function to set the target for deletion
    window.setDeleteTarget = function (icon) {
        deleteTarget = icon.closest("tr"); // Get the row to delete
    };

    // Handle the delete confirmation
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
    confirmDeleteButton.addEventListener("click", function () {
        const passwordInput = document.getElementById("passwordInput").value;

        if (passwordInput === "") {
            alert("Please enter your password to confirm.");
            return;
        }

        // Simulate password validation (replace with actual validation logic)
        if (passwordInput === "admin") {
            alert("Item deleted successfully!");
            if (deleteTarget) {
                deleteTarget.remove(); // Remove the row from the table
            }
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById("deleteModal"));
            deleteModal.hide(); // Close the modal
        } else {
            alert("Incorrect password. Please try again.");
        }
    });
});
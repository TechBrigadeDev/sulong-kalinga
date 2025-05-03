<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('landing') }}">
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end bg-light" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('messages') ? 'active' : '' }}" href="#">Messages</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-notification-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span>Notifications</span>
                        <span class="badge bg-danger rounded-pill notification-count">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-notifications dropdown-menu-end p-0" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0">Notifications</h6>
                            <small class="mark-all-read">Mark all as read</small>
                        </div>
                        <div class="notification-list">
                            <!-- Admin Notifications -->
                            <div class="notification-item unread" data-id="1">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon info">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <strong>System Update Available</strong>
                                        <p class="mb-1 text-truncate">A new system version (2.3.1) is now available with improved security features and performance enhancements.</p>
                                        <span class="notification-time">2 mins ago</span>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-link mark-as-read" data-id="1">Mark as read</button>
                                </div>
                            </div>
                            
                            <!-- Case Worker Notifications -->
                            <div class="notification-item unread" data-id="2">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <strong>New Case Assignment</strong>
                                        <p class="mb-1 text-truncate">You've been assigned to case #CW-2023-045 (Family Support Request) with high priority.</p>
                                        <span class="notification-time">1 hour ago</span>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-link mark-as-read" data-id="2">Mark as read</button>
                                </div>
                            </div>
                            
                            <!-- Beneficiary Notifications -->
                            <div class="notification-item" data-id="3">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon success">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <strong>Application Approved</strong>
                                        <p class="mb-1 text-truncate">Your assistance application (Ref #APP-2023-789) has been approved. Please check your email for details.</p>
                                        <span class="notification-time">5 hours ago</span>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-link mark-as-read" data-id="3">Mark as read</button>
                                </div>
                            </div>
                            
                            <!-- Volunteer Notifications -->
                            <div class="notification-item unread" data-id="4">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon danger">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>
                                    <div class="notification-content">
                                        <strong>Event Cancellation Notice</strong>
                                        <p class="mb-1 text-truncate">The community outreach event scheduled for Friday, November 10th has been cancelled due to weather conditions.</p>
                                        <span class="notification-time">Yesterday</span>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-link mark-as-read" data-id="4">Mark as read</button>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-footer text-center py-2">
                            <a href="#" class="text-primary view-all-notifications" data-bs-toggle="modal" data-bs-target="#notificationsModal">View all notifications</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('account') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('account') ? 'active' : '' }}" href="viewProfile">Account Profile</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                        <li>
                            <div class="dropdown-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-translate me-2"></i>
                                    <label for="languageToggle" class="m-0" style="cursor: pointer;" onclick="event.stopPropagation();">
                                        <span>Tagalog</span>
                                    </label>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" id="languageToggle" style="cursor: pointer;">
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Notifications Modal -->
<div class="modal fade notification-modal" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationsModalLabel">All Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Showing all notifications</span>
                    <button class="btn btn-sm btn-outline-primary mark-all-read-modal">Mark all as read</button>
                </div>
                
                <div class="notification-list">
                    <!-- Sample notifications - in a real app these would be dynamically generated -->
                    <div class="notification-item unread" data-id="1">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon info">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <div class="notification-content">
                                <strong>System Update Available</strong>
                                <p class="mb-1">A new system version (2.3.1) is now available with improved security features and performance enhancements. Please update at your earliest convenience.</p>
                                <span class="notification-time">2 mins ago</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-link mark-as-read" data-id="1">Mark as read</button>
                        </div>
                    </div>
                    
                    <div class="notification-item unread" data-id="2">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <strong>New Case Assignment</strong>
                                <p class="mb-1">You've been assigned to case #CW-2023-045 (Family Support Request) with high priority. Client is expecting your contact within 24 hours.</p>
                                <span class="notification-time">1 hour ago</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-link mark-as-read" data-id="2">Mark as read</button>
                        </div>
                    </div>
                    
                    <div class="notification-item" data-id="3">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <strong>Application Approved</strong>
                                <p class="mb-1">Your assistance application (Ref #APP-2023-789) has been approved. Please check your email for details about the next steps and required documentation.</p>
                                <span class="notification-time">5 hours ago</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-link mark-as-read" data-id="3">Mark as read</button>
                        </div>
                    </div>
                    
                    <div class="notification-item unread" data-id="4">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon danger">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <div class="notification-content">
                                <strong>Event Cancellation Notice</strong>
                                <p class="mb-1">The community outreach event scheduled for Friday, November 10th has been cancelled due to incoming severe weather conditions. A reschedule date will be announced next week.</p>
                                <span class="notification-time">Yesterday</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-link mark-as-read" data-id="4">Mark as read</button>
                        </div>
                    </div>
                    
                    <!-- Older notifications -->
                    <div class="notification-item" data-id="5">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon info">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="notification-content">
                                <strong>New Volunteer Orientation</strong>
                                <p class="mb-1">The next volunteer orientation session is scheduled for November 15th at 2:00 PM in the community center.</p>
                                <span class="notification-time">3 days ago</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-link mark-as-read" data-id="5">Mark as read</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute('data-id');
            const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            
            if (notificationItem.classList.contains('unread')) {
                notificationItem.classList.remove('unread');
                updateUnreadCount();
            }
        });
    });
    
    // Mark all notifications as read (dropdown)
    document.querySelector('.mark-all-read').addEventListener('click', function(e) {
        e.stopPropagation();
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
        });
        updateUnreadCount();
    });
    
    // Mark all notifications as read (modal)
    document.querySelector('.mark-all-read-modal')?.addEventListener('click', function() {
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
        });
        updateUnreadCount();
    });
    
    // Notification click handler
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            console.log(`Notification ${notificationId} clicked`);
            
            if (this.classList.contains('unread')) {
                this.classList.remove('unread');
                updateUnreadCount();
            }
        });
    });
    
    // Update the unread count badge
    function updateUnreadCount() {
        const unreadCount = document.querySelectorAll('.notification-item.unread').length;
        const countBadge = document.querySelector('.notification-count');
        
        countBadge.textContent = unreadCount;
        if (unreadCount === 0) {
            countBadge.style.display = 'none';
        } else {
            countBadge.style.display = 'inline-block';
        }
    }
    
    // Initialize count
    updateUnreadCount();
    
    // When modal is shown, update the notifications in it (in a real app, this would fetch from server)
    const notificationsModal = document.getElementById('notificationsModal');
    if (notificationsModal) {
        notificationsModal.addEventListener('show.bs.modal', function() {
            // In a real app, you would fetch all notifications here
            console.log('Loading all notifications...');
        });
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const languageToggle = document.getElementById('languageToggle');

        // Initialize the toggle state (optional, for demo purposes)
        let isTagalog = false;

        // Add event listener for the toggle
        languageToggle.addEventListener('change', function () {
            isTagalog = this.checked;
            if (isTagalog) {
                console.log('Tagalog selected');
                // Add any additional frontend behavior here
            } else {
                console.log('English selected');
                // Add any additional frontend behavior here
            }
        });
    });
</script>
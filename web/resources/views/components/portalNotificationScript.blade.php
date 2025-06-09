@php
    // Determine endpoints based on user type (beneficiary or family)
    $userType = null;
    $urlPrefix = "/"; // Use relative URLs
    
    if (Auth::guard('beneficiary')->check()) {
        $notificationsUrl = $urlPrefix . 'beneficiary/notifications';
        $markAllReadUrl = $urlPrefix . 'beneficiary/notifications/read-all';
        $messagingUrl = $urlPrefix . 'beneficiary/messaging';
        $messageUnreadCountUrl = $urlPrefix . 'beneficiary/messaging/unread-count';
        $messageRecentUrl = $urlPrefix . 'beneficiary/messaging/recent-messages';
        $messageReadAllUrl = $urlPrefix . 'beneficiary/messaging/read-all';
        $roleName = 'beneficiary';
        $rolePrefix = 'beneficiary';
        $userType = 'beneficiary';
    } elseif (Auth::guard('family')->check()) {
        $notificationsUrl = $urlPrefix . 'family/notifications';
        $markAllReadUrl = $urlPrefix . 'family/notifications/read-all';
        $messagingUrl = $urlPrefix . 'family/messaging';
        $messageUnreadCountUrl = $urlPrefix . 'family/messaging/unread-count';
        $messageRecentUrl = $urlPrefix . 'family/messaging/recent-messages';
        $messageReadAllUrl = $urlPrefix . 'family/messaging/read-all';
        $roleName = 'family';
        $rolePrefix = 'family';
        $userType = 'family_member';
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up notification and messaging system for {{ $roleName }}');
    
    let refreshPaused = false;    
    // =============================================
    // MESSAGING SYSTEM
    // =============================================
    
   // Helper function to update unread message count display
    function updateUnreadMessageCount(count) {
        const messageCount = document.querySelector('.message-count');
        if (messageCount) {
            if (count > 0) {
                messageCount.style.display = 'inline-block';
                messageCount.textContent = count;
                console.log(`Message badge updated: ${count} unread messages`); // Debug logging
            } else {
                messageCount.style.display = 'none';
                console.log('No unread messages, hiding badge');
            }
        } else {
            console.error('Message count badge element not found in DOM');
        }
    }
    
    // Load unread message count from server
    function loadUnreadMessageCount() {
        // CRITICAL FIX: Don't refresh if dropdown is open
        if (refreshPaused) {
            console.log('Count refresh paused because dropdown is open');
            return;
        }
        
        console.log('Fetching unread message count from:', '{{ $messageUnreadCountUrl }}');
        
        fetch('{{ $messageUnreadCountUrl }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Unread message count response:', data);
                updateUnreadMessageCount(data.count);
            })
            .catch(error => {
                console.error('Error loading unread message count:', error);
            });
    }
    
    // Load recent messages for message dropdown
    function loadRecentMessages() {
        const container = document.getElementById('message-preview-container');
        if (!container) return;

        console.log('Fetching recent messages from server...');
        
        // Show loading indicator
        container.innerHTML = `
            <div class="dropdown-item text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        fetch('{{ $messageRecentUrl }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Clear the container
                container.innerHTML = '';
                
                console.log('Messages loaded:', data);
                
                // CRITICAL FIX: DON'T update badge with dropdown data - let the scheduled refreshes handle it
                // This prevents the count from changing when dropdown opens
                // updateUnreadMessageCount(data.unread_count || 0);
                
                // Check if we have any messages
                if (data.success && data.messages && data.messages.length > 0) {
                    console.log(`Rendering ${data.messages.length} message previews`);
                    
                    // Add each message
                    data.messages.forEach(message => {
                        // Create container element
                        const messageItem = document.createElement('div');
                        messageItem.className = `dropdown-item message-preview-item ${message.unread ? 'unread' : ''}`;
                        messageItem.onclick = function() {
                            window.location.href = `{{ $messagingUrl }}?conversation=${message.conversation_id}`;
                        };
                        
                        // Format the message preview - use conversation_name for display
                        const displayName = message.conversation_name || 'Unknown';
                        
                        // Build the HTML content
                        messageItem.innerHTML = `
                            <div class="d-flex align-items-start py-2">
                                <div class="flex-shrink-0 me-2">
                                    <img src="/images/defaultProfile.png" 
                                        class="rounded-circle" width="40" height="40" alt="User">
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">${displayName}</span>
                                        <small class="text-muted ms-2">${message.time_ago || '-'}</small>
                                    </div>
                                    <div class="text-truncate ${message.unread ? 'fw-semibold' : 'text-muted'}" style="max-width: 250px;">
                                        ${message.is_unsent ? 'This message was unsent' : (message.content || 'No message')}
                                    </div>
                                </div>
                                ${message.unread ? '<div class="unread-message-indicator bg-primary rounded-circle ms-2" style="width: 8px; height: 8px;"></div>' : ''}
                            </div>
                        `;
                        
                        container.appendChild(messageItem);
                    });
                } else {
                    // Show no messages message
                    container.innerHTML = `
                        <div class="dropdown-item text-center py-3">
                            <span class="text-muted">No messages</span>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching recent messages:', error);
                container.innerHTML = `
                    <div class="dropdown-item text-center py-3">
                        <span class="text-danger">Failed to load messages</span>
                    </div>
                `;
            });
    }
    
    // Mark all messages as read
    function markAllMessagesAsRead() {
        console.log('Marking all messages as read...'); // Added log
        const markAllReadButtons = document.querySelectorAll('.mark-all-read');
        markAllReadButtons.forEach(btn => {
            if (btn.dataset.type === 'message') {
                btn.disabled = true;
                btn.textContent = 'Updating...';
            }
        });
        
        fetch('{{ $messageReadAllUrl }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark all messages as read - server response:', data);
            
            // Update UI immediately to reflect changes
            document.querySelectorAll('.message-preview-item.unread').forEach(el => {
                el.classList.remove('unread');
            });
            
            // Remove all unread indicators from messages
            document.querySelectorAll('.unread-message-indicator').forEach(el => {
                el.remove();
            });
            
            // Reset text styling on messages that were unread
            document.querySelectorAll('.message-preview-item .fw-semibold').forEach(el => {
                el.classList.remove('fw-semibold');
                el.classList.add('text-muted');
            });
            
            // Update message count badge
            updateUnreadMessageCount(0);
            
            // Reset button state
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                btn.textContent = 'Mark all as read';
            });
            
            // Show quick confirmation
            const dropdown = document.querySelector('.message-dropdown');
            if (dropdown) {
                const successEl = document.createElement('div');
                successEl.className = 'alert alert-success text-center py-1 px-2 m-2';
                successEl.textContent = 'All messages marked as read';
                dropdown.prepend(successEl);
                setTimeout(() => successEl.remove(), 3000);
            }
            
            // Most important: Reload message list to completely refresh content
            loadRecentMessages();
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
            // Reset button state
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                btn.textContent = 'Mark all as read';
            });
        });
    }
    
    // =============================================
    // NOTIFICATION SYSTEM
    // =============================================
    
    // DOM elements we need to reference frequently
    const dropdownList = document.querySelector('.dropdown-notifications .notification-list');
    const modalList = document.querySelector('#notificationsModal .notification-list');
    const countBadge = document.querySelector('.notification-count');
    const notificationsModal = document.getElementById('notificationsModal');
    
    // Track selected notification ID for modal focusing
    let selectedNotificationId = null;
    
    // Keep track of unread count in a variable instead of counting DOM elements
    let currentUnreadCount = 0;
    
    // Use the dynamic endpoints determined by the server
    const notificationsEndpoint = "{{ $notificationsUrl }}";
    const markAllReadEndpoint = "{{ $markAllReadUrl }}";
    console.log(`Using notifications endpoint for {{ $roleName }}: ${notificationsEndpoint}`);
    
    // Dropdown configuration to prevent closing when clicking inside
    const dropdown = document.querySelector('.dropdown-menu.dropdown-notifications');
    if (dropdown) {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Update the notification count badge
    function updateNotificationCount(count) {
        currentUnreadCount = count;
        const countBadge = document.querySelector('.notification-count');
        if (!countBadge) {
            console.error('Notification count badge element not found');
            return;
        }
        
        if (count > 0) {
            countBadge.textContent = count;
            countBadge.style.display = 'inline-block';
            console.log(`Badge updated: ${count} unread notifications`);
        } else {
            countBadge.style.display = 'none';
            console.log('No unread notifications, hiding badge');
        }
    }
        
    // Add fetch notifications functionality
    function loadNotifications() {
        console.log('Fetching notifications from server...');
        fetch(notificationsEndpoint)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Notifications loaded:', data);
                
                if (data.success) {
                    // Update notification count badge
                    updateNotificationCount(data.unread_count);
                    
                    // Render notifications in dropdown and modal
                    renderNotifications(data.notifications);
                    
                    // Log successful update
                    console.log(`Updated notification count: ${data.unread_count}`);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                
                // Show a user-friendly message in the notifications dropdown
                if (dropdownList) {
                    dropdownList.innerHTML = '<div class="p-3 text-center text-muted">Unable to load notifications</div>';
                }
                
                if (modalList) {
                    modalList.innerHTML = '<div class="p-3 text-center text-muted">Unable to load notifications</div>';
                }
            });
    }
    
    // Render all notifications to both dropdown and modal
    function renderNotifications(notifications) {
        // Skip if the elements don't exist
        if (!dropdownList || !modalList) return;
        
        // Clear existing notifications
        dropdownList.innerHTML = '';
        modalList.innerHTML = '';
        
        if (!notifications || notifications.length === 0) {
            const emptyMessage = '<div class="p-3 text-center text-muted">No notifications</div>';
            dropdownList.innerHTML = emptyMessage;
            modalList.innerHTML = emptyMessage;
            return;
        }
        
        console.log('Rendering notifications:', notifications.length);
        
        // Add notifications to both dropdown and modal
        notifications.forEach(notification => {
            const iconInfo = getNotificationIcon(notification);
            const timeAgo = formatTimeAgo(new Date(notification.date_created));
            
            // Create and append dropdown item (truncated)
            dropdownList.innerHTML += createNotificationHTML(
                notification, 
                iconInfo.icon,
                iconInfo.type,
                timeAgo,
                true // truncate for dropdown
            );
            
            // Create and append modal item (full text)
            modalList.innerHTML += createNotificationHTML(
                notification,
                iconInfo.icon,
                iconInfo.type,
                timeAgo,
                false // don't truncate for modal
            );
        });
        
        // Attach click handlers to buttons after rendering
        addButtonClickHandlers();
    }

    function fixMessagePreviews() {
        // Process attachment indicators
        document.querySelectorAll('.message-preview .small.text-truncate').forEach(element => {
            const content = element.textContent;
            if (content.includes('📎')) {
                const parts = content.split('📎 ');
                const fileName = parts[1] || 'Attachment';
                element.innerHTML = `<span class="attachment-indicator"><i class="bi bi-paperclip"></i> ${fileName}</span>`;
            } else if (content === 'No content' && element.closest('.message-preview').querySelector('.bi-paperclip')) {
                element.textContent = 'Attachment';
            }
        });

        // Add unread badges to unread messages
        document.querySelectorAll('.message-preview.unread').forEach(item => {
            const container = item.querySelector('.d-flex');
            if (container && !container.querySelector('.unread-badge')) {
                const badge = document.createElement('span');
                badge.className = 'unread-badge';
                badge.textContent = 'New';
                container.appendChild(badge);
            }
        });
    }

    // Create HTML string for a notification item
    function createNotificationHTML(notification, iconClass, iconType, timeAgo, truncate) {
        return `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                 id="notification-${notification.notification_id}" 
                 data-id="${notification.notification_id}">
                <div class="notification-icon notification-icon-${iconType}">
                    <i class="bi ${iconClass}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">
                        ${notification.message_title || 'Notification'}
                        ${notification.is_read ? '' : '<span class="unread-indicator"></span>'}
                    </div>
                    <div class="notification-text ${truncate ? 'text-truncate' : ''}">
                        ${notification.message}
                    </div>
                    <div class="notification-meta">
                        <span class="notification-time">${timeAgo}</span>
                        ${notification.is_read ? '' : `
                            <button class="btn btn-sm btn-link p-0 ms-2 mark-as-read" 
                                    data-id="${notification.notification_id}">
                                Mark as read
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;
    }
    
    // Add click handlers to all mark-as-read buttons
    function addButtonClickHandlers() {
        // Individual mark as read buttons
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                const notificationId = this.getAttribute('data-id');
                markAsRead(notificationId);
            };
        });
        
        // Mark all as read buttons
        const markAllReadNotifBtn = document.querySelector('.mark-all-read[data-type="notification"]');
        if (markAllReadNotifBtn) {
            markAllReadNotifBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                markAllAsRead();
            };
        }
        
        const markAllReadModalBtn = document.querySelector('.mark-all-read-modal');
        if (markAllReadModalBtn) {
            markAllReadModalBtn.onclick = function(e) {
                e.preventDefault();
                markAllAsRead();
            };
        }
        
        // Message mark all as read buttons
        const markAllReadMsgBtn = document.querySelector('.mark-all-read[data-type="message"]');
        if (markAllReadMsgBtn) {
            markAllReadMsgBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                markAllMessagesAsRead();
            };
        }
        
        // Make notification items in dropdown clickable to open modal and focus on that notification
        document.querySelectorAll('.dropdown-notifications .notification-item').forEach(item => {
            item.onclick = function(e) {
                // If clicking on a button, don't trigger this action
                if (e.target.tagName === 'BUTTON') return;
                
                const notificationId = this.getAttribute('data-id');
                selectedNotificationId = notificationId;
                
                // Open the modal programmatically
                const notificationsModal = new bootstrap.Modal(document.getElementById('notificationsModal'));
                notificationsModal.show();
            };
        });
    }
    
    // Mark a single notification as read
    function markAsRead(notificationId) {
        if (!notificationId) return;
        
        console.log('Marking notification as read:', notificationId);
        
        // Disable the mark-as-read button to prevent double-clicks
        const markAsReadButtons = document.querySelectorAll(`.mark-as-read[data-id="${notificationId}"]`);
        markAsReadButtons.forEach(btn => {
            btn.disabled = true;
            btn.textContent = 'Updating...';
        });
        
        // Then update on server first
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`${notificationsEndpoint}/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // If successful, update UI
                const dropdownItem = document.querySelector(`.dropdown-notifications #notification-${notificationId}`);
                const modalItem = document.querySelector(`#notificationsModal #notification-${notificationId}`);
                
                // Remove unread class from items
                if (dropdownItem) dropdownItem.classList.remove('unread');
                if (modalItem) modalItem.classList.remove('unread');
                
                // Remove unread indicator
                const dropdownIndicator = dropdownItem ? dropdownItem.querySelector('.unread-indicator') : null;
                const modalIndicator = modalItem ? modalItem.querySelector('.unread-indicator') : null;
                if (dropdownIndicator) dropdownIndicator.remove();
                if (modalIndicator) modalIndicator.remove();
                
                // Remove the mark as read buttons
                markAsReadButtons.forEach(btn => btn.remove());
                
                // Update unread count
                currentUnreadCount = Math.max(0, currentUnreadCount - 1);
                updateNotificationCount(currentUnreadCount);
            } else {
                // Re-enable buttons if there was an error
                markAsReadButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.textContent = 'Mark as read';
                });
                console.error('Error marking notification as read:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons
            markAsReadButtons.forEach(btn => {
                btn.disabled = false;
                btn.textContent = 'Mark as read';
            });
        });
    }
    
    // Mark all notifications as read
    function markAllAsRead() {
        console.log('Marking all notifications as read');
        
        // Disable the mark-all-read buttons
        const markAllReadButtons = document.querySelectorAll('.mark-all-read, .mark-all-read-modal');
        markAllReadButtons.forEach(btn => {
            btn.disabled = true;
            if (btn.tagName === 'BUTTON') {
                btn.textContent = 'Updating...';
            } else {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
        });
        
        // Then update on server first
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(markAllReadEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // If successful, update UI
                
                // Remove unread class from all notification items
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                
                // Remove all unread indicators
                document.querySelectorAll('.unread-indicator').forEach(indicator => {
                    indicator.remove();
                });
                
                // Remove all mark as read buttons
                document.querySelectorAll('.mark-as-read').forEach(button => {
                    button.remove();
                });
                
                // Update notification count
                updateNotificationCount(0);
                
                // Show quick confirmation in dropdown
                const dropdown = document.querySelector('.dropdown-notifications');
                if (dropdown) {
                    const successEl = document.createElement('div');
                    successEl.className = 'alert alert-success text-center py-1 px-2 m-2';
                    successEl.textContent = 'All notifications marked as read';
                    dropdown.prepend(successEl);
                    setTimeout(() => successEl.remove(), 3000);
                }
                
                // Show quick confirmation in modal
                const modal = document.querySelector('#notificationsModal .modal-body');
                if (modal) {
                    const successEl = document.createElement('div');
                    successEl.className = 'alert alert-success text-center py-1 px-2 mb-3';
                    successEl.textContent = 'All notifications marked as read';
                    modal.prepend(successEl);
                    setTimeout(() => successEl.remove(), 3000);
                }
            } else {
                console.error('Error marking all notifications as read:', data.message);
            }
            
            // Re-enable buttons regardless of outcome
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                if (btn.tagName === 'BUTTON') {
                    btn.textContent = 'Mark all as read';
                } else {
                    btn.innerHTML = 'Mark all as read';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                if (btn.tagName === 'BUTTON') {
                    btn.textContent = 'Mark all as read';
                } else {
                    btn.innerHTML = 'Mark all as read';
                }
            });
        });
    }
    
    // Focus and highlight a specific notification in the modal
    function focusNotificationInModal(notificationId) {
        // First, remove highlight from any previously highlighted items
        document.querySelectorAll('.notification-item.highlight').forEach(item => {
            item.classList.remove('highlight');
        });
        
        // Find the notification in the modal
        const notificationItem = modalList.querySelector(`#notification-${notificationId}`);
        
        if (notificationItem) {
            // Add highlight class
            notificationItem.classList.add('highlight');
            
            // Scroll the item into view with smooth animation
            notificationItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Optional: Flash effect to draw attention
            setTimeout(() => {
                notificationItem.classList.add('flash');
                setTimeout(() => {
                    notificationItem.classList.remove('flash');
                }, 1000);
            }, 300);
        } else {
            console.log('Notification not found in modal:', notificationId);
        }
    }
    
    // Get appropriate icon for notification type
    function getNotificationIcon(notification) {
        let icon = 'bi-info-circle';
        let type = 'info';
        
        if (notification.message_title) {
            const title = notification.message_title.toLowerCase();
            
            // Location-specific icons
            if (title.includes('municipality') || title.includes('barangay')) {
                icon = 'bi-geo-alt';
                type = 'primary';
            }
            // Admin notification icons
            else if (title.includes('administrator') || 
                    title.includes('admin')) {
                icon = 'bi-person-badge';
                type = 'warning';
            }
            // Care Manager notification icons
            else if (title.includes('care manager')) {
                icon = 'bi-person-workspace';
                type = 'info';
            }
            // Welcome back notifications
            else if (title.includes('welcome back')) {
                icon = 'bi-house-door';
                type = 'success';
            }
            // Status change notification
            else if (title.includes('status')) {
                icon = 'bi-arrow-clockwise';
                type = 'warning';
            }
            // Profile update notification
            else if (title.includes('profile was updated')) {
                icon = 'bi-person-check';
                type = 'primary';
            }
            // Medication related notifications
            else if (title.includes('medication')) {
                icon = 'bi-capsule';
                type = 'primary';
            }
            // Visit related notifications
            else if (title.includes('visit') || title.includes('appointment')) {
                icon = 'bi-calendar-event';
                type = 'info';
            }
            // Emergency notifications
            else if (title.includes('emergency')) {
                icon = 'bi-exclamation-triangle';
                type = 'danger';
            }
            // Health related notifications
            else if (title.includes('health')) {
                icon = 'bi-heart-pulse';
                type = 'primary';
            }
            // Other existing conditions
            else if (title.includes('warning') || title.includes('assign')) {
                type = 'warning';
            } else if (title.includes('success') || title.includes('approved') || title.includes('welcome')) {
                type = 'success';
            } else if (title.includes('error') || title.includes('cancel') || title.includes('denied') || title.includes('deactivated')) {
                type = 'danger';
            } else if (title.includes('care worker')) {
                type = 'info';
            } else if (title.includes('beneficiary') || title.includes('welcome to sulong kalinga')) {
                type = 'success';
            } else if (title.includes('family member')) {
                type = 'primary';
            }
        }
        
        return { icon, type };
    }
    
    // Format time ago from date
    function formatTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + ' years ago';
        if (interval === 1) return '1 year ago';
        
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + ' months ago';
        if (interval === 1) return '1 month ago';
        
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + ' days ago';
        if (interval === 1) return '1 day ago';
        
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + ' hours ago';
        if (interval === 1) return '1 hour ago';
        
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + ' minutes ago';
        if (interval === 1) return '1 minute ago';
        
        return 'just now';
    }
    
    // Configure modal events
    if (notificationsModal) {
        // When the modal is shown, focus on the selected notification if any
        notificationsModal.addEventListener('shown.bs.modal', function() {
            if (selectedNotificationId) {
                focusNotificationInModal(selectedNotificationId);
                selectedNotificationId = null;
            }
        });
        
        // Reload notifications when modal is opened
        notificationsModal.addEventListener('show.bs.modal', loadNotifications);
    }
    
    // ==================================================
    // SETUP MESSAGE DROPDOWN AND NOTIFICATION TRIGGERS
    // ==================================================
    
    // Set up click handler for message dropdown
    const messagesDropdown = document.getElementById('messagesDropdown');
    if (messagesDropdown) {
        // Pause refreshes when dropdown is opened
        messagesDropdown.addEventListener('show.bs.dropdown', function() {
            console.log('Messages dropdown opened - pausing refresh');
            refreshPaused = true;
            // Force load messages when dropdown opens
            loadRecentMessages();
        });
        
        // Resume refreshes when dropdown is closed
        messagesDropdown.addEventListener('hidden.bs.dropdown', function() {
            console.log('Messages dropdown closed - resuming refresh');
            refreshPaused = false;
            // Update count after dropdown closes
            setTimeout(loadUnreadMessageCount, 500);
        });
    }
    
    // Initialize
    loadUnreadMessageCount();
    loadNotifications();
    
    // Set up periodic refresh
    setInterval(() => {
        if (!refreshPaused) {
            console.log('Periodic refresh: Updating unread message count');
            loadUnreadMessageCount();
        } else {
            console.log('Skipping message count refresh: dropdown is open');
            // No longer calling loadUnreadMessageCount() when paused
        }
    }, 8000); // Every 8 seconds

    setInterval(() => {
        console.log('Periodic refresh: Updating notifications');
        loadNotifications();
    }, 8000); // Every 8 seconds
    
    // Add data-type attribute to mark-all-read buttons when DOM is loaded
    document.querySelectorAll('.message-dropdown .mark-all-read').forEach(btn => {
        btn.setAttribute('data-type', 'message');
    });
    
    document.querySelectorAll('.dropdown-notifications .mark-all-read').forEach(btn => {
        btn.setAttribute('data-type', 'notification');
    });

});
</script>
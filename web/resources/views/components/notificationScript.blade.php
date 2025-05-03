@php
    // Determine endpoints based on user role
    $userRole = Auth::user()->role_id;
    
    if ($userRole == 1) {
        // Admin
        $notificationsUrl = url('admin/notifications');
        $markAllReadUrl = route('admin.notifications.read-all');
        $messagingUrl = url('admin/messaging');
        $messageUnreadCountUrl = route('admin.messaging.unread-count');
        $messageRecentUrl = route('admin.messaging.recent');
        $messageReadAllUrl = route('admin.messaging.read-all');
        $roleName = 'admin';
        $rolePrefix = 'admin';
    } elseif ($userRole == 2) {
        // Care Manager
        $notificationsUrl = url('care-manager/notifications');
        $markAllReadUrl = route('care-manager.notifications.read-all');
        $messagingUrl = url('care-manager/messaging');
        $messageUnreadCountUrl = route('care-manager.messaging.unread-count');
        $messageRecentUrl = route('care-manager.messaging.recent');
        $messageReadAllUrl = route('care-manager.messaging.read-all');
        $roleName = 'care-manager';
        $rolePrefix = 'care-manager';
    } elseif ($userRole == 3) {
        // Care Worker
        $notificationsUrl = url('care-worker/notifications');
        $markAllReadUrl = route('care-worker.notifications.read-all');
        $messagingUrl = url('care-worker/messaging');
        $messageUnreadCountUrl = route('care-worker.messaging.unread-count');
        $messageRecentUrl = route('care-worker.messaging.recent');
        $messageReadAllUrl = route('care-worker.messaging.read-all');
        $roleName = 'care-worker';
        $rolePrefix = 'care-worker';
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up notification and messaging system for {{ $roleName }}');
    
    // =============================================
    // MESSAGING SYSTEM
    // =============================================
    
    // Helper function to update unread message count display
    function updateUnreadMessageCount(count) {
        const messageCount = document.querySelector('.message-count');
        if (messageCount) {
            if (count > 0) {
                messageCount.textContent = count;
                messageCount.style.display = 'block';
            } else {
                messageCount.style.display = 'none';
            }
        }
    }
    
    // Load unread message count from server
    function loadUnreadMessageCount() {
        fetch('{{ $messageUnreadCountUrl }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
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
        
        // Show loading indicator
        container.innerHTML = `
            <li class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </li>
        `;
        
        fetch('{{ $messageRecentUrl }}')
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                
                if (!data.conversations || data.conversations.length === 0) {
                    container.innerHTML = `
                        <li class="text-center py-3">
                            <span class="text-muted">No new messages</span>
                        </li>
                    `;
                    return;
                }
                
                data.conversations.forEach(conversation => {
                    const lastMessage = conversation.last_message;
                    const senderName = lastMessage && lastMessage.sender ? 
                        (lastMessage.sender.first_name + ' ' + lastMessage.sender.last_name) : 
                        'Unknown';
                    
                    // ADD YOUR CODE RIGHT HERE - for safely processing message content
                    let messageContent = lastMessage && lastMessage.content ? lastMessage.content : 'No content';
                    
                    // If message is unsent, override the content
                    if (lastMessage && lastMessage.is_unsent) {
                        messageContent = '<em class="text-muted">This message was unsent</em>';
                    }
                    
                    // If checking for attachments:
                    if (messageContent && typeof messageContent === 'string' && messageContent.includes('📎')) {
                        // Process attachment indicators - extract file name from attachment indicator
                        const fileName = messageContent.split('📎')[1].trim();
                        messageContent = `<span class="attachment-indicator"><i class="bi bi-paperclip"></i> ${fileName}</span>`;
                    }
                    
                    // Determine if conversation has unread messages
                    const isUnread = conversation.has_unread || false; // ADD THIS LINE
                    
                    const previewHtml = `
                        <li>
                            <a class="dropdown-item message-preview ${isUnread ? 'unread' : ''}" 
                            href="{{ $messagingUrl }}?conversation=${conversation.conversation_id}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        ${conversation.is_group_chat ?
                                            `<div class="rounded-circle profile-img-sm d-flex justify-content-center align-items-center bg-primary text-white">
                                                <span>${conversation.name ? conversation.name.charAt(0) : 'G'}</span>
                                            </div>` :
                                            `<img src="{{ asset('images/defaultProfile.png') }}" class="rounded-circle profile-img-sm" alt="User">`
                                        }
                                        ${isUnread ? '<span class="unread-indicator"></span>' : ''}
                                    </div>
                                    <div class="flex-grow-1 ms-2 overflow-hidden">
                                        <p class="mb-0 fw-bold">${conversation.is_group_chat ? conversation.name : (conversation.other_participant_name || 'Unknown')}</p>
                                        <p class="small text-truncate mb-0">${conversation.is_group_chat && lastMessage.sender_name ? lastMessage.sender_name + ': ' : ''}${messageContent}</p>
                                        <p class="text-muted small mb-0">${lastMessage ? timeSince(new Date(lastMessage.message_timestamp)) : ''}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                    `;
                    
                    container.innerHTML += previewHtml;
                });
                
                // Fix attachment indicators and add unread badges
                fixMessagePreviews();
            })
            .catch(error => {
                console.error('Error loading recent messages:', error);
                container.innerHTML = `
                    <li class="text-center py-3">
                        <span class="text-muted">Could not load messages</span>
                    </li>
                `;
            });
    }
    
    // Mark all messages as read
    function markAllMessagesAsRead() {
        const markAllReadButtons = document.querySelectorAll('.mark-all-read');
        markAllReadButtons.forEach(btn => {
            if (btn.dataset.type === 'message') {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
        });
        
        fetch('{{ $messageReadAllUrl }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update UI to show all messages as read
            document.querySelectorAll('.message-preview.unread').forEach(el => {
                el.classList.remove('unread');
            });
            
            // Remove all unread indicators and badges
            document.querySelectorAll('.unread-indicator, .message-badge').forEach(el => {
                el.remove();
            });

            // Update message count
            updateUnreadMessageCount(0);
            
            // Reset button state
            markAllReadButtons.forEach(btn => {
                if (btn.dataset.type === 'message') {
                    btn.disabled = false;
                    btn.innerHTML = 'Mark all as read';
                }
            });
            
            // Show quick confirmation
            const dropdown = document.querySelector('.message-dropdown');
            if (dropdown) {
                const confirmation = document.createElement('div');
                confirmation.className = 'text-center text-success py-2 read-confirmation';
                confirmation.textContent = 'All messages marked as read';
                dropdown.appendChild(confirmation);
                
                // Remove confirmation after 2 seconds
                setTimeout(() => {
                    document.querySelectorAll('.read-confirmation').forEach(el => el.remove());
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
            // Reset button state
            markAllReadButtons.forEach(btn => {
                if (btn.dataset.type === 'message') {
                    btn.disabled = false;
                    btn.innerHTML = 'Mark all as read';
                }
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
        const countBadge = document.querySelector('.notification-count');
        if (!countBadge) return;
        
        if (count > 0) {
            countBadge.textContent = count;
            countBadge.style.display = 'inline-block';
        } else {
            countBadge.style.display = 'none';
        }
        
        console.log('Notification count updated:', count, countBadge);
    }
    
    // Add fetch notifications functionality
    function loadNotifications() {
        console.log('Fetching notifications from server...');
        fetch(notificationsEndpoint)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Notification data received:', data);
                if (data.success) {
                    renderNotifications(data.notifications);
                    
                    // Set the unread count from server response
                    currentUnreadCount = data.unread_count;
                    updateNotificationCount(currentUnreadCount);
                    
                    // After rendering, check if we need to focus a notification
                    if (selectedNotificationId && notificationsModal && notificationsModal.classList.contains('show')) {
                        focusNotificationInModal(selectedNotificationId);
                    }
                } else {
                    console.error('API returned error:', data.message || 'Unknown error');
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
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
                // Replace plain text attachment indicator with styled version
                const fileName = content.replace('📎 ', '');
                element.innerHTML = `<span class="attachment-indicator"><i class="bi bi-paperclip"></i>${fileName}</span>`;
            } else if (content === 'No content' && element.closest('.message-preview').querySelector('.bi-paperclip')) {
                // There's already a paperclip icon, so this is an attachment
                element.innerHTML = `<span class="attachment-indicator"><i class="bi bi-paperclip"></i>Attachment</span>`;
            }
        });

        // Add unread badges to unread messages
        document.querySelectorAll('.message-preview.unread').forEach(item => {
            const container = item.querySelector('.d-flex');
            if (container && !container.querySelector('.unread-badge')) {
                container.style.position = 'relative';
                const badge = document.createElement('span');
                badge.className = 'badge bg-danger unread-badge';
                badge.textContent = '1';
                container.appendChild(badge);
            }
        });
    }

    // Add observer to watch for message preview changes
    const messageContainer = document.getElementById('message-preview-container');
    if (messageContainer) {
        const observer = new MutationObserver(fixMessagePreviews);
        observer.observe(messageContainer, { childList: true, subtree: true });
    }

    function addUnreadBadges() {
        const previewItems = document.querySelectorAll('.message-preview');
        
        previewItems.forEach(item => {
            // Check if this item has the unread class
            if (item.classList.contains('unread')) {
                // Create badge if it doesn't exist already
                if (!item.querySelector('.unread-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-danger unread-badge';
                    badge.textContent = '1'; // Or get the actual count
                    
                    // Add to the first child's position relative container
                    const container = item.querySelector('.d-flex');
                    if (container) {
                        container.style.position = 'relative';
                        container.appendChild(badge);
                    }
                }
            }
        });
    }

    // Add this to the mutation observer
    const originalObserver = messageContainer ? document.querySelector('.message-preview-container') : null;
    if (originalObserver) {
        const enhancedObserver = new MutationObserver(() => {
            fixMessagePreviews();
            addUnreadBadges();
        });
        enhancedObserver.observe(originalObserver, { childList: true, subtree: true });
    }
        
    // Create HTML string for a notification item
    function createNotificationHTML(notification, iconClass, iconType, timeAgo, truncate) {
        return `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                 data-id="${notification.notification_id}" 
                 id="notification-${notification.notification_id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${iconType}">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="notification-content">
                        <strong>${notification.message_title || 'Notification'}</strong>
                        <p class="mb-1 ${truncate ? 'text-truncate' : ''}">${notification.message}</p>
                        <span class="notification-time">${timeAgo}</span>
                    </div>
                </div>
                ${!notification.is_read ? 
                    `<div class="notification-actions">
                        <button class="btn btn-sm btn-link mark-as-read" data-id="${notification.notification_id}">Mark as read</button>
                    </div>` 
                    : ''}
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
                // Only handle if they didn't click the mark-as-read button
                if (!e.target.closest('.mark-as-read')) {
                    const notificationId = this.getAttribute('data-id');
                    
                    // If it's unread, mark it as read
                    if (this.classList.contains('unread')) {
                        markAsRead(notificationId);
                    }
                    
                    // Store the ID to focus on when modal opens
                    selectedNotificationId = notificationId;
                    
                    // Open the modal programmatically
                    if (notificationsModal) {
                        const bsModal = new bootstrap.Modal(notificationsModal);
                        bsModal.show();
                    }
                }
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
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // Only update the UI after server confirms success
                const items = document.querySelectorAll(`.notification-item[data-id="${notificationId}"]`);
                
                items.forEach(item => {
                    // Only process if it's unread
                    if (item.classList.contains('unread')) {
                        // Remove unread class
                        item.classList.remove('unread');
                        
                        // Remove the mark-as-read button
                        const actionDiv = item.querySelector('.notification-actions');
                        if (actionDiv) {
                            actionDiv.remove();
                        }
                    }
                });
                
                // Decrement the unread count by 1 and update badge
                currentUnreadCount = Math.max(0, currentUnreadCount - 1);
                updateNotificationCount(currentUnreadCount);
            } else {
                console.error('Failed to mark notification as read');
                // Re-enable buttons but don't reload all notifications
                markAsReadButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.textContent = 'Mark as read';
                });
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
                btn.textContent = 'Updating...';
            }
        });
        
        // Then update on server first
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(markAllReadEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // Only update UI after server success
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    
                    // Remove mark as read buttons
                    const actionDiv = item.querySelector('.notification-actions');
                    if (actionDiv) {
                        actionDiv.remove();
                    }
                });
                
                // Update count to zero
                currentUnreadCount = 0;
                updateNotificationCount(0);
                
                // Re-enable buttons with original text
                markAllReadButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.tagName === 'BUTTON') {
                        btn.textContent = 'Mark all as read';
                    } else {
                        btn.textContent = 'Mark all as read';
                    }
                });
            } else {
                console.error('Failed to mark all notifications as read');
                // Re-enable buttons
                markAllReadButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.tagName === 'BUTTON') {
                        btn.textContent = 'Mark all as read';
                    } else {
                        btn.textContent = 'Mark all as read';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                if (btn.tagName === 'BUTTON') {
                    btn.textContent = 'Mark all as read';
                } else {
                    btn.textContent = 'Mark all as read';
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
                notificationItem.style.transition = 'background-color 0.5s';
                notificationItem.style.backgroundColor = '#f8f9fa';
                
                setTimeout(() => {
                    notificationItem.style.backgroundColor = '';
                    
                    // Reset selected ID after focusing
                    setTimeout(() => {
                        selectedNotificationId = null;
                        
                        // Automatically remove highlight after 3 seconds
                        setTimeout(() => {
                            notificationItem.classList.remove('highlight');
                        }, 1500);
                        
                    }, 300);
                }, 300);
            }, 100);
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
                icon = 'bi-geo-alt-fill';
                type = 'primary';
            }
            // Admin notification icons
            else if (title.includes('administrator') || 
                    (title.includes('welcome') && !title.includes('care manager'))) {
                icon = 'bi-person-badge';
                type = 'info';
            }
            // Care Manager notification icons
            else if (title.includes('care manager')) {
                icon = 'bi-people';
                type = 'info';
            }
            // Welcome back notifications
            else if (title.includes('welcome back')) {
                icon = 'bi-door-open';
                type = 'success';
            }
            // Status change notification
            else if (title.includes('status')) {
                icon = 'bi-toggle-on';
                type = 'warning';  
            }
            // Profile update notification
            else if (title.includes('profile was updated')) {
                icon = 'bi-pencil-square';
                type = 'info';
            }
            // Other existing conditions
            else if (title.includes('warning') || title.includes('assign')) {
                icon = 'bi-exclamation-triangle';
                type = 'warning';
            } else if (title.includes('success') || title.includes('approved') || title.includes('welcome')) {
                icon = 'bi-check-circle';
                type = 'success';
            } else if (title.includes('error') || title.includes('cancel') || title.includes('denied') || title.includes('deactivated')) {
                icon = 'bi-x-circle';
                type = 'danger';
            } else if (title.includes('care worker')) {
                icon = 'bi-person-lines-fill';
                type = 'info';
            } else if (title.includes('beneficiary') || title.includes('welcome to sulong kalinga')) {
                icon = 'bi-person-heart';
                type = 'success';
            } else if (title.includes('family member')) {
                icon = 'bi-people-fill';
                type = 'primary';
            } else if (title.includes('report') || title.includes('new report')) {
                icon = 'bi-file-earmark-text';
                type = 'info';
            } else if (title.includes('reminder') || title.includes('alert')) {
                icon = 'bi-alarm';
                type = 'warning';
            } else if (title.includes('message') || title.includes('chat')) {
                icon = 'bi-chat-left-text';
                type = 'info';
            } else if (title.includes('update') || title.includes('new version')) {
                icon = 'bi-arrow-repeat';
                type = 'info';
            } else if (title.includes('weekly care plan')) {
                icon = 'bi-calendar-week';
                type = 'info';
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
    
    // Helper function for message time formatting
    function timeSince(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        // Years
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + " years ago";
        if (interval === 1) return "1 year ago";
        
        // Months
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + " months ago";
        if (interval === 1) return "1 month ago";
        
        // Days
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + " days ago";
        if (interval === 1) return "1 day ago";
        
        // Hours
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + " hours ago";
        if (interval === 1) return "1 hour ago";
        
        // Minutes
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + " minutes ago";
        if (interval === 1) return "1 minute ago";
        
        return "Just now";
    }
    
    // Configure modal events
    if (notificationsModal) {
        // When the modal is shown, focus on the selected notification if any
        notificationsModal.addEventListener('shown.bs.modal', function() {
            if (selectedNotificationId) {
                focusNotificationInModal(selectedNotificationId);
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
        messagesDropdown.addEventListener('click', function() {
            loadRecentMessages();
        });
    }
    
    // Initialize
    loadUnreadMessageCount();
    loadNotifications();
    
    // Set up periodic refresh
    setInterval(loadUnreadMessageCount, 30000); // Every 30 seconds
    setInterval(loadNotifications, 60000); // Every 60 seconds
    
    // Add data-type attribute to mark-all-read buttons when DOM is loaded
    document.querySelectorAll('.message-dropdown .mark-all-read').forEach(btn => {
        btn.setAttribute('data-type', 'message');
    });
    
    document.querySelectorAll('.dropdown-notifications .mark-all-read').forEach(btn => {
        btn.setAttribute('data-type', 'notification');
    });
});
</script>
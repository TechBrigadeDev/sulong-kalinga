<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/portalNavbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/message-dropdown.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@include('components.portalNotificationScript')

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('family.dashboard') }}">
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end bg-light" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Messages Link with Dropdown -->
                <li class="nav-item dropdown me-2">
                    <a class="nav-link nav-message-link {{ Request::routeIs('care-worker.messaging.*') ? 'active' : '' }}" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span class="d-none d-md-inline">Chat with COSE</span>
                        <span class="badge bg-danger rounded-pill message-count" style="display: none;" title="Total unread messages"></span>
                    </a>
                    <!-- Messages dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end message-dropdown p-0" aria-labelledby="messagesDropdown">
                        <li class="dropdown-header">
                            <h6 class="m-0">Messages</h6>
                            <small class="mark-all-read">Mark all as read</small>
                        </li>
                        
                        <!-- Message container -->
                        <div id="message-preview-container">
                            <!-- Messages will load here -->
                        </div>
                        
                        <li class="dropdown-footer">
                            <a href="{{ route(($rolePrefix ?? (Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family')).'.messaging.index') }}" class="text-decoration-none text-primary">
                                <i class="bi bi-chat-text me-1"></i> See all messages
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-notification-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span>Notifications</span>
                        <span class="badge bg-danger rounded-pill notification-count" style="display: none;"></span>
                    </a>
                    <div class="dropdown-menu dropdown-notifications dropdown-menu-end p-0" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0">Notifications</h6>
                            <small class="mark-all-read">Mark all as read</small>
                        </div>
                        <div class="notification-list">
                            <!-- Admin Notifications -->
                        </div>
                        <div class="dropdown-footer text-center py-2">
                            <a href="#" class="text-primary view-all-notifications" data-bs-toggle="modal" data-bs-target="#notificationsModal">View all notifications</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('family.profile.*') ? 'active' : '' }}" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('family.profile.index') ? 'active' : '' }}" href="{{ route('family.profile.index') }}">Account Profile</a>
                        </li>
                        <!-- Keep the existing language toggle -->
                        <li>
                             <div class="dropdown-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-translate me-2"></i>
                                    <label for="languageToggle" class="m-0" style="cursor: pointer;" onclick="event.stopPropagation();">
                                        <span>{{ $useTagalog ? 'Tagalog' : 'Tagalog' }}</span>
                                    </label>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" id="languageToggle" style="cursor: pointer;" {{ $useTagalog ? 'checked' : '' }}>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('family.profile.settings') ? 'active' : '' }}" href="{{ route('family.profile.settings') }}">Settings</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
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
                    <!-- Notifications loaded dynamically -->
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

     // Cached messages and state management
let cachedMessages = null;
let lastValidMessages = null; // NEW: Tracks last known valid message list
let isLoadingMessages = false;
let activeDropdown = false;
let lastRenderTime = 0;
let messageLoadAttempts = 0;

// Function to load recent messages with improved rendering control
function loadRecentMessages(forceRefresh = false, showSpinner = true) {
    console.log('Load called - force:', forceRefresh, 'show spinner:', showSpinner, 'active:', activeDropdown);
    
    // Always use cache first unless forced refresh
    if (cachedMessages && !forceRefresh) {
        renderMessages(cachedMessages, false);
        return Promise.resolve(cachedMessages);
    }
    
    // Prevent concurrent requests
    if (isLoadingMessages) {
        console.log('Already loading messages, skipping additional request');
        return Promise.resolve(cachedMessages);
    }
    
    isLoadingMessages = true;
    messageLoadAttempts++;
    const loadId = messageLoadAttempts; // Track this specific load attempt
    
    // If we have valid messages already, don't show spinner - prevents flicker
    const shouldShowSpinner = showSpinner && !lastValidMessages;
    
    // Get container and possibly show spinner
    const container = document.getElementById('message-preview-container');
    if (container && shouldShowSpinner) {
        container.innerHTML = `
            <div class="spinner-container">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
    }

    // Get route prefix safely
    const routePrefix = '{{ $rolePrefix ?? (Auth::guard("beneficiary")->check() ? "beneficiary" : "family") }}';
    
    // Make AJAX request with cache busting
    console.log('Fetching messages from server, load ID:', loadId);
    return fetch(`/${routePrefix}/messaging/recent-messages?_=${Date.now()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Cache-Control': 'no-cache, no-store, must-revalidate'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response error: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Got message data for load ID:', loadId, 'count:', data?.messages?.length);
        
        // Only update cached data if we get valid message data
        if (data && data.messages) {
            cachedMessages = data;
            
            // CRITICAL: Always update the badge count
            updateMessageBadge(data.unread_count);
            
            // Only render/update the last valid messages if this load ID is relevant
            if (data.messages && data.messages.length > 0) {
                // We have valid messages, save them
                lastValidMessages = data;
                
                // Only render if dropdown is active
                if (activeDropdown) {
                    renderMessages(data, false); // Render without forcing
                }
            } else if (!lastValidMessages && activeDropdown) {
                // We have no valid messages AND we've never had any - safe to show empty state
                renderEmptyState();
            }
        }
        
        isLoadingMessages = false;
        return data;
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        isLoadingMessages = false;
        
        // Only show error if we have no cached data AND this was a visible request
        if (shouldShowSpinner && !lastValidMessages && activeDropdown) {
            if (container) {
                container.innerHTML = `
                    <div class="p-3 text-center text-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <p class="mb-0">Unable to load messages</p>
                    </div>
                `;
            }
        }
        
        return cachedMessages || { messages: [], unread_count: 0 };
    });
}

// Update badge count without updating messages
function updateMessageBadge(count) {
    const messageCount = document.querySelector('.message-count');
    if (messageCount) {
        if (count > 0) {
            messageCount.textContent = count > 99 ? '99+' : count;
            messageCount.style.display = 'inline-block';
        } else {
            messageCount.style.display = 'none';
        }
    }
}

// Render empty state only when appropriate
function renderEmptyState() {
    const container = document.getElementById('message-preview-container');
    if (!container) return;
    
    console.log('Rendering empty state');
    
    container.innerHTML = `
        <div class="p-3 text-center text-muted">
            <i class="bi bi-chat-dots"></i>
            <p class="mb-0">No messages yet</p>
        </div>
    `;
}

// Render messages with protection against "No messages" flicker
function renderMessages(data, forceRender = false) {
    if (!data || !data.messages) return;
    
    // Always update badge
    updateMessageBadge(data.unread_count);
    
    // Skip rendering if dropdown isn't active
    if (!activeDropdown) {
        console.log('Skipping render - dropdown not active');
        return;
    }
    
    // Get container
    const container = document.getElementById('message-preview-container');
    if (!container) return;
    
    // ANTI-FLICKER PROTECTION: Don't show "No messages" if we already have message items
    // unless explicitly forced to re-render
    const existingMessages = container.querySelectorAll('.message-preview-item');
    if (!forceRender && (!data.messages || data.messages.length === 0) && existingMessages.length > 0) {
        console.log('Prevented "No messages" flicker - keeping existing messages');
        return;
    }
    
    // Save timestamp to prevent race conditions
    lastRenderTime = Date.now();
    const thisRenderTime = lastRenderTime;
    
    // If no messages, show empty state
    if (!data.messages || data.messages.length === 0) {
        renderEmptyState();
        return;
    }
    
    console.log('Rendering', data.messages.length, 'messages');
    
    // Build HTML for message items
    let html = '';
    data.messages.forEach(message => {
        // Format message preview text
        let previewText = '';
        if (message.last_message.is_unsent) {
            previewText = '<span class="fst-italic text-muted">This message was unsent</span>';
        } else {
            const senderPrefix = message.is_group_chat && message.last_message.sender_name !== 'You' 
                ? `<span class="text-muted">${message.last_message.sender_name}: </span>` 
                : message.last_message.sender_name === 'You' 
                    ? '<span class="text-muted">You: </span>' 
                    : '';
            
            previewText = senderPrefix + message.last_message.content;
        }
        
        html += `
            <li class="message-preview-item ${message.has_unread ? 'unread' : ''}" data-conversation-id="${message.conversation_id}">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-person text-secondary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold">${message.name}</div>
                            <small class="text-muted ms-2">${message.last_message.time_ago}</small>
                        </div>
                        <div class="message-preview-text">
                            ${previewText}
                        </div>
                    </div>
                    ${message.unread_count > 0 ? 
                        `<div class="ms-2 unread-message-indicator">
                            <span class="badge rounded-pill bg-primary">${message.unread_count > 99 ? '99+' : message.unread_count}</span>
                        </div>` : ''}
                </div>
            </li>
        `;
    });
    
    // Ensure this render is still relevant 
    if (lastRenderTime !== thisRenderTime) {
        console.log('Skipping outdated render');
        return;
    }
    
    // Update container
    container.innerHTML = html;
    
    // Add click handlers for message items
    const routePrefix = '{{ $rolePrefix ?? (Auth::guard("beneficiary")->check() ? "beneficiary" : "family") }}';
    document.querySelectorAll('.message-preview-item').forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.dataset.conversationId;
            window.location.href = `/${routePrefix}/messaging?conversation=${conversationId}`;
        });
    });
}

// DOM ready handler with improved dropdown handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing message dropdown system');
    
    // Do a silent background load on page load
    loadRecentMessages(true, false).catch(() => {
        console.log('Initial silent message load failed');
    });
    
    // Setup polling that only runs when dropdown is closed
    setInterval(() => {
        if (!activeDropdown) {
            // Silent background update - only update badge
            loadRecentMessages(true, false).catch(() => {});
        }
    }, 60000);
    
    // Set up dropdown events
    const messagesDropdown = document.getElementById('messagesDropdown');
    if (messagesDropdown) {
        // When dropdown is about to open
        messagesDropdown.addEventListener('show.bs.dropdown', function() {
            console.log('Messages dropdown opened - pausing refresh');
            activeDropdown = true;
            
            // Show what we have immediately
            if (lastValidMessages) {
                // We have previously valid messages - show them
                renderMessages(lastValidMessages, false);
                
                // Then do a background refresh
                loadRecentMessages(true, false);
            } else if (cachedMessages) {
                // We have some cached data - show it
                renderMessages(cachedMessages, false);
                
                // Then do a background refresh
                loadRecentMessages(true, true);
            } else {
                // First time opening, show spinner and load
                loadRecentMessages(true, true);
            }
        });
        
        // When dropdown is closed
        messagesDropdown.addEventListener('hide.bs.dropdown', function() {
            console.log('Messages dropdown closed - resuming background refresh');
            activeDropdown = false;
        });
    }
    
    // "Mark all as read" handler
    document.querySelector('.mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const routePrefix = '{{ $rolePrefix ?? (Auth::guard("beneficiary")->check() ? "beneficiary" : "family") }}';
        
        // Show visual feedback
        this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Marking...';
        
        fetch(`/${routePrefix}/messaging/read-all`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({}),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(() => {
            // Reset the button text
            this.innerHTML = 'Mark all as read';
            
            // Force refresh messages
            loadRecentMessages(true, true);
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
            this.innerHTML = 'Mark all as read';
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const languageToggle = document.getElementById('languageToggle');
    if (languageToggle) {
        languageToggle.addEventListener('change', function(event) {
            // Prevent the dropdown from closing
            event.stopPropagation();
            
            const useTagalog = this.checked;
            
            // Update the label immediately
            const label = document.querySelector('label[for="languageToggle"] span');
            if (label) {
                label.textContent = useTagalog ? 'Tagalog' : 'English';
            }
            
            // Send AJAX request to update preference
            fetch('/toggle-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ use_tagalog: useTagalog })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to apply language change
                    location.reload();
                }
            })
            .catch(error => console.error('Error toggling language:', error));
        });
    }
});
</script>
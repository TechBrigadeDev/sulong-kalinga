<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="role-prefix" content="{{ $rolePrefix }}">
<script>const role_base_url = '{{ url("/".$rolePrefix) }}';</script>
    <title>Messaging - SulongKalinga</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
</head>
<body class="messaging-page">

    <!-- Navigation based on user role -->
    @if(auth()->user()->role_id == 1)
        @include('components.adminNavbar')
        @include('components.adminSidebar')
    @elseif(auth()->user()->role_id == 2)
        @include('components.careManagerNavbar')
        @include('components.careManagerSidebar')
    @elseif(auth()->user()->role_id == 3)
        @include('components.careWorkerNavbar')
        @include('components.careWorkerSidebar')
    @endif

    <!-- Main Content -->
    <main class="main-content">
        <div class="messaging-container">
            <!-- Conversation List (Left Sidebar) -->
            <div class="conversation-list">
                <!-- Search and New Chat Button -->
                <div class="conversation-search">
                    <div class="search-container">
                        <div class="search-input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="conversationSearch" placeholder="Search conversations...">
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="newChatDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="newChatDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newConversationModal">Private Message</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newGroupModal">New Group</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Conversation List Items -->
                <div class="conversation-list-items">
                    @include('admin.conversation-list', ['conversations' => $conversations])
                </div>
            </div>
            
            <!-- Message Area (Right Side) -->
            <div class="message-area">
                <div id="conversationContent">
                    <!-- Empty State / Select Conversation -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h4>Select a conversation</h4>
                        <p class="mb-4">Choose a conversation from the list or start a new one.</p>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="newChatDropdownEmpty" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg"></i> New Chat
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="newChatDropdownEmpty">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newConversationModal">Private Message</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newGroupModal">New Group</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Toggle Button for Conversation List -->
        <button class="toggle-conversation-list">
            <i class="bi bi-chat-left-text-fill"></i>
        </button>

        <!-- New Private Conversation Modal -->
        <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newConversationModalLabel">New Private Conversation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="newConversationForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="userType" class="form-label">Recipient Type</label>
                                <select class="form-select" id="userType" name="participant_type" required>
                                    <option value="" selected disabled>Select recipient type</option>
                                    <option value="cose_staff">Staff Member</option>
                                    <option value="beneficiary">Beneficiary</option>
                                    <option value="family_member">Family Member</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userSearch" class="form-label">Search for recipient</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="userSearch" placeholder="Type to search...">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="recipientSelect" class="form-label">Select Recipient</label>
                                <select class="form-select" id="recipientSelect" name="participant_id" required disabled>
                                    <option value="" selected disabled>First select a user type</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="initialMessage" class="form-label">Initial Message (Optional)</label>
                                <textarea class="form-control" id="initialMessage" name="initial_message" rows="3" placeholder="Write an initial message..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="startConversationBtn">Start Conversation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- New Group Conversation Modal -->
        <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newGroupModalLabel">Create Group Chat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="newGroupForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="groupName" class="form-label">Group Name</label>
                                <input type="text" class="form-control" id="groupName" name="name" required placeholder="Enter a name for this group">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Add Participants</label>
                                
                                <div class="participant-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="m-0">Staff Members</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-section="staff">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                    <div class="participant-list staff-list" style="display: none;">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" class="form-control user-search" data-type="cose_staff" placeholder="Search staff...">
                                        </div>
                                        <div class="user-checkboxes cose_staff-users scrollable-checklist">
                                            <!-- Staff checkboxes will be added here -->
                                            <div class="text-center p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="participant-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="m-0">Beneficiaries</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-section="beneficiaries">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                    <div class="participant-list beneficiaries-list" style="display: none;">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" class="form-control user-search" data-type="beneficiary" placeholder="Search beneficiaries...">
                                        </div>
                                        <div class="user-checkboxes beneficiary-users scrollable-checklist">
                                            <!-- Beneficiary checkboxes will be added here -->
                                            <div class="text-center p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="participant-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="m-0">Family Members</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-section="family">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                    <div class="participant-list family-list" style="display: none;">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" class="form-control user-search" data-type="family_member" placeholder="Search family members...">
                                        </div>
                                        <div class="user-checkboxes family_member-users scrollable-checklist">
                                            <!-- Family member checkboxes will be added here -->
                                            <div class="text-center p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="selectedParticipants" class="form-label">Selected Participants <span class="badge bg-primary" id="participant-count">0</span></label>
                                <div id="selectedParticipants" class="selected-participants p-2 border rounded">
                                    <div class="text-muted text-center no-participants">No participants selected</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="groupInitialMessage" class="form-label">Initial Message (Optional)</label>
                                <textarea class="form-control" id="groupInitialMessage" name="initial_message" rows="3" placeholder="Write an initial message..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="createGroupBtn">Create Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Leave Group Confirmation Modal -->
    <div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-labelledby="leaveGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveGroupModalLabel">Leave Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="leaveGroupMessage">Are you sure you want to leave this group?</p>
                    <div id="lastMemberWarning" class="alert alert-warning d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Warning:</strong> You are the last member of this group. If you leave, the group and all its messages will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLeaveBtn">Leave Group</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Group Members Modal -->
    <div class="modal fade" id="viewMembersModal" tabindex="-1" aria-labelledby="viewMembersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMembersModalLabel">Group Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="groupMembersList" class="list-group">
                        <!-- Members will be loaded dynamically here -->
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
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

    <!-- Add Group Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Add Member to Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMemberForm">
                    <div class="modal-body">
                        <input type="hidden" id="groupConversationId" name="conversation_id" value="">
                        
                        <div class="mb-3">
                            <label for="memberUserType" class="form-label">Member Type</label>
                            <select class="form-select" id="memberUserType" name="participant_type" required>
                                <option value="" selected disabled>Select member type</option>
                                <option value="cose_staff">Staff Member</option>
                                <option value="beneficiary">Beneficiary</option>
                                <option value="family_member">Family Member</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="memberSearch" class="form-label">Search for member</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="memberSearch" placeholder="Type to search...">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="memberSelect" class="form-label">Select Member</label>
                            <select class="form-select" id="memberSelect" name="participant_id" required disabled>
                                <option value="" selected disabled>First select a user type</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addMemberBtn">Add to Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Unsend Modal -->
    <div class="modal fade" id="confirmUnsendModal" tabindex="-1" aria-labelledby="confirmUnsendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmUnsendModalLabel">Confirm Unsend Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to unsend this message? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUnsendButton">Unsend Message</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // ============= GLOBAL VARIABLES AND CONFIGURATION =============
        const DEBUG = true;
        let currentLeaveGroupId = null;
        window.intentionalClear = false;

        // Timing variables to prevent conflicts
        window.lastRefreshTimestamp = 0;
        window.lastMessageSendTimestamp = 0;
        window.messageFormInitialized = false;
        window.isRefreshing = false;
        window.preventRefreshUntil = 0;
        window.lastBlurContent = '';
        window.lastBlurTime = 0;
        window.lastTypingTime = 0;

        // Debug logging function
        function debugLog(...args) {
            if (DEBUG) {
                console.log('[DEBUG]', ...args);
            }
        }

        const rolePrefix = document.querySelector('meta[name="role-prefix"]')?.getAttribute('content') || 'admin';
        window.route_prefix = rolePrefix + '.messaging';

        // Function to format message preview text
        function formatMessagePreview(lastMessage, isGroupChat, senderName) {
            // If there's no message
            if (!lastMessage) return 'No messages';
            
            // If message is unsent
            if (lastMessage.is_unsent) {
                return '<em class="text-muted">This message was unsent</em>';
            }
            
            // Add null check before accessing content
            const content = lastMessage.content || '';
            
            // Format based on chat type and sender
            let prefix = '';
            if (isGroupChat && senderName) {
                prefix = `<span class="text-muted">${senderName}: </span>`;
            }
            
            return prefix + content;
        }

        // Make sure this is run after page load
        document.addEventListener('DOMContentLoaded', function() {
            // Modify any navbar message loading functions (after page loads)
            const originalMessageLoad = window.loadRecentMessages;
            if (typeof originalMessageLoad === 'function') {
                window.loadRecentMessages = function() {
                    // Call the original function
                    const result = originalMessageLoad.apply(this, arguments);
                    
                    // Add a small delay to ensure the messages have been loaded and rendered
                    setTimeout(function() {
                        // Find all dropdown messages and check if any contain unsent messages
                        document.querySelectorAll('.dropdown-item.message-preview .small.text-truncate').forEach(preview => {
                            // Match "This message was unsent" text (case insensitive)
                            if (preview.textContent.match(/This message was unsent/i)) {
                                // Set italic style for unsent messages in dropdown
                                preview.innerHTML = '<em class="text-muted">This message was unsent</em>';
                            }
                        });
                    }, 100);
                    
                    return result;
                };
            }
        });

        function getRouteUrl(routeName) {
            // Extract just the endpoint part after the last dot
            const endpoint = routeName.replace(/^.*\./, '');
            
            // Build the full URL
            const baseUrl = `${window.location.origin}/${rolePrefix}/messaging/`;
            const fullUrl = baseUrl + endpoint;
            
            console.log(`Converting route ${routeName} to URL: ${fullUrl}`);
            return fullUrl;
        }

        let lastKnownScrollPosition = 0;
        let scrollTimeoutId = null;

        // Add this helper function right after the loadConversation function
        function logResponseDetails(response) {
            console.log('Response headers:', {
                'content-type': response.headers.get('content-type'),
                'status': response.status
            });
            
            return response.text().then(text => {
                try {
                    const json = JSON.parse(text);
                    console.log('Response parsed as JSON:', json);
                    return json;
                } catch (e) {
                    console.error('Response is not valid JSON:', text.substring(0, 500) + '...');
                    throw new Error('Invalid JSON response');
                }
            });
        }

        // Helper to determine if user is at bottom of container
        function isAtBottom(container) {
            const buffer = Math.min(150, container.clientHeight * 0.2); // 20% of container height or 150px
            return container.scrollHeight - container.scrollTop - container.clientHeight <= buffer;
        }

        // Helper to extract message HTML from content
        function extractMessagesHTML(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const messagesContainer = tempDiv.querySelector('.messages-container');
            return messagesContainer ? messagesContainer.innerHTML : '';
        }

        // Helper function to determine file icon class based on file type
        function getFileIconClass(file) {
            const fileName = file.name.toLowerCase();
            
            if (file.type.includes('pdf') || fileName.endsWith('.pdf')) {
                return 'bi-file-earmark-pdf';
            } else if (fileName.endsWith('.doc') || fileName.endsWith('.docx')) {
                return 'bi-file-earmark-word';
            } else if (fileName.endsWith('.xls') || fileName.endsWith('.xlsx')) {
                return 'bi-file-earmark-excel';
            } else if (fileName.endsWith('.txt')) {
                return 'bi-file-earmark-text';
            } else if (file.type.startsWith('image/')) {
                return 'bi-file-earmark-image';
            } else {
                return 'bi-file-earmark';
            }
        }

        function createFilePreview(file, container) {
            const filePreview = document.createElement('div');
            filePreview.className = 'file-preview';
            
            // Loading state
            const loadingContainer = document.createElement('div');
            loadingContainer.className = 'file-loading';
            loadingContainer.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';
            filePreview.appendChild(loadingContainer);
            
            // Add file name below loading spinner
            const fileName = document.createElement('div');
            fileName.className = 'file-name';
            fileName.textContent = file.name;
            filePreview.appendChild(fileName);
            
            // Add remove button
            const removeBtn = document.createElement('div');
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', function() {
                console.log('Remove button clicked - starting file removal process');
                filePreview.remove();
                
                // Create a new FileList without this file
                const dataTransfer = new DataTransfer();
                const fileInput = document.getElementById('fileUpload');
                
                // Log the current state of the file input
                console.log('Current files in input:', fileInput?.files?.length);
                
                if (fileInput && fileInput.files) {
                    Array.from(fileInput.files || []).forEach(f => {
                        if (f.name !== file.name || f.size !== file.size) {
                            dataTransfer.items.add(f);
                        }
                    });
                    
                    // Update the file input with new FileList
                    fileInput.files = dataTransfer.files;
                    console.log('Updated files in input after removal:', fileInput.files.length);
                }
                
                // Update any stored files in global storage
                const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                if (conversationId && window.savedAttachments && window.savedAttachments.has(conversationId)) {
                    const files = window.savedAttachments.get(conversationId);
                    const updatedFiles = files.filter(f => f.name !== file.name || f.size !== file.size);
                    window.savedAttachments.set(conversationId, updatedFiles);
                    console.log(`Updated stored files after removal: ${updatedFiles.length} files remaining`);
                }
                
                // CRITICAL FIX: Ensure the input can receive new files
                // We'll use a simpler approach that doesn't replace the element
                if (fileInput) {
                    // Remove all existing event listeners by cloning and replacing
                    const newFileInput = fileInput.cloneNode(true);
                    fileInput.parentNode.replaceChild(newFileInput, fileInput);
                    
                    // Add the handleFileInputChange event listener to the new input
                    newFileInput.addEventListener('change', handleFileInputChange);
                    console.log('Reinstalled file input change handler');
                    
                    // Reconnect the attachment button to the new file input
                    const attachmentBtn = document.getElementById('attachmentBtn');
                    if (attachmentBtn) {
                        // First, remove any existing listeners by cloning
                        const newAttachmentBtn = attachmentBtn.cloneNode(true);
                        attachmentBtn.parentNode.replaceChild(newAttachmentBtn, attachmentBtn);
                        
                        // Add the click event listener
                        newAttachmentBtn.addEventListener('click', function() {
                            document.getElementById('fileUpload').click();
                        });
                        console.log('Reconnected attachment button to file input');
                    }
                }
            });
            filePreview.appendChild(removeBtn);
            container.appendChild(filePreview);
            
            // Process preview based on file type
            if (file.type.startsWith('image/')) {
                const img = new Image();
                img.onload = function() {
                    loadingContainer.remove();
                    filePreview.insertBefore(img, filePreview.firstChild);
                };
                img.onerror = function() {
                    loadingContainer.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i>';
                };
                img.src = URL.createObjectURL(file);
                img.className = 'file-preview-img';
            } else {
                // For non-image files, show icon based on file type
                setTimeout(() => {
                    const iconClass = getFileIconClass(file);
                    loadingContainer.innerHTML = `<i class="bi ${iconClass} fs-2"></i>`;
                }, 500);
            }

            // Store file in global storage
            const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
            if (conversationId) {
                if (!window.savedAttachments) {
                    window.savedAttachments = new Map();
                }
                
                if (!window.savedAttachments.has(conversationId)) {
                    window.savedAttachments.set(conversationId, []);
                }
                
                const files = window.savedAttachments.get(conversationId);
                // Only add if not already there
                if (!files.some(f => f.name === file.name && f.size === file.size)) {
                    files.push(file);
                    console.log(`Stored file in window.savedAttachments: ${file.name}`);
                }
            }
        }

        function syncFilePreviewsWithInput() {
                const filePreviewContainer = document.getElementById('filePreviewContainer');
                const fileInput = document.getElementById('fileUpload');
                
                if (!filePreviewContainer || !fileInput) return;
                
                // If there are previews but no files in the input, fix the input
                const previews = filePreviewContainer.querySelectorAll('.file-preview');
                if (previews.length > 0 && (!fileInput.files || fileInput.files.length === 0)) {
                    console.log('Reconnecting file previews to file input');
                    
                    // Create a new FormData to collect files from previews
                    const dataTransfer = new DataTransfer();
                    
                    // Find saved files in our global storage
                    const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                    if (conversationId && window.savedAttachments && window.savedAttachments.has(conversationId)) {
                        const files = window.savedAttachments.get(conversationId);
                        if (files && files.length > 0) {
                            files.forEach(file => {
                                dataTransfer.items.add(file);
                            });
                            
                            // Assign the files back to the input
                            fileInput.files = dataTransfer.files;
                            console.log('Restored', dataTransfer.files.length, 'files to input');
                        }
                    }
                }
            }

        function smoothRefreshConversationList() {
            console.log('Refreshing conversation list...');
            
            // INCREASE DELAY - Give server more time to process the message
            setTimeout(() => {
                // Strong cache-busting
                const timestamp = new Date().getTime();
                const url = getRouteUrl(route_prefix + '.get-conversations') + 
                        '?nocache=' + timestamp;
                
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0',
                        'If-Modified-Since': '0'
                    }
                })
                .then(response => {
                    console.log('Conversation list response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`Error fetching conversations: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received conversation list data:', data.success);
                    
                    if (data.success && data.html) {
                        // Find the container
                        const conversationListItems = document.querySelector('.conversation-list-items');
                        if (!conversationListItems) {
                            console.error('Conversation list items container not found');
                            return;
                        }
                        
                        // Get currently active conversation ID
                        const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
                        
                        // CRITICAL FIX: Parse the new HTML before inserting
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');
                        const newContent = doc.body.innerHTML;
                        
                        // Force-update the HTML immediately
                        conversationListItems.innerHTML = newContent;
                        console.log('Updated conversation list HTML with fresh content');
                        
                        // Re-add active class to current conversation
                        if (activeConversationId) {
                            const newActiveItem = conversationListItems.querySelector(
                                `.conversation-item[data-conversation-id="${activeConversationId}"]`
                            );
                            if (newActiveItem) {
                                newActiveItem.classList.add('active');
                            }
                        }
                        
                        // Reattach click handlers
                        addConversationClickHandlers();
                    } else {
                        console.error('Invalid conversation list data received:', data);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing conversation list:', error);
                });
            }, 800); // INCREASED DELAY from 300ms to 800ms for server processing
        }

        function handleFileInputChange(event) {
            const fileInput = event.target;
            const files = fileInput.files;
            
            if (files.length === 0) return;
            
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (!filePreviewContainer) return;
            
            // Process each selected file
            for (let i = 0; i < files.length; i++) {
                if (isValidFileType(files[i])) {
                    createFilePreview(files[i], filePreviewContainer);
                } else {
                    const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    fileErrorContainer.innerHTML = 'Invalid file type. Please select images, PDFs, Word, Excel, or text files.';
                    fileErrorContainer.classList.remove('d-none');
                }
            }
        }

        // ============= INPUT PROTECTION =============
        // Override the textarea value setter to detect and prevent unwanted clearing
        window.intentionalClear = false;

        /*const originalValueSetter = Object.getOwnPropertyDescriptor(HTMLTextAreaElement.prototype, 'value').set;
        Object.defineProperty(HTMLTextAreaElement.prototype, 'value', {
            set(val) {
                // Allow clearing if it's intentional (after message send)
                if (val === '' && this.value !== '' && !window.intentionalClear && Date.now() - window.lastBlurTime < 3000) {
                    console.warn('Prevented textarea from being cleared unexpectedly');
                    window.preventRefreshUntil = Date.now() + 5000; // Prevent refresh for 5 seconds
                    return;
                }
                
                // Reset the flag after use
                if (window.intentionalClear) {
                    window.intentionalClear = false;
                }
                
                originalValueSetter.call(this, val);
            }
        });*/

        // Save content before page unload
        window.addEventListener('beforeunload', function() {
            const textarea = document.getElementById('messageContent');
            const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
            if (textarea && textarea.value.trim() !== '' && conversationId) {
                localStorage.setItem('messageContent_' + conversationId, textarea.value);
            }
        });

        // ============= UTILITY FUNCTIONS =============
        // Function to mark a conversation as read
        function markConversationAsRead(conversationId) {
            if (!conversationId) return;
            
            fetch(getRouteUrl(route_prefix + '.mark-as-read'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ conversation_id: conversationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the UI to remove unread indicators
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('unread');
                        const unreadBadge = conversationItem.querySelector('.unread-badge');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                    }
                    // Update navbar count
                    updateNavbarUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }

        // Function to update navbar unread count
        function updateNavbarUnreadCount() {
            fetch(getRouteUrl(route_prefix + '.unread-count'))
            .then(response => response.json())
            .then(data => {
                // Update the badge in navbar if it exists
                const messageCount = document.querySelector('.message-count');
                if (messageCount) {
                    if (data.count > 0) {
                        messageCount.textContent = data.count;
                        messageCount.style.display = 'inline-block';
                    } else {
                        messageCount.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error updating unread count:', error);
            });
        }

        // Helper function to extract just the messages HTML
        function extractMessagesHTML(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const messagesContainer = tempDiv.querySelector('.messages-container');
            return messagesContainer ? messagesContainer.innerHTML : '';
        }

        // Check file type validity
        function isValidFileType(file) {
            const allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain'
            ];
            
            // Check by MIME type first
            if (allowedTypes.includes(file.type)) {
                return true;
            }
            
            // Fallback to extension check for certain file types
            const fileName = file.name.toLowerCase();
            if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || 
                fileName.endsWith('.png') || fileName.endsWith('.gif') || 
                fileName.endsWith('.webp') || fileName.endsWith('.pdf') ||
                fileName.endsWith('.doc') || fileName.endsWith('.docx') ||
                fileName.endsWith('.xls') || fileName.endsWith('.xlsx') ||
                fileName.endsWith('.txt')) {
                return true;
            }
            
            return false;
        }

        // ============= CONVERSATION LIST AND LOADING =============
        // Function to add click handlers to conversation items
        let conversationClicksInitialized = false;

        function addConversationClickHandlers() {
            console.log('Adding conversation click handlers');
            
            // Use event delegation instead of attaching to each item
            const conversationListContainer = document.querySelector('.conversation-list-items');
            
            if (!conversationListContainer) {
                console.error('Conversation list container not found');
                return;
            }
            
            // Remove any existing handler
            conversationListContainer.removeEventListener('click', handleConversationListClick);
            
            // Add a single event handler to the container
            conversationListContainer.addEventListener('click', handleConversationListClick);
            
            console.log('Added conversation click handler to container');
        }

        // Add this new function to handle clicks through event delegation
        function handleConversationListClick(e) {
            // Find the closest conversation item
            const conversationItem = e.target.closest('.conversation-item');
            
            if (!conversationItem) return;
            
            console.log('Conversation clicked:', conversationItem.dataset.conversationId);
            
            // Remove active class from all items
            document.querySelectorAll('.conversation-item').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to clicked item
            conversationItem.classList.add('active');
            
            // Get conversation ID and load it
            const conversationId = conversationItem.dataset.conversationId;
            if (!conversationId) {
                console.error('Missing conversation ID on clicked item', conversationItem);
                return;
            }
            
            // Load the conversation
            loadConversation(conversationId);
            
            // Mark conversation as read
            markConversationAsRead(conversationId);
            
            // Hide conversation list on mobile after selecting
            if (window.innerWidth < 768) {
                const conversationList = document.querySelector('.conversation-list');
                if (conversationList) {
                    conversationList.classList.add('hidden');
                }
            }
        }

        // Function to load conversation
        function loadConversation(conversationId) {
            console.log('Loading conversation:', conversationId);
            
            if (!conversationId) {
                console.error('No conversation ID provided');
                return;
            }
            
            // Show loading state
            const messageArea = document.querySelector('.message-area');
            if (!messageArea) {
                console.error('Message area not found');
                return;
            }
            
            messageArea.innerHTML = `
                <div id="conversationContent" class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Generate URL with protection against malformed URLs
            const url = getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId;
            console.log('Fetching conversation from URL:', url);
            
            // Fetch conversation content via AJAX
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received conversation data successfully');
                
                if (data.success) {
                    // Update the message area with the conversation
                    messageArea.innerHTML = data.html;
                    
                    // Initialize the message form
                    window.messageFormInitialized = false;
                    initializeMessageForm(conversationId);
                    
                   // Initialize the search button with a specific delay to ensure DOM is ready
                    setTimeout(() => {
                        console.log('Initializing search after conversation load');
                        window.initializeSearchButton();
                    }, 500);
                    
                    // Scroll to bottom of messages container
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    
                    // Also update the conversation list to show this conversation as active
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        document.querySelectorAll('.conversation-item').forEach(el => {
                            el.classList.remove('active');
                        });
                        conversationItem.classList.add('active');
                    }
                    
                    // ADDED CODE: Reset search functionality when conversation changes
                    const searchContainer = document.getElementById('messageSearchContainer');
                    if (searchContainer) {
                        // Hide search bar
                        searchContainer.style.display = 'none';
                        
                        // Remove search-active class from messages container
                        const messagesContainer = document.getElementById('messagesContainer');
                        if (messagesContainer) {
                            messagesContainer.classList.remove('search-active');
                        }
                        
                        // Clear any previous search input
                        const searchInput = document.getElementById('messageSearchInput');
                        if (searchInput) {
                            searchInput.value = '';
                        }
                        
                        // Reset results counter
                        const resultsCount = document.getElementById('searchResultsCount');
                        if (resultsCount) {
                            resultsCount.textContent = '';
                            resultsCount.classList.remove('too-many');
                        }
                    }

                     // Reset search UI after loading a new conversation
                    if (typeof window.resetSearchAfterConversationLoad === 'function') {
                        window.resetSearchAfterConversationLoad();
                    }

                    setTimeout(() => {
                        initializeMessageActions();
                    }, 500);

                } else {
                    messageArea.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle-fill empty-icon"></i>
                            <h4>Error Loading Conversation</h4>
                            <p>${data.message || 'Could not load conversation. Please try again.'}</p>
                        </div>
                    `;
                    console.error('Failed to load conversation:', data.message);
                }

                // Mark conversation as read
                markConversationAsRead(conversationId);
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                messageArea.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle-fill empty-icon"></i>
                        <h4>Error Loading Conversation</h4>
                        <p>Could not load conversation. Please try again.</p>
                    </div>
                `;
            });
        }

        // ============= MESSAGE FORM HANDLING =============
        // Initialize message form
        function initializeMessageForm(conversationId, isRefresh = false) {
            const messageForm = document.getElementById('messageForm');
            if (!messageForm) return;
            
            // If already initialized and not a refresh, skip
            if (window.messageFormInitialized && !isRefresh) return;
            
            // Set flag to prevent duplicate initialization
            window.messageFormInitialized = true;
            
            // Get form elements
            const textarea = document.getElementById('messageContent');
            const fileInput = document.getElementById('fileUpload');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const attachmentBtn = document.getElementById('attachmentBtn');
            
            // Create error container if it doesn't exist
            const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();

            function createErrorContainer() {
                const fileErrorContainer = document.createElement('div');
                fileErrorContainer.id = 'fileErrorContainer';
                fileErrorContainer.className = 'file-error-container alert alert-danger d-none';
                
                const filePreviewContainer = document.getElementById('filePreviewContainer');
                if (filePreviewContainer) {
                    filePreviewContainer.parentNode.insertBefore(fileErrorContainer, filePreviewContainer);
                }
                
                return fileErrorContainer;
            }

            // Restore previous content if this is first initialization (not refresh)
            if (!isRefresh && textarea && textarea.value.trim() === '' && conversationId) {
                const savedContent = localStorage.getItem('messageContent_' + conversationId);
                if (savedContent) {
                    textarea.value = savedContent;
                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight) + 'px';
                }
            }
            
            // Auto-resize textarea
            if (textarea) {
                // Initial resize
                if (textarea.value) {
                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight) + 'px';
                }
                
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                    
                    // Save content to local storage
                    if (conversationId) {
                        localStorage.setItem('messageContent_' + conversationId, this.value);
                    }
                    
                    // Update typing timestamp
                    window.lastTypingTime = Date.now();
                });
                
                // Focus/blur protection
                textarea.addEventListener('blur', function() {
                    window.lastBlurTime = Date.now();
                    window.lastBlurContent = this.value;
                });
                
                textarea.addEventListener('focus', function() {
                    // If content was cleared unexpectedly, restore it
                    if (this.value === '' && window.lastBlurContent && 
                        Date.now() - window.lastBlurTime < 3000) {
                        console.warn('Restored textarea content after unexpected clear');
                        this.value = window.lastBlurContent;
                    }
                });
            }

            if (textarea) {
                // Clear error message when typing starts
                textarea.addEventListener('input', function() {
                    const fileErrorContainer = document.getElementById('fileErrorContainer');
                    if (fileErrorContainer && !fileErrorContainer.classList.contains('d-none')) {
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.innerHTML = '';
                    }
                });
            }
            
            // File attachment handling
            if (attachmentBtn && fileInput) {
                // Handle attachment button click
                attachmentBtn.addEventListener('click', function() {
                    fileInput.click();
                    // Clear errors when opening file picker
                    fileErrorContainer.classList.add('d-none');
                    fileErrorContainer.textContent = '';
                });
                
                if (fileInput && filePreviewContainer) {
                    fileInput.addEventListener('change', function() {
                        // Clear previous error messages
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.innerHTML = '';
                        
                        if (this.files.length === 0) return;
                        
                        // Check for maximum of 5 files
                        const existingFiles = filePreviewContainer.querySelectorAll('.file-preview').length;
                        const totalFilesAfterAdd = existingFiles + this.files.length;
                        
                        if (totalFilesAfterAdd > 5) {
                            fileErrorContainer.innerHTML = 'You can upload a maximum of 5 files at once';
                            fileErrorContainer.classList.remove('d-none');
                            return;
                        }
                        
                        const errors = [];
                        const maxFileSize = 10 * 1024 * 1024; // 10MB
                        
                        // Validate each file
                        Array.from(this.files).forEach(file => {
                            // Size validation
                            if (file.size > maxFileSize) {
                                errors.push(`File "${file.name}" exceeds the 10MB limit`);
                                return;
                            }
                            
                            // Type validation
                            if (!isValidFileType(file)) {
                                errors.push(`File "${file.name}" is not an allowed type. Please use JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, XLS, XLSX, or TXT files.`);
                                return;
                            }
                            
                            // Create preview for valid files
                            createFilePreview(file, filePreviewContainer);
                        });
                        
                        // Show errors if any
                        if (errors.length > 0) {
                            fileErrorContainer.innerHTML = errors.map(msg => `<div>${msg}</div>`).join('');
                            fileErrorContainer.classList.remove('d-none');
                        }
                        
                        // Reset the file input value to allow selecting the same file again
                        // but preserve the FileList for form submission
                        const fileList = this.files;
                        this.value = '';  
                        Object.defineProperty(this, 'files', {
                            value: fileList,
                            writable: true
                        });
                    });
                }
            }
            
            // Message form submission
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear any existing error messages
                const fileErrorContainer = document.getElementById('fileErrorContainer');
                if (fileErrorContainer) {
                    fileErrorContainer.classList.add('d-none');
                    fileErrorContainer.innerHTML = '';
                }
                
                // Get direct references to all required elements
                const textarea = document.getElementById('messageContent');
                const fileInput = document.getElementById('fileUpload');
                const filePreviewContainer = document.getElementById('filePreviewContainer');

                // Ensure global storage exists
                if (!window.savedAttachments) {
                    window.savedAttachments = new Map();
                }
                
                // Store the conversation ID
                const conversationId = document.querySelector('input[name="conversation_id"]').value;

                // Debug the current file input state
                console.log('Before sync - fileInput files:', fileInput?.files?.length || 0);

                // Basic validation
                const hasTextContent = textarea.value.trim() !== '';
                const hasFilePreview = filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0;
                
                if (!hasTextContent && !hasFilePreview) {
                    const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    errorContainer.innerHTML = 'Please enter a message or attach a file.';
                    errorContainer.classList.remove('d-none');
                    return;
                }
                
                // Show "sending" state
                const sendBtn = document.getElementById('sendMessageBtn');
                const originalBtnContent = sendBtn.innerHTML;
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                // Create a completely fresh FormData object
                const formData = new FormData();
                
                // Add form fields manually
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('conversation_id', document.querySelector('input[name="conversation_id"]').value);
                formData.append('content', textarea.value);
                
                // Add files directly from the input element without any manipulation
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    console.log(`DIRECT UPLOAD: Found ${fileInput.files.length} files`);
                    
                    for (let i = 0; i < fileInput.files.length; i++) {
                        // Log file details before appending
                        console.log(`File ${i+1}: ${fileInput.files[i].name} (${fileInput.files[i].size} bytes)`);
                        
                        // Just append with standard array notation
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }

                // Check if we have previews but no files
                if (filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0) {
                    if (!fileInput.files || fileInput.files.length === 0) {
                        console.log('Detected file previews without fileInput.files - fixing');
                        
                        // Create new Files list
                        const dataTransfer = new DataTransfer();
                        
                        // Get file objects from preview elements
                        filePreviewContainer.querySelectorAll('.file-preview').forEach(preview => {
                            const fileName = preview.querySelector('.file-name')?.textContent;
                            if (fileName) {
                                // Try to find the file in savedAttachments or recreate it
                                let foundFile = null;
                                
                                // Look in global storage
                                if (window.savedAttachments && window.savedAttachments.has(conversationId)) {
                                    const savedFiles = window.savedAttachments.get(conversationId);
                                    if (savedFiles && savedFiles.length > 0) {
                                        console.log(`Using ${savedFiles.length} files from savedAttachments`);
                                        
                                        for (let i = 0; i < savedFiles.length; i++) {
                                            // IMPORTANT: Use the exact name format Laravel expects
                                            formData.append('attachments[]', savedFiles[i]);
                                            console.log(`Directly added file to FormData: ${savedFiles[i].name} (${Math.round(savedFiles[i].size/1024)}KB)`);
                                        }
                                    }
                                } else if (fileInput && fileInput.files && fileInput.files.length > 0) {
                                    // Fall back to fileInput.files if savedAttachments doesn't have entries
                                    console.log(`Using ${fileInput.files.length} files from fileInput`);
                                    
                                    for (let i = 0; i < fileInput.files.length; i++) {
                                        formData.append('attachments[]', fileInput.files[i]);
                                        console.log(`Added file from input to FormData: ${fileInput.files[i].name} (${Math.round(fileInput.files[i].size/1024)}KB)`);
                                    }
                                }
                                
                                if (foundFile) {
                                    dataTransfer.items.add(foundFile);
                                    console.log(`Retrieved file from storage: ${foundFile.name}`);
                                }
                            }
                        });
                        
                        // If we found any files, set them to the input
                        if (dataTransfer.files.length > 0) {
                            fileInput.files = dataTransfer.files;
                            console.log(`Restored ${dataTransfer.files.length} files to input`);
                        }
                    }
                }

                // Log what we have after sync attempt
                console.log('After sync - fileInput files:', fileInput?.files?.length || 0);
                if (fileInput?.files?.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        console.log(`File ${i+1}:`, fileInput.files[i].name, fileInput.files[i].size);
                    }
                }
                
                // Log all form data to verify contents
                console.log('FINAL FORM DATA:');
                for (let pair of formData.entries()) {
                    console.log(`${pair[0]}: ${pair[1] instanceof File ? 
                        `File: ${pair[1].name} (${Math.round(pair[1].size/1024)}KB)` : 
                        pair[1].toString().substring(0, 30)}`);
                }
                
                // Make a direct fetch request with proper AJAX headers
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                        // DO NOT SET 'Content-Type' here - it will be set automatically with proper boundary
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    
                    // Check content type before parsing
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    
                    // Only parse as JSON if it's actually JSON
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // Handle HTML or other non-JSON responses
                        return response.text().then(text => {
                            console.error('Received non-JSON response:', text.substring(0, 500) + '...');
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                })
                
                .then(data => {
                    console.log('Message sent successfully:', data);
                    
                    if (data.success) {
                        // BRUTE FORCE APPROACH - Find the text area and forcefully clear it multiple ways
                        const textarea = document.getElementById('messageContent');
                        
                        console.log('Before clearing, textarea value:', textarea?.value);
                        
                        if (textarea) {
                            // Try all possible ways to clear it
                            textarea.value = '';
                            
                            
                            // Also directly modify the DOM element via innerHTML
                            textarea.innerHTML = '';
                            
                            console.log('After first clearing attempt, value is:', textarea?.value);
                            
                            // Do it again with a delay to make sure it happens
                            setTimeout(() => {
                                textarea.value = '';
                                textarea.innerHTML = '';
                                
                                // Reset height
                                if (textarea.style) {
                                    textarea.style.height = 'auto';
                                }
                                
                                // Clear any localStorage drafts
                                const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                                if (conversationId) {
                                    localStorage.removeItem('messageContent_' + conversationId);
                                }
                                
                                console.log('After delayed clearing, textarea value:', textarea?.value);
                                
                                // Clear global variables that might restore content
                                window.lastBlurContent = '';
                            }, 50);
                            
                            // NUCLEAR OPTION - Replace the entire textarea with a new one
                            setTimeout(() => {
                                if (textarea.value !== '') {
                                    console.log('Textarea still not cleared, using nuclear option');
                                    
                                    // Create a new textarea with same attributes but empty value
                                    const newTextarea = document.createElement('textarea');
                                    newTextarea.id = textarea.id;
                                    newTextarea.name = textarea.name;
                                    newTextarea.className = textarea.className;
                                    newTextarea.placeholder = textarea.placeholder;
                                    
                                    // Replace the old one
                                    textarea.parentNode.replaceChild(newTextarea, textarea);
                                    
                                    // Initialize height adjust for the new textarea
                                    newTextarea.addEventListener('input', function() {
                                        this.style.height = 'auto';
                                        this.style.height = (this.scrollHeight) + 'px';
                                    });
                                }
                            }, 100);
                
                            // 5. Clear any stored content
                            if (conversationId) {
                                localStorage.removeItem('messageContent_' + conversationId);
                            }
                            window.lastBlurContent = '';

                            // After the message is sent and processed, reinitialize the action handlers
                            setTimeout(() => {
                                addMessageActionHandlers();
                            }, 100);
                        }
                                        
                        // 2. Clear file previews if they exist
                        // Also properly reset the file input
                        fileUpload.value = '';
                        if (filePreviewContainer) {
                            filePreviewContainer.innerHTML = '';
                        }
                        
                        // 3. Clear file input by resetting value
                        // After sending a message and clearing the input, we need to reattach handlers
                        if (fileInput) {
                            // First, clear the FileList by resetting value
                            fileInput.value = '';

                            // Clone and replace the file input to completely reset it
                            const newFileInput = fileInput.cloneNode(false);
                            fileInput.parentNode.replaceChild(newFileInput, fileInput);
                            newFileInput.addEventListener('change', handleFileInputChange);

                            const attachmentBtn = document.getElementById('attachmentBtn');
                            if (attachmentBtn) {
                                // Remove any existing click handlers
                                attachmentBtn.replaceWith(attachmentBtn.cloneNode(true));
                                
                                // Get the fresh reference
                                const newAttachmentBtn = document.getElementById('attachmentBtn');
                                
                                // Add the click handler to the button
                                newAttachmentBtn.addEventListener('click', function() {
                                    document.getElementById('fileUpload').click();
                                });
                                
                                console.log('Attachment button reconnected to new file input');
                            }
                            
                            console.log('File input completely reset with new event handlers');
                        }

                        // Clear window.savedAttachments for this conversation
                        if (window.savedAttachments && window.savedAttachments.has(conversationId)) {
                            window.savedAttachments.set(conversationId, []);
                            console.log('Cleared saved attachments for conversation', conversationId);
                        }
                        
                        // 4. Reset the sending state IMMEDIATELY
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        // 5. Trigger conversation refresh with delay to ensure server processing
                        setTimeout(function() {
                            const conversationId = document.querySelector('input[name="conversation_id"]').value;
                            console.log("Refreshing conversation:", conversationId);
                            forceRefreshConversation(conversationId);
                            
                            // Also refresh the conversation list
                            setTimeout(() => {
                                smoothRefreshConversationList();
                            }, 300);
                        }, 90);
                    } else {
                        // Handle failure
                        console.error('Failed to send message:', data.error || 'Unknown error');
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                        errorContainer.innerHTML = 'Failed to send message. Please try again.';
                        errorContainer.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalBtnContent;
                    
                    const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    errorContainer.innerHTML = 'An error occurred while sending your message. Please try again.';
                    errorContainer.classList.remove('d-none');
                });
            });
        }

        // To ensure the overridden value property of textareas works correctly
        document.addEventListener('DOMContentLoaded', function() {
            // Clean up any old drafts or restore if needed
            const messageContent = document.getElementById('messageContent');
            if (messageContent) {
                // Ensure we start fresh and override any stored text
                window.intentionalClear = true;
                messageContent.value = '';
                setTimeout(() => { window.intentionalClear = false; }, 100);
            }
        });

        // ============= REFRESH FUNCTIONALITY - FIXED =============
        // Function to refresh active conversation with protection for attachments and scroll
        window.refreshActiveConversation = function() {
            // Skip if conditions prevent refresh
            if (Date.now() < window.preventRefreshUntil) {
                debugLog('Refresh prevented by time lock');
                return;
            }

            // IMPROVED CHECK: Check for file previews more reliably
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0) {
                debugLog('Refresh prevented - attachments present');
                return;
            }

            // Don't refresh if the user is actively typing
            const textarea = document.getElementById('messageContent');
            if (textarea && textarea.value.trim() && Date.now() - window.lastTypingTime < 5000) {
                debugLog('Refresh prevented - user is typing');
                return;
            }

            // Don't refresh if we just sent a message in the last 3 seconds
            if (Date.now() - window.lastMessageSendTimestamp < 3000) {
                debugLog('Refresh prevented - message recently sent');
                return;
            }

            // Don't refresh if already refreshing
            if (window.isRefreshing) {
                debugLog('Refresh prevented - already refreshing');
                return;
            }
            
            // Get active conversation
            const activeConversationItem = document.querySelector('.conversation-item.active');
            if (!activeConversationItem) return;
            
            // Set flag to prevent concurrent refreshes
            window.isRefreshing = true;
            debugLog('Starting refresh');
            
            const conversationId = activeConversationItem.dataset.conversationId;
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                window.isRefreshing = false;
                return;
            }
            
            // Save scroll state BEFORE any DOM changes
            const wasAtBottom = isAtBottom(messagesContainer);
            
            // Keep track of a message in the middle of the visible area for better anchoring
            let anchorMessage = null;
            let anchorOffsetRatio = 0;
            
            if (!wasAtBottom) {
                // Find a message to use as anchor point
                const visibleMessages = Array.from(messagesContainer.querySelectorAll('.message[data-message-id]'))
                    .filter(msg => {
                        const rect = msg.getBoundingClientRect();
                        const containerRect = messagesContainer.getBoundingClientRect();
                        return (rect.top >= containerRect.top && rect.top <= containerRect.bottom) ||
                            (rect.bottom >= containerRect.top && rect.bottom <= containerRect.bottom);
                    });
                
                if (visibleMessages.length > 0) {
                    // Use the message closest to the middle of the viewport as anchor
                    const containerMiddle = messagesContainer.getBoundingClientRect().top + messagesContainer.clientHeight / 2;
                    let closestDistance = Infinity;
                    
                    visibleMessages.forEach(msg => {
                        const msgMiddle = msg.getBoundingClientRect().top + msg.offsetHeight / 2;
                        const distance = Math.abs(msgMiddle - containerMiddle);
                        
                        if (distance < closestDistance) {
                            closestDistance = distance;
                            anchorMessage = msg;
                            
                            // Calculate ratio of message's position in viewport (0 = top, 1 = bottom)
                            const msgTop = msg.getBoundingClientRect().top - messagesContainer.getBoundingClientRect().top;
                            anchorOffsetRatio = msgTop / messagesContainer.clientHeight;
                        }
                    });
                }
            }
            
            // Save form state
            const textareaContent = textarea?.value || '';
            const fileInput = document.getElementById('fileUpload');
            const savedFiles = fileInput?.files || null;
            const filePreviewHTML = filePreviewContainer?.innerHTML || '';
            
            // Fetch updated content
            fetch(getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contentDiv = document.getElementById('conversationContent');
                    if (!contentDiv) {
                        window.isRefreshing = false;
                        return;
                    }
                    
                    // Extract and compare just the messages part
                    const currentContent = contentDiv.innerHTML;
                    const currentMessagesHTML = extractMessagesHTML(currentContent);
                    const newMessagesHTML = extractMessagesHTML(data.html);
                    
                    // Only update if content has changed
                    if (newMessagesHTML !== currentMessagesHTML) {
                        // Create a hidden clone to preload images without affecting the visible DOM
                        const tempContainer = document.createElement('div');
                        tempContainer.style.position = 'absolute';
                        tempContainer.style.left = '-9999px';
                        tempContainer.style.visibility = 'hidden';
                        tempContainer.innerHTML = data.html;
                        document.body.appendChild(tempContainer);
                        
                        // Store anchorMessage ID before DOM update
                        const anchorMessageId = anchorMessage ? anchorMessage.dataset.messageId : null;
                        
                        // Preload all images in the temp container
                        const imagesToPreload = tempContainer.querySelectorAll('img');
                        let loadedImages = 0;
                        const totalImages = imagesToPreload.length;
                        
                        const preloadComplete = function() {
                            debugLog('Preload complete, updating DOM');
                            
                            // Replace content with the preloaded version
                            contentDiv.innerHTML = tempContainer.innerHTML;
                            document.body.removeChild(tempContainer);
                            
                            // Restore form state
                            if (textarea && textareaContent) {
                                textarea.value = textareaContent;
                                textarea.style.height = 'auto';
                                textarea.style.height = (textarea.scrollHeight) + 'px';
                            }
                            
                            // Restore file input
                            if (savedFiles && savedFiles.length > 0) {
                                const fileInput = document.getElementById('fileUpload');
                                if (fileInput) {
                                    Object.defineProperty(fileInput, 'files', {
                                        value: savedFiles,
                                        writable: true
                                    });
                                }
                            }
                            
                            // Restore preview container
                            const filePreviewContainer = document.getElementById('filePreviewContainer');
                            if (filePreviewContainer && filePreviewHTML) {
                                filePreviewContainer.innerHTML = filePreviewHTML;
                                
                                // Reattach event listeners
                                filePreviewContainer.querySelectorAll('.remove-file').forEach(button => {
                                    button.addEventListener('click', function() {
                                        button.closest('.file-preview').remove();
                                    });
                                });
                            }
                            
                            // Reset message form initialization
                            window.messageFormInitialized = false;
                            initializeMessageForm(conversationId, true);
                            
                            // Reset scroll position AFTER DOM update
                            const newMessagesContainer = document.getElementById('messagesContainer');
                            if (newMessagesContainer) {
                                if (wasAtBottom) {
                                    // If user was at bottom, scroll to bottom
                                    newMessagesContainer.scrollTop = newMessagesContainer.scrollHeight;
                                    debugLog('Restoring scroll: to bottom');
                                } else if (anchorMessageId) {
                                    // Find the anchor message in the new DOM
                                    const newAnchorMessage = newMessagesContainer.querySelector(`.message[data-message-id="${anchorMessageId}"]`);
                                    if (newAnchorMessage) {
                                        // Calculate the new position to maintain the same relative view
                                        const newScrollTop = newAnchorMessage.offsetTop - (anchorOffsetRatio * newMessagesContainer.clientHeight);
                                        newMessagesContainer.scrollTop = newScrollTop;
                                        debugLog('Restoring scroll: to anchor message');
                                    }
                                }
                            }
                            
                            markConversationAsRead(conversationId);
                            updateNavbarUnreadCount();
                            window.isRefreshing = false;
                        };
                        
                        // If no images, update immediately
                        if (totalImages === 0) {
                            preloadComplete();
                        } else {
                            // Preload images with timeout
                            const imageTimeout = setTimeout(() => {
                                if (window.isRefreshing) {
                                    debugLog('Image preload timed out');
                                    preloadComplete();
                                }
                            }, 2000);
                            
                            // Preload each image
                            imagesToPreload.forEach(img => {
                                // Show images in the temp container
                                if (img.style.display === 'none') {
                                    img.style.display = 'block';
                                }
                                
                                const tempImg = new Image();
                                tempImg.onload = tempImg.onerror = function() {
                                    loadedImages++;
                                    if (loadedImages >= totalImages && window.isRefreshing) {
                                        clearTimeout(imageTimeout);
                                        preloadComplete();
                                    }
                                };
                                tempImg.src = img.src;
                            });
                        }
                    } else {
                        // No changes in content
                        debugLog('No changes in content, skipping update');
                        window.isRefreshing = false;
                    }
                } else {
                    window.isRefreshing = false;
                }
            })
            .catch(error => {
                console.error('Error refreshing conversation:', error);
                window.isRefreshing = false;
            });
        };

        // Add these helper functions
        function isAtBottom(container) {
            return container.scrollHeight - container.scrollTop <= container.clientHeight + 150;
        }

        function getVisibleMessages(container) {
            const messages = container.querySelectorAll('.message');
            const result = [];
            
            // Get container bounds
            const containerTop = container.scrollTop;
            const containerBottom = containerTop + container.clientHeight;
            
            // Find messages that are fully or partially visible
            messages.forEach(message => {
                const rect = message.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                const messageTop = rect.top - containerRect.top + container.scrollTop;
                const messageBottom = messageTop + rect.height;
                
                // If message is visible
                if ((messageTop >= containerTop && messageTop <= containerBottom) ||
                    (messageBottom >= containerTop && messageBottom <= containerBottom)) {
                    
                    // Store message ID and its position relative to viewport
                    result.push({
                        id: message.dataset.messageId,
                        position: (messageTop - containerTop) / container.clientHeight
                    });
                }
            });
            
            return result;
        }

        function restoreScrollToMessage(container, visibleMessages) {
            if (visibleMessages.length === 0) return;
            
            // Try to find the anchor message with the most centered position
            let bestMatch = null;
            let closestPosition = 1;
            
            visibleMessages.forEach(msg => {
                // Find the closest message to the center of the previous view
                const distance = Math.abs(msg.position - 0.5);
                if (distance < closestPosition) {
                    closestPosition = distance;
                    bestMatch = msg;
                }
            });
            
            if (!bestMatch) return;
            
            // Find the message element
            const messageElement = container.querySelector(`.message[data-message-id="${bestMatch.id}"]`);
            if (!messageElement) return;
            
            // Calculate where to scroll
            const rect = messageElement.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            const messageTop = rect.top - containerRect.top + container.scrollTop;
            
            // Set scroll position to align same message at same relative position
            const newScrollTop = messageTop - (bestMatch.position * container.clientHeight);
            container.scrollTop = newScrollTop;
        }

        function restoreFormState(textareaContent, savedFiles, filePreviewHTML) {
            // Restore textarea
            const textarea = document.getElementById('messageContent');
            if (textarea && textareaContent) {
                textarea.value = textareaContent;
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            }
            
            // Restore file input
            if (savedFiles && savedFiles.length > 0) {
                const fileInput = document.getElementById('fileUpload');
                if (fileInput) {
                    Object.defineProperty(fileInput, 'files', {
                        value: savedFiles,
                        writable: true
                    });
                }
            }
            
            // Restore preview container
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (filePreviewContainer && filePreviewHTML) {
                filePreviewContainer.innerHTML = filePreviewHTML;
                
                // Reattach event listeners
                filePreviewContainer.querySelectorAll('.remove-file').forEach(button => {
                    button.addEventListener('click', function() {
                        button.closest('.file-preview').remove();
                    });
                });
            }
        }

        // CRITICAL FIX: Preload all images to prevent layout shifts
        function preloadImages(container) {
            return new Promise(resolve => {
                const images = container.querySelectorAll('img[style="display: none;"]');
                if (images.length === 0) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                let resolvedAlready = false;
                
                // Set a timeout to resolve anyway after 2 seconds
                const timeout = setTimeout(() => {
                    if (!resolvedAlready) {
                        resolvedAlready = true;
                        resolve();
                    }
                }, 2000);
                
                // Track image loading
                images.forEach(img => {
                    // Create a temporary image to load in background
                    const tempImg = new Image();
                    tempImg.onload = function() {
                        loadedCount++;
                        
                        // When this image loads, update the actual image
                        const actualImg = document.getElementById(img.id);
                        if (actualImg) {
                            actualImg.style.display = 'block';
                            const loadingId = actualImg.id.replace('img-', 'loading-');
                            const loadingEl = document.getElementById(loadingId);
                            if (loadingEl) {
                                loadingEl.style.display = 'none';
                            }
                        }
                        
                        // If all images loaded, resolve
                        if (loadedCount === images.length && !resolvedAlready) {
                            clearTimeout(timeout);
                            resolvedAlready = true;
                            resolve();
                        }
                    };
                    
                    tempImg.onerror = function() {
                        loadedCount++;
                        if (loadedCount === images.length && !resolvedAlready) {
                            clearTimeout(timeout);
                            resolvedAlready = true;
                            resolve();
                        }
                    };
                    
                    // Start loading
                    if (img.src) {
                        tempImg.src = img.src;
                    } else {
                        loadedCount++;
                    }
                });
            });
        }



        // Add scroll event listener to detect user interaction
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('scroll', function(e) {
                if (e.target.id === 'messagesContainer') {
                    lastKnownScrollPosition = e.target.scrollTop;
                    if (scrollTimeoutId) {
                        clearTimeout(scrollTimeoutId);
                    }
                    scrollTimeoutId = setTimeout(() => {
                        scrollTimeoutId = null;
                    }, 1000);
                }
            }, true);
        });

        // ============= DOCUMENT READY INITIALIZATION =============
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('DOM content loaded, initializing messaging...');
            
            // Set up minimized sidebar
            function setupMinimizedSidebar() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.classList.contains('close')) {
                    sidebar.classList.add('close');
                }
            }
            
            // Mobile view setup - CRITICAL FIX
            function setupMobileView() {
                const toggleBtn = document.querySelector('.toggle-conversation-list');
                const conversationList = document.querySelector('.conversation-list');
                
                if (toggleBtn && conversationList) {
                    toggleBtn.addEventListener('click', function() {
                        conversationList.classList.toggle('hidden');
                    });
                    
                    // Initialize hidden state on mobile
                    if (window.innerWidth < 768) {
                        conversationList.classList.add('hidden');
                        toggleBtn.style.display = 'flex';
                    } else {
                        toggleBtn.style.display = 'none';
                    }
                    
                    // Update on resize
                    window.addEventListener('resize', function() {
                        if (window.innerWidth < 768) {
                            toggleBtn.style.display = 'flex';
                        } else {
                            toggleBtn.style.display = 'none';
                            conversationList.classList.remove('hidden');
                        }
                    });
                }
            }
            
            // Initialize leave group functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.leave-group-btn')) {
                    const btn = e.target.closest('.leave-group-btn');
                    currentLeaveGroupId = btn.dataset.conversationId;
                }
            });
            
            document.getElementById('confirmLeaveGroup')?.addEventListener('click', function() {
                if (!currentLeaveGroupId) return;
                
                fetch(getRouteUrl(route_prefix + '.leave-group'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ conversation_id: currentLeaveGroupId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = role_base_url + '/messaging';
                    } else {
                        alert(data.message || 'An error occurred while leaving the group.');
                    }
                })
                .catch(error => {
                    console.error('Error leaving group:', error);
                    alert('An error occurred while leaving the group.');
                });
            });
            
            // New conversation form handler
            document.getElementById('newConversationForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(getRouteUrl(route_prefix + '.create-conversation'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = role_base_url + '/messaging?conversation=' + data.conversation_id;
                    }
                })
                .catch(error => {
                    console.error('Error creating conversation:', error);
                });
            });
            
            // New group form handler
            document.getElementById('newGroupForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(getRouteUrl(route_prefix + '.create-group'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = role_base_url + '/messaging?conversation=' + data.conversation_id;
                    }
                })
                .catch(error => {
                    console.error('Error creating group:', error);
                });
            });
            
            // Setup conversation list search
            const searchInput = document.getElementById('conversationSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    let found = false;
                    
                    conversationItems.forEach(item => {
                        const participantNames = item.dataset.participantNames?.toLowerCase() || '';
                        const groupParticipants = item.dataset.groupParticipants?.toLowerCase() || '';
                        const previewText = item.querySelector('.conversation-preview')?.textContent.toLowerCase() || '';
                        
                        if (participantNames.includes(searchTerm) || 
                            groupParticipants.includes(searchTerm) || 
                            previewText.includes(searchTerm)) {
                            item.style.display = '';
                            found = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Show/hide no results message
                    const noResults = document.getElementById('noSearchResults');
                    if (noResults) {
                        if (!found && searchTerm.length > 0) {
                            noResults.style.display = 'block';
                        } else {
                            noResults.style.display = 'none';
                        }
                    }
                });
            }
            
            // Set up minimized sidebar
            setupMinimizedSidebar();
            
            // Set up mobile view
            setupMobileView();
            
            // Add click handlers to conversation items
            addConversationClickHandlers();
            
            // Initial update of unread count
            updateNavbarUnreadCount();
            
            // Handle selected conversation from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const selectedConversationId = urlParams.get('conversation');

            if (selectedConversationId) {
                console.log('Found conversation ID in URL:', selectedConversationId);
                
                // More aggressive retry strategy with multiple attempts
                let maxRetries = 10; // Try up to 10 times (total of ~5 seconds)
                let retryCount = 0;
                let retryDelay = 200; // Start with 200ms, then increase
                
                function attemptLoadConversation() {
                    console.log(`Attempt ${retryCount+1} to find conversation ${selectedConversationId}`);
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${selectedConversationId}"]`);
                    
                    if (conversationItem) {
                        console.log('Found conversation element, selecting it');
                        
                        // Remove active class from all items
                        document.querySelectorAll('.conversation-item').forEach(el => {
                            el.classList.remove('active');
                        });
                        
                        // Add active class
                        conversationItem.classList.add('active');
                        
                        // Manually trigger the conversation load
                        loadConversation(selectedConversationId);
                        
                        // Mark as read
                        markConversationAsRead(selectedConversationId);
                        
                        // Clean URL without reloading page
                        window.history.replaceState({}, document.title, window.location.pathname);
                        return true;
                    }
                    
                    if (retryCount < maxRetries) {
                        retryCount++;
                        retryDelay = Math.min(retryDelay * 1.5, 1000); // Increase delay but cap at 1 second
                        
                        console.log(`Conversation not found yet, retrying in ${retryDelay}ms (attempt ${retryCount}/${maxRetries})`);
                        
                        setTimeout(attemptLoadConversation, retryDelay);
                    } else {
                        console.log('Max retries reached, loading conversation directly via AJAX');
                        // Direct load as last resort
                        loadConversation(selectedConversationId);
                        
                        // Clean URL without reloading page
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                    
                    return false;
                }
                
                // Start the retry process
                attemptLoadConversation();
            }
            
            // Track typing activity
            document.body.addEventListener('keydown', function() {
                window.lastTypingTime = Date.now();
            });
            
            // Set up automatic refresh
            setInterval(function() {
                window.refreshActiveConversation();
            }, 8000); // Every 8 seconds
        });

        // Add this CSS to fix the spinner animation
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation styles if not already present
            if (!document.getElementById('spinner-animation-styles')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'spinner-animation-styles';
                styleEl.textContent = `
                    @keyframes spinner-border {
                        to { transform: rotate(360deg); }
                    }
                    
                    .spinner-border {
                        display: inline-block;
                        width: 2rem;
                        height: 2rem;
                        vertical-align: text-bottom;
                        border: 0.25em solid currentColor;
                        border-right-color: transparent;
                        border-radius: 50%;
                        animation: spinner-border 0.75s linear infinite;
                    }
                    
                    .spinner-border-sm {
                        width: 1rem;
                        height: 1rem;
                        border-width: 0.2em;
                    }
                    
                    @keyframes pulse {
                        0% { opacity: 0.6; }
                        50% { opacity: 1; }
                        100% { opacity: 0.6; }
                    }
                    
                    .loading-pulse {
                        animation: pulse 1.5s infinite ease-in-out;
                    }

                    /* Add transition for smooth opacity changes */
                    .messages-container {
                        transition: opacity 0.05s ease;
                    }
                    
                    /* Class for hiding during refresh */
                    .updating-content {
                        opacity: 0;
                    }
                `;
                document.head.appendChild(styleEl);
            }
        });

        // Add this function after your message form initialization
        function appendNewMessage(message) {
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            // Only append if container exists
            const isCurrentUserSender = message.sender_id == {{ auth()->id() }} && 
                                        message.sender_type == 'cose_staff';
            
            // Create a temporary wrapper to hold the HTML
            const temp = document.createElement('div');
            
            // Format the timestamp
            const timestamp = new Date(message.message_timestamp);
            const timeString = timestamp.toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});
            
            // Create the message HTML
            temp.innerHTML = `
                <div class="message ${isCurrentUserSender ? 'outgoing' : 'incoming'}" data-message-id="${message.message_id}">
                    <div class="message-content">
                        ${message.content || ''}
                        ${message.attachments && message.attachments.length > 0 ? 
                            '<div class="message-attachments"><small>Attachments will appear after refresh...</small></div>' : ''}
                    </div>
                    <div class="message-time">
                        <small>${timeString}</small>
                    </div>
                </div>
            `;
            
            // Append the new message
            messagesContainer.appendChild(temp.firstChild);
            
            // Scroll to bottom to show the new message
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            // After the message is added to DOM, initialize action handlers
            setTimeout(() => {
                addMessageActionHandlers();
            }, 100);
        }

        // Add this function to force a clean, non-flashing refresh
        function forceRefreshConversation(conversationId) {
            if (!conversationId) return;
            
            const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
            if (activeConversationId !== conversationId) return;
            
            // Add a timestamp parameter to prevent caching
            const cacheBuster = '&_=' + new Date().getTime();
            fetch(getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId + cacheBuster, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Create an off-screen div to prepare the content
                    const tempDiv = document.createElement('div');
                    tempDiv.style.position = 'absolute';
                    tempDiv.style.left = '-9999px';
                    tempDiv.style.visibility = 'hidden';
                    tempDiv.innerHTML = data.html;
                    document.body.appendChild(tempDiv);
                    
                    // Get references to current containers
                    const currentMessagesContainer = document.getElementById('messagesContainer');
                    if (!currentMessagesContainer) {
                        document.body.removeChild(tempDiv);
                        return;
                    }
                    
                    // Keep scroll position at bottom
                    const wasAtBottom = isAtBottom(currentMessagesContainer);
                    
                    // Preload all images in the temp container
                    const images = tempDiv.querySelectorAll('img');
                    let loadedImages = 0;
                    const totalImages = images.length;
                                        
                    const updateContent = function() {
                        const newMessagesContainer = tempDiv.querySelector('.messages-container');
                        if (newMessagesContainer) {
                            // Instead of replacing the whole container, just update its content
                            const currentMessagesContainer = document.getElementById('messagesContainer');
                            if (currentMessagesContainer) {
                                // Keep scroll position at bottom
                                const wasAtBottom = isAtBottom(currentMessagesContainer);
                                
                                // Update content
                                currentMessagesContainer.innerHTML = newMessagesContainer.innerHTML;
                                
                                // Restore scroll position
                                if (wasAtBottom) {
                                    currentMessagesContainer.scrollTop = currentMessagesContainer.scrollHeight;
                                }
                            }
                        }
                        
                        // Clean up
                        document.body.removeChild(tempDiv);
                    };
                    
                    // If no images, update immediately
                    if (totalImages === 0) {
                        updateContent();
                    } else {
                        // Set a timeout to ensure we don't wait forever
                        const timeout = setTimeout(() => {
                            updateContent();
                        }, 2000);
                        
                        // Preload each image
                        images.forEach(img => {
                            const tempImg = new Image();
                            tempImg.onload = tempImg.onerror = function() {
                                loadedImages++;
                                if (loadedImages >= totalImages) {
                                    clearTimeout(timeout);
                                    updateContent();
                                }
                            };
                            tempImg.src = img.src;
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing conversation after sending:', error);
            });
        }

        // Private Conversation Modal Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeSelect = document.getElementById('userType');
            const userSearch = document.getElementById('userSearch');
            const recipientSelect = document.getElementById('recipientSelect');
            const newConversationForm = document.getElementById('newConversationForm');
            
            // Handle user type selection
            userTypeSelect?.addEventListener('change', function() {
                const userType = this.value;
                console.log('Selected recipient type:', userType);
                
                // Show loading state
                if (recipientSelect) {
                    recipientSelect.innerHTML = '<option value="" selected disabled>Loading users...</option>';
                    recipientSelect.disabled = true;
                }
                
                // Construct URL with direct path to ensure it works
                const url = `${window.location.origin}/${rolePrefix}/messaging/get-users?type=${userType}`;
                console.log('Fetching users from:', url);
                
                // Use existing endpoint with the current route prefix
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received user data:', data);
                    
                    if (recipientSelect) {
                        // Handle multiple possible response formats
                        let usersArray = null;
                        
                        if (data.users && Array.isArray(data.users)) {
                            // Standard format: { users: [...] }
                            usersArray = data.users;
                        } else if (Array.isArray(data)) {
                            // Alternative format: direct array
                            usersArray = data;
                        } else if (data.data && Array.isArray(data.data)) {
                            // Another possible format: { data: [...] }
                            usersArray = data.data;
                        }
                        
                        if (usersArray && usersArray.length > 0) {
                            console.log('Found', usersArray.length, 'users');
                            
                            // Add users to options array for filtering
                            window.userOptions = usersArray;
                            
                            // Enable select and update UI
                            recipientSelect.disabled = false;
                            
                            // Initial rendering of all options
                            updateRecipientOptions('');
                        } else {
                            console.warn('No users found in response');
                            recipientSelect.innerHTML = '<option value="" selected disabled>No users found</option>';
                            recipientSelect.disabled = true;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    if (recipientSelect) {
                        recipientSelect.innerHTML = '<option value="" selected disabled>Error loading users: ' + error.message + '</option>';
                        recipientSelect.disabled = true;
                    }
                });


            });
            
            // Function to update options based on search text
            function updateRecipientOptions(searchTerm) {
                if (!recipientSelect || !window.userOptions) return;
                
                // Clear existing options
                recipientSelect.innerHTML = '';
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                defaultOption.textContent = searchTerm ? 'Search results' : 'Select a recipient';
                recipientSelect.appendChild(defaultOption);
                
                // Filter users based on search term
                const filteredUsers = window.userOptions.filter(user => {
                    return searchTerm === '' || 
                        user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                        (user.email && user.email.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (user.mobile && user.mobile.includes(searchTerm));
                });
                
                // Add filtered users to dropdown
                filteredUsers.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    recipientSelect.appendChild(option);
                });
                
                // Update dropdown visibility
                if (searchTerm && filteredUsers.length > 0) {
                    // Make dropdown visible by setting size, but don't change focus
                    recipientSelect.size = Math.min(10, filteredUsers.length + 1);
                    recipientSelect.classList.add('active-dropdown');
                    recipientSelect.setAttribute('data-dropdown-visible', 'true');
                } else {
                    recipientSelect.size = 1;
                    recipientSelect.classList.remove('active-dropdown');
                    recipientSelect.removeAttribute('data-dropdown-visible');
                }
            }

            // Add this new code directly after the updateRecipientOptions function:

            // Ensure recipientSelect handles click events properly
            if (recipientSelect) {
                recipientSelect.addEventListener('click', function(e) {
                    if (e.target.tagName === 'OPTION' && e.target.value) {
                        // Set the selected value
                        this.value = e.target.value;
                        
                        // Reset display after selection
                        this.size = 1;
                        this.classList.remove('active-dropdown');
                        this.removeAttribute('data-dropdown-visible');
                        
                        // Clear the search field
                        if (userSearch) {
                            userSearch.value = '';
                        }
                    }
                });
                
                // Close dropdown when clicking elsewhere
                document.addEventListener('click', function(e) {
                    if (recipientSelect && !recipientSelect.contains(e.target) && !userSearch.contains(e.target)) {
                        recipientSelect.size = 1;
                        recipientSelect.classList.remove('active-dropdown');
                        recipientSelect.removeAttribute('data-dropdown-visible');
                    }
                });
            }
            
            // Handle real-time search filtering
            userSearch?.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                updateRecipientOptions(searchTerm);
            });
            
            // Handle form submission
            newConversationForm?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = document.getElementById('startConversationBtn');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
                }
                
                // Get form data
                const userType = userTypeSelect?.value;
                const userId = recipientSelect?.value;
                const initialMessage = document.getElementById('initialMessage')?.value;
                
                // Validate required fields
                if (!userType || !userId) {
                    alert('Please select both user type and recipient');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Start Conversation';
                    }
                    return;
                }
                
                // Create FormData object
                const formData = new FormData();
                formData.append('participant_type', userType);
                formData.append('participant_id', userId);
                formData.append('initial_message', initialMessage || '');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Submit the form
                fetch(`${window.location.origin}/${rolePrefix}/messaging/create-conversation`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Add this line to ensure proper AJAX handling
                        'Accept': 'application/json'          // Add this line to request JSON response
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.headers.get('content-type')?.includes('text/html')) {
                            // Handle HTML error response
                            return response.text().then(text => {
                                throw new Error('Server returned HTML error page instead of JSON');
                            });
                        }
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
                        if (modal) modal.hide();
                        
                        // Redirect to the new conversation
                        window.location.href = `${window.location.origin}/${rolePrefix}/messaging/conversation/${data.conversation_id}`;
                    } else {
                        throw new Error(data.message || 'Failed to create conversation');
                    }
                })
                .catch(error => {
                    console.error('Error creating conversation:', error);
                    alert('Failed to create conversation: ' + error.message);
                    
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Start Conversation';
                    }
                });
            });

            // Create container for feedback messages in the modal
            const feedbackContainer = document.createElement('div');
            feedbackContainer.id = 'conversationFormFeedback';
            feedbackContainer.className = 'mb-3 d-none';
            feedbackContainer.innerHTML = '<div class="alert alert-info mb-0"></div>';

            // Insert feedback container before the modal footer
            const modalBody = document.querySelector('#newConversationModal .modal-body');
            const modalFooter = document.querySelector('#newConversationModal .modal-footer');
            if (modalBody && modalFooter) {
                modalBody.appendChild(feedbackContainer);
            }

            // Add event listener to recipient select to check for existing conversations
            recipientSelect?.addEventListener('change', function() {
                const userId = this.value;
                const userType = userTypeSelect?.value;
                
                if (!userId || !userType) return;
                
                // Show loading state
                feedbackContainer.classList.remove('d-none');
                feedbackContainer.innerHTML = `
                    <div class="alert alert-info mb-0 d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <div>Checking for existing conversations...</div>
                    </div>
                `;
                
                // Perform check by attempting to find conversations with this user
                fetch(`${window.location.origin}/${rolePrefix}/messaging/get-conversations-with-recipient`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        participant_type: userType,
                        participant_id: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // Show "conversation exists" message
                        feedbackContainer.innerHTML = `
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                A conversation with this recipient already exists.
                                <a href="${window.location.origin}/${rolePrefix}/messaging?conversation=${data.conversation_id}" 
                                class="alert-link ms-2" id="goToExistingConversation">
                                Go to conversation
                                </a>
                            </div>
                        `;
                        
                        // Change button text to indicate going to existing conversation
                        const submitButton = document.getElementById('startConversationBtn');
                        if (submitButton) {
                            submitButton.textContent = 'Go to Existing Conversation';
                            submitButton.dataset.conversationId = data.conversation_id;
                            submitButton.classList.remove('btn-primary');
                            submitButton.classList.add('btn-info');
                        }
                    } else {
                        // Hide feedback if no existing conversation
                        feedbackContainer.classList.add('d-none');
                        
                        // Reset button
                        const submitButton = document.getElementById('startConversationBtn');
                        if (submitButton) {
                            submitButton.textContent = 'Start Conversation';
                            delete submitButton.dataset.conversationId;
                            submitButton.classList.remove('btn-info');
                            submitButton.classList.add('btn-primary');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking for existing conversation:', error);
                    feedbackContainer.classList.add('d-none');
                });
            });

            // Update the form submission handler to handle existing conversations
            const originalSubmitHandler = newConversationForm.onsubmit;
            newConversationForm.onsubmit = function(e) {
                e.preventDefault();
                
                const submitButton = document.getElementById('startConversationBtn');
                
                // If we're going to an existing conversation, redirect instead of submitting
                if (submitButton && submitButton.dataset.conversationId) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
                    if (modal) modal.hide();
                    
                    // Navigate to existing conversation
                    window.location.href = `${window.location.origin}/${rolePrefix}/messaging?conversation=${submitButton.dataset.conversationId}`;
                    return;
                }
                
                // Otherwise proceed with normal form submission
                if (originalSubmitHandler) {
                    originalSubmitHandler.call(this, e);
                }
            };
        });

        // Group Conversation Modal Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const newGroupForm = document.getElementById('newGroupForm');
            const selectedParticipantsContainer = document.getElementById('selectedParticipants');
            const participantCountBadge = document.getElementById('participant-count');
            let selectedParticipants = []; // Array to store selected participants
            
            // Toggle participant sections
            document.querySelectorAll('.toggle-section').forEach(button => {
                button.addEventListener('click', function() {
                    const section = this.getAttribute('data-section');
                    const listElement = document.querySelector(`.${section}-list`);
                    
                    if (listElement.style.display === 'none') {
                        // Show section and load users if not already loaded
                        listElement.style.display = 'block';
                        button.innerHTML = '<i class="bi bi-dash"></i> Hide';
                        
                        // Load users if they haven't been loaded yet
                        const userContainer = listElement.querySelector('.user-checkboxes');
                        const userType = getUserTypeFromSection(section);
                        
                        if (userContainer && !userContainer.dataset.loaded) {
                            loadUsers(userType, userContainer);
                        }
                    } else {
                        // Hide section
                        listElement.style.display = 'none';
                        button.innerHTML = '<i class="bi bi-plus"></i> Add';
                    }
                });
            });
            
            // Function to get user type from section name
            function getUserTypeFromSection(section) {
                switch (section) {
                    case 'staff': return 'cose_staff';
                    case 'beneficiaries': return 'beneficiary';
                    case 'family': return 'family_member';
                    default: return 'cose_staff';
                }
            }
            
            // Load users for a specific type
            function loadUsers(userType, container) {
                // Show loading indicator
                container.innerHTML = `
                    <div class="text-center p-2">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;
                
                // Load users from server
                fetch(`${window.location.origin}/${rolePrefix}/messaging/get-users?type=${userType}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';
                    
                    if (data.users && data.users.length > 0) {
                        // Store users for filtering
                        window[`${userType}Users`] = data.users;
                        
                        renderFilteredUsers(userType, data.users, container);
                        
                        // Mark as loaded
                        container.dataset.loaded = 'true';
                    } else {
                        container.innerHTML = '<div class="text-muted text-center p-3">No users available</div>';
                    }
                })
                .catch(error => {
                    console.error(`Error loading ${userType} users:`, error);
                    container.innerHTML = '<div class="text-danger text-center p-3">Error loading users</div>';
                });
            }
            
            // Render filtered users
            function renderFilteredUsers(userType, users, container, searchTerm = '') {
                // Clear container
                container.innerHTML = '';
                
                // Filter users if search term provided
                const filteredUsers = searchTerm ? 
                    users.filter(user => user.name.toLowerCase().includes(searchTerm.toLowerCase())) :
                    users;
                
                if (filteredUsers.length === 0) {
                    container.innerHTML = `<div class="text-muted text-center p-3">No ${userType.replace('_', ' ')} match "${searchTerm}"</div>`;
                    return;
                }
                
                // Skip the current user for staff section
                filteredUsers.forEach(user => {
                    // Skip the current user for staff section
                    if (userType === 'cose_staff' && user.id === {{ Auth::id() }}) {
                        return;
                    }
                    
                    const checkbox = document.createElement('div');
                    checkbox.className = 'form-check user-checkbox';
                    
                    // Check if user is already selected
                    const isSelected = selectedParticipants.some(p => p.id === user.id && p.type === userType);
                    
                    checkbox.innerHTML = `
                        <input class="form-check-input" type="checkbox" 
                            id="${userType}_${user.id}" 
                            data-id="${user.id}" 
                            data-type="${userType}" 
                            data-name="${user.name}"
                            ${isSelected ? 'checked' : ''}>
                        <label class="form-check-label" for="${userType}_${user.id}">
                            ${user.name}
                        </label>
                    `;
                    
                    container.appendChild(checkbox);
                    
                    // Add event listener to checkbox
                    const input = checkbox.querySelector('input');
                    input.addEventListener('change', function() {
                        if (this.checked) {
                            addParticipant(this.dataset.id, this.dataset.type, this.dataset.name);
                        } else {
                            removeParticipant(this.dataset.id, this.dataset.type);
                        }
                    });
                });
            }
            
            // Handle search filtering
            document.querySelectorAll('.user-search').forEach(searchInput => {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const userType = this.dataset.type;
                    const checkboxesContainer = document.querySelector(`.${userType}-users`);
                    
                    // Get users array for this type
                    const usersArray = window[`${userType}Users`];
                    
                    if (usersArray && checkboxesContainer) {
                        renderFilteredUsers(userType, usersArray, checkboxesContainer, searchTerm);
                    }
                });
            });
            
            // Add participant to selection
            function addParticipant(id, type, name) {
                // Check if already selected
                if (selectedParticipants.some(p => p.id === id && p.type === type)) return;
                
                // Add to array
                selectedParticipants.push({ id, type, name });
                
                // Update UI
                updateSelectedParticipantsUI();
                updateParticipantCount();
            }
            
            // Remove participant from selection
            function removeParticipant(id, type) {
                // Filter out the removed participant
                selectedParticipants = selectedParticipants.filter(p => !(p.id === id && p.type === type));
                
                // Update UI
                updateSelectedParticipantsUI();
                updateParticipantCount();
                
                // Update checkbox state
                const checkbox = document.getElementById(`${type}_${id}`);
                if (checkbox) checkbox.checked = false;
            }
            
            // Update participant count badge
            function updateParticipantCount() {
                if (participantCountBadge) {
                    participantCountBadge.textContent = selectedParticipants.length;
                }
            }
            
            // Update selected participants UI
            function updateSelectedParticipantsUI() {
                // Clear container
                if (!selectedParticipantsContainer) return;
                
                selectedParticipantsContainer.innerHTML = '';
                
                if (selectedParticipants.length === 0) {
                    selectedParticipantsContainer.innerHTML = '<div class="text-muted text-center no-participants">No participants selected</div>';
                    return;
                }
                
                // Add each participant badge
                selectedParticipants.forEach(participant => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-primary me-2 mb-2 participant-badge';
                    badge.innerHTML = `
                        ${participant.name} 
                        <i class="bi bi-x-circle-fill remove-participant" 
                        data-id="${participant.id}" 
                        data-type="${participant.type}"></i>
                    `;
                    selectedParticipantsContainer.appendChild(badge);
                    
                    // Add click handler for removal
                    badge.querySelector('.remove-participant').addEventListener('click', function() {
                        removeParticipant(this.dataset.id, this.dataset.type);
                    });
                });
            }
            
            // Handle group creation form submission
            newGroupForm?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = document.getElementById('createGroupBtn');
                const groupName = document.getElementById('groupName')?.value.trim();
                const initialMessage = document.getElementById('groupInitialMessage')?.value;
                
                // Validate
                if (!groupName) {
                    alert('Please enter a group name');
                    return;
                }
                
                if (selectedParticipants.length === 0) {
                    alert('Please select at least one participant');
                    return;
                }
                
                // Disable button and show loading state
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
                }
                
                // Create FormData object
                const formData = new FormData();
                formData.append('name', groupName);
                formData.append('initial_message', initialMessage || '');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Add participants
                selectedParticipants.forEach((participant, index) => {
                    formData.append(`participants[${index}][id]`, participant.id);
                    formData.append(`participants[${index}][type]`, participant.type);
                });
                
                // Submit the form with explicit AJAX headers
                fetch(`${window.location.origin}/${rolePrefix}/messaging/create-group`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Group creation response status:', response.status);
                    
                    // Check content type before parsing
                    const contentType = response.headers.get('content-type');
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    
                    // Only parse as JSON if the response is actually JSON
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // Handle HTML or other non-JSON responses
                        return response.text().then(text => {
                            console.log('Received non-JSON response for group creation');
                            
                            // If the group was created successfully, the page may contain a redirect URL
                            // Look for a redirect URL in the HTML that contains the conversation ID
                            const match = text.match(/messaging\?conversation=(\d+)/);
                            if (match && match[1]) {
                                return { 
                                    success: true, 
                                    conversation_id: match[1],
                                    message: 'Group created successfully (extracted from HTML)'
                                };
                            }
                            
                            throw new Error('Server returned HTML instead of JSON, but we cannot extract the conversation ID');
                        });
                    }
                })
                .then(data => {
                    console.log('Group creation successful:', data);
                    
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('newGroupModal'));
                        if (modal) modal.hide();
                        
                        // Redirect to the new group conversation
                        window.location.href = `${window.location.origin}/${rolePrefix}/messaging?conversation=${data.conversation_id}`;
                    } else {
                        throw new Error(data.message || 'Failed to create group');
                    }
                })
                .catch(error => {
                    console.error('Error creating group:', error);
                    
                    // Special handling - if this is likely a success but with HTML response
                    if (error.message.includes('HTML instead of JSON')) {
                        alert('The group may have been created successfully. Reloading the page...');
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }
                    
                    alert('Failed to create group: ' + error.message);
                    
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Create Group';
                    }
                });
            });
            
            // Reset modals when closed
            document.getElementById('newGroupModal')?.addEventListener('hidden.bs.modal', function() {
                selectedParticipants = [];
                updateSelectedParticipantsUI();
                updateParticipantCount();
                
                if (document.getElementById('groupName')) {
                    document.getElementById('groupName').value = '';
                }
                
                if (document.getElementById('groupInitialMessage')) {
                    document.getElementById('groupInitialMessage').value = '';
                }
                
                if (document.getElementById('createGroupBtn')) {
                    document.getElementById('createGroupBtn').disabled = false;
                    document.getElementById('createGroupBtn').textContent = 'Create Group';
                }
                
                // Hide all sections and reset buttons
                document.querySelectorAll('.participant-list').forEach(list => {
                    list.style.display = 'none';
                });
                
                document.querySelectorAll('.toggle-section').forEach(button => {
                    button.innerHTML = '<i class="bi bi-plus"></i> Add';
                });
                
                // Clear search inputs
                document.querySelectorAll('.user-search').forEach(input => {
                    input.value = '';
                });
            });
        });

        let leaveGroupModal;
        let currentConversationId;
        let isLastGroupMember = false;

        // Initialize leave group modal when DOM loads
        document.addEventListener('DOMContentLoaded', function() {
            leaveGroupModal = new bootstrap.Modal(document.getElementById('leaveGroupModal'));
            
            // Add event listener for the leave group button in conversation header
            document.addEventListener('click', function(e) {
                const leaveBtn = e.target.closest('.leave-group-btn');
                if (!leaveBtn) return;
                
                e.preventDefault();
                
                const conversationId = leaveBtn.getAttribute('data-conversation-id');
                if (!conversationId) return;
                
                currentConversationId = conversationId;
                
                // Check if user is the last participant
                checkLastParticipant(conversationId);
            });
            
            // Handle confirmation button click
            document.getElementById('confirmLeaveBtn')?.addEventListener('click', function() {
                if (!currentConversationId) return;
                
                leaveConversation(currentConversationId);
            });
        });

        // Function to check if user is the last participant
        function checkLastParticipant(conversationId) {
            console.log('Checking if last participant for conversation:', conversationId);
            
            // Show loading state
            const lastMemberWarning = document.getElementById('lastMemberWarning');
            lastMemberWarning.classList.add('d-none');
            document.getElementById('leaveGroupMessage').innerHTML = '<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Checking group status...</div>';
            
            fetch(`${window.location.origin}/${rolePrefix}/messaging/check-last-participant/${conversationId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Last participant check result:', data);
                isLastGroupMember = data.is_last;
                
                if (isLastGroupMember) {
                    lastMemberWarning.classList.remove('d-none');
                    document.getElementById('leaveGroupMessage').textContent = 'You are about to leave this group.';
                    document.getElementById('confirmLeaveBtn').textContent = 'Leave & Delete Group';
                } else {
                    lastMemberWarning.classList.add('d-none');
                    document.getElementById('leaveGroupMessage').textContent = 'Are you sure you want to leave this group?';
                    document.getElementById('confirmLeaveBtn').textContent = 'Leave Group';
                }
                
                // Show the modal
                leaveGroupModal.show();
            })
            .catch(error => {
                console.error('Error checking if last participant:', error);
                alert('Could not check group status. Please try again.');
            });
        }

        // Function to leave the conversation
        function leaveConversation(conversationId) {
            // Show loading state
            const confirmBtn = document.getElementById('confirmLeaveBtn');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            fetch(`${window.location.origin}/${rolePrefix}/messaging/leave-conversation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    conversation_id: conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the modal first
                    leaveGroupModal.hide();
                    
                    // IMMEDIATELY update the content area to show empty state
                    const messageArea = document.querySelector('.message-area');
                    if (messageArea) {
                        if (data.was_last) {
                            messageArea.innerHTML = `
                                <div class="empty-state">
                                    <i class="bi bi-check-circle-fill empty-icon text-success"></i>
                                    <h4>Group Deleted</h4>
                                    <p>You were the last member of the group. It has been successfully deleted.</p>
                                    <button class="btn btn-primary mt-3" onclick="window.location.href='${window.location.origin}/${rolePrefix}/messaging'">
                                        <i class="bi bi-arrow-left me-2"></i>Return to Messages
                                    </button>
                                </div>
                            `;
                        } else {
                            messageArea.innerHTML = `
                                <div class="empty-state">
                                    <i class="bi bi-check-circle-fill empty-icon text-success"></i>
                                    <h4>Left Group</h4>
                                    <p>You have successfully left the group conversation.</p>
                                    <button class="btn btn-primary mt-3" onclick="window.location.href='${window.location.origin}/${rolePrefix}/messaging'">
                                        <i class="bi bi-arrow-left me-2"></i>Return to Messages
                                    </button>
                                </div>
                            `;
                        }
                    }
                    
                    // Reset current conversation tracking
                    window.currentlyLoadedConversation = null;
                    
                    // Remove active class from conversation list
                    document.querySelectorAll('.conversation-item.active').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Refresh the conversation list
                    smoothRefreshConversationList();
                    
                    // Remove group from UI immediately rather than waiting for refresh
                    const groupItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (groupItem) {
                        groupItem.remove();
                    }
                    
                    // Add a clear indicator to the window title
                    document.title = `Left Group - ${document.title.split('-')[1] || 'SulongKalinga'}`;
                } else {
                    throw new Error(data.message || 'Failed to leave conversation');
                }
            })
            .catch(error => {
                console.error('Error leaving conversation:', error);
                alert('Failed to leave conversation: ' + error.message);
                
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            });
        }

        // Group Members Management
        let viewMembersModal;
        let addMemberModal;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            viewMembersModal = new bootstrap.Modal(document.getElementById('viewMembersModal'));
            addMemberModal = new bootstrap.Modal(document.getElementById('addMemberModal'));
            
            // Event delegation for dynamic elements
            document.addEventListener('click', function(e) {
                // View members button
                if (e.target.closest('.view-members-btn')) {
                    e.preventDefault();
                    const btn = e.target.closest('.view-members-btn');
                    const conversationId = btn.getAttribute('data-conversation-id');
                    loadGroupMembers(conversationId);
                }
                
                // Add member button
                if (e.target.closest('.add-member-btn')) {
                    e.preventDefault();
                    const btn = e.target.closest('.add-member-btn');
                    const conversationId = btn.getAttribute('data-conversation-id');
                    document.getElementById('groupConversationId').value = conversationId;
                    addMemberModal.show();
                }
            });
            
            // Set up add member form functionality similar to new conversation
            const memberUserTypeSelect = document.getElementById('memberUserType');
            const memberSearch = document.getElementById('memberSearch');
            const memberSelect = document.getElementById('memberSelect');
            
            // Handle user type selection
            memberUserTypeSelect?.addEventListener('change', function() {
                const userType = this.value;
                console.log('Selected member type:', userType);
                
                // Show loading state
                if (memberSelect) {
                    memberSelect.innerHTML = '<option value="" selected disabled>Loading users...</option>';
                    memberSelect.disabled = true;
                }
                
                // Fetch users of selected type
                fetch(`${window.location.origin}/${rolePrefix}/messaging/get-users?type=${userType}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received users data:', data);
                    
                    if (memberSelect) {
                        let usersArray = null;
                        
                        if (data.users && Array.isArray(data.users)) {
                            usersArray = data.users;
                        } else if (Array.isArray(data)) {
                            usersArray = data;
                        } else if (data.data && Array.isArray(data.data)) {
                            usersArray = data.data;
                        }
                        
                        if (usersArray && usersArray.length > 0) {
                            console.log('Found', usersArray.length, 'users');
                            
                            // Store users for filtering
                            window.memberOptions = usersArray;
                            
                            // Enable select and update options
                            memberSelect.disabled = false;
                            
                            // Initial rendering of all options
                            updateMemberOptions('');
                        } else {
                            console.warn('No users found');
                            memberSelect.innerHTML = '<option value="" selected disabled>No users found</option>';
                            memberSelect.disabled = true;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    if (memberSelect) {
                        memberSelect.innerHTML = '<option value="" selected disabled>Error loading users: ' + error.message + '</option>';
                        memberSelect.disabled = true;
                    }
                });
            });
            
            // Handle search input
            memberSearch?.addEventListener('input', function() {
                updateMemberOptions(this.value);
            });
            
            // Handle form submission
            document.getElementById('addMemberForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const conversationId = document.getElementById('groupConversationId').value;
                const participantId = memberSelect.value;
                const participantType = memberUserTypeSelect.value;
                
                if (!conversationId || !participantId || !participantType) {
                    alert('Please select a member to add to the group');
                    return;
                }
                
                const submitBtn = document.getElementById('addMemberBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
                
                // Send request to add member
                fetch(`${window.location.origin}/${rolePrefix}/messaging/add-group-member`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId,
                        participant_id: participantId,
                        participant_type: participantType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal and reset form
                        addMemberModal.hide();
                        this.reset();
                        
                        // Reset memberSelect
                        if (memberSelect) {
                            memberSelect.innerHTML = '<option value="" selected disabled>First select a user type</option>';
                            memberSelect.disabled = true;
                            memberSelect.size = 1;
                            memberSelect.classList.remove('active-dropdown');
                            memberSelect.removeAttribute('data-dropdown-visible');
                        }
                        
                        if (memberSearch) {
                            memberSearch.value = '';
                        }
                        
                        // Create success notification that stays visible during reload
                        const notificationDiv = document.createElement('div');
                        notificationDiv.className = 'alert alert-success position-fixed top-0 end-0 m-3';
                        notificationDiv.style.zIndex = "9999";
                        notificationDiv.innerHTML = `
                            <strong>Success!</strong> New member has been added to the group. Refreshing...
                        `;
                        document.body.appendChild(notificationDiv);
                        
                        // Store success flag in sessionStorage to show notification after reload
                        sessionStorage.setItem('memberAdded', 'true');
                        sessionStorage.setItem('memberAddedTime', Date.now());
                        
                        // Always force a hard page reload with the conversation ID
                        // This is the most reliable way to ensure the conversation area shows the latest content
                        const cacheBuster = Date.now();
                        window.location.href = `${window.location.origin}/${rolePrefix}/messaging?conversation=${conversationId}&refresh=${cacheBuster}`;
                    } else {
                        throw new Error(data.message || 'Failed to add member');
                    }
                })
                .catch(error => {
                    console.error('Error adding member:', error);
                    alert('Failed to add member: ' + error.message);
                })
                .finally(() => {
                    // Reset button (this will only run if there's an error)
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Add to Group';
                });
            });

            // Add this to the document.addEventListener('DOMContentLoaded', function() {...}) section
            // to handle displaying the notification after page reload
            document.addEventListener('DOMContentLoaded', function() {
                // Check if we just added a member
                if (sessionStorage.getItem('memberAdded') === 'true') {
                    const addedTime = parseInt(sessionStorage.getItem('memberAddedTime') || '0');
                    const currentTime = Date.now();
                    
                    // Only show if the flag was set within the last 3 seconds (to avoid stale notifications)
                    if (currentTime - addedTime < 3000) {
                        // Show temporary success notification
                        const notificationDiv = document.createElement('div');
                        notificationDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                        notificationDiv.style.zIndex = "9999";
                        notificationDiv.innerHTML = `
                            <strong>Success!</strong> New member has been added to the group.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.body.appendChild(notificationDiv);
                        
                        // Auto-dismiss after 5 seconds
                        setTimeout(() => {
                            if (notificationDiv.parentNode) {
                                notificationDiv.parentNode.removeChild(notificationDiv);
                            }
                        }, 5000);
                    }
                    
                    // Clear flags
                    sessionStorage.removeItem('memberAdded');
                    sessionStorage.removeItem('memberAddedTime');
                }
            });
            
            // Function to update member options based on search
            function updateMemberOptions(searchTerm) {
                if (!memberSelect || !window.memberOptions) return;
                
                // Clear existing options
                memberSelect.innerHTML = '';
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                defaultOption.textContent = searchTerm ? 'Search results' : 'Select a member';
                memberSelect.appendChild(defaultOption);
                
                // Filter users based on search term
                const filteredUsers = searchTerm 
                    ? window.memberOptions.filter(user => 
                        user.name.toLowerCase().includes(searchTerm.toLowerCase()))
                    : window.memberOptions;
                
                // Add matching users to dropdown
                if (filteredUsers.length === 0) {
                    const option = document.createElement('option');
                    option.disabled = true;
                    option.textContent = 'No matching members found';
                    memberSelect.appendChild(option);
                } else {
                    // Add each filtered user to the dropdown
                    filteredUsers.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name; // Just name, no email
                        memberSelect.appendChild(option);
                    });
                }
                
                // Keep dropdown visible during search
                memberSelect.size = Math.min(10, filteredUsers.length + 1);
                memberSelect.classList.add('active-dropdown');
                memberSelect.setAttribute('data-dropdown-visible', 'true');
            }

            // Add these event listeners for member selection dropdown
            if (memberSelect) {
                // Handle click on option
                memberSelect.addEventListener('click', function(e) {
                    if (e.target.tagName === 'OPTION' && e.target.value) {
                        // Set the selected value
                        this.value = e.target.value;
                        
                        // Reset display and close dropdown
                        this.size = 1;
                        this.classList.remove('active-dropdown');
                        this.removeAttribute('data-dropdown-visible');
                        
                        // Clear the search field
                        if (memberSearch) {
                            memberSearch.value = '';
                        }
                    }
                });
                
                // Add focus handlers to keep dropdown open
                memberSearch?.addEventListener('focus', function() {
                    if (window.memberOptions && window.memberOptions.length > 0) {
                        memberSelect.size = Math.min(10, window.memberOptions.length + 1);
                        memberSelect.classList.add('active-dropdown');
                        memberSelect.setAttribute('data-dropdown-visible', 'true');
                    }
                });
                
                // Add click handler on document to close dropdown when clicking elsewhere
                document.addEventListener('click', function(e) {
                    if (memberSelect && 
                        !memberSelect.contains(e.target) && 
                        !memberSearch?.contains(e.target) &&
                        memberSelect.getAttribute('data-dropdown-visible') === 'true') {
                        memberSelect.size = 1;
                        memberSelect.classList.remove('active-dropdown');
                        memberSelect.removeAttribute('data-dropdown-visible');
                    }
                });
            }
        });

        // Function to load group members
        function loadGroupMembers(conversationId) {
            const membersList = document.getElementById('groupMembersList');
            
            // Show loading state
            membersList.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Show modal while loading
            viewMembersModal.show();
            
            // Fetch members from server
            fetch(`${window.location.origin}/${rolePrefix}/messaging/group-members/${conversationId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.members || data.members.length === 0) {
                    membersList.innerHTML = '<div class="text-center py-3">No members found</div>';
                    return;
                }
                
                // Clear loading state
                membersList.innerHTML = '';
                
                // Add members to list
                data.members.forEach(member => {
                    // Determine badge color based on member type
                    let badgeClass = 'bg-secondary';
                    let userType = 'Unknown';
                    
                    if (member.participant_type === 'cose_staff') {
                        switch(member.role_id) {
                            case 1:
                                badgeClass = 'bg-danger';
                                userType = 'Administrator';
                                break;
                            case 2:
                                badgeClass = 'bg-primary';
                                userType = 'Care Manager';
                                break;
                            case 3:
                                badgeClass = 'bg-info';
                                userType = 'Care Worker';
                                break;
                        }
                    } else if (member.participant_type === 'beneficiary') {
                        badgeClass = 'bg-success';
                        userType = 'Beneficiary';
                    } else if (member.participant_type === 'family_member') {
                        badgeClass = 'bg-warning text-dark';
                        userType = 'Family Member';
                    }
                    
                    const memberItem = document.createElement('div');
                    memberItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    memberItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img src="/images/defaultProfile.png" class="rounded-circle" width="40" height="40" alt="${member.name}">
                            </div>
                            <div>
                                <div class="fw-bold">${member.name}</div>
                                <div class="small text-muted">${member.email || 'No email'}</div>
                            </div>
                        </div>
                        <span class="badge ${badgeClass}">${userType}</span>
                    `;
                    
                    membersList.appendChild(memberItem);
                });
            })
            .catch(error => {
                console.error('Error loading group members:', error);
                membersList.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading members: ${error.message}
                    </div>
                `;
            });
        }

        // ============= IMPROVED MESSAGE SEARCH FUNCTIONALITY =============
        // Global search variables
        let searchMatches = [];
        let currentMatchIndex = -1;
        let searchInitialized = false;

        // Debounce helper for search input
        function debounce(func, delay) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

        // Escape special characters in search query for regex safety
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Clear search highlights
        function clearSearch() {
            console.log('Clearing search');
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            clearSearchHighlights();
            
            // Reset search state
            searchMatches = [];
            currentMatchIndex = -1;
            
            // Update UI
            updateSearchNavigation(0);
        }

        // Remove highlight spans and restore original text
        function clearSearchHighlights() {
            console.log('Clearing search highlights');
            const highlightedElements = document.querySelectorAll('.search-highlight');
            console.log(`Found ${highlightedElements.length} highlighted elements to clear`);
            
            highlightedElements.forEach(element => {
                // Replace the highlight span with its text content
                const textContent = document.createTextNode(element.textContent);
                element.parentNode.replaceChild(textContent, element);
            });
        }

        // Update navigation button states and result count display
        function updateSearchNavigation(matchCount) {
            console.log(`Updating search navigation with ${matchCount} matches`);
            const prevBtn = document.getElementById('searchPrevBtn');
            const nextBtn = document.getElementById('searchNextBtn');
            const resultsCount = document.getElementById('searchResultsCount');
            
            // Enable/disable navigation buttons
            if (prevBtn) prevBtn.disabled = matchCount === 0;
            if (nextBtn) nextBtn.disabled = matchCount === 0;
            
            // Update results count text with improved feedback
            if (resultsCount) {
                const query = document.getElementById('messageSearchInput')?.value || '';
                
                if (query.trim().length < 2) {
                    // Show "query too short" message when query is too short
                    resultsCount.textContent = 'Type at least 2 characters to search';
                    resultsCount.classList.add('text-muted');
                } else if (matchCount === 0) {
                    resultsCount.textContent = 'No matches found';
                    resultsCount.classList.remove('text-muted');
                } else {
                    resultsCount.textContent = `${Math.min(currentMatchIndex + 1, matchCount)} of ${matchCount} matches`;
                    resultsCount.classList.remove('text-muted');
                }
            }
        }

        // Navigate between search matches
        function navigateSearch(direction) {
            console.log(`Navigating search ${direction}, matches: ${searchMatches.length}`);
            if (searchMatches.length === 0) return;
            
            // Remove current highlight
            if (currentMatchIndex >= 0 && currentMatchIndex < searchMatches.length) {
                searchMatches[currentMatchIndex].classList.remove('current');
            }
            
            // FIXED DIRECTION LOGIC: 
            // "prev" (up button) should navigate to previous match (decreasing index)
            // "next" (down button) should navigate to next match (increasing index)
            if (direction === 'next') {
                currentMatchIndex = (currentMatchIndex + 1) % searchMatches.length;
            } else {
                currentMatchIndex = (currentMatchIndex - 1 + searchMatches.length) % searchMatches.length;
            }
            
            // Get current match
            const currentMatch = searchMatches[currentMatchIndex];
            if (!currentMatch) {
                console.error('Current match is undefined');
                return;
            }
            
            // Highlight current match
            currentMatch.classList.add('current');
            
            // Scroll match into view
            try {
                currentMatch.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } catch (err) {
                console.error('Error scrolling to match:', err);
            }
            
            // Update status text
            const resultsCount = document.getElementById('searchResultsCount');
            if (resultsCount) {
                resultsCount.textContent = `${currentMatchIndex + 1} of ${searchMatches.length} matches`;
            }
        }

        // Simple approach to highlight text in each message
        function performSearch(query) {
            // Reset current search state
            clearSearch();
            
            // IMPROVED: Always update navigation to show feedback, even for short queries
            if (!query || query.trim().length < 2) {
                console.log('Search query too short');
                updateSearchNavigation(0);
                return;
            }
            
            console.log('Searching for:', query);
            
            // Get messages container
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                console.error('Messages container not found');
                return;
            }
            
            // Find all message elements
            const messageElements = messagesContainer.querySelectorAll('.message');
            console.log(`Searching through ${messageElements.length} messages`);
            
            if (messageElements.length === 0) {
                console.error('No message elements found. Check your DOM structure.');
                return;
            }
            
            searchMatches = [];
            
            // Process each message
            messageElements.forEach(messageElement => {
                // Skip system messages
                if (messageElement.classList.contains('system')) return;
                
                // Focus on message content
                const messageContent = messageElement.querySelector('.message-content');
                if (!messageContent) return;
                
                // Get text content
                const text = messageContent.textContent;
                if (!text) return;
                
                // Check for match using case-insensitive comparison
                if (!text.toLowerCase().includes(query.toLowerCase())) return;
                
                console.log('Found matching message:', text.substring(0, 30) + '...');
                
                // Replace text with highlighted version
                try {
                    const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
                    const html = messageContent.innerHTML;
                    
                    // This approach preserves existing HTML structure while adding highlights
                    const highlightedHTML = html.replace(regex, '<span class="search-highlight">$1</span>');
                    messageContent.innerHTML = highlightedHTML;
                    
                    // Find all highlighted spans we just created
                    const highlights = messageContent.querySelectorAll('.search-highlight');
                    highlights.forEach(highlight => {
                        searchMatches.push(highlight);
                    });
                } catch (error) {
                    console.error('Error highlighting text:', error);
                }
            });
            
            // Update navigation UI
            console.log(`Found ${searchMatches.length} matches total`);
            updateSearchNavigation(searchMatches.length);
            
            // Select first match if available
            if (searchMatches.length > 0) {
                navigateSearch('next');
            }
        }

        // Define the search button initialization function
        window.initializeSearchButton = function() {
            console.log('Initializing search button');
            const searchBtn = document.getElementById('messageSearchBtn');
            const searchContainer = document.getElementById('messageSearchContainer');
            
            // Exit gracefully if required elements don't exist
            if (!searchBtn || !searchContainer) {
                console.log('Search UI elements not found', {
                    searchBtn: !!searchBtn,
                    searchContainer: !!searchContainer
                });
                return;
            }
            
            console.log('Found search elements, setting up event listeners');
            
            // Clear existing listeners to prevent duplicates
            const newSearchBtn = searchBtn.cloneNode(true);
            if (searchBtn.parentNode) {
                searchBtn.parentNode.replaceChild(newSearchBtn, searchBtn);
            }
            
            // Add click event listener for search button
            newSearchBtn.addEventListener('click', function() {
                console.log('Search button clicked');
                const isVisible = searchContainer.style.display === 'block';
                const messagesContainer = document.getElementById('messagesContainer');
                
                if (!isVisible) {
                    // Show search
                    searchContainer.style.display = 'block';
                    searchContainer.classList.add('active');
                    
                    // Focus search input
                    const searchInput = document.getElementById('messageSearchInput');
                    if (searchInput) setTimeout(() => searchInput.focus(), 50);
                    
                    // Add active class to messages container
                    if (messagesContainer) messagesContainer.classList.add('search-active');
                } else {
                    // Hide search - FIXED closing logic
                    closeSearchUI();
                }
            });
            
            // Ensure search input has event listeners
            ensureSearchInputWorks();
            
            console.log('Search button initialization complete');
        };

        // Make sure search input responds to typing
        function ensureSearchInputWorks() {
            const searchInput = document.getElementById('messageSearchInput');
            const prevBtn = document.getElementById('searchPrevBtn');
            const nextBtn = document.getElementById('searchNextBtn');
            const closeBtn = document.getElementById('closeSearchBtn');
            
            if (!searchInput) {
                console.log('Search input not found, will try again later');
                setTimeout(ensureSearchInputWorks, 500);
                return;
            }
            
            console.log('Setting up search input event listeners');
            
            // Clear any existing event listeners by cloning
            const newInput = searchInput.cloneNode(true);
            searchInput.parentNode.replaceChild(newInput, searchInput);
            
            // Add input handler with debounce
            newInput.addEventListener('input', debounce(function() {
                console.log('Search input changed:', this.value);
                performSearch(this.value);
            }, 300));
            
            // Add keyboard navigation
            newInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (e.shiftKey) {
                        navigateSearch('prev');
                    } else {
                        navigateSearch('next');
                    }
                } else if (e.key === 'Escape') {
                    // FIXED escape key behavior to properly close search
                    closeSearchUI();
                }
            });
            
            // Make sure navigation buttons work too
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    navigateSearch('prev');
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    navigateSearch('next');
                });
            }
            
            // FIXED close button handler
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    closeSearchUI();
                });
            }
            
            console.log('Search input event listeners initialized');
        }

        // ADDED new function to consistently close search UI
        function closeSearchUI() {
            const searchContainer = document.getElementById('messageSearchContainer');
            if (!searchContainer) return;
            
            // First remove active class for animation
            searchContainer.classList.remove('active');
            
            // Then hide after animation completes
            setTimeout(() => {
                searchContainer.style.display = 'none';
            }, 300);
            
            // Clear existing search results
            clearSearch();
            
            // Remove active class from messages container
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.classList.remove('search-active');
            }
            
            console.log('Search UI closed');
        }

        // Reset search UI when conversations change
        window.resetSearchAfterConversationLoad = function() {
            const searchContainer = document.getElementById('messageSearchContainer');
            if (!searchContainer) return;
            
            // Hide search UI
            searchContainer.style.display = 'none';
            searchContainer.classList.remove('active');
            
            // Reset messages container
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.classList.remove('search-active');
            }
            
            // Clear search input
            const searchInput = document.getElementById('messageSearchInput');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Reset results counter
            const resultsCount = document.getElementById('searchResultsCount');
            if (resultsCount) {
                resultsCount.textContent = '';
            }
            
            // Clear any highlights
            clearSearchHighlights();
            searchMatches = [];
            currentMatchIndex = -1;
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for page to fully load before initializing search
            setTimeout(ensureSearchInputWorks, 1500);
        });

        // At the end of your loadConversation function, right before the final curly brace:
            setTimeout(() => {
                initializeMessageActions();
            }, 500);

        function addMessageActionHandlers() {
            document.querySelectorAll('.unsend-message').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const messageId = this.getAttribute('data-message-id');
                    unsendMessage(messageId);
                });
            });
        }

        function unsendMessage(messageId) {
            // Store the message ID for the confirm button
            let currentMessageToUnsend = messageId;
            
            // Show confirmation modal instead of using confirm()
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmUnsendModal'));
            
            // Set up the confirm button handler
            document.getElementById('confirmUnsendButton').onclick = function() {
                // Hide the confirmation modal
                confirmModal.hide();
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/${rolePrefix}/messaging/unsend-message/${currentMessageToUnsend}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI to show message as unsent
                        const messageElement = document.querySelector(`.message[data-message-id="${currentMessageToUnsend}"]`);
                        if (messageElement) {
                            // First, save references to all elements we need to modify
                            const contentElement = messageElement.querySelector('.message-content');
                            const attachmentsElement = messageElement.querySelector('.message-attachments');
                            const actionsElement = messageElement.querySelector('.message-actions');
                            const timeElement = messageElement.querySelector('.message-time');
                            
                            // Add unsent class to the entire message element for CSS styling
                            messageElement.classList.add('has-unsent');
                            
                            // Check if this message had attachments
                            const hadAttachments = !!attachmentsElement;
                            
                            if (contentElement) {
                                // Replace content with "unsent" message
                                contentElement.classList.add('unsent');
                                contentElement.innerHTML = '<em>This message was unsent</em>';
                            }
                            
                            // Handle attachments - remove them completely
                            if (attachmentsElement) {
                                attachmentsElement.remove();
                            }
                            
                            // Remove the actions dropdown to prevent further unsend attempts
                            if (actionsElement) {
                                actionsElement.remove();
                            }
                            
                            // Keep the time element but make it more subtle
                            if (timeElement) {
                                timeElement.classList.add('unsent-time');
                            }
                            
                            // Force a conversation refresh after a short delay if this was a message with attachments
                            if (hadAttachments || data.had_attachments) {
                                setTimeout(() => {
                                    const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                                    if (conversationId) {
                                        console.log("Message had attachments - forcing conversation refresh");
                                        forceRefreshConversation(conversationId);
                                    }
                                }, 300);
                            }
                        }
                        
                        // Force refresh the conversation list to update preview
                        smoothRefreshConversationList();
                        
                        // Also update the navbar dropdown
                        if (typeof loadRecentMessages === 'function') {
                            loadRecentMessages();
                        }
                    } else {
                        // Show error in modal
                        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                        document.getElementById('errorModalMessage').textContent = data.message || 'An error occurred while unsending the message.';
                        errorModal.show();
                    }
                })
                .catch(error => {
                    console.error('Error unsending message:', error);
                    
                    // Show error in modal
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    document.getElementById('errorModalMessage').textContent = 'An error occurred while trying to unsend the message.';
                    errorModal.show();
                });
            };
            
            // Show the confirmation modal
            confirmModal.show();
        }

        // Add this to your loadConversation function, right before the final curly brace
        function initializeMessageActions() {
            setTimeout(() => {
                addMessageActionHandlers();
            }, 200);
        }

        // Use event delegation for handling unsend button clicks
        // Improved function using event delegation for better handling of dynamic content
        function addMessageActionHandlers() {
            // Get container for event delegation
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            // Remove any existing click handlers to prevent duplicates
            messagesContainer.removeEventListener('click', handleUnsendClick);
            
            // Add a single event listener to the container
            messagesContainer.addEventListener('click', handleUnsendClick);
            
            console.log('Message action handlers initialized with event delegation');
        }

        // Handle clicks on unsend buttons via event delegation
        function handleUnsendClick(e) {
            // Find if the click was on an unsend button or its child elements
            let target = e.target;
            
            // Navigate up to find the unsend-message link
            while (target !== this && !target.classList.contains('unsend-message')) {
                target = target.parentNode;
                if (!target) return; // Click was not on any element we care about
            }
            
            // If we found the unsend-message link, handle it
            if (target.classList.contains('unsend-message')) {
                e.preventDefault();
                const messageId = target.getAttribute('data-message-id');
                unsendMessage(messageId);
            }
        }

    </script>
</body>
</html>
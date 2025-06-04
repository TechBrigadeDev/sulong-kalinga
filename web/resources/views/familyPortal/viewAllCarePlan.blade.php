<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Care Plans</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewAllCareplan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <!-- Acknowledgment Confirmation Modal -->
    <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-labelledby="acknowledgmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="acknowledgmentModalLabel">Confirm Acknowledgment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>By acknowledging this care plan, you confirm that:</p>
                    <ul>
                        <li>You have thoroughly reviewed all of the information in this care plan.</li>
                        <li>You understand the assessment, care needs, and interventions outlined.</li>
                        <li>You agree with the care plan as documented for your family member.</li>
                    </ul>
                    <p>This action will be recorded with your name, date, and time.</p>
                    
                    <form id="acknowledgmentForm" method="POST" action="">
                        @csrf
                        <input type="hidden" name="confirmation" value="confirmed">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAcknowledgment">I Acknowledge This Care Plan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-left">CARE PLAN RECORDS</div>
            <a href="{{ route('family.care.plan.allCarePlans') }}" class="btn btn-primary">
                <i class="bi bi-bar-chart-line"></i> Care Plan Statistics
            </a>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form action="{{ route('family.care.plan.index') }}" method="GET" id="searchFilterForm">
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="search-container d-flex">
                                        <div class="position-relative flex-grow-1">
                                            <i class="bi bi-search search-icon"></i>
                                            <input type="text" name="search" class="form-control search-input" 
                                                placeholder="Search by author or date..." value="{{ $search ?? '' }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary ms-2">
                                            Search
                                        </button>
                                    </div>
                                    <div class="filter-container">
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? 'all') == 'all' ? 'active' : '' }}" data-filter="all">All</button>
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? '') == 'pending' ? 'active' : '' }}" data-filter="pending">Pending</button>
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? '') == 'acknowledged' ? 'active' : '' }}" data-filter="acknowledged">Acknowledged</button>
                                    </div>
                                    <input type="hidden" name="filter" id="filterInput" value="{{ $filter ?? 'all' }}">
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body p-0">
                            @if(isset($carePlans) && $carePlans->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Author</th>
                                                <th>Status</th>
                                                <th>Date Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carePlans as $plan)
                                                <tr>
                                                    <td>{{ $plan->author->first_name }} {{ $plan->author->last_name }}</td>
                                                    <td>
                                                        @if($plan->acknowledged_by_beneficiary || $plan->acknowledged_by_family)
                                                            <span class="status-badge status-acknowledged">Acknowledged</span>
                                                        @else
                                                            <span class="status-badge status-pending">Pending Review</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $plan->created_at->format('M d, Y') }}</td>
                                                    <td class="actions-cell">
                                                        @if(!$plan->acknowledged_by_beneficiary && !$plan->acknowledged_by_family)
                                                            <button class="btn btn-sm btn-primary acknowledge-btn" 
                                                                data-id="{{ $plan->weekly_care_plan_id }}"
                                                                title="Acknowledge">
                                                                Acknowledge
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('family.care.plan.view', $plan->weekly_care_plan_id) }}" 
                                                            class="btn btn-sm btn-info" title="View Details">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state p-4 text-center">
                                    <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                                    <h4 class="mt-3">No Care Plans Found</h4>
                                    <p class="text-muted">There are currently no care plan records available for your family member.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($carePlans) && $carePlans->count() > 0)
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
                            <div class="pagination-info text-muted mb-2 mb-md-0">
                                Showing {{ $carePlans->firstItem() }} to {{ $carePlans->lastItem() }} of {{ $carePlans->total() }} entries
                            </div>
                            {{ $carePlans->appends(['search' => $search ?? '', 'filter' => $filter ?? 'all'])->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Set the filter value and load results via AJAX
                    document.getElementById('filterInput').value = this.dataset.filter;
                    loadCarePlans();
                });
            });
            
            // Regular form submission - no AJAX
            document.getElementById('searchFilterForm').addEventListener('submit', function(e) {
                // Let the form submit normally
            });
            
            // AJAX function to load care plans
            function loadCarePlans(page = 1) {
                const search = document.querySelector('input[name="search"]').value;
                const filter = document.getElementById('filterInput').value;
                const container = document.querySelector('.card-body');
                const loadingHtml = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                
                // Show loading indicator
                container.innerHTML = loadingHtml;
                
                fetch(`{{ route('family.care.plan.index') }}?search=${encodeURIComponent(search)}&filter=${filter}&page=${page}&ajax=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    
                    // Re-attach event listeners to the new content
                    attachEventListeners();
                    
                    // Update URL without refreshing
                    window.history.pushState(
                        {search: search, filter: filter, page: page},
                        '',
                        `{{ route('family.care.plan.index') }}?search=${encodeURIComponent(search)}&filter=${filter}&page=${page}`
                    );
                })
                .catch(error => {
                    console.error('Error loading care plans:', error);
                    container.innerHTML = '<div class="alert alert-danger">Error loading care plans. Please try again.</div>';
                });
            }
            
            // Attach event listeners to dynamic content
            function attachEventListeners() {
                // Pagination links
                document.querySelectorAll('.pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;
                        loadCarePlans(page);
                    });
                });
                
                // Acknowledgment button handlers
                document.querySelectorAll('.acknowledge-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const careplanId = this.dataset.id;
                        const modal = new bootstrap.Modal(document.getElementById('acknowledgmentModal'));
                        
                        // Set the form action
                        document.getElementById('acknowledgmentForm').action = `{{ url('family/care-plan/acknowledge') }}/${careplanId}`;
                        
                        // Show the modal
                        modal.show();
                    });
                });
            }
            
            // Add missing text to acknowledge buttons
            document.querySelectorAll('.acknowledge-btn').forEach(btn => {
                if (!btn.innerHTML.trim()) {
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Acknowledge';
                }
            });
            
            // Confirm acknowledgment button
            document.getElementById('confirmAcknowledgment').addEventListener('click', function() {
                document.getElementById('acknowledgmentForm').submit();
            });
        });
    </script>
</body>
</html>
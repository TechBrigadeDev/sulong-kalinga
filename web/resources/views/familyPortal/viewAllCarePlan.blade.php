<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Plans | Family Portal - </title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewAllCareplan.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <!-- Acknowledgment Confirmation Modal -->
    <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-labelledby="acknowledgmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="acknowledgmentModalLabel">{{ T::translate('Confirm Acknowledgment', 'Kumpirmahin ang Pagkilala')}}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ T::translate('By acknowledging this care plan, you confirm that', 'Sa pamamagitan ng pagkilala sa care plan na it, kinukumpirma mo na')}}:</p>
                    <ul>
                        <li>{{ T::translate('Nasuri mo nang lubusan ang lahat ng impormasyon sa plano ng pangangalagang ito', 'Nasuri mo nang lubusan ang lahat ng impormasyon sa plano ng pangangalagang ito')}}.</li>
                        <li>{{ T::translate('You understand the assessment, care needs, and interventions outlined', 'Nauunawaan mo ang pagtatasa, mga pangangailangan sa pangangalaga, at mga interbensyon na nakabalangkas')}}.</li>
                        <li>{{ T::translate('You agree with the care plan as documented for your family member', 'Sumasang-ayon ka sa plano ng pangangalaga na dokumentado para sa miyembro ng iyong pamilya')}}.</li>
                    </ul>
                    <p>{{ T::translate('This action will be recorded with your name, date, and time', 'Ang aksyon na ito ay itatala kasama ang iyong pangalan, petsa, at oras')}}.</p>
                    
                    <form id="acknowledgmentForm" method="POST" action="">
                        @csrf
                        <input type="hidden" name="confirmation" value="confirmed">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
                    <button type="button" class="btn btn-primary" id="confirmAcknowledgment">{{ T::translate('I Acknowledge This Care Plan', 'Aking Kinikilala ang Care Plan na ito')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-left">{{ T::translate('CARE PLAN RECORDS', 'MGA TALA NG CARE PLAN')}}</div>
            <a href="{{ route('family.care.plan.allCarePlans') }}" class="btn btn-primary">
                <i class="bi bi-bar-chart-line"></i> {{ T::translate('Care Plan Statistics', 'Istatistika ng Care Plan')}}
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
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" name="search" class="form-control search-input"  
                                                placeholder="{{ T::translate('Search by author or date', 'Maghanap ayon sa may-akda o petsa')}}..." value="{{ $search ?? '' }}">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap')}}</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="filter-container">
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? 'all') == 'all' ? 'active' : '' }}" data-filter="all">{{ T::translate('All', 'Lahat')}}</button>
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? '') == 'pending' ? 'active' : '' }}" data-filter="pending">{{ T::translate('Pending', 'Nakabinbin')}}</button>
                                        <button type="button" class="btn btn-outline-secondary filter-btn {{ ($filter ?? '') == 'acknowledged' ? 'active' : '' }}" data-filter="acknowledged">{{ T::translate('Acknowledged', 'Kinilala')}}</button>
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
                                                <th>{{ T::translate('Author', 'May-akda')}}</th>
                                                <th>Status</th>
                                                <th>{{ T::translate('Date Created', 'Petsa ng Paglikha')}}</th>
                                                <th>{{ T::translate('Actions', 'Aksyon')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carePlans as $plan)
                                                <tr>
                                                    <td>{{ $plan->author->first_name }} {{ $plan->author->last_name }}</td>
                                                    <td>
                                                        @if($plan->acknowledged_by_beneficiary || $plan->acknowledged_by_family)
                                                            <span class="status-badge status-acknowledged">{{ T::translate('Acknowledged', 'Kinilala')}}</span>
                                                        @else
                                                            <span class="status-badge status-pending">{{ T::translate('Pending Review', 'Nakabinbing Pagsusuri')}}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $plan->created_at->format('M d, Y') }}</td>
                                                    <td class="actions-cell">
                                                        @if(!$plan->acknowledged_by_beneficiary && !$plan->acknowledged_by_family)
                                                            <button class="btn btn-sm btn-primary acknowledge-btn" 
                                                                data-id="{{ $plan->weekly_care_plan_id }}"
                                                                title="Acknowledge">
                                                                {{ T::translate('Acknowledge', 'Kilalanin')}}
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('family.care.plan.view', $plan->weekly_care_plan_id) }}" 
                                                            class="btn btn-sm btn-info" title="View Details">
                                                            <i class="bi bi-eye"></i> {{ T::translate('View', 'Tingnan')}}
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
                                    <h4 class="mt-3">{{ T::translate('No Care Plans Found', 'Walang mga Care Plan ang nakita')}}</h4>
                                    <p class="text-muted">{{ T::translate('There are currently no care plan records available for your family member', 'Kasalukuyang walang available na talaan ng care plan para sa miyembro ng iyong pamilya')}}.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($carePlans) && $carePlans->count() > 0)
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
                            <div class="pagination-info text-muted mb-2 mb-md-0">
                                Showing {{ $carePlans->firstItem() }} to {{ $carePlans->lastItem() }} of {{ $carePlans->total() }} entries
                            </div>
                            <div class="main-pagination-container">
                                {{ $carePlans->appends(['search' => $search ?? '', 'filter' => $filter ?? 'all'])->links('pagination::bootstrap-5') }}
                            </div>
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
            // Initialize the acknowledgment modal
            const acknowledgmentModal = new bootstrap.Modal(document.getElementById('acknowledgmentModal'));

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
            
            // Regular form submission
            document.getElementById('searchFilterForm').addEventListener('submit', function(e) {
                // Default form submission
            });
            
            // AJAX function to load care plans
            function loadCarePlans(page = 1) {
                const search = document.querySelector('input[name="search"]').value;
                const filter = document.getElementById('filterInput').value;
                
                // Get the table container
                let tableContainer = document.querySelector('.card:not(.mb-3) .card-body.p-0');
                if (!tableContainer) {
                    tableContainer = document.querySelector('.card:not(.mb-3) .card-body');
                    if (!tableContainer) {
                        console.error('Could not find table container');
                        return;
                    }
                }
                
                // Show loading indicator
                tableContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">{{ T::translate('Loading', 'Naglo-load')}}...</span></div></div>';
                
                // Get the secure URL for the route
                const baseUrl = window.location.pathname.includes('/family/') 
                    ? '{{ secure_url(route("family.care.plan.index")) }}'
                    : '{{ secure_url(route("beneficiary.care.plan.index")) }}';
                
                // Make the AJAX request
                fetch(`${baseUrl}?search=${encodeURIComponent(search)}&filter=${filter}&page=${page}&ajax=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Insert the HTML into the table container
                    tableContainer.innerHTML = data.html;
                    
                    // Update the main pagination (outside of the table container)
                    const mainPaginationContainer = document.querySelector('.main-pagination-container');
                    if (mainPaginationContainer && data.pagination) {
                        mainPaginationContainer.innerHTML = data.pagination;
                    }
                    
                    // Update pagination info text
                    const paginationInfo = document.querySelector('.pagination-info');
                    if (paginationInfo && data.meta) {
                        paginationInfo.textContent = `Showing ${data.meta.firstItem} to ${data.meta.lastItem} of ${data.meta.total} entries`;
                    }
                    
                    // Re-attach event listeners
                    attachEventListeners();
                    
                    // Update pagination info
                    updatePaginationInfo();
                    
                    // Update URL without refreshing - using secure URL
                    window.history.pushState(
                        {search: search, filter: filter, page: page},
                        '',
                        `${baseUrl}?search=${encodeURIComponent(search)}&filter=${filter}&page=${page}`
                    );
                })
                .catch(error => {
                    console.error('Error loading care plans:', error);
                    tableContainer.innerHTML = '<div class="alert alert-danger">{{ T::translate('Error loading care plans. Please refresh the page.', 'Error sa paglo-load ng mga care plan. Mangyaring i-refresh ang page')}}</div>';
                });
            }

            // Add this new function to update pagination info
            function updatePaginationInfo() {
                // Get pagination data from the response
                const firstItem = document.querySelector('[data-first-item]')?.getAttribute('data-first-item');
                const lastItem = document.querySelector('[data-last-item]')?.getAttribute('data-last-item');
                const totalItems = document.querySelector('[data-total-items]')?.getAttribute('data-total-items');
                
                // Update pagination info if available
                if (firstItem && lastItem && totalItems) {
                    const paginationInfo = document.querySelector('.pagination-info');
                    if (paginationInfo) {
                        paginationInfo.textContent = `Showing ${firstItem} to ${lastItem} of ${totalItems} entries`;
                    }
                }
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
                
                // Setup acknowledge buttons
                setupAcknowledgeButtons();
            }
                        
            // Function to handle acknowledge button setup with HTTPS support
            function setupAcknowledgeButtons() {
                document.querySelectorAll('.acknowledge-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const careplanId = this.dataset.id;
                        
                        // Set the form action with secure URL
                        const formEl = document.getElementById('acknowledgmentForm');
                        // Use the proper route based on user type
                        const secureUrl = window.location.pathname.includes('/family/') 
                            ? `{{ secure_url('family/care-plan/acknowledge') }}/${careplanId}`
                            : `{{ secure_url('beneficiary/care-plan/acknowledge') }}/${careplanId}`;
                            
                        formEl.action = secureUrl;
                        
                        // Show the modal
                        acknowledgmentModal.show();
                    });
                });
                
                // Add missing text to acknowledge buttons if needed
                document.querySelectorAll('.acknowledge-btn').forEach(btn => {
                    if (!btn.innerHTML.trim()) {
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> {{ T::translate('Acknowledge', 'Kilalanin')}}';
                    }
                });
            }
            
            // Initial setup of buttons
            setupAcknowledgeButtons();
            
            // Confirm acknowledgment button
            document.getElementById('confirmAcknowledgment').addEventListener('click', function() {
                document.getElementById('acknowledgmentForm').submit();
            });
        });
    </script>
</body>
</html>
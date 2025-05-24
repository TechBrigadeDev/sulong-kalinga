<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Records Summarization</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/nlpUI.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-purple: #6f42c1;
            --primary-teal: #20c997;
            --primary-indigo: #4e73df;
            --primary-slate: #5a6268;
            --success-green: #198754;
        }
        
        .page-header {
            color:rgb(39, 39, 39);
            font-weight: 600;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .search-container {
            flex: 1;
            min-width: 250px;
            display: flex;
        }
        .date-filter-container {
            display: flex;
            gap: 10px;
        }
        .date-filter {
            width: 150px;
        }
        .form-control, .btn {
            height: 38px;
        }
        
        /* Table header styling */
        .table thead {
            background-color: var(--primary-indigo);
            color: white;
        }
        
        /* Card header styling */
        .search-card .card-header {
            background-color: var(--primary-indigo);
            color: white;
        }
        
        .results-card .card-header {
            background-color: var(--primary-indigo);
            color: white;
        }
        
        .details-card .card-header {
            background-color: var(--primary-indigo);
            color: white;
        }
        
        .assessment-card .card-header {
            background-color: var(--primary-teal);
            color: white;
        }
        
        .evaluation-card .card-header {
            background-color: var(--primary-purple);
            color: white;
        }
        
        /* Section header styling */
        .section-header-teal {
            background: linear-gradient(to right, #19a57d, #3dd6b0);
            padding: 12px 15px;
            border-left: 4px solid #137a5c;
            margin-bottom: 15px;
            border-radius: 4px;
            color: white;
        }
        
        .section-header-purple {
            background: linear-gradient(to right, #5e35b1, #8962d5);
            padding: 12px 15px;
            border-left: 4px solid #4527a0;
            margin-bottom: 15px;
            border-radius: 4px;
            color: white;
        }
        
        .section-title {
            color: #ffffff;
            font-weight: 600;
            margin: 0;
        }
        
        /* Button styling */
        .btn-generate {
            background-color: var(--primary-indigo);
            border-color: var(--primary-indigo);
            color: white;
        }
        
        .btn-generate:hover {
            background-color:rgb(20, 42, 117);
            border-color:rgb(20, 42, 117);
            color: white;
        }
        
        /* Other styling */
        .summary-section-teal {
            border-left: 3px solid var(--primary-teal);
            padding-left: 15px;
        }
        
        .summary-section-purple {
            border-left: 3px solid var(--primary-purple);
            padding-left: 15px;
        }
        
        .custom-badge {
            font-size: 0.9rem;
            padding: 6px 10px;
        }
        .action-btn {
            transition: all 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
        .section-card-teal {
            border-left: 3px solid var(--primary-teal);
            transition: all 0.2s;
        }
        .section-card-teal:hover {
            border-left-color: #198754;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .section-card-purple {
            border-left: 3px solid var(--primary-purple);
            transition: all 0.2s;
        }
        .section-card-purple:hover {
            border-left-color: #5e35b1;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        #carePlanDetails {
            animation: fadeIn 0.5s ease-out;
            scroll-margin-top: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .filters-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .search-wrapper {
            flex: 2;
            min-width: 300px;
            display: flex;
        }
        .date-filters {
            flex: 1;
            min-width: 320px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="page-header">
            <i class="bi bi-stars me-2"></i>NLP-POWERED REPORT SUMMARY
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Report Selection -->
                    <div class="card border-0 shadow-sm mb-3 search-card">
                        <div class="card-header py-3">
                            <h5 class="mb-0">Search Weekly Care Plans</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="filters-wrapper">
                                <div class="search-wrapper">
                                    <input type="text" class="form-control" id="search" placeholder="Search by beneficiary name or care plan ID">
                                    <button class="btn btn-primary ms-2 d-flex align-items-center" id="searchBtn">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                                <div class="date-filters">
                                    <div class="input-group">
                                        <span class="input-group-text">From:</span>
                                        <input type="date" class="form-control" id="date_from">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">To:</span>
                                        <input type="date" class="form-control" id="date_to">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Care Plan Results Table -->
                    <div class="card border-0 shadow-sm mb-3 results-card">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Weekly Care Plans</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Beneficiary</th>
                                            <th>Care Worker</th>
                                            <th>Date</th>
                                            <th>AI Summary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="carePlansTable">
                                        <!-- Care plans will be loaded here -->
                                    </tbody>
                                </table>
                                <div id="pagination" class="d-flex justify-content-center mt-3">
                                    <!-- Pagination will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Care Plan Details Section (Hidden Initially) -->
                    <div id="carePlanDetails" style="display: none;">
                        <!-- Care Plan Info -->
                        <div class="card border-0 shadow-sm mb-3 details-card">
                            <div class="card-header d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Care Plan Details</h5>
                                <span class="badge bg-light text-primary custom-badge" id="carePlanId"></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong><i class="bi bi-person me-2"></i>Beneficiary:</strong> <span id="beneficiaryName"></span></p>
                                        <p><strong><i class="bi bi-people me-2"></i>Care Worker:</strong> <span id="careWorkerName"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><i class="bi bi-calendar-date me-2"></i>Date:</strong> <span id="carePlanDate"></span></p>
                                        <p><strong><i class="bi bi-person-badge me-2"></i>Author:</strong> <span id="authorName"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Section -->
                        <div class="card border-0 shadow-sm mb-4 assessment-card">
                            <div class="card-header py-3">
                                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Assessment</h5>
                            </div>
                            <div class="card-body">
                                <div class="section-header-teal">
                                    <h6 class="section-title">Original Assessment</h6>
                                </div>
                                <div class="mb-4">
                                    <div class="form-control bg-light" style="min-height: 150px; max-height: 300px; overflow-y: auto;" id="originalAssessment"></div>
                                </div>
                                
                                <div class="text-end">
                                    <button class="btn btn-generate action-btn" id="generateAssessmentSummary">
                                        <i class="bi bi-stars me-1"></i> Generate AI Summary
                                    </button>
                                </div>
                                
                                <!-- Assessment Summary Section (Hidden Initially) -->
                                <div id="assessmentSummarySection" style="display: none;" class="mt-4 summary-section-teal">
                                    <hr>
                                    <div class="section-header-teal">
                                        <h6 class="section-title">Assessment Summary</h6>
                                    </div>

                                    <!-- New translation section -->
                                    <div id="assessmentTranslationSection" class="summary-section-blue mb-4" style="display: none;">
                                        <h6 class="fw-bold"><i class="bi bi-translate me-2"></i>English Translation</h6>
                                        <div id="assessmentTranslationDraft" class="fst-italic"></div>
                                        <div class="mt-2">
                                        </div>
                                        <button class="btn btn-sm btn-outline-success" id="useAssessmentTranslation">
                                            <i class="bi bi-check-circle"></i> Use Translation as Final
                                        </button>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-control bg-light" id="assessmentSummaryDraft"></div>
                                    </div>
                                    
                                    <div id="assessmentSummarySections" class="mb-3">
                                        <!-- Section cards will be added here -->
                                    </div>
                                    
                                    <div class="text-end">
                                        <button class="btn btn-outline-secondary action-btn" id="editAssessmentSummary">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-success action-btn" id="saveAssessmentSummary" style="display: none;">
                                            <i class="bi bi-check-lg me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Evaluation Section -->
                        <div class="card border-0 shadow-sm mb-4 evaluation-card">
                            <div class="card-header py-3">
                                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Evaluation</h5>
                            </div>
                            <div class="card-body">
                                <div class="section-header-purple">
                                    <h6 class="section-title">Original Evaluation</h6>
                                </div>
                                <div class="mb-4">
                                    <div class="form-control bg-light" style="min-height: 150px; max-height: 300px; overflow-y: auto;" id="originalEvaluation"></div>
                                </div>
                                
                                <div class="text-end">
                                    <button class="btn btn-generate action-btn" id="generateEvaluationSummary">
                                        <i class="bi bi-stars me-1"></i> Generate AI Summary
                                    </button>
                                </div>
                                
                                <!-- Evaluation Summary Section (Hidden Initially) -->
                                <div id="evaluationSummarySection" style="display: none;" class="mt-4 summary-section-purple">
                                    <hr>
                                    <div class="section-header-purple">
                                        <h6 class="section-title">Evaluation Summary</h6>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-control bg-light" id="evaluationSummaryDraft"></div>
                                    </div>
                                    
                                    <div id="evaluationSummarySections" class="mb-3">
                                        <!-- Section cards will be added here -->
                                    </div>
                                    
                                    <div class="text-end">
                                        <button class="btn btn-outline-secondary action-btn" id="editEvaluationSummary">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-success action-btn" id="saveEvaluationSummary" style="display: none;">
                                            <i class="bi bi-check-lg me-1"></i> Save
                                        </button>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button class="btn btn-outline-secondary action-btn" id="editEvaluationSummary">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <button id="translateEvaluationSummary" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-translate"></i> Translate
                                        </button>
                                        <button class="btn btn-success action-btn" id="saveEvaluationSummary" style="display: none;">
                                            <i class="bi bi-check-lg me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Finalize Button -->
                        <div class="text-center mb-4">
                            <button class="btn btn-success btn-lg action-btn" id="finalizeSummaries">
                                <i class="bi bi-check-circle me-1"></i> Finalize Summaries
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Animation Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 id="loadingText">Processing...</h5>
                    <div class="progress mt-3">
                        <div id="loadingProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Original Report Modal -->
    <div class="modal fade" id="originalReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="originalReportModalTitle">{{ T::translate('Original Care Plan Report', 'Orihinal na Care Plan Report')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="originalReportContent">
                    <!-- Original report content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentCarePlanId = null;
            let currentPage = 1;
            
            // Load initial data
            loadCarePlans(1);
            
            // Search button click
            $('#searchBtn').click(function() {
                loadCarePlans(1);
            });
            
            // Search on Enter key press
            $('#search').keypress(function(e) {
                if (e.which == 13) {
                    loadCarePlans(1);
                }
            });
            
            // Date filter change
            $('#date_from, #date_to').change(function() {
                loadCarePlans(1);
            });
            
            // Load care plans via AJAX
            function loadCarePlans(page) {
                currentPage = page;
                let search = $('#search').val();
                let dateFrom = $('#date_from').val();
                let dateTo = $('#date_to').val();
                
                $.ajax({
                    url: '/admin/ai-summary/search',
                    type: 'GET',
                    data: {
                        search: search,
                        date_from: dateFrom,
                        date_to: dateTo,
                        page: page
                    },
                    success: function(response) {
                        displayCarePlans(response);
                    },
                    error: function(xhr) {
                        console.error('Error loading care plans:', xhr);
                        alert('Failed to load care plans. Please try again.');
                    }
                });
            }
            
            // Display care plans in table
            function displayCarePlans(data) {
                let tbody = $('#carePlansTable');
                tbody.empty();
                
                if (data.data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center py-3">No care plans found</td></tr>');
                    $('#pagination').empty();
                    return;
                }
                
                data.data.forEach(function(carePlan) {
                    let careWorkerName = 'Not Assigned';
                    
                    // Use the joined data from the query
                    if (carePlan.care_worker_id && carePlan.care_worker_first_name) {
                        careWorkerName = `${carePlan.care_worker_first_name} ${carePlan.care_worker_last_name}`;
                    }

                    let row = `<tr>
                        <td>${carePlan.weekly_care_plan_id}</td>
                        <td>${carePlan.first_name} ${carePlan.last_name}</td>
                        <td>${careWorkerName}</td>
                        <td>${new Date(carePlan.created_at).toLocaleDateString()}</td>
                        <td>${carePlan.has_ai_summary ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary view-btn" data-id="${carePlan.weekly_care_plan_id}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>`;
                    tbody.append(row);
                });
                
                // Set up pagination - limit visible page numbers
                let pagination = $('#pagination');
                pagination.empty();
                
                if (data.last_page > 1) {
                    let paginationHtml = '<nav><ul class="pagination">';
                    
                    // Previous button
                    paginationHtml += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>`;
                    
                    // First page
                    if (data.current_page > 3) {
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>`;
                        
                        if (data.current_page > 4) {
                            paginationHtml += `<li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>`;
                        }
                    }
                    
                    // Visible pages (current ± 2)
                    const startPage = Math.max(1, data.current_page - 2);
                    const endPage = Math.min(data.last_page, data.current_page + 2);
                    
                    for (let i = startPage; i <= endPage; i++) {
                        paginationHtml += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                    }
                    
                    // Last page
                    if (data.current_page < data.last_page - 2) {
                        if (data.current_page < data.last_page - 3) {
                            paginationHtml += `<li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>`;
                        }
                        
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${data.last_page}">${data.last_page}</a>
                        </li>`;
                    }
                    
                    // Next button
                    paginationHtml += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${data.current_page + 1}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>`;
                    
                    paginationHtml += '</ul></nav>';
                    pagination.html(paginationHtml);
                    
                    // Add click handlers for pagination
                    $('.page-link').click(function(e) {
                        e.preventDefault();
                        let page = $(this).data('page');
                        if (page > 0 && page <= data.last_page) {
                            loadCarePlans(page);
                        }
                    });
                }
                
                // Add click handlers for view buttons
                $('.view-btn').click(function() {
                    let id = $(this).data('id');
                    loadCarePlanDetails(id);
                });
            }
            
            // Load care plan details
            function loadCarePlanDetails(id) {
                $.ajax({
                    url: `/admin/ai-summary/care-plan/${id}`,
                    type: 'GET',
                    success: function(carePlan) {
                        currentCarePlanId = carePlan.weekly_care_plan_id;
                        displayCarePlanDetails(carePlan);
                    },
                    error: function(xhr) {
                        console.error('Error loading care plan details:', xhr);
                        alert('Failed to load care plan details. Please try again.');
                    }
                });
            }
            
            // Display care plan details
            function displayCarePlanDetails(carePlan) {
                // Basic info
                $('#carePlanId').text(`#${carePlan.weekly_care_plan_id}`);
                $('#beneficiaryName').text(`${carePlan.beneficiary.first_name} ${carePlan.beneficiary.last_name}`);
                $('#careWorkerName').text(carePlan.care_worker ? 
                    `${carePlan.care_worker.first_name} ${carePlan.care_worker.last_name}` : 'N/A');
                $('#carePlanDate').text(new Date(carePlan.created_at).toLocaleDateString());
                $('#authorName').text(carePlan.author ? 
                    `${carePlan.author.first_name} ${carePlan.author.last_name}` : 'N/A');
                
                // Original content
                $('#originalAssessment').text(carePlan.assessment || 'No assessment available');
                $('#originalEvaluation').text(carePlan.evaluation_recommendations || 'No evaluation available');
                
                // Show previously generated summaries if available
                if (carePlan.has_ai_summary) {
                    if (carePlan.assessment_summary_draft) {
                        $('#assessmentSummarySection').show();
                        $('#assessmentSummaryDraft').text(carePlan.assessment_summary_draft);
                        displaySummarySections('assessment', carePlan.assessment_summary_sections);
                    }
                    
                    if (carePlan.evaluation_summary_draft) {
                        $('#evaluationSummarySection').show();
                        $('#evaluationSummaryDraft').text(carePlan.evaluation_summary_draft);
                        displaySummarySections('evaluation', carePlan.evaluation_summary_sections);
                    }
                } else {
                    // Hide summary sections if no summaries yet
                    $('#assessmentSummarySection').hide();
                    $('#evaluationSummarySection').hide();
                }
                
                // Show details section
                $('#carePlanDetails').show();
                
                // Scroll to details with a small offset and behavior smooth
                document.getElementById('carePlanDetails').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            
            // Generate assessment summary
            $('#generateAssessmentSummary').click(function() {
                if (!currentCarePlanId) return;
                
                const text = $('#originalAssessment').text();
                if (!text || text === 'No assessment available') {
                    alert('No assessment text available to summarize');
                    return;
                }
                
                // Show loading modal
                $('#loadingText').text('Generating AI Summary...');
                $('#loadingProgressBar').css('width', '0%');
                $('#loadingModal').modal('show');
                
                // Simulate progress (for better UX)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    $('#loadingProgressBar').css('width', `${progress}%`);
                    
                    if (progress >= 90) {
                        clearInterval(progressInterval);
                    }
                }, 150);
                
                $.ajax({
                    url: '/admin/ai-summary/summarize',
                    type: 'POST',
                    data: {
                        text: text,
                        type: 'assessment',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        clearInterval(progressInterval);
                        $('#loadingProgressBar').css('width', '100%');
                        setTimeout(() => {
                            $('#loadingModal').modal('hide');
                            
                            // Display summary and sections
                            $('#assessmentSummaryDraft').text(response.summary);
                            displaySummarySections('assessment', response.sections);
                            $('#assessmentSummarySection').show();
                            
                            // Save the generated summary
                            saveSummary('assessment', response.summary, response.sections);
                        }, 500);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        $('#loadingModal').modal('hide');
                        console.error('Error generating summary:', xhr);
                        alert('Failed to generate summary. Please try again.');
                    }
                });
            });
            
            // Generate evaluation summary
            $('#generateEvaluationSummary').click(function() {
                if (!currentCarePlanId) return;
                
                const text = $('#originalEvaluation').text();
                if (!text || text === 'No evaluation available') {
                    alert('No evaluation text available to summarize');
                    return;
                }
                
                // Show loading modal
                $('#loadingText').text('Generating AI Summary...');
                $('#loadingProgressBar').css('width', '0%');
                $('#loadingModal').modal('show');
                
                // Simulate progress (for better UX)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    $('#loadingProgressBar').css('width', `${progress}%`);
                    
                    if (progress >= 90) {
                        clearInterval(progressInterval);
                    }
                }, 150);
                
                $.ajax({
                    url: '/admin/ai-summary/summarize',
                    type: 'POST',
                    data: {
                        text: text,
                        type: 'evaluation',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        clearInterval(progressInterval);
                        $('#loadingProgressBar').css('width', '100%');
                        setTimeout(() => {
                            $('#loadingModal').modal('hide');
                            
                            // Display summary and sections
                            $('#evaluationSummaryDraft').text(response.summary);
                            displaySummarySections('evaluation', response.sections);
                            $('#evaluationSummarySection').show();
                            
                            // Save the generated summary
                            saveSummary('evaluation', response.summary, response.sections);
                        }, 500);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        $('#loadingModal').modal('hide');
                        console.error('Error generating summary:', xhr);
                        alert('Failed to generate summary. Please try again.');
                    }
                });
            });
            
            // Display summary sections
            function displaySummarySections(type, sections) {
                if (!sections) return;
                
                let container = $(`#${type}SummarySections`);
                container.empty();
                
                Object.entries(sections).forEach(([key, value]) => {
                    let sectionTitle = key.replace('_', ' ');
                    sectionTitle = sectionTitle.charAt(0).toUpperCase() + sectionTitle.slice(1);
                    
                    let sectionIcon = '';
                    switch(key) {
                        case 'vital_signs': sectionIcon = 'bi-heart-pulse'; break;
                        case 'symptoms': sectionIcon = 'bi-thermometer-half'; break;
                        case 'observations': sectionIcon = 'bi-eye'; break;
                        case 'findings': sectionIcon = 'bi-search'; break;
                        case 'recommendations': sectionIcon = 'bi-lightbulb'; break;
                        case 'treatment': sectionIcon = 'bi-capsule'; break;
                        case 'follow_up': sectionIcon = 'bi-calendar-check'; break;
                        default: sectionIcon = 'bi-card-text';
                    }
                    
                    // Use different card style based on type
                    const cardClass = type === 'assessment' ? 'section-card-teal' : 'section-card-purple';
                    
                    let sectionHtml = `
                    <div class="card mb-3 ${cardClass}">
                        <div class="card-header py-2 bg-light d-flex align-items-center">
                            <i class="bi ${sectionIcon} me-2"></i>
                            <h6 class="mb-0">${sectionTitle}</h6>
                        </div>
                        <div class="card-body py-2">
                            <p class="section-content mb-0" data-section="${key}">${value}</p>
                            <textarea class="form-control section-editor" data-section="${key}" style="display: none;">${value}</textarea>
                        </div>
                    </div>`;
                    
                    container.append(sectionHtml);
                });
            }
            
            // Edit assessment summary
            $('#editAssessmentSummary').click(function() {
                toggleEditMode('assessment', true);
            });
            
            // Edit evaluation summary
            $('#editEvaluationSummary').click(function() {
                toggleEditMode('evaluation', true);
            });
            
            // Save assessment summary
            $('#saveAssessmentSummary').click(function() {
                saveEditedSummary('assessment');
            });
            
            // Save evaluation summary
            $('#saveEvaluationSummary').click(function() {
                saveEditedSummary('evaluation');
            });
            
            // Toggle edit mode
            function toggleEditMode(type, isEdit) {
                if (isEdit) {
                    // Switch to edit mode
                    $(`#${type}SummarySections .section-content`).hide();
                    $(`#${type}SummarySections .section-editor`).show();
                    $(`#edit${type.charAt(0).toUpperCase() + type.slice(1)}Summary`).hide();
                    $(`#save${type.charAt(0).toUpperCase() + type.slice(1)}Summary`).show();
                    
                    // Make the main summary draft editable
                    let currentText = $(`#${type}SummaryDraft`).text();
                    $(`#${type}SummaryDraft`).html(`<textarea class="form-control" id="${type}SummaryDraftEditor">${currentText}</textarea>`);
                } else {
                    // Switch back to view mode
                    $(`#${type}SummarySections .section-content`).show();
                    $(`#${type}SummarySections .section-editor`).hide();
                    $(`#edit${type.charAt(0).toUpperCase() + type.slice(1)}Summary`).show();
                    $(`#save${type.charAt(0).toUpperCase() + type.slice(1)}Summary`).hide();
                    
                    // Make the main summary draft non-editable
                    let currentText = $(`#${type}SummaryDraftEditor`).val();
                    $(`#${type}SummaryDraft`).text(currentText);
                }
            }
            
            // Save edited summary
            function saveEditedSummary(type) {
                if (!currentCarePlanId) return;
                
                // Collect section data
                let sections = {};
                $(`#${type}SummarySections .section-editor`).each(function() {
                    let section = $(this).data('section');
                    let content = $(this).val();
                    sections[section] = content;
                    
                    // Also update the displayed content
                    $(this).siblings('.section-content').text(content);
                });
                
                // Get the main summary text
                let summaryText = $(`#${type}SummaryDraftEditor`).val();
                
                // Show loading modal
                $('#loadingText').text('Saving changes...');
                $('#loadingProgressBar').css('width', '0%');
                $('#loadingModal').modal('show');
                
                // Simulate progress (for better UX)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    $('#loadingProgressBar').css('width', `${progress}%`);
                    
                    if (progress >= 90) {
                        clearInterval(progressInterval);
                    }
                }, 100);
                
                // Switch back to view mode
                toggleEditMode(type, false);
                
                // Save to database
                let data = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                };
                
                data[`${type}_summary_draft`] = summaryText;
                data[`${type}_summary_sections`] = sections;
                
                $.ajax({
                    url: `/admin/ai-summary/update/${currentCarePlanId}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        clearInterval(progressInterval);
                        $('#loadingProgressBar').css('width', '100%');
                        setTimeout(() => {
                            $('#loadingModal').modal('hide');
                            
                            // Show success toast or notification
                            const toast = `<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                                <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header bg-success text-white">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <strong class="me-auto">Success</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Summary saved successfully!
                                    </div>
                                </div>
                            </div>`;
                            
                            $(toast).appendTo('body');
                            setTimeout(() => {
                                $('.toast').toast('hide');
                                setTimeout(() => {
                                    $('.toast').remove();
                                }, 500);
                            }, 3000);
                            
                        }, 500);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        $('#loadingModal').modal('hide');
                        console.error('Error saving summary:', xhr);
                        alert('Failed to save summary. Please try again.');
                    }
                });
            }
            
            // Save summary to database
            function saveSummary(type, summary, sections) {
                if (!currentCarePlanId) return;
                
                let data = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                };
                
                data[`${type}_summary_draft`] = summary;
                data[`${type}_summary_sections`] = sections;
                
                $.ajax({
                    url: `/admin/ai-summary/update/${currentCarePlanId}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        console.log('Summary saved successfully');
                    },
                    error: function(xhr) {
                        console.error('Error saving summary:', xhr);
                        alert('Failed to save summary. Please try again.');
                    }
                });
            }
            
            // Finalize summaries
            $('#finalizeSummaries').click(function() {
                if (!currentCarePlanId) return;
                
                let assessmentSummary = $('#assessmentSummaryDraft').text();
                let evaluationSummary = $('#evaluationSummaryDraft').text();
                
                if (!assessmentSummary && !evaluationSummary) {
                    alert('Please generate at least one summary before finalizing.');
                    return;
                }
            });

        });

        function finalizeSummary(type) {
            if (!currentCarePlanId) return;
            
            // Get the appropriate content based on type
            const summaryContent = type === 'assessment' 
                ? $('#assessmentSummaryDraft').text() 
                : $('#evaluationSummaryDraft').text();
            
            if (!summaryContent.trim()) {
                alert(`No ${type} summary available to finalize.`);
                return;
            }
            
            if (confirm(`Are you sure you want to finalize this ${type} summary? This will mark it as the official summary.`)) {
                // Show loading
                $('#loadingText').text(`Finalizing ${type} summary...`);
                $('#loadingProgressBar').css('width', '0%');
                $('#loadingModal').modal('show');
                
                // Show loading modal
                $('#loadingText').text('Finalizing summaries...');
                $('#loadingProgressBar').css('width', '0%');
                $('#loadingModal').modal('show');
                
                // Simulate progress (for better UX)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    $('#loadingProgressBar').css('width', `${progress}%`);
                    
                    if (progress >= 90) {
                        clearInterval(progressInterval);
                    }
                }, 100);
                
                $.ajax({
                    url: `/admin/ai-summary/finalize/${currentCarePlanId}`,
                    type: 'PUT',
                    data: {
                        assessment_summary_final: assessmentSummary,
                        evaluation_summary_final: evaluationSummary,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        clearInterval(progressInterval);
                        $('#loadingProgressBar').css('width', '100%');
                        setTimeout(() => {
                            $('#loadingModal').modal('hide');
                            
                            // Show success alert
                            const successAlert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> Summaries have been finalized successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                            
                            // Insert at the top of the care plan details section
                            $(successAlert).prependTo('#carePlanDetails');
                            
                            // Refresh the care plan list to update status
                            loadCarePlans(currentPage);
                            
                            // Auto dismiss after 5 seconds
                            setTimeout(() => {
                                $('.alert').alert('close');
                            }, 5000);
                            
                        }, 500);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        $('#loadingModal').modal('hide');
                        console.error('Error finalizing summaries:', xhr);
                        alert('Failed to finalize summaries. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
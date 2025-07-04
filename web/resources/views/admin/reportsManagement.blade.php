<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Records Management | Admin</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/careRecords.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
</head>
<body>
    
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.viewGcpRedirect')
    @include('components.modals.editGcpRedirect')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <div class="text-left">{{ T::translate('RECORDS MANAGEMENT', 'PAMAMAHALA SA MGA TALA')}}</div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="row" id="home-content">
                <div class="card-container">
                    <div class="filter-section">
                        <form action="{{ route('admin.reports') }}" method="GET" id="searchFilterForm">
                            <div class="filter-row">
                                <!-- Search Bar -->
                                <div>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="{{ T::translate('Search by author or beneficiary...', 'Maghanap ayon sa may-akda o benepisyaryo...') }}" 
                                            id="searchBar" name="search" value="{{ $search ?? '' }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap')}}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Dropdown -->
                                <div>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-funnel"></i>
                                        </span>
                                        <select class="form-select" id="filterDropdown" name="filter" onchange="this.form.submit()">
                                            <option value="" {{ empty($filterType ?? '') ? 'selected' : '' }}>{{ T::translate('Filter by', 'Salain sa')}}</option>
                                            <option value="author" {{ ($filterType ?? '') == 'author' ? 'selected' : '' }}>{{ T::translate('Author', 'May-akda')}}</option>
                                            <option value="type" {{ ($filterType ?? '') == 'type' ? 'selected' : '' }}>{{ T::translate('Report Type', 'Uri ng Report')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Reports Per Page -->
                                <div>
                                    <div class="input-group">
                                        <label class="input-group-text" for="perPageSelect">
                                            <i class="bi bi-list-ol"></i>
                                        </label>
                                        <select class="form-select" id="perPageSelect" onchange="changePerPage(this.value)">
                                            <option value="15" {{ request()->get('per_page', 15) == 15 ? 'selected' : '' }}>15 {{ T::translate('per page', 'kada pahina')}}</option>
                                            <option value="25" {{ request()->get('per_page') == 25 ? 'selected' : '' }}>25 {{ T::translate('per page', 'kada pahina')}}</option>
                                            <option value="50" {{ request()->get('per_page') == 50 ? 'selected' : '' }}>50 {{ T::translate('per page', 'kada pahina')}}</option>
                                            <option value="100" {{ request()->get('per_page') == 100 ? 'selected' : '' }}>100 {{ T::translate('per page', 'kada pahina')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sort Order Toggle -->
                                <div>
                                    <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center" id="sortToggle" 
                                        onclick="toggleSortOrder()">
                                        <i class="bi {{ ($sortOrder ?? 'asc') == 'desc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up' }} me-1 me-sm-2"></i> 
                                        <span class="d-none d-sm-inline">{{ ($sortOrder ?? 'asc') == 'desc' ? 'Newest First' : 'Oldest First' }}</span>
                                    </button>
                                    <input type="hidden" name="sort" id="sortOrder" value="{{ $sortOrder ?? 'asc' }}">
                                </div>

                                <!-- Export Dropdown -->
                                <div>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-none d-sm-inline">{{ T::translate('Export', 'I-Export')}}</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                            <li><a class="dropdown-item" href="#" onclick="checkSelectedReports()"><i class="bi bi-file-earmark-pdf me-2"></i>{{ T::translate('Selected as PDF', 'Pinili bilang PDF')}}</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="selectAllAndExport()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>{{ T::translate('All as PDF', 'Lahat bilang PDF')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        @if(isset($reports) && count($reports) > 0)
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="checkbox-cell">
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th scope="col">{{ T::translate('Author', 'May-akda')}}</th>
                                    <th scope="col">{{ T::translate('Report Type', 'Uri ng Report')}}</th>
                                    <th scope="col">{{ T::translate('Related Beneficiary', 'Kaugnay na Benepisyaryo')}}</th>
                                    <th scope="col" class="d-none d-sm-table-cell">{{ T::translate('Date Uploaded', 'Petsa ng Pag-upload')}}</th>
                                    <th scope="col">{{ T::translate('Actions', 'Aksyon')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td class="checkbox-cell">
                                            <input type="checkbox" class="rowCheckbox" data-id="{{ $report->report_id }}" data-type="{{ $report->report_type }}" />
                                        </td>
                                        <td>{{ $report->author_first_name ?? T::translate('Unknown', 'Hindi Kilala') }} {{ $report->author_last_name ?? '' }}</td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $report->report_type ?? T::translate('Unknown', 'Hindi Kilala') }}
                                            </span>
                                        </td>
                                        <td>{{ $report->beneficiary_first_name ?? T::translate('Unknown', 'Hindi Kilala') }} {{ $report->beneficiary_last_name ?? '' }}</td>
                                        <td class="d-none d-sm-table-cell">{{ isset($report->created_at) ? \Carbon\Carbon::parse($report->created_at)->format('M d, Y') : T::translate('Unknown', 'Hindi Kilala') }}</td>
                                        <td>
                                            <div class="action-icons">
                                                @if($report->report_type == 'Weekly Care Plan')
                                                <a href="{{ route('admin.weeklycareplans.show', $report->report_id) }}" title="{{ T::translate('View Weekly Care Plan', 'Tingnan ang Weekly Care Plan') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.weeklycareplans.edit', $report->report_id) }}" title="{{ T::translate('Edit Weekly Care Plan', 'I-edit ang Weekly Care Plan') }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                @elseif($report->report_type === 'General Care Plan')
                                                    <a href="javascript:void(0)" title="{{ T::translate('View General Care Plan', 'Tingnan ang General Care Plan') }}" 
                                                    onclick="openViewGcpRedirectModal('{{ $report->beneficiary_id }}')">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" title="{{ T::translate('Edit General Care Plan', 'I-edit ang General Care Plan') }}" 
                                                    onclick="openEditGcpRedirectModal('{{ $report->beneficiary_id }}')">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @else
                                                    <a href="#" title="{{ T::translate('View Not Available', 'Hindi Available ang Pagtingin') }}" onclick="alert('{{ T::translate('Viewing not available for this report type', 'Hindi available ang pagtingin para sa ganitong uri ng report') }}')">
                                                        <i class="bi bi-eye text-muted"></i>
                                                    </a>
                                                    <a href="#" title="{{ T::translate('Edit Not Available', 'Hindi Available ang Pag-edit') }}" onclick="alert('{{ T::translate('Editing not available for this report type', 'Hindi available ang pag-edit para sa ganitong uri ng report') }}')">
                                                        <i class="bi bi-pencil-square text-muted"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="empty-state">
                            <i class="bi bi-file-earmark-text"></i>
                            <h4>{{ T::translate('No reports found', 'Walang Report na nakita.')}}</h4>
                            <p class="text-muted">{{ T::translate('Try adjusting your search or filter criteria', 'Subukang ayusin ang iyong pamantayan sa paghahanap o filter')}}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if(isset($reports) && count($reports) > 0)
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reports->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Export Reports Confirmation Modal -->
    <div class="modal fade" id="exportReportsModal" tabindex="-1" aria-labelledby="exportReportsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportReportsModalLabel">{{ T::translate('Export Reports', 'IExport ang mga Report')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="exportWarning" class="alert alert-warning d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span id="exportWarningMessage"></span>
                    </div>
                    
                    <div id="exportInfo" class="mb-3">
                        <p>{{ T::translate('You are about to export', 'Malapit ka nang mag-export')}} <strong><span id="exportCount">0</span></strong> {{ T::translate('reports as PDF.', 'ng mga Report bilang PDF')}}</p>
                        <div id="exportTypeBreakdown" class="bg-light p-2 rounded"></div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="includeBeneficiaryDetails">
                        <label class="form-check-label" for="includeBeneficiaryDetails">
                            {{ T::translate('Include detailed beneficiary information', 'Isama ang detalyadong impormasyon ng benepisyaryo') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin')}}</button>
                    <button type="button" id="confirmExportBtn" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> {{ T::translate('Export', 'I-Export')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- No Selection Warning Modal -->
    <div class="modal fade" id="noSelectionModal" tabindex="-1" aria-labelledby="noSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noSelectionModalLabel">{{ T::translate('No Selection', 'Walang Napili')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: clamp(1.5rem, 4vw, 2rem);"></i>
                    <p class="mt-2">{{ T::translate('Please select at least one report to export.', 'Maaring pumili ng kahit na isang report upang i-export.')}}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/forCheckbox.js') }}"></script>
    
    <script>
        // Updated toggle function to always control date sorting
        function toggleSortOrder() {
            const currentOrder = document.getElementById('sortOrder').value || 'asc';
            const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';
            
            document.getElementById('sortOrder').value = newOrder;
            
            // Always use date terminology since we're always sorting by date
            const buttonText = newOrder === 'desc' ? 'Newest First' : 'Oldest First';
            const iconClass = newOrder === 'desc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up';
            
            document.getElementById('sortToggle').innerHTML = `
                <i class="bi ${iconClass} me-1 me-sm-2"></i> 
                <span class="d-none d-sm-inline">${buttonText}</span>
            `;
            
            document.getElementById('searchFilterForm').submit();
        }

        function changePerPage(value) {
            // Get current URL
            let url = new URL(window.location.href);
            
            // Update the per_page parameter
            url.searchParams.set('per_page', value);
            
            // Reset to first page when changing items per page
            url.searchParams.set('page', 1);
            
            // Navigate to the new URL
            window.location.href = url.toString();
        }
    </script>

    <script>
        // Variables to store selected reports
        let selectedReports = {
            weekly: [],
            general: []
        };
        
        // Process checkbox selections
        $(document).on('change', '.rowCheckbox', function() {
            const reportId = $(this).data('id');
            const reportType = $(this).data('type');
            
            if (this.checked) {
                if (reportType === 'Weekly Care Plan') {
                    selectedReports.weekly.push(reportId);
                } else if (reportType === 'General Care Plan') {
                    selectedReports.general.push(reportId);
                }
            } else {
                if (reportType === 'Weekly Care Plan') {
                    selectedReports.weekly = selectedReports.weekly.filter(id => id !== reportId);
                } else if (reportType === 'General Care Plan') {
                    selectedReports.general = selectedReports.general.filter(id => id !== reportId);
                }
            }
            
            updateSelectedCount();
        });
        
        // Select all checkbox
        $('#selectAll').on('change', function() {
            const isChecked = this.checked;
            
            $('.rowCheckbox').each(function() {
                $(this).prop('checked', isChecked);
                
                const reportId = $(this).data('id');
                const reportType = $(this).data('type');
                
                if (isChecked) {
                    if (reportType === 'Weekly Care Plan' && !selectedReports.weekly.includes(reportId)) {
                        selectedReports.weekly.push(reportId);
                    } else if (reportType === 'General Care Plan' && !selectedReports.general.includes(reportId)) {
                        selectedReports.general.push(reportId);
                    }
                } else {
                    // Only remove selections for visible rows instead of clearing all
                    if (reportType === 'Weekly Care Plan') {
                        selectedReports.weekly = selectedReports.weekly.filter(id => id !== reportId);
                    } else if (reportType === 'General Care Plan') {
                        selectedReports.general = selectedReports.general.filter(id => id !== reportId);
                    }
                }
            });
            
            updateSelectedCount();
        });
        
        // Select all and export function
        function selectAllAndExport() {
            $('#selectAll').prop('checked', true).trigger('change');
            checkSelectedReports();
        }
        
        // Update the selected count display
        function updateSelectedCount() {
            const totalCount = selectedReports.weekly.length + selectedReports.general.length;
            $('#selectedCount').text(totalCount);
        }
        
        // Check if reports are selected before opening the export modal
        function checkSelectedReports() {
            const totalCount = selectedReports.weekly.length + selectedReports.general.length;
            
            if (totalCount === 0) {
                $('#noSelectionModal').modal('show');
                return;
            }
            
            // Update modal information
            $('#exportCount').text(totalCount);
            
            let typeInfo = '';
            if (selectedReports.weekly.length > 0) {
                typeInfo += `Weekly Care Plans: ${selectedReports.weekly.length}`;
            }
            
            if (selectedReports.general.length > 0) {
                if (typeInfo) typeInfo += '<br>';
                typeInfo += `General Care Plans: ${selectedReports.general.length}`;
            }
            
            $('#exportTypeBreakdown').html(typeInfo);
            
            // Check if selection exceeds limit
            if (totalCount > 100) {
                $('#exportWarning').removeClass('d-none');
                $('#exportWarningMessage').text('{{ T::translate('Your selection exceeds the limit of 100 reports. Only the first 100 will be exported.', 'Ang iyong napili ay lumampas sa limitasyon ng 100 na mga report. Ang unang 100 lamang ang i-e-export.') }}');
            } else {
                $('#exportWarning').addClass('d-none');
            }
            
            $('#exportReportsModal').modal('show');
        }
        
        // Handle export confirmation
        $('#confirmExportBtn').on('click', function() {
            const includeBeneficiaryDetails = $('#includeBeneficiaryDetails').is(':checked');
            
            // Limit to first 100 if needed
            let weeklySelection = [...selectedReports.weekly];
            let generalSelection = [...selectedReports.general];
            
            // Ensure total selection is no more than 100
            const totalSelected = weeklySelection.length + generalSelection.length;
            if (totalSelected > 100) {
                const weeklyCount = Math.min(weeklySelection.length, Math.floor(100 * weeklySelection.length / totalSelected));
                const generalCount = Math.min(100 - weeklyCount, generalSelection.length);
                
                weeklySelection = weeklySelection.slice(0, weeklyCount);
                generalSelection = generalSelection.slice(0, generalCount);
            }
            
            // Create form and submit
            const form = $('<form>', {
                'action': '/admin/exports/reports-pdf',
                'method': 'post',
                'target': '_blank'
            });
            
            form.append($('<input>', {
                'name': '_token',
                'value': '{{ csrf_token() }}',
                'type': 'hidden'
            }));
            
            form.append($('<input>', {
                'name': 'weekly_care_plans',
                'value': JSON.stringify(weeklySelection),
                'type': 'hidden'
            }));
            
            form.append($('<input>', {
                'name': 'general_care_plans',
                'value': JSON.stringify(generalSelection),
                'type': 'hidden'
            }));
            
            form.append($('<input>', {
                'name': 'include_beneficiary_details',
                'value': includeBeneficiaryDetails ? '1' : '0',
                'type': 'hidden'
            }));
            
            $('body').append(form);
            form.attr('method', 'POST'); // Explicitly set method again to ensure POST is used
            form.submit();
            form.remove();
            
            $('#exportReportsModal').modal('hide');
        });

        function storeSelections() {
            sessionStorage.setItem('weeklySelections', JSON.stringify(selectedReports.weekly));
            sessionStorage.setItem('generalSelections', JSON.stringify(selectedReports.general));
        }

        // Restore selections on page load
        $(document).ready(function() {
            // Check if we're coming from pagination or a fresh page load
            const fromPagination = sessionStorage.getItem('navigatingPagination') === 'true';
            sessionStorage.removeItem('navigatingPagination');
            
            if (fromPagination) {
                // Restore selections from session storage
                const savedWeekly = sessionStorage.getItem('weeklySelections');
                const savedGeneral = sessionStorage.getItem('generalSelections');
                
                if (savedWeekly) selectedReports.weekly = JSON.parse(savedWeekly);
                if (savedGeneral) selectedReports.general = JSON.parse(savedGeneral);
                
                // Update checkboxes based on stored selections
                $('.rowCheckbox').each(function() {
                    const id = $(this).data('id');
                    const type = $(this).data('type');
                    
                    if ((type === 'Weekly Care Plan' && selectedReports.weekly.includes(id)) ||
                        (type === 'General Care Plan' && selectedReports.general.includes(id))) {
                        $(this).prop('checked', true);
                    }
                });
            } else {
                // Clear selections on fresh page load
                clearSelections();
            }
            
            // Update the UI
            updateSelectAllCheckbox();
            updateSelectedCount();
            
            // Add event handlers to mark when navigating pagination
            $('a.page-link, button[type="submit"]').on('click', function(e) {
                // Only store selections when navigating through pagination
                if (!$(this).hasClass('btn-close') && !$(this).hasClass('btn-primary') && this.type !== 'button') {
                    sessionStorage.setItem('navigatingPagination', 'true');
                    storeSelections();
                }
            });
        });

        // Add this new function to clear selections
        function clearSelections() {
            // Reset the selection arrays
            selectedReports = {
                weekly: [],
                general: []
            };
            
            // Uncheck all checkboxes
            $('.rowCheckbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            
            // Clear the stored selections from session storage
            sessionStorage.removeItem('weeklySelections');
            sessionStorage.removeItem('generalSelections');
        }

        // Function to update the "select all" checkbox based on current state
        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.rowCheckbox').length;
            const checkedCheckboxes = $('.rowCheckbox:checked').length;
            
            $('#selectAll').prop({
                indeterminate: checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes,
                checked: checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0
            });
        }
    </script>
</body>
</html>
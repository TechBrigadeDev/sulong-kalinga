<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
    <style>
        .action-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .action-icons a {
            color: #000 !important;
            font-size: 1.1rem;
        }
        
        .action-icons a:hover {
            color: #555 !important;
        }
    </style>
</head>
<body>
    
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.viewGcpRedirect')
    @include('components.modals.editGcpRedirect')
    
    <div class="home-section">
    @if(session('success'))
        <div id="success-message" class="alert alert-success alert-dismissible fade show mx-3" 
            style="display: block !important; visibility: visible !important; opacity: 1 !important; margin-top: 15px !important; margin-bottom: 15px !important;">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
        <div class="text-left">REPORTS MANAGEMENT</div>
        <div class="container-fluid text-center">
        <form action="{{ route('care-manager.reports') }}" method="GET" id="searchFilterForm">
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-6 col-lg-6 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-search-alt"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Search by author or beneficiary..." 
                                id="searchBar" name="search" value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-filter-alt"></i>
                            </span>
                            <select class="form-select" id="filterDropdown" name="filter" onchange="this.form.submit()">
                                <option value="" {{ empty($filterType ?? '') ? 'selected' : '' }}>Filter by</option>
                                <option value="author" {{ ($filterType ?? '') == 'author' ? 'selected' : '' }}>Author</option>
                                <option value="type" {{ ($filterType ?? '') == 'type' ? 'selected' : '' }}>Report Type</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sort Order Toggle - Update initial button text -->
                    <div class="col-6 col-md-3 col-lg-2 mb-2">
                        <button type="button" class="btn btn-outline-secondary w-100" id="sortToggle" 
                            onclick="toggleSortOrder()">
                            <i class="bx {{ ($sortOrder ?? 'asc') == 'desc' ? 'bx-sort-z-a' : 'bx-sort-a-z' }}"></i> 
                            {{ ($sortOrder ?? 'asc') == 'desc' ? 'Newest First' : 'Oldest First' }}
                        </button>
                        <input type="hidden" name="sort" id="sortOrder" value="{{ $sortOrder ?? 'asc' }}">
                    </div>

                    <!-- Export Dropdown -->
                    <div class="col-6 col-md-3 col-lg-2 mb-2">
                        <div class="dropdown-center">
                            <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-export"></i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                                <li><a class="dropdown-item" href="#">Export as Excel</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
                      
            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Report Type</th>
                                    <th scope="col">Related Beneficiary</th>
                                    <th scope="col" class="d-none d-sm-table-cell">Date Uploaded</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($reports) && count($reports) > 0)
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="rowCheckbox" />
                                            </td>
                                            <td>{{ $report->author_first_name ?? 'Unknown' }} {{ $report->author_last_name ?? '' }}</td>
                                            <td>{{ $report->report_type ?? 'Unknown' }}</td>
                                            <td>{{ $report->beneficiary_first_name ?? 'Unknown' }} {{ $report->beneficiary_last_name ?? '' }}</td>
                                            <td class="d-none d-sm-table-cell">{{ isset($report->created_at) ? \Carbon\Carbon::parse($report->created_at)->format('M d, Y') : 'Unknown' }}</td>
                                            <td>
                                            <div class="action-icons">
                                                @if($report->report_type == 'Weekly Care Plan')
                                                <a href="{{ route('care-manager.weeklycareplans.show', $report->report_id) }}" title="View Weekly Care Plan">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @elseif($report->report_type === 'General Care Plan')
                                                    <!-- View GCP link -->
                                                    <a href="javascript:void(0)" title="View General Care Plan" 
                                                    onclick="openViewGcpRedirectModal('{{ $report->beneficiary_id }}')">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a href="#" title="View Not Available" onclick="alert('Viewing not available for this report type')">
                                                        <i class="fa fa-eye text-muted"></i>
                                                    </a>
                                                @endif
                                                @if($report->report_type == 'Weekly Care Plan')
                                                    <a href="{{ route('care-manager.weeklycareplans.edit', $report->report_id) }}" title="Edit Weekly Care Plan">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    @elseif($report->report_type === 'General Care Plan')
                                                        <!-- Edit GCP link -->
                                                        <a href="javascript:void(0)" title="Edit General Care Plan" 
                                                        onclick="openEditGcpRedirectModal('{{ $report->beneficiary_id }}')">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    @else
                                                    <a href="#" title="Edit Not Available" onclick="alert('Editing not available for this report type')">
                                                        <i class="bx bx-edit text-muted"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">No reports found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

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
            const iconClass = newOrder === 'desc' ? 'bx-sort-z-a' : 'bx-sort-a-z';
            
            document.getElementById('sortToggle').innerHTML = `
                <i class="bx ${iconClass}"></i> ${buttonText}
            `;
            
            document.getElementById('searchFilterForm').submit();
        }
    </script>
</body>
</html>
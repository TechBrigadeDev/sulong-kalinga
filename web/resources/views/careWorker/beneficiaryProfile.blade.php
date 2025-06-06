<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
    
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    
    <div class="home-section">
        <div class="text-left">BENEFICIARY PROFILES</div>
        <div class="container-fluid text-center">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="row mb-3 align-items-center">
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-6 mb-2">
                    <form action="{{ route('care-worker.beneficiaries.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-search-alt"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Enter beneficiary name..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" name="filter" id="filterDropdown" onchange="this.form.submit()">
                            <option value="" {{ request('filter') ? '' : 'selected' }}>Filter by</option>
                            <option value="category" {{ request('filter') == 'category' ? 'selected' : '' }}>Category</option>
                            <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                            <option value="municipality" {{ request('filter') == 'municipality' ? 'selected' : '' }}>Municipality</option>
                        </select>
                    </div>
                </div>
                </form>

                <!-- Export Dropdown -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-export"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#" id="exportPdf">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Hidden form for exporting -->
                <form id="exportForm" action="{{ route('care-worker.exports.beneficiaries-pdf') }}" method="POST" style="display: none;"
                    data-pdf-route="{{ route('care-worker.exports.beneficiaries-pdf') }}" 
                    data-excel-route="{{ route('care-worker.exports.beneficiaries-excel') }}">
                    @csrf
                    <input type="hidden" name="selected_beneficiaries" id="selectedBeneficiaries">
                </form>

                <!-- Add Beneficiary Button -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <a href="{{ route('care-worker.beneficiaries.create') }}">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bx bx-plus"></i> Add Beneficiary
                    </button>
                    </a>
                </div>
            </div>

            <div class="row" id="recentReports">
                <div class="col-12">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info alert-dismissible fade show">
                            <i class="bx bx-info-circle me-2"></i>
                            You can view and edit beneficiary details, but only administrators and care managers can change a beneficiary's status.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="selectAll" /> <!-- Checkbox to select all rows -->
                                    </th>
                                    <th scope="col">Fullname</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">Municipality</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($beneficiaries as $beneficiary)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" value="{{ $beneficiary->beneficiary_id }}" />
                                        </td>
                                        <td>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</td>
                                        <td>{{ $beneficiary->category->category_name }}</td>
                                        <td>{{ $beneficiary->mobile }}</td>
                                        <td>{{ $beneficiary->barangay->barangay_name }}</td>
                                        <td>{{ $beneficiary->municipality->municipality_name }}</td>
                                        <td>
                                            <span class="badge {{ $beneficiary->status->status_name == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $beneficiary->status->status_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-icons" style="gap: 0px !important;">
                                                <!-- Form to VIEW PROFILE DETAILS -->
                                                <form action="{{ route('care-worker.beneficiaries.view-details') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->beneficiary_id }}">
                                                    <button type="submit" class="btn btn-link text-decoration-none" style="color:black;">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('care-worker.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-link text-decoration-none" style="color:black;">
                                                    <i class="bx bxs-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
    <script src="{{ asset('js/forBeneficiaryExport.js') }}"></script>

    <script>
    // Show info alert only once per session
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we've already shown the alert this session
        const alertShown = sessionStorage.getItem('beneficiaryAlertShown');
        
        if (alertShown) {
            // If already shown in this session, hide it
            const alertElement = document.querySelector('.alert-info.alert-dismissible');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        } else {
            // Mark as shown for this session
            sessionStorage.setItem('beneficiaryAlertShown', 'true');
        }
    });
    </script>
</body>
</html>
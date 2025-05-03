<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.statusChangeCareworker')
    
    <div class="home-section">
        <div class="text-left">CARE WORKER PROFILES</div>
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
                    <form action="{{ route('care-manager.careworkers.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-search-alt"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Enter care worker name..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <form action="{{ route('care-manager.careworkers.index') }}" method="GET" id="filterForm">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-filter-alt"></i>
                            </span>
                            <select class="form-select" name="filter" id="filterDropdown" onchange="document.getElementById('filterForm').submit()">
                                <option value="" {{ request('filter') ? '' : 'selected' }}>Filter by</option>
                                <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="municipality" {{ request('filter') == 'municipality' ? 'selected' : '' }}>Municipality</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Export Dropdown -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-export"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#" id="exportPdf">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>                        </ul>
                    </div>
                </div>

                <!-- Hidden form for exporting -->
                <form id="exportForm" action="{{ route('care-manager.exports.careworkers-pdf') }}" method="POST" style="display: none;"
                    data-pdf-route="{{ route('care-manager.exports.careworkers-pdf') }}"
                    data-excel-route="{{ route('care-manager.exports.careworkers-excel') }}">
                    @csrf
                    <input type="hidden" name="selected_careworkers" id="selectedCareworkers">
                </form>

                <!-- Add Report Button -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <a href="{{ route('care-manager.careworkers.create') }}">
                    <button class="btn btn-primary w-100" id="addButton" style="padding:6px;">
                        <i class="bx bx-plus"></i> Add Careworker
                    </button>
                    </a>
                </div>
            </div>

            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="selectAll" /> <!-- Checkbox to select all rows -->
                                    </th>
                                    <th scope="col">Fullname</th>
                                    <th scope="col">Municipality</th>
                                    <th scope="col">Care Manager</th>
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($careworkers as $careworker)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" value="{{ $careworker->id }}"/>
                                        </td>
                                        <td>{{ $careworker->first_name }} {{ $careworker->last_name }}</td>
                                        <td>{{ $careworker->municipality->municipality_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($careworker->assignedCareManager)
                                                {{ $careworker->assignedCareManager->first_name }} {{ $careworker->assignedCareManager->last_name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $careworker->mobile }}</td>
                                        <td>
                                            <select class="form-select" name="status" id="statusSelect{{ $careworker->id }}" select onchange="window.openStatusChangeCareworkerModal(this, 'Care Worker', {{ $careworker->id }}, '{{ $careworker->is_active ? 'active' : 'inactive' }}')">
                                                <option value="Active" {{ $careworker->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ $careworker->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                            <form action="{{ route('care-manager.careworkers.view') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="careworker_id" value="{{ $careworker->id }}">
                                                <button type="submit" class="btn btn-link text-decoration-none" style="color:black;">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('care-manager.careworkers.edit', ['id' => $careworker->id]) }}" class="btn btn-link text-decoration-none" style="color:black;">
                                                <i class="bx bx-edit"></i>
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

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/forCheckbox.js') }}"></script>
    <script src="{{ asset('js/forCareworkerExport.js') }}"></script>

</body>
</html>
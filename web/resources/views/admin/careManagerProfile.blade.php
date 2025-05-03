<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCaremanager')
    
    <div class="home-section">
        <div class="text-left">CARE MANAGER PROFILES</div>
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
                    <form action="{{ route('admin.caremanagers.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Enter care manager name..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <form action="{{ route('admin.caremanagers.index') }}" method="GET" id="filterForm">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-funnel"></i>
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
                            <i class="bi bi-box-arrow-up"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#" id="exportPdf">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Hidden form for exporting -->
                <form id="exportForm" action="{{ route('admin.export.caremanagers.pdf') }}" method="POST" style="display: none;"
                    data-pdf-route="{{ route('admin.export.caremanagers.pdf') }}" 
                    data-excel-route="{{ route('admin.export.caremanagers.excel') }}">
                    @csrf
                    <input type="hidden" name="selected_caremanagers" id="selectedCaremanagers">
                </form>

                <!-- Add Report Button -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                <a href="{{ route('admin.caremanagers.create') }}">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bi bi-plus"></i> Add Manager
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
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($caremanagers as $caremanager)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" value="{{ $caremanager->id }}"/>
                                        </td>
                                        <td>{{ $caremanager->first_name }} {{ $caremanager->last_name }}</td>
                                        <td>{{ $caremanager->municipality->municipality_name ?? 'N/A' }}</td>
                                        <td>{{ $caremanager->mobile }}</td>
                                        <td>
                                            <select class="form-select" name="status" id="statusSelect{{ $caremanager->id }}" onchange="openStatusChangeCaremanagerModal(this, 'Care Manager', {{ $caremanager->id }}, '{{ $caremanager->status }}')">
                                                <option value="Active" {{ $caremanager->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ $caremanager->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                            <form action="{{ route('admin.caremanagers.view') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="caremanager_id" value="{{ $caremanager->id }}">
                                                <button type="submit" class="btn btn-link pe-2" style="color:black;">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.caremanagers.edit', $caremanager->id) }}" class="btn btn-link ps-2" style="color:black;">
                                                <i class="bi bi-pencil-square"></i>
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
    <script src="{{ asset('js/forCaremanagerExport.js') }}"></script>

</body>
</html>
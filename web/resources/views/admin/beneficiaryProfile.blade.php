<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
    
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeBeneficiary')
    
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
                    <form action="{{ route('admin.beneficiaries.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Enter beneficiary name..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-funnel"></i>
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
                            <i class="bi bi-box-arrow-up"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#" id="exportPdf">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Hidden form for exporting -->
                <form id="exportForm" action="{{ route('admin.export.beneficiaries.pdf') }}" method="POST" style="display: none;"
                    data-pdf-route="{{ route('admin.export.beneficiaries.pdf') }}" 
                    data-excel-route="{{ route('admin.export.beneficiaries.excel') }}">
                    @csrf
                    <input type="hidden" name="selected_beneficiaries" id="selectedBeneficiaries">
                </form>

                <!-- Add Beneficiary Button -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <a href="{{ route('admin.beneficiaries.create') }}">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bi bi-plus"></i> Add Beneficiary
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
                                        <select class="form-select" name="status" id="statusSelect{{ $beneficiary->beneficiary_id }}" onchange="openStatusChangeModal(this, 'Beneficiary', {{ $beneficiary->beneficiary_id }}, '{{ $beneficiary->status->status_name }}')">
                                            <option value="Active" {{ $beneficiary->status->status_name == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ $beneficiary->status->status_name == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        </td>
                                        <td>
                                            <div class="action-icons" style="gap: 0px !important;">
                                                <!-- Form to VIEW PROFILE DETAILS -->
                                                <form action="{{ route('admin.beneficiaries.view') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->beneficiary_id }}">
                                                    <button type="submit" class="btn btn-link pe-2" style="color:black;">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-link ps-2" style="color:black;">
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

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/forCheckbox.js') }}"></script>
    <script src="{{ asset('js/forBeneficiaryExport.js') }}"></script>
</body>
</html>
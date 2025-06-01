<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Beneficiary Profiles</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #343a40;
            --success-color: #28a745;
            --warning-color: #ffc107;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 767.98px) {
            .card-container {
                padding: 1rem;
                margin-bottom: 1rem;
            }
        }
        
        .filter-section {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.25rem;
        }
        
        .input-group-text {
            background-color: var(--light-gray);
            border-color: var(--medium-gray);
        }
        
        .form-control, .form-select {
            border-color: var(--medium-gray);
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        /* Improved responsive buttons */
        .btn-primary, .btn-secondary {
            white-space: nowrap;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        @media (max-width: 575.98px) {
            .btn-primary, .btn-secondary {
                padding: 0.4rem 0.75rem;
                font-size: 0.8125rem;
            }
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
        }
        
        /* Enhanced table responsiveness */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-top: 1rem;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            margin-bottom: 0;
            min-width: 800px; /* Ensures table doesn't collapse too much */
        }
        
        @media (max-width: 991.98px) {
            .table {
                min-width: 100%;
            }
        }
        
        .table thead th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
            vertical-align: middle;
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table tbody td {
            vertical-align: middle;
            padding: 0.75rem;
            border-color: var(--medium-gray);
            font-size: 0.875rem;
        }
        
        /* Action buttons */
        .action-icons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-icons .btn-link {
            color: var(--dark-gray);
            transition: all 0.2s ease;
            padding: 0.375rem;
            border-radius: 4px;
            font-size: 1.5rem;
        }

        .action-icons .btn-link:hover {
            transform: scale(1.2);
        }
        
        @media (max-width: 767.98px) {
            .action-icons .btn-link {
                padding: 0.25rem;
                font-size: 0.875rem;
            }
        }
        
        /* Filter section responsiveness */
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .filter-row > div {
            flex: 1 1 auto;
            min-width: 200px;
        }
        
        @media (max-width: 767.98px) {
            .filter-row > div {
                min-width: 100%;
            }
        }
        
        /* Status select dropdown */
        .form-select.status-select {
            min-width: 120px;
            cursor: pointer;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            text-align: center;
            font-weight: 500;
            border-width: 2px;
            font-size: 0.875rem;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--dark-gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--medium-gray);
            margin-bottom: 1rem;
        }
        
        /* Checkbox cells */
        .checkbox-cell {
            width: 40px;
        }
        
        .checkbox-cell input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        /* Alert messages */
        .alert {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeBeneficiary')
    
    <div class="home-section">
        <div class="text-left">BENEFICIARY PROFILES</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
        
        <div class="card-container">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="filter-section">
                <div class="filter-row">
                    <!-- Search Bar -->
                    <div>
                        <form action="{{ route('admin.beneficiaries.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" placeholder="Search beneficiaries..." id="searchBar" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> <span class="d-none d-sm-inline">Search</span>
                                </button>
                            </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div>
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
                    <div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-none d-sm-inline">Export</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Beneficiary Button -->
                    <div>
                        <a href="{{ route('admin.beneficiaries.create') }}" class="w-100">
                            <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                <i class="bi bi-plus-lg me-1 me-sm-2"></i> <span class="d-none d-sm-inline">Add Beneficiary</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hidden form for exporting -->
            <form id="exportForm" method="POST" style="display: none;" 
                action="/admin/export/beneficiaries-pdf"
                data-pdf-route="/admin/export/beneficiaries-pdf" 
                data-excel-route="/admin/export/beneficiaries-excel">
                @csrf
                <input type="hidden" name="selected_beneficiaries" id="selectedBeneficiaries">
            </form>

            <div class="table-responsive">
                @if($beneficiaries->count() > 0)
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="checkbox-cell">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Barangay</th>
                            <th scope="col">Municipality</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($beneficiaries as $beneficiary)
                            <tr>
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="rowCheckbox" value="{{ $beneficiary->beneficiary_id }}" />
                                </td>
                                <td>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</td>
                                <td>{{ $beneficiary->category->category_name }}</td>
                                <td>{{ $beneficiary->mobile }}</td>
                                <td>{{ $beneficiary->barangay->barangay_name }}</td>
                                <td>{{ $beneficiary->municipality->municipality_name }}</td>
                                <td>
                                    <select class="form-select status-select" name="status" id="statusSelect{{ $beneficiary->beneficiary_id }}" onchange="openStatusChangeModal(this, 'Beneficiary', {{ $beneficiary->beneficiary_id }}, '{{ $beneficiary->status->status_name }}')">
                                        <option value="Active" {{ $beneficiary->status->status_name == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ $beneficiary->status->status_name == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="action-icons">
                                        <!-- Form to VIEW PROFILE DETAILS -->
                                        <form action="{{ route('admin.beneficiaries.view') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->beneficiary_id }}">
                                            <button type="submit" class="btn btn-link" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-link" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h4>No beneficiaries found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                </div>
                @endif
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
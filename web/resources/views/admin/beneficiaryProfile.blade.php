<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Beneficiary Profiles</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profilePages.css') }}">
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
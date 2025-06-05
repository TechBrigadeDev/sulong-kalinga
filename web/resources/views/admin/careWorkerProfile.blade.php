<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Care Worker Profiles</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profilepages.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCareworker')
    
    <div class="home-section">
        <div class="text-left">CARE WORKER PROFILES</div>
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
                        <form action="{{ route('admin.careworkers.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" placeholder="Search care workers..." id="searchBar" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> <span class="d-none d-sm-inline">Search</span>
                                </button>
                            </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div>
                        <form action="{{ route('admin.careworkers.index') }}" method="GET" id="filterForm">
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

                    <!-- Add Care Worker Button -->
                    <div>
                        <a href="{{ route('admin.careworkers.create') }}" class="w-100">
                            <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                <i class="bi bi-plus-lg me-1 me-sm-2"></i> <span class="d-none d-sm-inline">Add Careworker</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hidden form for exporting -->
            <form id="exportForm" method="POST" style="display: none;"
                action="/admin/export/careworkers-pdf"
                data-pdf-route="/admin/export/careworkers-pdf" 
                data-excel-route="/admin/export/careworkers-excel">
                @csrf
                <input type="hidden" name="selected_careworkers" id="selectedCareworkers">
            </form>

            <div class="table-responsive">
                @if($careworkers->count() > 0)
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="checkbox-cell">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Municipality</th>
                            <th scope="col">Care Manager</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($careworkers as $careworker)
                            <tr>
                                <td class="checkbox-cell">
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
                                    <select class="form-select status-select" name="status" id="statusSelect{{ $careworker->id }}" onchange="window.openStatusChangeCareworkerModal(this, 'Care Worker', {{ $careworker->id }}, '{{ $careworker->is_active ? 'active' : 'inactive' }}')">
                                        <option value="Active" {{ $careworker->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ $careworker->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="action-icons">
                                        <form action="{{ route('admin.careworkers.view') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="careworker_id" value="{{ $careworker->id }}">
                                            <button type="submit" class="btn btn-link" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.careworkers.edit', ['id' => $careworker->id]) }}" class="btn btn-link" title="Edit">
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
                    <i class="bi bi-person-workspace"></i>
                    <h4>No care workers found</h4>
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
    <script src="{{ asset('js/forCareworkerExport.js') }}"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Care Manger Profiles</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profilepages.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCaremanager')
    
    <div class="home-section">
        <div class="text-left">{{ T::translate('CARE MANAGER PROFILES', 'PROFILE NG MGA CARE MANAGER') }}</div>
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
                        <form action="{{ route('admin.caremanagers.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" placeholder="{{ T::translate('Search care managers...', 'Maghanap ng care managers...')}}" id="searchBar" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap')}}</span>
                                </button>
                            </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div>
                        <form action="{{ route('admin.caremanagers.index') }}" method="GET" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-funnel"></i>
                                </span>
                                <select class="form-select" name="filter" id="filterDropdown" onchange="document.getElementById('filterForm').submit()">
                                    <option value="" {{ request('filter') ? '' : 'selected' }}>{{ T::translate('Filter by', 'Salain sa')}}</option>
                                    <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                                    <option value="municipality" {{ request('filter') == 'municipality' ? 'selected' : '' }}>{{ T::translate('Municipality', 'Munisipalidad')}}</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Export Dropdown -->
                    <div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-none d-sm-inline">{{ T::translate('Export', 'I-Export')}}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Care Manager Button -->
                    <div>
                        <a href="{{ route('admin.caremanagers.create') }}" class="w-100">
                            <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                <i class="bi bi-plus-lg me-1 me-sm-2"></i> <span class="d-none d-sm-inline">{{ T::translate('Add Manager', 'Magdagdag ng Manager')}}</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hidden form for exporting -->
            <form id="exportForm" method="POST" style="display: none;"
                action="/admin/export/caremanagers-pdf"
                data-pdf-route="/admin/export/caremanagers-pdf" 
                data-excel-route="/admin/export/caremanagers-excel">
                @csrf
                <input type="hidden" name="selected_caremanagers" id="selectedCaremanagers">
            </form>

            <div class="table-responsive">
                @if($caremanagers->count() > 0)
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="checkbox-cell">
                                <input type="checkbox" id="selectAll" />
                            </th>   
                            <th scope="col">{{ T::translate('Full Name', 'Buong Pangalan')}}</th>
                            <th scope="col">{{ T::translate('Municipality', 'Munisipalidad')}}</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Status</th>
                            <th scope="col">{{ T::translate('Actions', 'Aksyon')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($caremanagers as $caremanager)
                            <tr>
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="rowCheckbox" value="{{ $caremanager->id }}"/>
                                </td>
                                <td>{{ $caremanager->first_name }} {{ $caremanager->last_name }}</td>
                                <td>{{ $caremanager->municipality->municipality_name ?? 'N/A' }}</td>
                                <td>{{ $caremanager->mobile }}</td>
                                <td>
                                    <select class="form-select status-select" name="status" id="statusSelect{{ $caremanager->id }}" onchange="openStatusChangeCaremanagerModal(this, 'Care Manager', {{ $caremanager->id }}, '{{ $caremanager->status }}')">
                                        <option value="Active" {{ $caremanager->status == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                        <option value="Inactive" {{ $caremanager->status == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="action-icons">
                                        <form action="{{ route('admin.caremanagers.view') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="caremanager_id" value="{{ $caremanager->id }}">
                                            <button type="submit" class="btn btn-link" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.caremanagers.edit', $caremanager->id) }}" class="btn btn-link" title="Edit">
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
                    <i class="bi bi-person-badge"></i>
                    <h4>{{ T::translate('No care managers found.', 'Walang care manager na nahanap.')}}</h4>
                    <p class="text-muted">{{ T::translate('Try adjusting your search or filter criteria','Maaring i-adjust ang pagsala sa iyo\'ng paghahanap.')}}</p>
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
    <script src="{{ asset('js/forCaremanagerExport.js') }}"></script>
</body>
</html>
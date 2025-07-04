<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Admin Profiles</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
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
    @include('components.modals.statusChangeAdmin')
    @php 
    use App\Helpers\StringHelper;
    use Illuminate\Support\Facades\Auth;
    @endphp
   
    <div class="home-section">
        <div class="text-left">{{ T::translate('ADMINISTRATOR PROFILES', 'PROFILE NG ADMINISTRATOR')}}</div>
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
                        <form action="{{ route('admin.administrators.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" placeholder="{{ T::translate('Search administrators...', 'Maghanap ng mga administrator...')}}" id="searchBar" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap')}}</span>
                                </button>
                            </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div>
                        <form action="{{ route('admin.administrators.index') }}" method="GET" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-funnel"></i>
                                </span>
                                <select class="form-select" name="filter" id="filterDropdown" onchange="document.getElementById('filterForm').submit()">
                                    <option value="" {{ request('filter') ? '' : 'selected' }}>{{ T::translate('Filter by', 'Salain sa')}}</option>
                                    <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                                    <option value="organizationrole" {{ request('filter') == 'organizationrole' ? 'selected' : '' }}>{{ T::translate('Organization Role', 'Tungkulin sa Organisasyon')}}</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Export Dropdown -->
                    <div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-sm-inline">{{ T::translate('Export', 'I-Export')}}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Admin Button - Only visible to Executive Admin -->
                    <div>
                        @if(Auth::user()->organization_role_id == 1)
                            <a href="{{ route('admin.administrators.create') }}" class="w-100">
                                <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                    <i class="bi bi-plus-lg me-1 me-sm-2"></i> <span class="d-sm-inline">{{ T::translate('Add Admin', 'Magdagdag ng Administrator')}}</span>
                                </button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hidden form for exporting -->
            <form id="exportForm" method="POST" style="display: none;"
                action="/admin/export/administrators-pdf"
                data-pdf-route="/admin/export/administrators-pdf"
                data-excel-route="/admin/export/administrators-excel">
                @csrf
                <input type="hidden" name="selected_administrators" id="selectedAdministrators">
            </form>

            <div class="table-responsive">
                @if($administrators->count() > 0)
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="checkbox-cell">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th scope="col">{{ T::translate('Full Name', 'Buong Pangalan')}}</th>
                            <th scope="col" class="d-none d-sm-table-cell">{{ T::translate('Organization Role', 'Tungkilin sa Organisasyon')}}</th>
                            <th scope="col" class="d-none d-sm-table-cell">Area</th>
                            <th scope="col" class="d-none d-sm-table-cell">Mobile</th>
                            <th scope="col" class="d-none d-md-table-cell">Email</th>
                            <th scope="col">Status</th>
                            <th scope="col">{{ T::translate('Actions', 'Aksyon')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($administrators as $administrator)
                            <tr>
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="rowCheckbox" value="{{ $administrator->id }}"/>
                                </td>
                                <td>{{ $administrator->first_name }} {{ $administrator->last_name }}</td>
                                <td class="d-none d-sm-table-cell">{{ ucwords(str_replace('_', ' ', $administrator->organizationRole->role_name ?? 'N/A')) }}</td>
                                <td class="d-none d-sm-table-cell">{{ StringHelper::formatArea($administrator->organizationRole->area ?? 'N/A') }}</td>
                                <td class="d-none d-sm-table-cell">{{ $administrator->mobile }}</td>
                                <td class="d-none d-md-table-cell">{{ $administrator->email }}</td>
                                <td>
                                    <div class="position-relative" 
                                        {{ isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director' ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Executive Director status cannot be changed."' : '' }}>
                                        @if(isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director')
                                            <span class="badge bg-primary" style="font-size: 18px;">{{ T::translate('Active', 'Aktibo')}}</span>
                                            <select class="form-select d-none" name="status" id="statusSelect{{ $administrator->id }}" disabled>
                                                <option value="Active" selected>{{ T::translate('Active', 'Aktibo')}}</option>
                                            </select>
                                        @else
                                            <select class="form-select status-select" name="status" id="statusSelect{{ $administrator->id }}" 
                                                onchange="openStatusChangeAdminModal(this, 'Administrator', {{ $administrator->id }}, '{{ $administrator->status }}')">
                                                <option value="Active" {{ $administrator->status == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                                <option value="Inactive" {{ $administrator->status == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
                                            </select>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="action-icons">
                                        <form action="{{ route('admin.administrators.view') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="administrator_id" value="{{ $administrator->id }}">
                                            <button type="submit" class="btn btn-link" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                        
                                        @if(Auth::user()->organization_role_id == 1)
                                            <a href="{{ route('admin.administrators.edit', $administrator->id) }}" class="btn btn-link" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
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
                    <i class="bi bi-person-gear"></i>
                    <h4>{{ T::translate('No administrators found.', 'Walang nahanap na administrator')}}</h4>
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
    <script src="{{ asset('js/forAdministratorExport.js') }}"></script>
    
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Prevent double submission of status change forms
            const statusForm = document.getElementById('statusChangeForm');
            if (statusForm) {
                statusForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                    }
                });
            }
        });
    </script>
</body>
</html>
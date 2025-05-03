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
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">FAMILY OR RELATIVE PROFILES</div>
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
                    <form action="{{ route('care-manager.families.index') }}" method="GET" id="filterForm">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-search-alt"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Search family members..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Filter Dropdown
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" name="filter" id="filterDropdown" form="filterForm" onchange="document.getElementById('filterForm').submit();">
                            <option value="" selected>Filter by</option>
                            <option value="access" {{ request('filter') == 'access' ? 'selected' : '' }}>Access</option>
                        </select>
                    </div>
                </div> 
                Removed filter dropdown as access is now dependent on beneficiary access-->

                <!-- Export Dropdown -->
                <div class="col-6 col-md-3 col-lg-3 mb-2">
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
                <form id="exportForm" action="{{ route('care-manager.exports.family-pdf') }}" method="POST" style="display: none;"
                    data-pdf-route="{{ route('care-manager.exports.family-pdf') }}" 
                    data-excel-route="{{ route('care-manager.exports.family-excel') }}">
                    @csrf
                    <input type="hidden" name="selected_family_members" id="selectedFamilyMembers">
                </form>

                <!-- Add Family Member Button -->
                <div class="col-6 col-md-3 col-lg-3 mb-2">
                    <a href="{{ route('care-manager.families.create') }}">
                        <button class="btn btn-primary w-100" id="addButton">
                            <i class="bx bx-plus"></i> Add Family
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
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Registered Beneficiary</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($family_members as $family_member)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" value="{{ $family_member->family_member_id }}" />
                                        </td>
                                        <td>{{ $family_member->first_name }} {{ $family_member->last_name }}</td>
                                        <td>{{ $family_member->mobile}}</td>
                                        <td>{{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}</td>
                                        <td>
                                            <div class="action-icons">
                                                <form action="{{ route('care-manager.families.view') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="family_member_id" value="{{ $family_member->family_member_id }}">
                                                    <button type="submit" class="btn btn-link text-decoration-none" style="color:black;">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('care-manager.families.edit', $family_member->family_member_id) }}" class="btn btn-link text-decoration-none" style="color:black;">
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
    <script src="{{ asset('js/forFamilyExport.js') }}"></script>

</body>
</html>
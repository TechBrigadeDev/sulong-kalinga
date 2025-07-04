<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beneficiary Profiles | Manager</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profilepages.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.statusChangeBeneficiary')
    
    <div class="home-section">
        <div class="text-left">{{ T::translate('BENEFICIARY PROFILES', 'MGA PROFILE NG BENEPISYARYO') }}</div>
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
                        <form action="{{ route('care-manager.beneficiaries.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" placeholder="{{ T::translate('Search beneficiaries...', 'Maghanap ng mga benepisyaryo...') }}" id="searchBar" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap') }}</span>
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
                                <option value="" {{ request('filter') ? '' : 'selected' }}>{{ T::translate('Filter by', 'Salain ayon sa') }}</option>
                                <option value="category" {{ request('filter') == 'category' ? 'selected' : '' }}>{{ T::translate('Category', 'Kategorya') }}</option>
                                <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="municipality" {{ request('filter') == 'municipality' ? 'selected' : '' }}>{{ T::translate('Municipality', 'Munisipalidad') }}</option>
                            </select>
                        </div>
                    </div>
                    </form>

                    <!-- Export Dropdown -->
                    <div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-sm-inline">{{ T::translate('Export', 'I-export') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Beneficiary Button -->
                    <div>
                        <a href="{{ route('care-manager.beneficiaries.create') }}" class="w-100">
                            <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                <i class="bi bi-plus me-1 me-sm-2"></i> <span class="d-sm-inline">{{ T::translate('Add Beneficiary', 'Magdagdag ng Benepisyaryo') }}</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hidden form for exporting -->
            <form id="exportForm" method="POST" style="display: none;" 
                action="/care-manager/export/beneficiaries-pdf"
                data-pdf-route="/care-manager/export/beneficiaries-pdf" 
                data-excel-route="/care-manager/export/beneficiaries-excel">
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
                            <th scope="col">{{ T::translate('Full Name', 'Buong Pangalan') }}</th>
                            <th scope="col">{{ T::translate('Category', 'Kategorya') }}</th>
                            <th scope="col">{{ T::translate('Mobile', 'Mobile') }}</th>
                            <th scope="col">{{ T::translate('Barangay', 'Barangay') }}</th>
                            <th scope="col">{{ T::translate('Municipality', 'Munisipalidad') }}</th>
                            <th scope="col">Status</th>
                            <th scope="col">{{ T::translate('Actions', 'Mga Aksyon') }}</th>
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
                                        <option value="Active" {{ $beneficiary->status->status_name == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo') }}</option>
                                        <option value="Inactive" {{ $beneficiary->status->status_name == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Hindi Aktibo') }}</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="action-icons">
                                        <!-- Form to VIEW PROFILE DETAILS -->
                                        <form action="{{ route('care-manager.beneficiaries.view-details') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->beneficiary_id }}">
                                            <button type="submit" class="btn btn-link" title="{{ T::translate('View Details', 'Tingnan ang Detalye') }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('care-manager.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-link" title="{{ T::translate('Edit', 'I-edit') }}">
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
                    <h4>{{ T::translate('No beneficiaries found', 'Walang nahanap na mga benepisyaryo') }}</h4>
                    <p class="text-muted">{{ T::translate('Try adjusting your search or filter criteria', 'Subukang ayusin ang iyong paghahanap o pamantayan sa pagsala') }}</p>
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
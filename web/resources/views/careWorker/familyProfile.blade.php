<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Profiles | Care Worker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profilepages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    <div class="home-section">
        <div class="text-left">{{ T::translate('FAMILY OR RELATIVE PROFILES', 'PROFILE NG PAMILYA O KA-ANAK') }}</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="card-container">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ T::translate('Close', 'Isara') }}"></button>
                    </div>
                    @endif
                    
                    <div class="filter-section">
                        <div class="filter-row">
                            <!-- Search Bar -->
                            <div>
                                <form action="{{ route('care-worker.families.index') }}" method="GET" id="filterForm">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" name="search" placeholder="{{ T::translate('Search family members...', 'Maghanap ng mga kapamilya...') }}" id="searchBar" value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> <span class="d-none d-sm-inline">{{ T::translate('Search', 'Maghanap') }}</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Export Dropdown -->
                            <div>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download me-1 me-sm-2"></i> <span class="d-none d-sm-inline">{{ T::translate('Export', 'I-export') }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                                        <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Add Family Member Button -->
                            <div>
                                <a href="{{ route('care-worker.families.create') }}" class="w-100">
                                    <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" id="addButton">
                                        <i class="bi bi-plus-lg me-1 me-sm-2"></i> <span class="d-none d-sm-inline">{{ T::translate('Add Family', 'Magdagdag ng Pamilya') }}</span>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden form for exporting -->
                    <form id="exportForm" method="POST" style="display: none;"
                        action="/care-worker/exports/family-pdf"
                        data-pdf-route="/care-worker/exports/family-pdf" 
                        data-excel-route="/care-worker/exports/family-excel">
                        @csrf
                        <input type="hidden" name="selected_family_members" id="selectedFamilyMembers">
                    </form>

                    <div class="table-responsive">
                        @if($family_members->count() > 0)
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="checkbox-cell">
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th scope="col">{{ T::translate('Full Name', 'Buong Pangalan') }}</th>
                                    <th scope="col">{{ T::translate('Mobile Number', 'Numero sa Mobile') }}</th>
                                    <th scope="col">{{ T::translate('Registered Beneficiary', 'Nakarehistrong Benepisyaryo') }}</th>
                                    <th scope="col">{{ T::translate('Actions', 'Mga Aksyon') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($family_members as $family_member)
                                    <tr>
                                        <td class="checkbox-cell">
                                            <input type="checkbox" class="rowCheckbox" value="{{ $family_member->family_member_id }}" />
                                        </td>
                                        <td>{{ $family_member->first_name }} {{ $family_member->last_name }}</td>
                                        <td>{{ $family_member->mobile }}</td>
                                        <td>{{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}</td>
                                        <td>
                                            <div class="action-icons">
                                                <form action="{{ route('care-worker.families.view') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="family_member_id" value="{{ $family_member->family_member_id }}">
                                                    <button type="submit" class="btn btn-link" title="{{ T::translate('View Details', 'Tingnan ang Detalye') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('care-worker.families.edit', $family_member->family_member_id) }}" class="btn btn-link" title="{{ T::translate('Edit', 'I-edit') }}">
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
                            <h4>{{ T::translate('No family members found', 'Walang nahanap na mga kapamilya') }}</h4>
                            <p class="text-muted">{{ T::translate('Try adjusting your search criteria', 'Subukang ayusin ang iyong pamantayan sa paghahanap') }}</p>
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
    <script src="{{ asset('js/forFamilyExport.js') }}"></script>
</body>
</html>
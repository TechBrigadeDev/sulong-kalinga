<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Archived Shift Histories</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        #home-content {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        #home-content th,
        #home-content td {
            font-size: 0.9rem; /* Increased font size */
            vertical-align: middle;
        }

        #home-content .card-header,
        #home-content .form-label {
            font-size: clamp(0.9rem, 1.1vw, 1.1rem);
        }

        #home-content .btn {
            font-size: 0.85rem; /* Slightly increased button text size */
        }
        
        .action-icons a {
            text-decoration: none;
            color: #333;
            margin: 0 5px;
            font-size: 1rem; /* Increased icon size */
        }
        
        .action-icons a:hover {
            color: #4e73df;
        }
        
        .table-responsive {
            min-height: 400px;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.85rem; /* Increased badge text size */
        }
    </style>
</head>
<body>
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="text-left">ARCHIVED SHIFT HISTORIES</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                            <h5 class="mb-0 text-primary">Archived Care Worker Shift Records</h5>
                            <a href="{{ route('admin.shift.histories.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Return to Current Histories
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.shift.histories.archived') }}" method="GET" id="searchFilterForm">
                                <div class="row mb-3 align-items-center">
                                    <!-- Search Bar -->
                                    <div class="col-12 col-md-5 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Search by care worker name..." 
                                                id="searchBar" name="search" value="{{ $search ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Date Filter -->
                                    <div class="col-12 col-md-4 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-calendar"></i>
                                            </span>
                                            <input type="date" class="form-control" 
                                                id="dateFilter" name="date" value="{{ $date ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Filter Button -->
                                    <div class="col-6 col-md-2 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-funnel"></i> Apply Filters
                                        </button>
                                    </div>

                                    <!-- Reset Button -->
                                    <div class="col-6 col-md-1 mb-2">
                                        <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Care Worker</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Shift Start</th>
                                            <th scope="col">Shift End</th>
                                            <th scope="col">Municipality</th>  <!-- Changed from "Location Tags" -->
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($shifts as $shift)
                                            <tr>
                                                <td>
                                                    <strong>
                                                        {{ $shift->careWorker->first_name ?? '' }} {{ $shift->careWorker->last_name ?? '' }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($shift->time_in)->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($shift->time_in)->format('h:i A') }}
                                                </td>
                                                <td>
                                                    {{ $shift->time_out ? \Carbon\Carbon::parse($shift->time_out)->format('h:i A') : '--:--' }}
                                                </td>
                                                <td>
                                                    {{ $shift->careWorker->municipality ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-icons">
                                                        <a href="{{ route('admin.shift.histories.shiftDetails', ['shiftId' => $shift->shift_id]) }}" title="View Shift Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="#" title="Download Report">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No archived shift records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($shifts->hasPages())
                                <nav aria-label="Page navigation" class="mt-4">
                                    {{ $shifts->withQueryString()->links() }}
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // Function to reset filters
        function resetFilters() {
            document.getElementById('searchBar').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('searchFilterForm').submit();
        }
    </script>
</body>
</html>
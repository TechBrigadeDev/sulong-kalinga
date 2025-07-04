<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Histories | Manager</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shiftHistory.css') }}">
</head>
<body>
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="text-left">SHIFT HISTORY</div>            
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="filter-section">
                        <form action="{{ route('care-manager.shift.histories.index') }}" method="GET" id="searchFilterForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5 col-12">
                                    <label for="searchBar" class="filter-label">Search Care Worker</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Search by name..." 
                                            id="searchBar" name="search" value="{{ $search ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label for="dateFilter" class="filter-label">Filter by Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" 
                                            id="dateFilter" name="date" value="{{ $date ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-2 col-6">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-1 col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()" title="Reset filters">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Care Worker Shift Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Care Worker</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Shift Time</th>
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
                                                    {{ \Carbon\Carbon::parse($shift->time_in)->format('h:i A') }} - 
                                                    {{ $shift->updated_at ? \Carbon\Carbon::parse($shift->updated_at)->format('h:i A') : '--:--' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-icons">
                                                        <a href="{{ route('care-manager.shift.histories.shiftDetails', ['shiftId' => $shift->id]) }}">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('care-manager.shift.histories.exportPdf', ['shiftId' => $shift->id]) }}" title="Download Report" target="_blank">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No shift records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($shifts->hasPages())
                                <nav aria-label="Page navigation" class="mt-4">
                                    {{ $shifts->withQueryString()->links('pagination::bootstrap-5') }}
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function resetFilters() {
            document.getElementById('searchBar').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('searchFilterForm').submit();
        }
    </script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
</body>
</html>
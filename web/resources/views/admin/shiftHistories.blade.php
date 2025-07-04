<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Histories | Admin</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shiftHistory.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="text-left">{{ T::translate('SHIFT HISTORY', 'KASAYSAYAN NG SHIFT')}}</div>            
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="filter-section">
                        <form action="{{ route('admin.shift.histories.index') }}" method="GET" id="searchFilterForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5 col-12">
                                    <label for="searchBar" class="filter-label">{{ T::translate('Search Care Worker', 'Maghanap ng care worker')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="{{ T::translate('Search by name...', 'Maghanap ayon sa pangalan...') }}" 
                                            id="searchBar" name="search" value="{{ $search ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label for="dateFilter" class="filter-label">{{ T::translate('Filter by Date', 'Maghanap ayon sa Petsa')}}</label>
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
                                        <i class="bi bi-funnel me-1"></i> {{ T::translate('Apply', 'I-Apply')}}
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
                            <h5 class="mb-0">{{ T::translate('Care Worker Shift Records', 'Talaan ng Shift ng Tagapag-alaga')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ T::translate('Care Worker', 'Tagapag-alaga')}}</th>
                                            <th scope="col">{{ T::translate('Date', 'Petsa')}}</th>
                                            <th scope="col">{{ T::translate('Shift Time', 'Oras ng Shift')}}</th>
                                            <th scope="col" class="text-center">{{ T::translate('Actions', 'Aksyon')}}</th>
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
                                                    {{ $shift->time_out ? \Carbon\Carbon::parse($shift->time_out)->format('h:i A') : '--:--' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-icons">
                                                        <a href="{{ route('admin.shift.histories.shiftDetails', ['shiftId' => $shift->id]) }}">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.shift.histories.exportPdf', ['shiftId' => $shift->id]) }}" title="Download Report" target="_blank">
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
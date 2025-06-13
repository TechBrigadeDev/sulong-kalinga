<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Histories | Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shiftHistory.css') }}">
</head>
<body>
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="text-left">SHIFT HISTORY</div>            
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="filter-section">
                        <form action="{{ route('admin.shift.histories.index') }}" method="GET" id="searchFilterForm">
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
                                            <th scope="col">Municipality</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>John Smith</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>08:00 AM - 04:00 PM</td>
                                            <td>Mondragon</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="{{ route('admin.shift.histories.shiftDetails') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Maria Garcia</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>09:00 AM - 05:00 PM</td>
                                            <td>San Roque</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="{{ route('admin.shift.histories.shiftDetails') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>David Johnson</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>02:00 PM - 10:00 PM</td>
                                            <td>Mondragon</td>
                                            <td><span class="badge badge-warning">In Progress</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="{{ route('admin.shift.histories.shiftDetails') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sarah Wilson</strong></td>
                                            <td>May 19, 2025</td>
                                            <td>08:00 AM - 04:00 PM</td>
                                            <td>San Roque</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="{{ route('admin.shift.histories.shiftDetails') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
</body>
</html>
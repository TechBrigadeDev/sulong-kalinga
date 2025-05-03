<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

     <!-- Welcome Back Modal -->
     <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">Welcome Back!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bx bxs-party display-4 text-primary mb-3'></i>
                    <h4>Hello, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h4>
                    <p>Welcome back to your Care Manager Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
      <div class="text-left">DASHBOARD CARE MANAGER</div>
        <div class="container-fluid">
            <div class="row boxbox">
              <!-- Statistics Row -->
              <div class="col-12 col-lg-8">
                  <div class="row" id="statistics">
                      <div class="col-6" id="caregiverStats" >
                        <div class="row" style="padding:5px; height: 100%;">
                            <div class="col-12 caregiverStatsBox" style="background-color: green;">
                                <h6 class="mt-1">Caregiver Statistics</h6>
                            </div>
                        </div>
                      </div>
                      <div class="col-6" id="beneficiaryStats" >
                        <div class="row" style="padding:5px; height: 100%; padding-left: 0;">
                            <div class="col-12 beneficiaryStatsBox" style="background-color: blue;">
                                <h6 class="mt-1">Beneficiary Statistics</h6>
                            </div>
                        </div>                          
                      </div>
                  </div>
                  <div class="row" id="recentReports" style="background-color: white;">
                      <div class="col-12 recentReportBox">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <h6 class="mt-1">Recent Reports</h6>
                                </div>
                                <div class="col-md-5 col-sm-7 pe-1">
                                    <form action="" method="GET" id="filterForm">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bx bx-search-alt"></i>
                                            </span>
                                            <input type="text" class="form-control" name="search" placeholder="Search report.." id="searchBar" value="{{ request('search') }}">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-3 col-sm-5 ps-0">
                                    <div class="input-group" id="filterGroup">
                                        <span class="input-group-text">
                                            <i class="bx bx-filter-alt"></i>
                                        </span>
                                        <select class="form-select" name="filter" id="filterDropdown" form="filterForm" onchange="document.getElementById('filterForm').submit();">
                                            <option value="" selected>Filter by</option>
                                            <option value="access" {{ request('filter') == 'access' ? 'selected' : '' }}>Access</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Author</th>
                                                <th style="width: 150px">Report Type</th>
                                                <th style="width: 120px">Date Uploaded</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>FIRST NAME LAST NAME</td>
                                                    <td>Weekly Careplan</td>
                                                    <td>03-25-2025</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                      </div>
                  </div>
              </div>
              <!-- Events and Performance Column -->
              <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                  <div class="row" id="events">
                      <div class="col-12 eventsBox" style="background-color: orange;">
                          <h6 class="mt-1">Upcoming Events</h6>
                      </div>
                  </div>
                  <div class="row" id="performance">
                      <div class="col-12 performanceBox" style="background-color: pink;">
                          <h6 class="mt-1">Caregiver Performance</h6>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </div>
   
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($showWelcome)
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeBackModal'));
                welcomeModal.show();
            @endif
        });
    </script>
</body>
</html>
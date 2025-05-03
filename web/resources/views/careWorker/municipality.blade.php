<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/municipality.css') }}">
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    @include('components.modals.confirmDelete')
    
    <div class="home-section">
        <div class="text-left">MUNICIPALITY CM</div>
        <div class="container-fluid text-center">
            <div class="row mb-3 align-items-center">
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-5 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-search-alt"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search reports..." id="searchBar">
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" id="filterDropdown">
                            <option value="mondragon" selected>Municipality of Mondragon</option>
                            <option value="author">Municipality of SanRoque</option>
                        </select>
                    </div>
                </div>

                <!-- Add Report Button -->
                <div class="col-12 col-md-6 col-lg-3 mb-2" style="display: none">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bx bx-plus"></i> Add Municipality
                    </button>
                </div>
            </div>


            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">Assigned Caregiver</th>
                                    <th scope="col">Assigned Care Manager</th>
                                    <th scope="col">Beneficiaries</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 33; $i++)
                                    <tr>
                                        <td>Barangay</td>
                                        <td>Mark</td>
                                        <td>@mdo</td>
                                        <td>Otto</td>
                                        <td>
                                            <div class="action-icons">
                                                <i class='bx bx-trash' data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteTarget(this)"></i>
                                                <i class='bx bxs-edit'></i>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/deleteModal.js') }}"></script>
</body>
</html>
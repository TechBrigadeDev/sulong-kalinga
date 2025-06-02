<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Care Plans</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewAllCareplan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button onclick="history.back()" class="btn btn-outline-primary btn-md">
                <i class="bi bi-arrow-left me-1"></i>Back
            </button>
            <div class="text-center flex-grow-1 me-4" style="font-size: 20px; font-weight: bold;">
                CARE PLAN REPORTS
            </div>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="search-container">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" class="form-control search-input" placeholder="Search reports...">
                                </div>
                                <div class="filter-container">
                                    <button class="btn btn-outline-secondary filter-btn active">All</button>
                                    <button class="btn btn-outline-secondary filter-btn">Pending</button>
                                    <button class="btn btn-outline-secondary filter-btn">Acknowledged</button>
                                    <button class="btn btn-outline-secondary filter-btn">Denied</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Date Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Dr. Sarah Johnson</td>
                                            <td><span class="status-badge status-pending">Pending Review</span></td>
                                            <td>Jun 15, 2023</td>
                                            <td class="actions-cell">
                                                <button class="btn-acknowledge" title="Acknowledge">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn-deny" title="Deny">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                                <button class="btn-view" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Nurse Mark Williams</td>
                                            <td><span class="status-badge status-approved">Approved</span></td>
                                            <td>Jun 10, 2023</td>
                                            <td class="actions-cell">
                                                <button class="btn-view" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <span class="status-badge status-acknowledged">Acknowledged</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Dr. Emily Chen</td>
                                            <td><span class="status-badge status-denied">Denied</span></td>
                                            <td>May 28, 2023</td>
                                            <td class="actions-cell">
                                                <button class="btn-view" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <span class="status-badge status-denied">Denied</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Nurse Lisa Rodriguez</td>
                                            <td><span class="status-badge status-pending">Pending Review</span></td>
                                            <td>Jun 18, 2023</td>
                                            <td class="actions-cell">
                                                <button class="btn-acknowledge" title="Acknowledge">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn-deny" title="Deny">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                                <button class="btn-view" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Empty state (hidden by default) -->
                            <div class="empty-state d-none">
                                <i class="bi bi-file-earmark-text"></i>
                                <h4>No Reports Found</h4>
                                <p>There are currently no care plan reports available.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
                        <div class="pagination-info text-muted mb-2 mb-md-0">Showing 1 to 4 of 12 entries</div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    // In real implementation, this would filter the table
                });
            });
            
            // Handle acknowledge action
            document.querySelectorAll('.btn-acknowledge').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelector('.status-badge').className = 'status-badge status-acknowledged';
                    row.querySelector('.status-badge').textContent = 'Acknowledged';
                    
                    // Replace action buttons with status badge
                    const actionsCell = row.querySelector('td:last-child');
                    actionsCell.innerHTML = `
                        <button class="btn-view me-2" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        <span class="status-badge status-acknowledged">Acknowledged</span>
                    `;
                });
            });
            
            // Handle deny action
            document.querySelectorAll('.btn-deny').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelector('.status-badge').className = 'status-badge status-denied';
                    row.querySelector('.status-badge').textContent = 'Denied';
                    
                    // Replace action buttons with status badge
                    const actionsCell = row.querySelector('td:last-child');
                    actionsCell.innerHTML = `
                        <button class="btn-view me-2" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        <span class="status-badge status-denied">Denied</span>
                    `;
                });
            });
        });
    </script>
</body>
</html>
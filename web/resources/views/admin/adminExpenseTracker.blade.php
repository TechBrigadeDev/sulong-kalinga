<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSE - Expense Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/expensetracker.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">EXPENSE TRACKER</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Summary Cards -->
                    <div class="row mb-3 g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-primary bg-opacity-10 border-primary border-opacity-25">
                                <h6 class="text-muted">Total Expenses (Monthly)</h6>
                                <h3 class="text-primary">₱24,750</h3>
                                <small class="text-success"><i class="bi bi-arrow-up"></i> 12% from last month</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-warning bg-opacity-10 border-warning border-opacity-25">
                                <h6 class="text-muted">Budget Remaining</h6>
                                <h3 class="text-warning">₱5,250</h3>
                                <small class="text-danger"><i class="bi bi-arrow-down"></i> 17% of budget used</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-success bg-opacity-10 border-success border-opacity-25">
                                <h6 class="text-muted">Most Spent Category</h6>
                                <h3 class="text-success">Medical Supplies</h3>
                                <small>₱8,900 (36% of total)</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-info bg-opacity-10 border-info border-opacity-25">
                                <h6 class="text-muted">Current Budget</h6>
                                <h3 class="text-info">₱30,000</h3>
                                <small>Last updated: Oct 1, 2023</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons and Filters -->
                    <div class="row mb-2 align-items-center g-2">
                        <div class="col-md-7 col-lg-8">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary btn-action" id="addExpenseBtn">
                                    <i class="bi bi-plus-circle"></i> Add Expense
                                </button>
                                <button class="btn btn-outline-primary btn-action" id="addBudgetBtn">
                                    <i class="bi bi-wallet2"></i> Add Budget
                                </button>
                                <button class="btn btn-outline-secondary btn-action" id="exportBtn">
                                    <i class="bi bi-download"></i> Export
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" id="categoryFilter">
                                        <option value="">All Categories</option>
                                        <option value="medical_supplies">Medical Supplies</option>
                                        <option value="food_nutrition">Food & Nutrition</option>
                                        <option value="transportation">Transportation</option>
                                        <option value="facility_maintenance">Facility Maintenance</option>
                                        <option value="staff_training">Staff Training</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="month" class="form-control form-control-sm" id="monthFilter" value="2023-10">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Left Column - Expenses and Budget History -->
                        <div class="col-lg-8">
                            <!-- Expenses Card -->
                            <div class="card expense-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">Recent Expenses</h5>
                                        <span class="badge bg-primary">This Month</span>
                                    </div>
                                    
                                    <div id="recentExpensesContainer">
                                        <!-- Expense items will be dynamically inserted here -->
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <button class="btn btn-outline-primary btn-action" id="viewAllExpensesBtn">
                                            <i class="bi bi-list-ul"></i> View All Expenses
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Budget History Card -->
                            <div class="card expense-card mt-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">Budget History</h5>
                                        <button class="btn btn-sm btn-outline-primary" id="viewFullHistoryBtn">
                                            <i class="bi bi-clock-history"></i> Full History
                                        </button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 25%;">Date</th>
                                                    <th style="width: 50%;">Description</th>
                                                    <th style="width: 25%;" class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="budgetHistoryContainer">
                                                <!-- Budget history items will be dynamically inserted here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Charts and Budget Progress -->
                        <div class="col-lg-4">
                            <div class="card expense-card">
                                <div class="card-body">
                                    <h5 class="card-title">Expense Breakdown</h5>
                                    <div class="chart-container" style="position: relative; height: 200px;">
                                        <canvas id="expenseChart"></canvas>
                                    </div>
                                    <div class="mt-3" id="chartLegend">
                                        <!-- Legend will be dynamically inserted here -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Budget Progress -->
                            <div class="card expense-card mt-3">
                                <div class="card-body">
                                    <h5 class="card-title">Budget Progress</h5>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 83%" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>₱5,250 remaining</small>
                                        <small>₱30,000 total</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-success"><i class="bi bi-arrow-down"></i> 17% remaining</small>
                                        <small class="text-danger"><i class="bi bi-arrow-up"></i> 83% spent</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="expenseForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Expense Title*</label>
                                    <input type="text" class="form-control" name="title" required placeholder="E.g., Medical supplies for MHCS">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Amount (₱)*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="amount" step="0.01" min="0" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date*</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category*</label>
                                    <select class="form-select" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="medical_supplies">Medical Supplies</option>
                                        <option value="medications">Medications</option>
                                        <option value="food_nutrition">Food & Nutrition</option>
                                        <option value="transportation">Transportation/Fuel</option>
                                        <option value="facility_maintenance">Facility Maintenance</option>
                                        <option value="staff_training">Staff Training</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Payment Method*</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="gcash">GCash</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Receipt/Reference No.</label>
                                    <input type="text" class="form-control" name="receipt_no" placeholder="OR/Invoice number">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Details about the expense"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Attach Receipt (Optional)</label>
                            <input type="file" class="form-control" name="receipt" accept="image/*,.pdf">
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveExpenseBtn">Save Expense</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Budget Modal -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add Budget Allocation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="budgetForm">
                        <div class="mb-3">
                            <label class="form-label">Amount (₱)*</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" name="amount" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Budget Period*</label>
                            <div class="budget-date-picker">
                                <input type="date" class="form-control" name="start_date" required placeholder="Start date">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" name="end_date" required placeholder="End date">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Budget Type*</label>
                            <select class="form-select" name="type" required>
                                <option value="">Select Type</option>
                                <option value="regular">Regular Allocation</option>
                                <option value="supplemental">Supplemental Budget</option>
                                <option value="grant">Grant Funding</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Purpose of this budget allocation"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBudgetBtn">Save Budget</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View All Expenses Modal -->
    <div class="modal fade" id="allExpensesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">All Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="allExpensesContainer">
                                <!-- All expenses will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full History Modal -->
    <div class="modal fade" id="fullHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Full Budget History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Period</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="fullHistoryContainer">
                                <!-- Full history will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Dummy data for expenses
        const expensesData = [
            {
                id: 1,
                title: "Medical Supplies Purchase",
                category: "medical_supplies",
                description: "For Mobile Health Care Service",
                amount: 3250,
                date: "2023-10-15",
                payment_method: "bank_transfer",
                receipt_no: "INV-2023-001"
            },
            {
                id: 2,
                title: "Weekly Groceries",
                category: "food_nutrition",
                description: "For Group Home in Bulacan",
                amount: 2800,
                date: "2023-10-14",
                payment_method: "cash",
                receipt_no: "OR-2023-045"
            },
            {
                id: 3,
                title: "Fuel for Mobile Health Van",
                category: "transportation",
                description: "Northern Samar route",
                amount: 1750,
                date: "2023-10-12",
                payment_method: "gcash",
                receipt_no: "GC-2023-112"
            },
            {
                id: 4,
                title: "Facility Repairs",
                category: "facility_maintenance",
                description: "Wellness Center maintenance",
                amount: 4500,
                date: "2023-10-10",
                payment_method: "check",
                receipt_no: "CHK-2023-078"
            },
            {
                id: 5,
                title: "Caregiver Training",
                category: "staff_training",
                description: "Home care techniques workshop",
                amount: 6800,
                date: "2023-10-08",
                payment_method: "bank_transfer",
                receipt_no: "INV-2023-002"
            },
            {
                id: 6,
                title: "Office Supplies",
                category: "other",
                description: "Monthly office supplies",
                amount: 1250,
                date: "2023-10-05",
                payment_method: "cash",
                receipt_no: "OR-2023-046"
            }
        ];

        // Dummy data for budget history
        const budgetHistoryData = [
            {
                id: 1,
                amount: 30000,
                start_date: "2023-10-01",
                end_date: "2023-10-31",
                type: "regular",
                description: "Monthly budget allocation"
            },
            {
                id: 2,
                amount: 2500,
                start_date: "2023-09-15",
                end_date: "2023-09-30",
                type: "supplemental",
                description: "Additional funds for MHCS"
            },
            {
                id: 3,
                amount: 28000,
                start_date: "2023-09-01",
                end_date: "2023-09-30",
                type: "regular",
                description: "Monthly budget allocation"
            },
            {
                id: 4,
                amount: -3000,
                start_date: "2023-08-20",
                end_date: "2023-08-31",
                type: "adjustment",
                description: "Budget adjustment"
            },
            {
                id: 5,
                amount: 25000,
                start_date: "2023-08-01",
                end_date: "2023-08-31",
                type: "regular",
                description: "Monthly budget allocation"
            },
            {
                id: 6,
                amount: 5000,
                start_date: "2023-07-15",
                end_date: "2023-07-31",
                type: "grant",
                description: "USAID Grant additional funds"
            }
        ];

        // Category icons mapping
        const categoryIcons = {
            medical_supplies: "bi-capsule-pill",
            food_nutrition: "bi-basket",
            transportation: "bi-truck",
            facility_maintenance: "bi-house-gear",
            staff_training: "bi-people",
            other: "bi-question-circle"
        };

        // Category colors mapping
        const categoryColors = {
            medical_supplies: "primary",
            food_nutrition: "success",
            transportation: "info",
            facility_maintenance: "warning",
            staff_training: "danger",
            other: "secondary"
        };

        // Chart colors mapping
        const chartColors = {
            medical_supplies: '#0d6efd', // primary
            food_nutrition: '#198754', // success
            transportation: '#0dcaf0', // info
            facility_maintenance: '#ffc107', // warning
            staff_training: '#dc3545', // danger
            other: '#6c757d' // secondary
        };

        // Chart instance variable
        let expenseChart;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            const addExpenseModal = new bootstrap.Modal(document.getElementById('addExpenseModal'));
            const addBudgetModal = new bootstrap.Modal(document.getElementById('addBudgetModal'));
            const allExpensesModal = new bootstrap.Modal(document.getElementById('allExpensesModal'));
            const fullHistoryModal = new bootstrap.Modal(document.getElementById('fullHistoryModal'));
            
            // Initialize chart
            initializeChart();
            
            // Render initial data
            renderRecentExpenses();
            renderBudgetHistory();
            renderAllExpenses();
            renderFullHistory();

            // Button event listeners
            document.getElementById('addExpenseBtn').addEventListener('click', function() {
                addExpenseModal.show();
            });
            
            document.getElementById('addBudgetBtn').addEventListener('click', function() {
                addBudgetModal.show();
            });
            
            document.getElementById('viewAllExpensesBtn').addEventListener('click', function() {
                allExpensesModal.show();
            });
            
            document.getElementById('viewFullHistoryBtn').addEventListener('click', function() {
                fullHistoryModal.show();
            });
            
            // Save Expense functionality
            document.getElementById('saveExpenseBtn').addEventListener('click', function() {
                const form = document.getElementById('expenseForm');
                if (form.checkValidity()) {
                    // In a real app, this would be an AJAX call to the server
                    alert('Expense added successfully!');
                    addExpenseModal.hide();
                    form.reset();
                    
                    // For demo purposes, we'll add the expense to our dummy data
                    const newExpense = {
                        id: expensesData.length + 1,
                        title: form.title.value,
                        category: form.category.value,
                        description: form.description.value,
                        amount: parseFloat(form.amount.value),
                        date: form.date.value,
                        payment_method: form.payment_method.value,
                        receipt_no: form.receipt_no.value
                    };
                    expensesData.unshift(newExpense);
                    renderRecentExpenses();
                    renderAllExpenses();
                    updateChart();
                } else {
                    form.reportValidity();
                }
            });
            
            // Save Budget functionality
            document.getElementById('saveBudgetBtn').addEventListener('click', function() {
                const form = document.getElementById('budgetForm');
                if (form.checkValidity()) {
                    // In a real app, this would be an AJAX call to the server
                    alert('Budget allocation added successfully!');
                    addBudgetModal.hide();
                    form.reset();
                    
                    // For demo purposes, we'll add the budget to our dummy data
                    const newBudget = {
                        id: budgetHistoryData.length + 1,
                        amount: parseFloat(form.amount.value),
                        start_date: form.start_date.value,
                        end_date: form.end_date.value,
                        type: form.type.value,
                        description: form.description.value
                    };
                    budgetHistoryData.unshift(newBudget);
                    renderBudgetHistory();
                    renderFullHistory();
                } else {
                    form.reportValidity();
                }
            });
            
            // Export button functionality
            document.getElementById('exportBtn').addEventListener('click', function() {
                alert('Export functionality would be implemented here');
            });
            
            // Filter functionality
            document.getElementById('categoryFilter').addEventListener('change', function() {
                renderRecentExpenses();
                updateChart();
            });
            
            document.getElementById('monthFilter').addEventListener('change', function() {
                renderRecentExpenses();
                updateChart();
            });
            
            // Initialize the chart
            function initializeChart() {
                const ctx = document.getElementById('expenseChart').getContext('2d');
                expenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
                
                // Initial chart update
                updateChart();
            }
            
            // Update chart based on filters
            function updateChart() {
                const categoryFilter = document.getElementById('categoryFilter').value;
                const monthFilter = document.getElementById('monthFilter').value;
                
                // Filter expenses
                let filteredExpenses = [...expensesData];
                
                if (categoryFilter) {
                    filteredExpenses = filteredExpenses.filter(expense => expense.category === categoryFilter);
                }
                
                if (monthFilter) {
                    const [year, month] = monthFilter.split('-');
                    filteredExpenses = filteredExpenses.filter(expense => {
                        const expenseDate = new Date(expense.date);
                        return expenseDate.getFullYear() == year && (expenseDate.getMonth() + 1) == month;
                    });
                }
                
                // Group by category and sum amounts
                const categoryTotals = {};
                filteredExpenses.forEach(expense => {
                    if (!categoryTotals[expense.category]) {
                        categoryTotals[expense.category] = 0;
                    }
                    categoryTotals[expense.category] += expense.amount;
                });
                
                // Prepare chart data
                const labels = [];
                const data = [];
                const backgroundColors = [];
                
                Object.keys(categoryTotals).forEach(category => {
                    labels.push(formatCategory(category));
                    data.push(categoryTotals[category]);
                    backgroundColors.push(chartColors[category]);
                });
                
                // Update chart
                expenseChart.data.labels = labels;
                expenseChart.data.datasets[0].data = data;
                expenseChart.data.datasets[0].backgroundColor = backgroundColors;
                expenseChart.update();
                
                // Update legend
                updateChartLegend(labels, data, backgroundColors);
            }
            
            // Update chart legend
            function updateChartLegend(labels, data, colors) {
                const legendContainer = document.getElementById('chartLegend');
                legendContainer.innerHTML = '';
                
                if (data.length === 0) {
                    legendContainer.innerHTML = '<div class="text-center text-muted">No data available</div>';
                    return;
                }
                
                const total = data.reduce((a, b) => a + b, 0);
                
                labels.forEach((label, index) => {
                    const percentage = Math.round((data[index] / total) * 100);
                    
                    const legendItem = document.createElement('div');
                    legendItem.className = 'd-flex align-items-center mb-2';
                    legendItem.innerHTML = `
                        <span class="badge me-2" style="background-color: ${colors[index]}; width: 12px; height: 12px;"></span>
                        <small>${label} (${percentage}%)</small>
                        <span class="ms-auto fw-bold">₱${data[index].toLocaleString()}</span>
                    `;
                    legendContainer.appendChild(legendItem);
                });
            }
            
            // Function to render recent expenses
            function renderRecentExpenses() {
                const container = document.getElementById('recentExpensesContainer');
                const categoryFilter = document.getElementById('categoryFilter').value;
                const monthFilter = document.getElementById('monthFilter').value;
                
                // Filter expenses
                let filteredExpenses = [...expensesData];
                
                if (categoryFilter) {
                    filteredExpenses = filteredExpenses.filter(expense => expense.category === categoryFilter);
                }
                
                if (monthFilter) {
                    const [year, month] = monthFilter.split('-');
                    filteredExpenses = filteredExpenses.filter(expense => {
                        const expenseDate = new Date(expense.date);
                        return expenseDate.getFullYear() == year && (expenseDate.getMonth() + 1) == month;
                    });
                }
                
                // Get only the first 3 expenses for the recent view
                const recentExpenses = filteredExpenses.slice(0, 3);
                
                // Clear container
                container.innerHTML = '';
                
                // Add expenses to container
                recentExpenses.forEach(expense => {
                    const iconClass = categoryIcons[expense.category] || 'bi-question-circle';
                    const colorClass = categoryColors[expense.category] || 'secondary';
                    
                    const expenseEl = document.createElement('div');
                    expenseEl.className = 'd-flex align-items-center mb-3 p-2 bg-light rounded';
                    expenseEl.innerHTML = `
                        <div class="expense-category-icon bg-${colorClass}-bg-opacity-10 text-${colorClass}">
                            <i class="bi ${iconClass}"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0" style="font-size: clamp(0.85rem, 1.5vw, 1rem);">${expense.title}</h6>
                            <small class="text-muted">${expense.description}</small>
                        </div>
                        <div class="text-end ms-3">
                            <span class="expense-amount text-danger">₱${expense.amount.toLocaleString()}</span>
                            <div class="expense-date">${formatDate(expense.date)}</div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-secondary ms-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="editExpense(${expense.id})"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="deleteExpense(${expense.id})"><i class="bi bi-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    `;
                    container.appendChild(expenseEl);
                });
                
                // If no expenses match the filter
                if (recentExpenses.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                            <p>No expenses found matching your criteria</p>
                        </div>
                    `;
                }
            }
            
            // Function to render budget history
            function renderBudgetHistory() {
                const container = document.getElementById('budgetHistoryContainer');
                
                // Clear container
                container.innerHTML = '';
                
                // Get only the first 4 budget items for the recent view
                const recentBudgets = budgetHistoryData.slice(0, 4);
                
                // Add budget items to container
                recentBudgets.forEach(budget => {
                    const isNegative = budget.amount < 0;
                    const amountClass = isNegative ? 'text-danger' : 'text-success';
                    const amountSign = isNegative ? '' : '+';
                    
                    const budgetEl = document.createElement('tr');
                    budgetEl.innerHTML = `
                        <td>${formatDate(budget.start_date)}</td>
                        <td>${budget.description}</td>
                        <td class="${amountClass} text-end">${amountSign}₱${Math.abs(budget.amount).toLocaleString()}</td>
                    `;
                    container.appendChild(budgetEl);
                });
            }
            
            // Function to render all expenses
            function renderAllExpenses() {
                const container = document.getElementById('allExpensesContainer');
                
                // Clear container
                container.innerHTML = '';
                
                // Add all expenses to container
                expensesData.forEach(expense => {
                    const iconClass = categoryIcons[expense.category] || 'bi-question-circle';
                    const colorClass = categoryColors[expense.category] || 'secondary';
                    
                    const expenseEl = document.createElement('tr');
                    expenseEl.innerHTML = `
                        <td>${formatDate(expense.date)}</td>
                        <td>
                            <span class="badge bg-${colorClass}-bg-opacity-10 text-${colorClass}">
                                <i class="bi ${iconClass} me-1"></i>
                                ${formatCategory(expense.category)}
                            </span>
                        </td>
                        <td>${expense.title}</td>
                        <td class="text-danger">₱${expense.amount.toLocaleString()}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="editExpense(${expense.id})"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="deleteExpense(${expense.id})"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    `;
                    container.appendChild(expenseEl);
                });
            }
            
            // Function to render full history
            function renderFullHistory() {
                const container = document.getElementById('fullHistoryContainer');
                
                // Clear container
                container.innerHTML = '';
                
                // Add all budget items to container
                budgetHistoryData.forEach(budget => {
                    const isNegative = budget.amount < 0;
                    const amountClass = isNegative ? 'text-danger' : 'text-success';
                    const amountSign = isNegative ? '' : '+';
                    
                    const budgetEl = document.createElement('tr');
                    budgetEl.innerHTML = `
                        <td>${formatDate(budget.start_date)}</td>
                        <td>${formatDate(budget.start_date)} to ${formatDate(budget.end_date)}</td>
                        <td>${formatBudgetType(budget.type)}</td>
                        <td>${budget.description}</td>
                        <td class="${amountClass}">${amountSign}₱${Math.abs(budget.amount).toLocaleString()}</td>
                    `;
                    container.appendChild(budgetEl);
                });
            }
            
            // Helper function to format date
            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                return new Date(dateString).toLocaleDateString(undefined, options);
            }
            
            // Helper function to format category
            function formatCategory(category) {
                const categoryNames = {
                    medical_supplies: "Medical Supplies",
                    food_nutrition: "Food & Nutrition",
                    transportation: "Transportation",
                    facility_maintenance: "Facility",
                    staff_training: "Training",
                    other: "Other"
                };
                return categoryNames[category] || category;
            }
            
            // Helper function to format budget type
            function formatBudgetType(type) {
                const typeNames = {
                    regular: "Regular",
                    supplemental: "Supplemental",
                    grant: "Grant",
                    adjustment: "Adjustment"
                };
                return typeNames[type] || type;
            }
        });
        
        // Global functions for action buttons
        function editExpense(id) {
            alert(`Would edit expense with ID: ${id}`);
            // In a real implementation, this would:
            // 1. Fetch the expense data
            // 2. Populate the edit form
            // 3. Show the edit modal
        }
        
        function deleteExpense(id) {
            if (confirm('Are you sure you want to delete this expense?')) {
                alert(`Would delete expense with ID: ${id}`);
                // In a real implementation, this would:
                // 1. Send delete request to server
                // 2. Remove from local data
                // 3. Refresh the views
            }
        }
    </script>
</body>
</html>
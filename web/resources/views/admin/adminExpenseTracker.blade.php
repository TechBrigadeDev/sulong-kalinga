<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Custom styles for Expense Tracker */
        .summary-card {
            padding: 20px;
            border-radius: 0.5rem;
            border: 1px solid;
            height: 100%;
        }
        
        .expense-card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .expense-item {
            padding: 12px;
            border-radius: 0.5rem;
            margin-bottom: 12px;
            border-left: 4px solid transparent;
            background: #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .expense-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .expense-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .expense-detail {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .expense-amount {
            font-weight: 700;
            color: #212529;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .chart-legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        /* Action buttons styling */
        .expense-actions {
            display: flex;
            gap: 6px;
            opacity: 0.6;
            transition: all 0.2s ease;
        }
        
        .expense-item:hover .expense-actions {
            opacity: 1;
        }
        
        .btn-action-icon {
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            color: #fff;
            background-color: #6c757d;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-action-icon.edit {
            background-color: #0d6efd;
        }
        
        .btn-action-icon.delete {
            background-color: #dc3545;
        }
        
        .btn-action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Required field indicator */
        .required-field::after {
            content: "*";
            color: #dc3545;
            margin-left: 4px;
        }
        
        /* Budget styles */
        .budget-item {
            border-left: 4px solid;
        }
        
        .budget-amount-positive {
            color: #198754;
            font-weight: 600;
        }
        
        .budget-amount-negative {
            color: #dc3545;
            font-weight: 600;
        }
        
        /* Filter controls */
        .filter-controls {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 0.5rem;
            margin-bottom: 16px;
        }
        
        /* Loading spinner */
        .spinner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 0.5rem;
        }

        .recent-activity-item {
            padding: 12px;
            border-radius: 0.5rem;
            margin-bottom: 12px;
            background: #f8f9fa;
            border-left: 4px solid #6c757d;
            transition: all 0.2s ease;
        }

        .recent-activity-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .activity-icon.expense {
            background-color: #0d6efd;
        }

        .activity-icon.budget {
            background-color: #198754;
        }

        .activity-title {
            font-weight: 600;
        }

        .activity-description {
            font-size: 0.85rem;
            margin-top: 4px;
            color: #6c757d;
        }

        .activity-creator {
            margin-top: 6px;
            font-size: 0.8rem;
        }

        .expense-amount {
            font-weight: 700;
            color: #212529;
            width: 120px; /* Fixed width for amount */
            text-align: right;
            margin-bottom: 6px; /* Add space between amount and buttons */
        }

        .expense-actions {
            display: flex;
            gap: 6px;
            opacity: 0.6;
            transition: all 0.2s ease;
            justify-content: flex-end; /* Align buttons to the right */
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">EXPENSE TRACKER</div>
        
        <!-- Success Alert -->
        <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none mx-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="successAlertMessage">Action completed successfully!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Summary Cards -->
                    <div class="row mb-3 g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-primary bg-opacity-10 border-primary border-opacity-25">
                                <h6 class="text-muted">Total Expenses (Monthly)</h6>
                                <h3 class="text-primary" id="totalExpenses">₱0.00</h3>
                                <small id="expensesTrend">No previous data available</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-warning bg-opacity-10 border-warning border-opacity-25">
                                <h6 class="text-muted">Total Expenses (Grand Total)</h6>
                                <h3 class="text-warning" id="grandTotalExpenses">₱0.00</h3>
                                <small id="grandTotalExpensesLabel">All-time expenses</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-success bg-opacity-10 border-success border-opacity-25">
                                <h6 class="text-muted">Most Spent Category (Monthly)</h6>
                                <h3 class="text-success" id="topCategory">None</h3>
                                <small id="topCategoryAmount">₱0.00 (0% of total)</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-info bg-opacity-10 border-info border-opacity-25">
                                <h6 class="text-muted">Overall Budget (Grand Total)</h6>
                                <h3 class="text-info" id="grandTotalBudget">₱0.00</h3>
                                <small id="grandTotalBudgetLabel">All-time budget allocations</small>
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
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-action dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item" href="#" id="exportExpensesExcel">Export Expenses (Excel)</a></li>
                                        <li><a class="dropdown-item" href="#" id="exportBudgetsExcel">Export Budgets (Excel)</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" id="categoryFilter">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" {{ $categoryId == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <input type="month" class="form-control form-control-sm" id="monthFilter" value="{{ $month ?? date('Y-m') }}">
                                        <button class="btn btn-sm btn-outline-info" type="button" id="clearMonthFilter">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
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
                                        <span class="badge bg-primary" id="expensesPeriodBadge">This Month</span>
                                    </div>
                                    
                                    <div id="recentExpensesContainer" class="position-relative">
                                        <div class="spinner-overlay d-none" id="expensesSpinner">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        
                                        @if($recentExpenses->count() > 0)
                                            @foreach($recentExpenses as $expense)
                                                <div class="expense-item" style="border-left-color: {{ $expense->category->color_code }};">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <div class="expense-title">
                                                                <i class="bi {{ $expense->category->icon ?? 'bi-tag' }} me-2 text-muted"></i>
                                                                {{ $expense->title }}
                                                            </div>
                                                            <div class="expense-detail">
                                                                <span>{{ $expense->category->name }}</span> • 
                                                                <span>{{ $expense->date->format('M d, Y') }}</span> •
                                                                <span>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-end d-flex flex-column">
                                                            <div class="expense-amount">₱{{ number_format($expense->amount, 2) }}</div>
                                                            <div class="expense-actions">
                                                                <button class="btn-action-icon edit" onclick="editExpense({{ $expense->expense_id }})" title="Edit">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <button class="btn-action-icon delete" onclick="deleteExpense({{ $expense->expense_id }})" title="Delete">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info">No expenses found for the selected period.</div>
                                        @endif
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
                                    
                                    <div class="table-responsive position-relative">
                                        <div class="spinner-overlay d-none" id="budgetSpinner">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Period</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="budgetHistoryBody">
                                                @if($recentBudgets->count() > 0)
                                                    @foreach($recentBudgets as $budget)
                                                        <tr>
                                                            <td class="{{ $budget->amount > 0 ? 'budget-amount-positive' : 'budget-amount-negative' }}">
                                                                ₱{{ number_format($budget->amount, 2) }}
                                                            </td>
                                                            <td>{{ $budget->budgetType->name }}</td>
                                                            <td>{{ $budget->start_date->format('M d, Y') }} to {{ $budget->end_date->format('M d, Y') }}</td>
                                                            <td>{{ $budget->description ?? 'No description' }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-outline-primary" onclick="editBudget({{ $budget->budget_allocation_id }})" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-danger" onclick="deleteBudget({{ $budget->budget_allocation_id }})" title="Delete">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center">No budget history found.</td>
                                                    </tr>
                                                @endif
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
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Expense Breakdown</h5>
                                    <div id="chartSpinner" class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div class="chart-container position-relative">
                                    <canvas id="expenseChart"></canvas>
                                </div>
                                <div class="mt-3" id="chartLegend">
                                    <!-- Legend will be dynamically generated -->
                                </div>
                            </div>
                        </div>
                            
                            <!-- Replace the budget progress card with this -->
                            <div class="card expense-card mb-3">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Recent Activities</h6>
                                    <div class="position-relative">
                                        <div id="activitiesSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="recentActivitiesContainer">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2">Loading activities...</p>
                                        </div>
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="expenseModalLabel">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- General error messages area -->
                    <div id="generalExpenseError" class="alert alert-danger expense-error" style="display:none;"></div>
                    
                    <form id="expenseForm" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expenseTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="expenseTitle" name="title" placeholder="Enter expense title" required>
                                <div id="titleError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="expenseCategory" class="form-label">Category</label>
                                <select class="form-select" id="expenseCategory" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div id="category_idError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expenseAmount" class="form-label">Amount (₱)</label>
                                <input type="number" class="form-control" id="expenseAmount" name="amount" step="0.01" min="0.01" max="1000000" placeholder="Enter amount" required>
                                <div id="amountError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="expensePaymentMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="expensePaymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="other">Other</option>
                                </select>
                                <div id="payment_methodError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expenseDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expenseDate" name="date" required>
                                <div id="dateError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="expenseReceiptNumber" class="form-label">Receipt Number</label>
                                <input type="text" class="form-control" id="expenseReceiptNumber" name="receipt_number" placeholder="Enter receipt number" required>
                                <div id="receipt_numberError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expenseDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="expenseDescription" name="description" rows="3" placeholder="Write a brief description about this expense" required></textarea>
                            <div id="descriptionError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expenseReceipt" class="form-label">Receipt Image (Optional)</label>
                            <input type="file" class="form-control" id="expenseReceipt" name="receipt" accept="image/jpeg,image/png,image/jpg,application/pdf">
                            <div id="receiptError" class="text-danger small mt-1 expense-error" style="display:none;"></div>
                            <div class="form-text">Upload receipt image or PDF (max 2MB)</div>
                            
                            <!-- Receipt preview area (for edit) -->
                            <div id="receiptPreview" class="mt-2" style="display: none;">
                                <p>Current Receipt: <a href="#" id="receiptLink" target="_blank">View</a></p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveExpenseBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Budget Modal -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="budgetModalTitle">Add New Budget Allocation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- General error messages area -->
                    <div id="generalBudgetError" class="alert alert-danger budget-error" style="display:none;"></div>
                    
                    <form id="budgetForm">
                        <div class="mb-3">
                            <label for="budgetAmount" class="form-label required-field">Amount (₱)</label>
                            <input type="number" class="form-control" id="budgetAmount" name="amount" step="0.01" min="0.01" max="1000000" required>
                            <div id="amountError" class="text-danger small mt-1 budget-error" style="display:none;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="budgetType" class="form-label required-field">Budget Type</label>
                            <select class="form-select" id="budgetType" name="budget_type_id" required>
                                <option value="">Select Budget Type</option>
                                @foreach($budgetTypes as $type)
                                    <option value="{{ $type->budget_type_id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div id="budget_type_idError" class="text-danger small mt-1 budget-error" style="display:none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="budgetStartDate" class="form-label required-field">Start Date</label>
                                <input type="date" class="form-control" id="budgetStartDate" name="start_date" required>
                                <div id="start_dateError" class="text-danger small mt-1 budget-error" style="display:none;"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="budgetEndDate" class="form-label required-field">End Date</label>
                                <input type="date" class="form-control" id="budgetEndDate" name="end_date" required>
                                <div id="end_dateError" class="text-danger small mt-1 budget-error" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="budgetDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="budgetDescription" name="description" rows="3"></textarea>
                            <div id="descriptionError" class="text-danger small mt-1 budget-error" style="display:none;"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBudgetBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View All Expenses Modal - Updated with pagination container -->
    <div class="modal fade" id="allExpensesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">All Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-controls mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="expensesFilterCategory" class="form-label">Category</label>
                                <select id="expensesFilterCategory" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="expensesFilterStartDate" class="form-label">Start Date</label>
                                <input type="date" id="expensesFilterStartDate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="expensesFilterEndDate" class="form-label">End Date</label>
                                <input type="date" id="expensesFilterEndDate" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="resetExpensesFilter" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button id="applyExpensesFilter" class="btn btn-primary me-2">Apply Filter</button>
                                <button id="exportFilteredExpenses" class="btn btn-outline-success">
                                    <i class="bi bi-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Add filter status indicator -->
                    <div id="expensesFilterStatus" class="mb-3"></div>
                    <div class="table-responsive position-relative">
                        <div class="spinner-overlay d-none" id="allExpensesSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Receipt #</th>
                                    <th>Created By</th>
                                    <th>Receipt File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="allExpensesBody">
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                        
                        <!-- Add pagination container -->
                        <div id="expensesPagination" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full History Modal - Updated with pagination container -->
    <div class="modal fade" id="fullHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Budget History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-controls mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="budgetFilterType" class="form-label">Budget Type</label>
                                <select id="budgetFilterType" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($budgetTypes as $type)
                                        <option value="{{ $type->budget_type_id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="budgetFilterStartDate" class="form-label">Start Date</label>
                                <input type="date" id="budgetFilterStartDate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="budgetFilterEndDate" class="form-label">End Date</label>
                                <input type="date" id="budgetFilterEndDate" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="resetBudgetFilter" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button id="applyBudgetFilter" class="btn btn-primary me-2">Apply Filter</button>
                                <button id="exportFilteredBudgets" class="btn btn-outline-success">
                                    <i class="bi bi-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Add filter status indicator -->
                    <div id="budgetFilterStatus" class="mb-3"></div>
                    
                    <div class="table-responsive position-relative">
                        <div class="spinner-overlay d-none" id="fullHistorySpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Period</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="fullHistoryBody">
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                        
                        <!-- Add pagination container -->
                        <div id="budgetPagination" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalTitle">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    
                    <div id="deleteItemDetails" class="mb-3 border-bottom pb-3">
                        <!-- Dynamically populated with item details -->
                    </div>
                    
                    <p id="deleteConfirmMessage">You're about to permanently delete this item. Consider editing instead if you only need to make changes.</p>
                    
                    <form id="deleteConfirmationForm" class="mt-3">
                        <input type="hidden" id="deleteItemId">
                        <input type="hidden" id="deleteItemType">
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label required-field">For security, please enter your password to confirm deletion</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input type="password" class="form-control" id="confirmPassword" name="password" required placeholder="Enter your password">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="editInsteadOfDelete()">
                        <i class="bi bi-pencil-square me-1"></i> Edit Instead
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        
        // Global variables
        let expenseChart;
        let currentExpenseId = null;
        let currentBudgetId = null;

        $(document).ready(function() {
            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Initialize modals
            const addExpenseModal = new bootstrap.Modal(document.getElementById('addExpenseModal'));
            const addBudgetModal = new bootstrap.Modal(document.getElementById('addBudgetModal'));
            const allExpensesModal = new bootstrap.Modal(document.getElementById('allExpensesModal'));
            const fullHistoryModal = new bootstrap.Modal(document.getElementById('fullHistoryModal'));
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            
            // Initialize chart
            initializeChart();
            $('#chartSpinner').hide();

            // Reset filter buttons
            $('#resetExpensesFilter').on('click', function() {
                resetExpensesFilter();
            });
            
            $('#resetBudgetFilter').on('click', function() {
                resetBudgetFilter();
            });

            $('#addExpenseModal').on('hidden.bs.modal', function() {
                clearExpenseForm();
            });
            
            $('#addBudgetModal').on('hidden.bs.modal', function() {
                clearBudgetForm();
            });
            
            // Load and display initial statistics
            updateDashboardStats();

            // Handle month input clearing
            $('#monthFilter').on('input', function() {
                if ($(this).val() === '') {
                    // Trigger dashboard update when month filter is cleared
                    updateDashboardWithFilters();
                }
            });

             $('#clearMonthFilter').on('click', function() {
                $('#monthFilter').val('');
                updateDashboardWithFilters();
            });
            
            // Button event listeners
            $('#addExpenseBtn').on('click', function() {
                clearExpenseForm();
                $('#expenseModalTitle').text('Add New Expense');
                $('#expenseDate').val(new Date().toISOString().substr(0, 10));
                addExpenseModal.show();
            });
            
            $('#addBudgetBtn').on('click', function() {
                clearBudgetForm();
                $('#budgetModalTitle').text('Add Budget Allocation');
                
                // Set default dates - start from today, end 1 month later
                const today = new Date();
                const oneMonthLater = new Date();
                oneMonthLater.setMonth(today.getMonth() + 1);
                
                $('#budgetStartDate').val(today.toISOString().substr(0, 10));
                $('#budgetEndDate').val(oneMonthLater.toISOString().substr(0, 10));
                
                addBudgetModal.show();
            });
            
            $('#viewAllExpensesBtn').on('click', function() {
                loadAllExpenses(1); // Load first page
                allExpensesModal.show();
            });
            
            $('#viewFullHistoryBtn').on('click', function() {
                loadFullBudgetHistory(1); // Load first page
                fullHistoryModal.show();
            });
            
            // Filter changes
            $('#categoryFilter, #monthFilter').on('change', function() {
                updateDashboardWithFilters();
            });
            
            // Apply expense filter
            $('#applyExpensesFilter').on('click', function() {
                loadFilteredExpenses(1); // Load first page of filtered results
            });
            
            // Apply budget filter
            $('#applyBudgetFilter').on('click', function() {
                loadFilteredBudgetHistory(1); // Load first page of filtered results
            });
            
            // Save expense
            $('#saveExpenseBtn').on('click', function() {
                saveExpense();
            });
            
            // Save budget
            $('#saveBudgetBtn').on('click', function() {
                saveBudget();
            });
            
            // Export buttons
            $('#exportFilteredExpenses').on('click', function() {
                exportFilteredExpensesToExcel();
            });
            
            $('#exportFilteredBudgets').on('click', function() {
                exportFilteredBudgetsToExcel();
            });
            
            $('#exportFilteredExpensesBtn').on('click', function() {
                exportFilteredExpensesToExcel();
            });
            
            $('#exportFilteredBudgetsBtn').on('click', function() {
                exportFilteredBudgetsToExcel();
            });

            // Export buttons in main dropdown menu
            $('#exportExpensesExcel').on('click', function(e) {
                e.preventDefault();
                exportExpensesToExcel();
            });
            
            $('#exportBudgetsExcel').on('click', function(e) {
                e.preventDefault();
                exportBudgetsToExcel();
            });
            
            // Confirm delete button
            $('#confirmDeleteBtn').on('click', function() {
                confirmDelete();
            });
        });

        // Initialize expense breakdown chart
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
                            display: false,
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Update the chart with new data
        function updateChart(categories, data, colors) {
            expenseChart.data.labels = categories;
            expenseChart.data.datasets[0].data = data;
            expenseChart.data.datasets[0].backgroundColor = colors;
            expenseChart.update();
            
            // Update the chart legend
            updateChartLegend(categories, data, colors);
        }

        // Update chart legend
        function updateChartLegend(labels, data, colors) {
            const legendContainer = document.getElementById('chartLegend');
            let legendHTML = '';
            
            // Calculate the total
            const total = data.reduce((a, b) => a + b, 0);
            
            // Build legend items
            for (let i = 0; i < labels.length; i++) {
                const percentage = total > 0 ? Math.round((data[i] / total) * 100) : 0;
                legendHTML += `
                    <div class="chart-legend-item">
                        <div class="chart-legend-color" style="background-color: ${colors[i]};"></div>
                        <div class="d-flex justify-content-between w-100">
                            <span>${labels[i]}</span>
                            <span>${percentage}%</span>
                        </div>
                    </div>
                `;
            }
            
            legendContainer.innerHTML = legendHTML;
        }

        // Clear expense form
        function clearExpenseForm() {
            currentExpenseId = null;
            $('#expenseId').val('');
            $('#expenseForm')[0].reset();
            
            // Reset form validations
            $('#expenseTitle').removeClass('is-invalid');
            $('#expenseCategory').removeClass('is-invalid');
            $('#expenseAmount').removeClass('is-invalid');
            $('#expensePaymentMethod').removeClass('is-invalid');
            $('#expenseDate').removeClass('is-invalid');
            $('#expenseReceiptNumber').removeClass('is-invalid');
            $('#expenseDescription').removeClass('is-invalid');
            $('#expenseReceipt').removeClass('is-invalid');
            
            // Clear all error messages (enhanced)
            $('.expense-error').text('').hide();
            $('#generalExpenseError').empty().hide();
            
            // Hide receipt preview
            $('#receiptPreview').hide();
        }

        // Clear budget form
        function clearBudgetForm() {
            currentBudgetId = null;
            $('#budgetId').val('');
            $('#budgetForm')[0].reset();
            
            // Reset form validations
            $('#budgetAmount').removeClass('is-invalid');
            $('#budgetStartDate').removeClass('is-invalid');
            $('#budgetEndDate').removeClass('is-invalid');
            $('#budgetType').removeClass('is-invalid');
            
            // Clear all error messages (enhanced)
            $('.budget-error').text('').hide();
            $('#generalBudgetError').empty().hide();
        }

        // Save expense
        function saveExpense() {
            // Clear previous error messages
            $('.expense-error').text('').hide();
            $('#generalExpenseError').empty().hide();
            
            // Create FormData object to handle file uploads
            let formData = new FormData($('#expenseForm')[0]);
            
            // Add the expense ID for updates
            if (currentExpenseId) {
                formData.append('_method', 'PUT');
            }
            
            // Show loading indicator
            $('#saveExpenseBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
            
            // Use named route for both create and update to ensure consistency
            $.ajax({
                url: currentExpenseId 
                    ? '{{ route("admin.expense.update", ["id" => "_id_"]) }}'.replace('_id_', currentExpenseId) 
                    : '{{ route("admin.expense.store") }}',
                method: 'POST', // Always use POST with _method for PUT
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Close modal and show success message
                    $('#addExpenseModal').modal('hide');
                    toastr.success(response.message || 'Expense saved successfully');
                    
                    // Update dashboard data
                    updateDashboardStats();

                    showSuccessAlert(currentExpenseId ? 'Expense updated successfully' : 'Expense added successfully');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Check for no changes message
                        if (xhr.responseJSON && xhr.responseJSON.message === 'No changes were made to the expense.') {
                            toastr.info(xhr.responseJSON.message);
                            return;
                        }
                        
                        // Validation errors handling
                        const errors = xhr.responseJSON.errors;
                        let hasGeneralErrors = false;
                        
                        // Display errors next to form fields
                        for (const field in errors) {
                            const errorMsg = errors[field][0];
                            const errorElement = $('#' + field + 'Error');
                            
                            if (errorElement.length > 0) {
                                errorElement.text(errorMsg).show();
                            } else {
                                // If no specific error element, show in general error area
                                $('#generalExpenseError').append('<div>' + errorMsg + '</div>');
                                hasGeneralErrors = true;
                            }
                        }
                        
                        // Only show general error container if it has content
                        if (hasGeneralErrors) {
                            $('#generalExpenseError').show();
                        }
                    } else {
                        // Show error details for debugging
                        console.error('Error saving expense:', xhr);
                        toastr.error('An error occurred while saving the expense. Please try again.');
                    }
                },
                complete: function() {
                    // Reset button state
                    $('#saveExpenseBtn').html('Save').prop('disabled', false);
                }
            });
        }

        // Save budget
        function saveBudget() {
            // Disable the save button and show loading spinner
            $('#saveBudgetBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            // Clear any previous error messages
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');
            
            // Prepare form data
            const formData = {
                amount: $('#budgetAmount').val(),
                budget_type_id: $('#budgetType').val(),
                start_date: $('#budgetStartDate').val(),
                end_date: $('#budgetEndDate').val(),
                description: $('#budgetDescription').val(), // Include description even if empty
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Log form data for debugging
            console.log('Submitting budget form data:', formData);
            
            // Determine if this is an edit or create operation
            const isEdit = currentBudgetId !== null;
            const url = isEdit 
                ? "{{ route('admin.expense.budget.update', ['id' => '_id_']) }}".replace('_id_', currentBudgetId)
                : "{{ route('admin.expense.budget.store') }}";
            const method = isEdit ? 'PUT' : 'POST';
            
            // Send the request
            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Reset button state
                    $('#saveBudgetBtn').text(isEdit ? 'Update Budget' : 'Save Budget').prop('disabled', false);
                    
                    // Show success message
                    toastr.success(isEdit ? 'Budget allocation updated successfully' : 'Budget allocation added successfully');
                    
                    // Close the modal
                    $('#addBudgetModal').modal('hide');
                    
                    // Update dashboard data
                    updateDashboardWithFilters();
                    
                    // Reset form and current ID
                    currentBudgetId = null;
                    
                    // Show success alert
                    showSuccessAlert(isEdit ? 'Budget allocation updated successfully' : 'Budget allocation added successfully');
                },
                error: function(xhr) {
                    // Reset button state
                    $('#saveBudgetBtn').text(isEdit ? 'Update Budget' : 'Save Budget').prop('disabled', false);
                    
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        
                        // Display each error on the form
                        Object.keys(errors).forEach(field => {
                            const errorMsg = errors[field][0];
                            const inputField = $(`#budget${field.charAt(0).toUpperCase() + field.slice(1)}`);
                            inputField.addClass('is-invalid');
                            inputField.next('.invalid-feedback').text(errorMsg).show();
                        });
                        
                        toastr.error('Please fix the errors in the form');
                    } else {
                        // Handle other errors
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                    }
                }
            });
        }

        // Edit expense
        function editExpense(id) {
            // Close any open modals first
            $('#allExpensesModal').modal('hide');
            $('#fullHistoryModal').modal('hide');
            
            // Clear any existing form data and errors
            clearExpenseForm();
            
            // Store the expense ID for use in the save function
            currentExpenseId = id;
            
            // Update modal title
            $('#expenseModalLabel').text('Edit Expense');   

            // Show loading state on the modal
            $('#saveExpenseBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop('disabled', true);
            
            // Fetch expense details
            $.ajax({
                url: '{{ route("admin.expense.get", "") }}/' + id,
                type: 'GET',
                success: function(response) {
                    const expense = response.expense;
                    
                    // Fill the form with expense data
                    $('#expenseTitle').val(expense.title);
                    $('#expenseCategory').val(expense.category_id);
                    $('#expenseAmount').val(expense.amount);
                    $('#expensePaymentMethod').val(expense.payment_method);
                    
                    // SAFER DATE HANDLING: Handle date without risking invalid Date objects
                    if (expense.date) {
                        try {
                            console.log('Original date from server:', expense.date);
                            
                            // Use a reliable parsing method
                            let parts = expense.date.split('-');
                            if (parts.length === 3) {
                                // Create date using year, month (0-based), day
                                let year = parseInt(parts[0], 10);
                                let month = parseInt(parts[1], 10) - 1; // JS months are 0-indexed
                                let day = parseInt(parts[2], 10) + 1; // Add one day to compensate for timezone
                                
                                // Create a new date and format it
                                let dateObj = new Date(year, month, day);
                                if (!isNaN(dateObj.getTime())) {
                                    // Format as YYYY-MM-DD
                                    let adjustedDate = dateObj.getFullYear() + '-' + 
                                        String(dateObj.getMonth() + 1).padStart(2, '0') + '-' + 
                                        String(dateObj.getDate()).padStart(2, '0');
                                        
                                    $('#expenseDate').val(adjustedDate);
                                    console.log('Adjusted date for form:', adjustedDate);
                                } else {
                                    // If still invalid, just use the original string
                                    $('#expenseDate').val(expense.date);
                                    console.log('Using original date - created date was invalid');
                                }
                            } else {
                                // If format isn't as expected, use as is
                                $('#expenseDate').val(expense.date);
                                console.log('Using original date - unexpected format');
                            }
                        } catch (e) {
                            // If any error occurs, fallback to original value
                            console.error('Date parsing error:', e);
                            $('#expenseDate').val(expense.date);
                        }
                    }
                    
                    $('#expenseReceiptNumber').val(expense.receipt_number);
                    $('#expenseDescription').val(expense.description);
                    
                    // Handle receipt preview if available
                    if (expense.receipt_path) {
                        const receiptUrl = expense.receipt_path.startsWith('http') 
                            ? expense.receipt_path 
                            : "{{ asset('storage') }}/" + expense.receipt_path;
                        $('#receiptLink').attr('href', receiptUrl);
                        $('#receiptPreview').show();
                    } else {
                        $('#receiptPreview').hide();
                    }
                    
                    // Reset button state
                    $('#saveExpenseBtn').text('Update Expense').prop('disabled', false);
                    
                    // Show the modal
                    $('#addExpenseModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading expense:', xhr);
                    toastr.error('Failed to load expense details');
                    $('#saveExpenseBtn').text('Save').prop('disabled', false);
                }
            });
        }

        // Edit budget
        function editBudget(id) {
            // Close any open modals first
            $('#allExpensesModal').modal('hide');
            $('#fullHistoryModal').modal('hide');
            
            // Clear the form first - ensures any previous errors are gone
            clearBudgetForm();
            
            // Set current ID for update operation
            currentBudgetId = id;
            
            // Show loading spinner
            $('#budgetModalTitle').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            $('#saveBudgetBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop('disabled', true);
            
            $.ajax({
                url: "{{ route('admin.expense.budget.get', ['id' => '_id_']) }}".replace('_id_', id),
                method: 'GET',
                success: function(response) {
                    const budget = response.budget;
                    
                    // Populate form fields
                    $('#budgetAmount').val(budget.amount);
                    
                    // CRITICAL FIX: Use exact dates from server with proper handling
                    if (budget.start_date) {
                        // Make sure we get just the YYYY-MM-DD part
                        const rawStartDate = typeof budget.start_date === 'string' 
                            ? budget.start_date.split('T')[0] 
                            : budget.start_date;
                        
                        $('#budgetStartDate').val(rawStartDate);
                        console.log('Setting budget start date:', rawStartDate);
                    }
                    
                    if (budget.end_date) {
                        // Make sure we get just the YYYY-MM-DD part
                        const rawEndDate = typeof budget.end_date === 'string'
                            ? budget.end_date.split('T')[0] 
                            : budget.end_date;
                            
                        $('#budgetEndDate').val(rawEndDate);
                        console.log('Setting budget end date:', rawEndDate);
                    }
                    
                    $('#budgetType').val(budget.budget_type_id);
                    
                    // CRITICAL FIX: Ensure description is properly handled
                    // Use explicit check against null/undefined to preserve empty strings
                    if (budget.description !== null && budget.description !== undefined) {
                        $('#budgetDescription').val(budget.description);
                        console.log('Setting description:', budget.description);
                    } else {
                        $('#budgetDescription').val('');
                        console.log('Setting empty description');
                    }
                    
                    // Update modal title
                    $('#budgetModalTitle').text('Edit Budget Allocation');
                    
                    // Reset button state
                    $('#saveBudgetBtn').text('Update Budget').prop('disabled', false);
                    
                    // Show the modal
                    $('#addBudgetModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Failed to load budget:', xhr);
                    toastr.error('Failed to load budget details. Please try again later.');
                    $('#saveBudgetBtn').text('Save').prop('disabled', false);
                }
            });
        }

        // Delete expense
       function deleteExpense(id) {
            // Close any open parent modals first
            $('#allExpensesModal').modal('hide');
            $('#fullHistoryModal').modal('hide');
            
            // Fetch the expense details first
            $.ajax({
                url: '{{ route("admin.expense.get", "") }}/' + id,
                type: 'GET',
                success: function(response) {
                    const expense = response.expense;
                    
                    // Set up delete confirmation modal for an expense
                    $('#deleteModalTitle').html('<i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Expense');
                    
                    // Prepare detail information
                    const detailsHtml = `
                        <div class="d-flex justify-content-between">
                            <strong>Title:</strong>
                            <span>${expense.title}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Category:</strong>
                            <span>${expense.category.name}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Amount:</strong>
                            <span class="text-danger">₱${formatNumber(expense.amount)}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Date:</strong>
                            <span>${formatDate(expense.date)}</span>
                        </div>
                    `;
                    
                    $('#deleteItemDetails').html(detailsHtml);
                    $('#deleteConfirmMessage').html(`
                        You're about to <strong>permanently delete</strong> this expense record. 
                        This action <strong>cannot be undone</strong>. Consider editing instead if you only need to make changes.
                    `);
                    
                    $('#deleteItemId').val(id);
                    $('#deleteItemType').val('expense');
                    $('#confirmPassword').val('');
                    
                    // Store the current expense ID for "Edit Instead" button
                    currentExpenseId = id;
                    currentBudgetId = null;
                    
                    // Show the modal
                    new bootstrap.Modal(document.getElementById('deleteConfirmationModal')).show();
                },
                error: function(xhr) {
                    console.error('Error loading expense details for deletion:', xhr);
                    toastr.error('Failed to load expense details');
                }
            });
        }

        function deleteBudget(id) {
            // Close any open parent modals first
            $('#allExpensesModal').modal('hide');
            $('#fullHistoryModal').modal('hide');
            
            // Fetch the budget details first
            $.ajax({
                url: "{{ route('admin.expense.budget.get', ['id' => '_id_']) }}".replace('_id_', id),
                method: 'GET',
                success: function(response) {
                    const budget = response.budget;
                    
                    // Set up delete confirmation modal for a budget allocation
                    $('#deleteModalTitle').html('<i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Budget Allocation');
                    
                    // Prepare detail information - FIXED DESCRIPTION ALIGNMENT
                    const detailsHtml = `
                        <div class="d-flex justify-content-between">
                            <strong>Budget Type:</strong>
                            <span>${budget.budget_type.name}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Amount:</strong>
                            <span class="${budget.amount >= 0 ? 'text-success' : 'text-danger'}">
                                ₱${formatNumber(budget.amount)}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Period:</strong>
                            <span>${formatDate(budget.start_date)} to ${formatDate(budget.end_date)}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>Description:</strong>
                            <span class="text-end" style="max-width: 65%;">${budget.description || 'No description'}</span>
                        </div>
                    `;
                    
                    $('#deleteItemDetails').html(detailsHtml);
                    $('#deleteConfirmMessage').html(`
                        You're about to <strong>permanently delete</strong> this budget allocation. 
                        This action <strong>cannot be undone</strong>. Consider editing instead if you only need to make changes.
                    `);
                    
                    $('#deleteItemId').val(id);
                    $('#deleteItemType').val('budget');
                    $('#confirmPassword').val('');
                    
                    // Store the current budget ID for "Edit Instead" button
                    currentBudgetId = id;
                    currentExpenseId = null;
                    
                    // Show the modal
                    new bootstrap.Modal(document.getElementById('deleteConfirmationModal')).show();
                },
                error: function(xhr) {
                    console.error('Error loading budget details for deletion:', xhr);
                    toastr.error('Failed to load budget details');
                }
            });
        }

        // Function for the "Edit Instead" button
        function editInsteadOfDelete() {
            // Hide the delete modal
            bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal')).hide();
            
            // Open the appropriate edit modal
            if (currentExpenseId) {
                editExpense(currentExpenseId);
            } else if (currentBudgetId) {
                editBudget(currentBudgetId);
            }
        }

        // Confirm delete action
        function confirmDelete() {
            const itemId = $('#deleteItemId').val();
            const itemType = $('#deleteItemType').val();
            const password = $('#confirmPassword').val();
            
            if (!password) {
                toastr.error('Please enter your password to confirm');
                $('#confirmPassword').addClass('is-invalid');
                return;
            }
            
            // Disable button and show spinner
            $('#confirmDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
            
            // Determine correct API endpoint and parameter name
            let url, paramName;
            
            if (itemType === 'expense') {
                url = '{{ route("admin.expense.delete") }}';
                paramName = 'expense_id';
            } else {
                url = '{{ route("admin.expense.budget.delete") }}';
                paramName = 'budget_id';
            }
            
            // Prepare form data
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append(paramName, itemId);
            formData.append('password', password);
            
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal')).hide();
                    
                    // Show success message
                    toastr.success(itemType === 'expense' ? 'Expense deleted successfully' : 'Budget allocation deleted successfully');
                    
                    showSuccessAlert(itemType === 'expense' ? 'Expense deleted successfully' : 'Budget allocation deleted successfully');
                    
                    // Update dashboard data
                    updateDashboardWithFilters();
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        $('#confirmPassword').addClass('is-invalid');
                        toastr.error('Incorrect password. Please try again.');
                    } else if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors || {};
                        let errorMessage = 'Please fix the following errors:';
                        Object.keys(errors).forEach(key => {
                            errorMessage += '<br>• ' + errors[key][0];
                        });
                        toastr.error(errorMessage);
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred during deletion');
                    }
                },
                complete: function() {
                    // Re-enable button
                    $('#confirmDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash me-1"></i> Delete');
                }
            });
        }

        // Update dashboard with current filters
        function updateDashboardWithFilters() {
            const category = $('#categoryFilter').val();
            const month = $('#monthFilter').val();
            
            // Show loading spinners
            $('#expensesSpinner').removeClass('d-none');
            $('#chartSpinner').show();
            $('#activitiesSpinner').removeClass('d-none');
            
            // Update expenses period badge
            $('#expensesPeriodBadge').text(month ? new Date(month + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'All Time');
            
            // Make AJAX call to get filtered data
            $.ajax({
                url: '{{ route("admin.expense.index") }}',
                method: 'GET',
                data: {
                    category_id: category,
                    month: month, // If empty, controller will return all-time data
                    format: 'json'
                },
                success: function(response) {
                    // Update statistics
                    if (response.stats) {
                        updateStatistics(response.stats);
                    }
                    
                    // Update recent expenses list
                    if (response.recentExpenses) {
                        updateRecentExpenses(response.recentExpenses);
                    }
                    
                    // Update chart
                    if (response.chartData) {
                        updateChart(
                            response.chartData.labels,
                            response.chartData.data,
                            response.chartData.colors
                        );
                    }
                    
                    // Update budget history
                    if (response.recentBudgets) {
                        updateBudgetHistory(response.recentBudgets);
                    }
                    
                    // Update recent activities
                    if (response.recentExpenses && response.recentBudgets) {
                        updateRecentActivities(response.recentExpenses, response.recentBudgets);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating dashboard:", error);
                    toastr.error('Failed to update dashboard data. Please try again later.');
                },
                complete: function() {
                    // Hide loading spinners
                    $('#expensesSpinner').addClass('d-none');
                    $('#chartSpinner').hide();
                    $('#activitiesSpinner').addClass('d-none');
                }
            });
        }

        // Update statistics
        function updateStatistics(stats) {
            // Total expenses (Monthly)
            $('#totalExpenses').text('₱' + formatNumber(stats.totalExpenses || 0));
            
            // Expense trend
            if (stats.previousPeriodTotal !== undefined && stats.previousPeriodTotal > 0) {
                const percentChange = ((stats.totalExpenses - stats.previousPeriodTotal) / stats.previousPeriodTotal) * 100;
                const trendIcon = percentChange >= 0 ? 'bi-arrow-up' : 'bi-arrow-down';
                const trendClass = percentChange >= 0 ? 'text-danger' : 'text-success';
                
                $('#expensesTrend').html(`<i class="bi ${trendIcon}"></i> ${Math.abs(percentChange).toFixed(1)}% from last period`);
                $('#expensesTrend').removeClass('text-danger text-success').addClass(trendClass);
            } else {
                $('#expensesTrend').text('No previous data available');
                $('#expensesTrend').removeClass('text-danger text-success');
            }
            
            // Total Expenses (Grand Total) - replacing Budget Remaining
            $('#grandTotalExpenses').text('₱' + formatNumber(stats.grandTotalExpenses || 0));
            $('#grandTotalExpensesLabel').text('All-time expenses');
            
            // Overall Budget (Grand Total) - replacing Current Budget
            $('#grandTotalBudget').text('₱' + formatNumber(stats.grandTotalBudget || 0));
            $('#grandTotalBudgetLabel').text('All-time budget allocations');
            
            // Top category
            if (stats.topCategory) {
                $('#topCategory').text(stats.topCategory.name || 'None');
                const percentage = stats.totalExpenses > 0 ? ((stats.topCategory.amount / stats.totalExpenses) * 100).toFixed(1) : 0;
                $('#topCategoryAmount').text(`₱${formatNumber(stats.topCategory.amount || 0)} (${percentage}% of total)`);
                
                // Update the color indicator if there is one
                if ($('#topCategoryIndicator').length > 0) {
                    $('#topCategoryIndicator').css('background-color', stats.topCategory.color);
                }
            } else {
                $('#topCategory').text('None');
                $('#topCategoryAmount').text('₱0.00 (0% of total)');
            }
        }

        // Update recent expenses list
       function updateRecentExpenses(expenses) {
            const container = $('#recentExpensesContainer');
            
            // Clear current content
            container.html('');
            
            if (expenses && expenses.length > 0) {
                expenses.forEach(expense => {
                    const expenseItem = `
                        <div class="expense-item" style="border-left-color: ${expense.category.color_code};">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="expense-title">${expense.title}</div>
                                    <div class="expense-detail">
                                        <span class="badge" style="background-color: ${expense.category.color_code}">${expense.category.name}</span>
                                        <span class="ms-2">${formatDate(expense.date)}</span>
                                    </div>
                                </div>
                                <div class="text-end d-flex flex-column">
                                    <div class="expense-amount">₱${formatNumber(expense.amount)}</div>
                                    <div class="expense-actions">
                                        <button class="btn-action-icon edit" onclick="editExpense(${expense.expense_id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-action-icon delete" onclick="deleteExpense(${expense.expense_id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(expenseItem);
                });
            } else {
                container.html('<div class="alert alert-info">No expenses found for the selected period.</div>');
            }
        }

        // Update budget history table
        function updateBudgetHistory(budgets) {
            const tableBody = $('#budgetHistoryBody');
            
            // Clear current content
            tableBody.html('');
            
            if (budgets && budgets.length > 0) {
                budgets.forEach(budget => {
                    // Create budget row
                    const budgetRow = `
                        <tr>
                            <td class="${budget.amount >= 0 ? 'budget-amount-positive' : 'budget-amount-negative'}">
                                ₱${formatNumber(budget.amount)}
                            </td>
                            <td>${budget.budget_type.name}</td>
                            <td>${formatDate(budget.start_date)} to ${formatDate(budget.end_date)}</td>
                            <td>${budget.description || 'No description'}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editBudget(${budget.budget_allocation_id})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteBudget(${budget.budget_allocation_id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    
                    tableBody.append(budgetRow);
                });
            } else {
                tableBody.html('<tr><td colspan="5" class="text-center">No budget history found.</td></tr>');
            }
        }

        function updateRecentActivities(recentExpenses, recentBudgets) {
            const container = $('#recentActivitiesContainer');
            container.html(''); // Clear existing content
            
            // Combine both expenses and budgets, then sort by date
            const allActivities = [];
            
            // Add expenses to activities array
            if (recentExpenses && recentExpenses.length > 0) {
                recentExpenses.forEach(expense => {
                    allActivities.push({
                        type: 'expense',
                        title: expense.title,
                        amount: expense.amount,
                        date: new Date(expense.date),
                        category: expense.category ? expense.category.name : 'Uncategorized',
                        color: expense.category ? expense.category.color_code : '#6c757d',
                        created_by: expense.creator ? `${expense.creator.first_name} ${expense.creator.last_name}` : 'System'
                    });
                });
            }
            
            // Add budgets to activities array
            if (recentBudgets && recentBudgets.length > 0) {
                recentBudgets.forEach(budget => {
                    allActivities.push({
                        type: 'budget',
                        title: budget.budget_type ? budget.budget_type.name : 'Budget Allocation',
                        amount: budget.amount,
                        date: new Date(budget.created_at),
                        description: budget.description || 'No description provided',
                        created_by: budget.creator ? `${budget.creator.first_name} ${budget.creator.last_name}` : 'System'
                    });
                });
            }
            
            // Sort by date (newest first)
            allActivities.sort((a, b) => b.date - a.date);
            
            // Display up to 5 most recent activities
            const activitiesToShow = allActivities.slice(0, 5);
            
            if (activitiesToShow.length > 0) {
                activitiesToShow.forEach(activity => {
                    let activityHtml;
                    
                    if (activity.type === 'expense') {
                        activityHtml = `
                            <div class="recent-activity-item">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="activity-icon expense me-2">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="activity-title">
                                        New expense: ${activity.title}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="activity-details">
                                        <span class="badge" style="background-color: ${activity.color}">${activity.category}</span>
                                        <small class="text-muted ms-2">${formatDate(activity.date)}</small>
                                    </div>
                                    <div class="activity-amount">
                                        ₱${formatNumber(activity.amount)}
                                    </div>
                                </div>
                                <div class="activity-creator">
                                    <small class="text-muted">Created by: ${activity.created_by}</small>
                                </div>
                            </div>
                        `;
                    } else {
                        // Budget activity
                        activityHtml = `
                            <div class="recent-activity-item">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="activity-icon budget me-2">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div class="activity-title">
                                        New budget: ${activity.title}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="activity-details">
                                        <small class="text-muted">${formatDate(activity.date)}</small>
                                    </div>
                                    <div class="activity-amount ${activity.amount >= 0 ? 'budget-amount-positive' : 'budget-amount-negative'}">
                                        ₱${formatNumber(activity.amount)}
                                    </div>
                                </div>
                                <div class="activity-description">
                                    <small>${activity.description}</small>
                                </div>
                                <div class="activity-creator">
                                    <small class="text-muted">Created by: ${activity.created_by}</small>
                                </div>
                            </div>
                        `;
                    }
                    
                    container.append(activityHtml);
                });
            } else {
                container.html('<div class="alert alert-info">No recent activities found.</div>');
            }
        }

        // Also update the initial stats function
        function updateDashboardStats() {
            const category = $('#categoryFilter').val();
            const month = $('#monthFilter').val();
            
            // Update the period badge immediately
            $('#expensesPeriodBadge').text(month ? new Date(month + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'All Time');
            
            // Show the spinners for both areas being updated
            $('#expensesSpinner').removeClass('d-none');
            $('#activitiesSpinner').removeClass('d-none');
            $('#chartSpinner').show();
            
            $.ajax({
                url: '{{ route("admin.expense.index") }}',
                method: 'GET',
                data: {
                    category_id: category,
                    month: month,
                    format: 'json' // Request JSON response instead of HTML
                },
                success: function(response) {
                    // Update statistics
                    updateStatistics(response.stats);
                    
                    // Update chart
                    if (response.chartData) {
                        updateChart(
                            response.chartData.labels,
                            response.chartData.data,
                            response.chartData.colors
                        );
                    }
                    
                    // Update recent expenses list 
                    updateRecentExpenses(response.recentExpenses);
                    
                    // Update budget history
                    updateBudgetHistory(response.recentBudgets);
                    
                    // Update recent activities - THIS WAS MISSING
                    updateRecentActivities(response.recentExpenses, response.recentBudgets);
                },
                error: function() {
                    toastr.error('Failed to load dashboard data');
                    
                    // Clear loading indicators on error
                    $('#recentActivitiesContainer').html('<div class="alert alert-danger">Failed to load activities</div>');
                },
                complete: function() {
                    // Hide all spinners
                    $('#expensesSpinner').addClass('d-none');
                    $('#activitiesSpinner').addClass('d-none');
                    $('#chartSpinner').hide();
                }
            });
        }

        // Load all expenses for modal with pagination
        function loadAllExpenses(page = 1) {
            $('#allExpensesSpinner').removeClass('d-none');

            // Clear filter form if it exists
            if ($('#expensesFilterCategory').length) {
                $('#expensesFilterCategory').val('');
                $('#expensesFilterStartDate').val('');
                $('#expensesFilterEndDate').val('');
            }
            
            $('#allExpensesSpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.filtered") }}', // Use the existing filtered route instead
                method: 'GET',
                // No filters means get all expenses
                data: {
                    category_id: '',
                    start_date: '',
                    end_date: '',
                    page: page,
                    per_page: 10
                },
                success: function(response) {
                    renderAllExpenses(response.expenses, response.pagination);
                },
                error: function() {
                    toastr.error('Failed to load expenses');
                },
                complete: function() {
                    $('#allExpensesSpinner').addClass('d-none');
                }
            });
        }

        // Render all expenses in the modal with pagination controls
        function renderAllExpenses(expenses, pagination) {
            const tableBody = $('#allExpensesBody');
            tableBody.html('');
            
            if (expenses && expenses.length > 0) {
                expenses.forEach(expense => {
                    // Improved receipt file link handling
                    let receiptFileHtml = '<span class="text-muted">No file</span>';
                    if (expense.receipt_path) {
                        // Ensure the receipt path has the proper URL
                        const receiptUrl = expense.receipt_path.startsWith('http') 
                            ? expense.receipt_path 
                            : "{{ asset('storage') }}/" + expense.receipt_path;
                        
                        receiptFileHtml = `<a href="${receiptUrl}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark"></i> View
                        </a>`;
                    }
                    
                    const row = `
                        <tr>
                            <td>${formatDate(expense.date)}</td>
                            <td>${expense.title}</td>
                            <td><span class="badge" style="background-color: ${expense.category.color_code}">${expense.category.name}</span></td>
                            <td>₱${formatNumber(expense.amount)}</td>
                            <td>${formatPaymentMethod(expense.payment_method)}</td>
                            <td>${expense.receipt_number}</td>
                            <td>${expense.creator ? expense.creator.first_name + ' ' + expense.creator.last_name : 'Unknown'}</td>
                            <td>${receiptFileHtml}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editExpense(${expense.expense_id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteExpense(${expense.expense_id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
                
                // Add pagination controls that keep the filters
                const paginationEl = $('#expensesPagination');
                paginationEl.html('');
                
                if (pagination.last_page > 1) {
                    let paginationControls = `
                        <nav aria-label="Expenses pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFilteredExpenses(1); return false;">First</a>
                                </li>
                                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFilteredExpenses(${pagination.current_page - 1}); return false;">Previous</a>
                                </li>
                    `;
                    
                    // Add page numbers
                    const startPage = Math.max(1, pagination.current_page - 2);
                    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
                    
                    for (let i = startPage; i <= endPage; i++) {
                        paginationControls += `
                            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadFilteredExpenses(${i}); return false;">${i}</a>
                            </li>
                        `;
                    }
                    
                    paginationControls += `
                        <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                            <a class="page-link" href="#" onclick="loadFilteredExpenses(${pagination.current_page + 1}); return false;">Next</a>
                        </li>
                        <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                            <a class="page-link" href="#" onclick="loadFilteredExpenses(${pagination.last_page}); return false;">Last</a>
                        </li>
                    </ul>
                </nav>
                    `;
                    
                    paginationEl.html(paginationControls);
                }
            } else {
                tableBody.html('<tr><td colspan="9" class="text-center">No expenses found</td></tr>');
            }
        }

        // Load filtered expenses with pagination
        function loadFilteredExpenses(page = 1) {
            const category = $('#expensesFilterCategory').val();
            const startDate = $('#expensesFilterStartDate').val();
            const endDate = $('#expensesFilterEndDate').val();
            
            // Validation
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                toastr.error('Start date cannot be after end date');
                return;
            }
            
            $('#allExpensesSpinner').removeClass('d-none');
            
            // Show filter status
            const filterInfo = [];
            if (category) filterInfo.push('Category: ' + $('#expensesFilterCategory option:selected').text());
            if (startDate) filterInfo.push('From: ' + new Date(startDate).toLocaleDateString());
            if (endDate) filterInfo.push('To: ' + new Date(endDate).toLocaleDateString());
            
            const filterStatus = filterInfo.length > 0 ? 
                '<div class="alert alert-info mb-3"><i class="bi bi-funnel-fill me-2"></i>Filters applied: ' + filterInfo.join(' • ') + '</div>' : '';
            $('#expensesFilterStatus').html(filterStatus);
            
            $.ajax({
                url: '{{ route("admin.expense.filtered") }}',
                method: 'GET',
                data: {
                    category_id: category,
                    start_date: startDate,
                    end_date: endDate,
                    page: page,
                    per_page: 10
                },
                success: function(response) {
                    renderAllExpenses(response.expenses, response.pagination);

                    // When attaching edit button event handlers
                    $('.edit-expense-btn').on('click', function() {
                        const expenseId = $(this).data('id');
                        editExpense(expenseId); // This will now handle modal closing
                    });
                },
                error: function(xhr) {
                    console.error('Filter request failed:', xhr);
                    toastr.error('Failed to filter expenses');
                },
                complete: function() {
                    $('#allExpensesSpinner').addClass('d-none');
                }
            });
        }

        // Load full budget history with pagination
        function loadFullBudgetHistory(page = 1) {
            $('#fullHistorySpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.budget.filtered") }}',
                method: 'GET',
                // No filters means get all budgets
                data: {
                    budget_type_id: '',
                    start_date: '',
                    end_date: '',
                    page: page,
                    per_page: 10
                },
                success: function(response) {
                    renderFullBudgetHistory(response.budgets, response.pagination);
                },
                error: function() {
                    toastr.error('Failed to load budget history');
                },
                complete: function() {
                    $('#fullHistorySpinner').addClass('d-none');
                }
            });
        }

        // Render full budget history with pagination controls
        function renderFullBudgetHistory(budgets, pagination) {
            const tableBody = $('#fullHistoryBody');
            tableBody.html('');
            
            if (budgets && budgets.length > 0) {
                budgets.forEach(budget => {
                    const budgetRow = `
                        <tr>
                            <td class="${budget.amount >= 0 ? 'budget-amount-positive' : 'budget-amount-negative'}">
                                ₱${formatNumber(budget.amount)}
                            </td>
                            <td>${budget.budget_type ? budget.budget_type.name : 'Unknown'}</td>
                            <td>${formatDate(budget.start_date)} to ${formatDate(budget.end_date)}</td>
                            <td>${budget.description || 'No description'}</td>
                            <td>${budget.creator ? budget.creator.first_name + ' ' + budget.creator.last_name : 'Unknown'}</td>
                            <td>${formatDateTime(budget.created_at)}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editBudget(${budget.budget_allocation_id})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteBudget(${budget.budget_allocation_id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    
                    tableBody.append(budgetRow);
                });
                
                // Add pagination controls
                const paginationEl = $('#budgetPagination');
                paginationEl.html('');
                
                if (pagination.last_page > 1) {
                    let paginationControls = `
                        <nav aria-label="Budget pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFullBudgetHistory(1); return false;">First</a>
                                </li>
                                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFullBudgetHistory(${pagination.current_page - 1}); return false;">Previous</a>
                                </li>
                    `;
                    
                    // Add page numbers
                    const startPage = Math.max(1, pagination.current_page - 2);
                    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
                    
                    for (let i = startPage; i <= endPage; i++) {
                        paginationControls += `
                            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadFullBudgetHistory(${i}); return false;">${i}</a>
                            </li>
                        `;
                    }
                    
                    paginationControls += `
                                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFullBudgetHistory(${pagination.current_page + 1}); return false;">Next</a>
                                </li>
                                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadFullBudgetHistory(${pagination.last_page}); return false;">Last</a>
                                </li>
                            </ul>
                        </nav>
                    `;
                    
                    paginationEl.html(paginationControls);
                }
            } else {
                tableBody.html('<tr><td colspan="7" class="text-center">No budget history found</td></tr>');
            }
        }

        // Load filtered budget history with pagination
        function loadFilteredBudgetHistory(page = 1) {
            const budgetType = $('#budgetFilterType').val();
            const startDate = $('#budgetFilterStartDate').val();
            const endDate = $('#budgetFilterEndDate').val();
            
            // Validation
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                toastr.error('Start date cannot be after end date');
                return;
            }
            
            $('#fullHistorySpinner').removeClass('d-none');
            
            // Show filter status
            const filterInfo = [];
            if (budgetType) filterInfo.push('Type: ' + $('#budgetFilterType option:selected').text());
            if (startDate) filterInfo.push('From: ' + new Date(startDate).toLocaleDateString());
            if (endDate) filterInfo.push('To: ' + new Date(endDate).toLocaleDateString());
            
            const filterStatus = filterInfo.length > 0 ? 
                '<div class="alert alert-info mb-3"><i class="bi bi-funnel-fill me-2"></i>Filters applied: ' + filterInfo.join(' • ') + '</div>' : '';
            $('#budgetFilterStatus').html(filterStatus);
            
            $.ajax({
                url: '{{ route("admin.expense.budget.filtered") }}',
                method: 'GET',
                data: {
                    budget_type_id: budgetType,
                    start_date: startDate,
                    end_date: endDate,
                    page: page,
                    per_page: 10
                },
                success: function(response) {
                    renderFullBudgetHistory(response.budgets, response.pagination);

                    // When attaching edit button event handlers
                    $('.edit-budget-btn').on('click', function() {
                        const budgetId = $(this).data('id');
                        editBudget(budgetId); // This will now handle modal closing
                    });
                },
                error: function(xhr) {
                    console.error('Filter request failed:', xhr);
                    toastr.error('Failed to filter budget history');
                },
                complete: function() {
                    $('#fullHistorySpinner').addClass('d-none');
                }
            });
        }

        // Export expenses to Excel
        function exportExpensesToExcel() {
            submitExportForm('{{ route("admin.expense.export.excel") }}');
        }

        // Export budgets to Excel
        function exportBudgetsToExcel() {
            submitExportForm('{{ route("admin.expense.budget.export.excel") }}');
        }

        // Export filtered expenses to Excel
        function exportFilteredExpensesToExcel() {
            const category = $('#expensesFilterCategory').val();
            const startDate = $('#expensesFilterStartDate').val();
            const endDate = $('#expensesFilterEndDate').val();
            
            submitExportForm('{{ route("admin.expense.export.excel") }}', {
                category_id: category || '',
                start_date: startDate || '',
                end_date: endDate || ''
            });
        }

        // Export filtered budgets to Excel
        function exportFilteredBudgetsToExcel() {
            const budgetType = $('#budgetFilterType').val();
            const startDate = $('#budgetFilterStartDate').val();
            const endDate = $('#budgetFilterEndDate').val();
            
            submitExportForm('{{ route("admin.expense.budget.export.excel") }}', {
                budget_type_id: budgetType || '',
                start_date: startDate || '',
                end_date: endDate || ''
            });
        }

        // Format number with comma separators
        function formatNumber(number) {
            return parseFloat(number).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Format date
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        // Format date and time
        function formatDateTime(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Format payment method
        function formatPaymentMethod(method) {
            if (!method) return '-';
            return method.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
        }

        // Format number with commas
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Format date in a readable way
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Show success alert
        function showSuccessAlert(message) {
            $('#successAlertMessage').text(message);
            $('#successAlert').removeClass('d-none').addClass('show');
            setTimeout(() => {
                $('#successAlert').removeClass('show').addClass('d-none');
            }, 5000);
        }

        // Helper function to submit POST requests for exports
        function submitExportForm(route, params = {}) {
            // Create a form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = route;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = $('meta[name="csrf-token"]').attr('content');
            form.appendChild(csrfToken);
            
            // Add any additional parameters
            for (const key in params) {
                if (params[key] !== null && params[key] !== undefined) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = params[key];
                    form.appendChild(input);
                }
            }
            
            // Add form to body, submit it, and remove it
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            toastr.success('Export started. The file will download shortly.');
        }

        // Add reset filter functions
        function resetExpensesFilter() {
            // Clear filter form fields
            $('#expensesFilterCategory').val('');
            $('#expensesFilterStartDate').val('');
            $('#expensesFilterEndDate').val('');
            
            // Clear filter status indicator
            $('#expensesFilterStatus').html('');
            
            // Reload all expenses (unfiltered)
            loadAllExpenses(1);
            
            // Show feedback
            toastr.info('Expense filters have been reset');
        }

        function resetBudgetFilter() {
            // Clear filter form fields
            $('#budgetFilterType').val('');
            $('#budgetFilterStartDate').val('');
            $('#budgetFilterEndDate').val('');
            
            // Clear filter status indicator
            $('#budgetFilterStatus').html('');
            
            // Reload all budgets (unfiltered)
            loadFullBudgetHistory(1);
            
            // Show feedback
            toastr.info('Budget filters have been reset');
        }
    </script>
</body>
</html>
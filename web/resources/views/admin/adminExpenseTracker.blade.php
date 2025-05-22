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
                                <h6 class="text-muted">Budget Remaining</h6>
                                <h3 class="text-warning" id="budgetRemaining">₱0.00</h3>
                                <small id="budgetUsedPercent">No active budget</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-success bg-opacity-10 border-success border-opacity-25">
                                <h6 class="text-muted">Most Spent Category</h6>
                                <h3 class="text-success" id="topCategory">None</h3>
                                <small id="topCategoryAmount">₱0.00 (0% of total)</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="summary-card bg-info bg-opacity-10 border-info border-opacity-25">
                                <h6 class="text-muted">Current Budget</h6>
                                <h3 class="text-info" id="currentBudget">₱0.00</h3>
                                <small id="budgetPeriod">No active budget</small>
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
                                            <option value="{{ $category->category_id }}" {{ $categoryFilter == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="month" class="form-control form-control-sm" id="monthFilter" value="{{ $dateFilter ?? date('Y-m') }}">
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
                                                        <div class="text-end">
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
                                    <h5 class="card-title">Expense Breakdown</h5>
                                    <div class="chart-container">
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
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="expenseModalTitle">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="expenseForm">
                        <input type="hidden" id="expenseId">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expenseTitle" class="form-label required-field">Expense Title</label>
                                    <input type="text" class="form-control" id="expenseTitle" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expenseCategory" class="form-label required-field">Category</label>
                                    <select class="form-select" id="expenseCategory" name="category_id" required>
                                        <option value="" selected disabled>Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expenseAmount" class="form-label required-field">Amount (₱)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="expenseAmount" name="amount" step="0.01" min="0.01" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expensePaymentMethod" class="form-label required-field">Payment Method</label>
                                    <select class="form-select" id="expensePaymentMethod" name="payment_method" required>
                                        <option value="" selected disabled>Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="gcash">GCash</option>
                                        <option value="paymaya">PayMaya</option>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="debit_card">Debit Card</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expenseDate" class="form-label required-field">Date</label>
                                    <input type="date" class="form-control" id="expenseDate" name="date" max="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expenseReceiptNumber" class="form-label required-field">Receipt/Reference Number</label>
                                    <input type="text" class="form-control" id="expenseReceiptNumber" name="receipt_number" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expenseDescription" class="form-label required-field">Description</label>
                            <textarea class="form-control" id="expenseDescription" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expenseReceipt" class="form-label">Attach Receipt (Optional)</label>
                            <input type="file" class="form-control" id="expenseReceipt" name="receipt" accept="image/jpeg,image/png,application/pdf">
                            <div class="form-text">Accepted formats: JPG, PNG, PDF (max 2MB)</div>
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
                    <h5 class="modal-title" id="budgetModalTitle">Add Budget Allocation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="budgetForm">
                        <input type="hidden" id="budgetId">
                        
                        <div class="mb-3">
                            <label for="budgetAmount" class="form-label required-field">Amount (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="budgetAmount" name="amount" step="0.01" required>
                            </div>
                            <div class="form-text">Use positive values for allocations, negative for deductions</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required-field">Budget Period</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text">Start</span>
                                        <input type="date" class="form-control" id="budgetStartDate" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text">End</span>
                                        <input type="date" class="form-control" id="budgetEndDate" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetType" class="form-label required-field">Budget Type</label>
                            <select class="form-select" id="budgetType" name="budget_type_id" required>
                                <option value="" selected disabled>Select Budget Type</option>
                                @foreach($budgetTypes as $type)
                                    <option value="{{ $type->budget_type_id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="budgetDescription" name="description" rows="3"></textarea>
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
                    <!-- Filter Controls -->
                    <div class="filter-controls mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="expensesFilterCategory">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="expensesFilterStartDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="expensesFilterEndDate">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="applyExpensesFilter">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive position-relative">
                        <div class="spinner-overlay d-none" id="allExpensesSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Receipt #</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="allExpensesBody">
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportFilteredExpensesBtn">
                        <i class="bi bi-download"></i> Export Filtered Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full History Modal -->
    <div class="modal fade" id="fullHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Budget History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Controls -->
                    <div class="filter-controls mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Budget Type</label>
                                <select class="form-select" id="budgetFilterType">
                                    <option value="">All Types</option>
                                    @foreach($budgetTypes as $type)
                                        <option value="{{ $type->budget_type_id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="budgetFilterStartDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="budgetFilterEndDate">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="applyBudgetFilter">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive position-relative">
                        <div class="spinner-overlay d-none" id="fullHistorySpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Creation Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="fullHistoryBody">
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportFilteredBudgetsBtn">
                        <i class="bi bi-download"></i> Export Filtered Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalTitle">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmMessage">Are you sure you want to delete this item? This action cannot be undone.</p>
                    
                    <form id="deleteConfirmationForm" class="mt-3">
                        <input type="hidden" id="deleteItemId">
                        <input type="hidden" id="deleteItemType">
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label required-field">Please enter your password to confirm</label>
                            <input type="password" class="form-control" id="confirmPassword" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
            
            // Load and display initial statistics
            updateDashboardStats();
            
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
                loadAllExpenses();
                allExpensesModal.show();
            });
            
            $('#viewFullHistoryBtn').on('click', function() {
                loadFullBudgetHistory();
                fullHistoryModal.show();
            });
            
            // Filter changes
            $('#categoryFilter, #monthFilter').on('change', function() {
                updateDashboardWithFilters();
            });
            
            // Apply expense filter
            $('#applyExpensesFilter').on('click', function() {
                loadFilteredExpenses();
            });
            
            // Apply budget filter
            $('#applyBudgetFilter').on('click', function() {
                loadFilteredBudgetHistory();
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
            $('#exportExpensesExcel').on('click', function(e) {
                e.preventDefault();
                exportExpensesToExcel();
            });
            
            $('#exportBudgetsExcel').on('click', function(e) {
                e.preventDefault();
                exportBudgetsToExcel();
            });
            
            $('#exportFilteredExpensesBtn').on('click', function() {
                exportFilteredExpensesToExcel();
            });
            
            $('#exportFilteredBudgetsBtn').on('click', function() {
                exportFilteredBudgetsToExcel();
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
            $('#expenseTitle').removeClass('is-invalid');
            $('#expenseCategory').removeClass('is-invalid');
            $('#expenseAmount').removeClass('is-invalid');
            $('#expensePaymentMethod').removeClass('is-invalid');
            $('#expenseDate').removeClass('is-invalid');
            $('#expenseReceiptNumber').removeClass('is-invalid');
            $('#expenseDescription').removeClass('is-invalid');
            $('#expenseReceipt').removeClass('is-invalid');
        }

        // Clear budget form
        function clearBudgetForm() {
            currentBudgetId = null;
            $('#budgetId').val('');
            $('#budgetForm')[0].reset();
            $('#budgetAmount').removeClass('is-invalid');
            $('#budgetStartDate').removeClass('is-invalid');
            $('#budgetEndDate').removeClass('is-invalid');
            $('#budgetType').removeClass('is-invalid');
        }

        // Save expense
        function saveExpense() {
            // Get form data
            const form = document.getElementById('expenseForm');
            const formData = new FormData(form);
            
            // Show loading state
            $('#saveExpenseBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            // Determine if this is a create or update operation
            const isUpdate = currentExpenseId !== null;
            
            // Set the correct URL based on operation type
            const url = isUpdate 
                ? "{{ route('admin.expense.update', ['id' => '_id_']) }}".replace('_id_', currentExpenseId)
                : "{{ route('admin.expense.store') }}";
            
            // Make AJAX request
            $.ajax({
                url: url,
                type: isUpdate ? 'POST' : 'POST', // Or PUT for update if your routes support it
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success message
                    toastr.success(isUpdate ? 'Expense updated successfully' : 'Expense added successfully');
                    
                    // Close modal and refresh data
                    $('#addExpenseModal').modal('hide');
                    updateDashboardWithFilters();
                    
                    // Clear form and reset currentExpenseId
                    clearExpenseForm();
                    currentExpenseId = null;
                    
                    // Show success alert
                    showSuccessAlert(isUpdate ? 'Expense updated successfully' : 'Expense added successfully');
                },
                error: function(xhr) {
                    // Handle errors
                    let errorMessage = 'Failed to save expense';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                        errorMessage = firstError;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $('#saveExpenseBtn').prop('disabled', false).html('Save Expense');
                }
            });
        }

        // Save budget
        function saveBudget() {
            // Form validation
            const form = document.getElementById('budgetForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Check that end date is after start date
            const startDate = new Date($('#budgetStartDate').val());
            const endDate = new Date($('#budgetEndDate').val());
            
            if (endDate < startDate) {
                toastr.error('End date must be after start date');
                $('#budgetEndDate').addClass('is-invalid');
                return;
            }

            // Prepare form data
            const formData = new FormData(form);
            
            // Add the hidden ID field for updates
            if (currentBudgetId) {
                formData.append('budget_allocation_id', currentBudgetId);
            }

            // Disable submit button and show spinner
            $('#saveBudgetBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            // FIXED URL: Properly include the ID parameter in the route
            const url = currentBudgetId ? 
                "{{ route('admin.expense.budget.update', ['id' => '_id_']) }}".replace('_id_', currentBudgetId) : 
                "{{ route('admin.expense.budget.store') }}";

            // Submit the form
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success(currentBudgetId ? 'Budget updated successfully' : 'Budget added successfully');
                    
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('addBudgetModal')).hide();
                    
                    // Show success alert
                    showSuccessAlert(currentBudgetId ? 'Budget updated successfully' : 'New budget allocation added successfully');
                    
                    // Update dashboard data
                    updateDashboardWithFilters();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            const inputField = document.getElementById(field);
                            if (inputField) {
                                inputField.classList.add('is-invalid');
                                
                                // Show error message
                                const errorMsg = document.createElement('div');
                                errorMsg.classList.add('invalid-feedback');
                                errorMsg.textContent = errors[field][0];
                                inputField.parentNode.appendChild(errorMsg);
                            }
                            toastr.error(errors[field][0]);
                        }
                    } else {
                        toastr.error('Failed to save budget. Please try again later.');
                    }
                },
                complete: function() {
                    // Re-enable submit button
                    $('#saveBudgetBtn').prop('disabled', false).text('Save Budget');
                }
            });
        }

        // Edit expense
        function editExpense(id) {
            // Store the expense ID for use in the save function
            currentExpenseId = id;
            
            // Show loading state
            $('#expenseModalContent').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading expense details...</p></div>');
            
            // Fetch expense details
            $.ajax({
                url: "{{ route('admin.expense.get', '') }}/" + id,
                type: 'GET',
                success: function(response) {
                    const expense = response.expense;
                    
                    // Fill the form with expense data
                    $('#expenseTitle').val(expense.title);
                    $('#expenseCategory').val(expense.category_id);
                    $('#expenseAmount').val(expense.amount);
                    $('#expensePaymentMethod').val(expense.payment_method);
                    $('#expenseDate').val(expense.date.substring(0, 10)); // YYYY-MM-DD format
                    $('#expenseReceiptNumber').val(expense.receipt_number);
                    $('#expenseDescription').val(expense.description);
                    
                    // Update modal title
                    $('#expenseModalLabel').text('Edit Expense');
                    
                    // Show the modal if not already visible
                    $('#addExpenseModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load expense details');
                }
            });
        }

        // Edit budget
        function editBudget(id) {
            // Clear the form first
            clearBudgetForm();
            
            // Set current ID for update operation
            currentBudgetId = id;
            
            // Show loading spinner
            $('#budgetModalTitle').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            
            // FIXED: Correctly include the ID parameter in the route
            $.ajax({
                url: "{{ route('admin.expense.budget.get', ['id' => '_id_']) }}".replace('_id_', id),
                method: 'GET',
                success: function(response) {
                    const budget = response.budget;
                    
                    // Populate form fields
                    $('#budgetAmount').val(budget.amount);
                    $('#budgetStartDate').val(budget.start_date);
                    $('#budgetEndDate').val(budget.end_date);
                    $('#budgetType').val(budget.budget_type_id);
                    $('#budgetDescription').val(budget.description);
                    
                    // Update modal title
                    $('#budgetModalTitle').text('Edit Budget Allocation');
                    
                    // Show the modal
                    new bootstrap.Modal(document.getElementById('addBudgetModal')).show();
                },
                error: function() {
                    toastr.error('Failed to load budget details. Please try again later.');
                }
            });
        }

        // Delete expense
        function deleteExpense(id) {
            // Set up delete confirmation modal for an expense
            $('#deleteModalTitle').text('Delete Expense');
            $('#deleteConfirmMessage').text('Are you sure you want to delete this expense? This action cannot be undone.');
            $('#deleteItemId').val(id);
            $('#deleteItemType').val('expense');
            $('#confirmPassword').val('');
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('deleteConfirmationModal')).show();
        }

        // Delete budget
        function deleteBudget(id) {
            // Set up delete confirmation modal for a budget
            $('#deleteModalTitle').text('Delete Budget Allocation');
            $('#deleteConfirmMessage').text('Are you sure you want to delete this budget allocation? This action cannot be undone.');
            $('#deleteItemId').val(id);
            $('#deleteItemType').val('budget');
            $('#confirmPassword').val('');
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('deleteConfirmationModal')).show();
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
            
            // Call appropriate delete endpoint
            const url = itemType === 'expense' ? 
                '{{ route("admin.expense.delete") }}' : 
                '{{ route("admin.expense.budget.delete") }}';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append(itemType === 'expense' ? 'expense_id' : 'budget_allocation_id', itemId);
            formData.append('password', password);
            
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success(itemType === 'expense' ? 'Expense deleted successfully' : 'Budget deleted successfully');
                    
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal')).hide();
                    
                    // Show success alert
                    showSuccessAlert(itemType === 'expense' ? 'Expense deleted successfully' : 'Budget allocation deleted successfully');
                    
                    // Update dashboard data
                    updateDashboardWithFilters();
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        toastr.error('Incorrect password. Please try again.');
                        $('#confirmPassword').addClass('is-invalid');
                    } else {
                        toastr.error('Failed to delete ' + itemType + '. Please try again later.');
                    }
                },
                complete: function() {
                    // Re-enable button
                    $('#confirmDeleteBtn').prop('disabled', false).text('Delete');
                }
            });
        }

        // Update dashboard with current filters
        function updateDashboardWithFilters() {
            const category = $('#categoryFilter').val();
            const month = $('#monthFilter').val();
            
            // Show loading spinners
            $('#expensesSpinner').removeClass('d-none');
            $('#activitiesSpinner').removeClass('d-none'); // Changed from budgetSpinner
            
            // Update expenses period badge
            $('#expensesPeriodBadge').text(month ? new Date(month + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'All Time');
            
            // Make AJAX call to get filtered data
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
                    
                    // Update recent expenses list
                    updateRecentExpenses(response.recentExpenses);
                    
                    // Update chart
                    if (response.chartData) {
                        updateChart(
                            response.chartData.labels,
                            response.chartData.data,
                            response.chartData.colors
                        );
                    }
                    
                    // Update budget history
                    updateBudgetHistory(response.recentBudgets);
                    
                    // Update recent activities (new function)
                    updateRecentActivities(response.recentExpenses, response.recentBudgets);
                },
                error: function() {
                    toastr.error('Failed to update dashboard data. Please try again later.');
                },
                complete: function() {
                    // Hide loading spinners
                    $('#expensesSpinner').addClass('d-none');
                    $('#activitiesSpinner').addClass('d-none'); // Changed from budgetSpinner
                }
            });
        }

        // Update statistics
        function updateStatistics(stats) {
            // Total expenses
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
            
            // Top category
            if (stats.topCategory) {
                $('#topCategory').text(stats.topCategory.name || 'None');
                const percentage = stats.totalExpenses > 0 ? ((stats.topCategory.amount / stats.totalExpenses) * 100).toFixed(1) : 0;
                $('#topCategoryAmount').text(`₱${formatNumber(stats.topCategory.amount || 0)} (${percentage}% of total)`);
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
                    // Create expense item
                    const expenseItem = `
                        <div class="expense-item" style="border-left-color: ${expense.category.color_code};">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="expense-title">
                                        <i class="bi ${expense.category.icon || 'bi-tag'} me-2 text-muted"></i>
                                        ${expense.title}
                                    </div>
                                    <div class="expense-detail">
                                        <span>${expense.category.name}</span> • 
                                        <span>${formatDate(expense.date)}</span> •
                                        <span>${formatPaymentMethod(expense.payment_method)}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="expense-amount">₱${formatNumber(expense.amount)}</div>
                                    <div class="expense-actions">
                                        <button class="btn-action-icon edit" onclick="editExpense(${expense.expense_id})" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-action-icon delete" onclick="deleteExpense(${expense.expense_id})" title="Delete">
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

        // Initial dashboard stats - UPDATED to use the same approach
        function updateDashboardStats() {
            const category = $('#categoryFilter').val();
            const month = $('#monthFilter').val();
            
            // Show the spinners for both areas being updated
            $('#expensesSpinner').removeClass('d-none');
            $('#activitiesSpinner').removeClass('d-none');
            
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
                }
            });
        }

        // Load all expenses for modal
        function loadAllExpenses() {
            $('#allExpensesSpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.filtered") }}', // Use the existing filtered route instead
                method: 'GET',
                // No filters means get all expenses
                data: {
                    category_id: '',
                    start_date: '',
                    end_date: ''
                },
                success: function(response) {
                    renderAllExpenses(response.expenses);
                },
                error: function() {
                    toastr.error('Failed to load expenses');
                },
                complete: function() {
                    $('#allExpensesSpinner').addClass('d-none');
                }
            });
        }

        // Render all expenses in the modal
        function renderAllExpenses(expenses) {
            const tableBody = $('#allExpensesBody');
            tableBody.html('');
            
            if (expenses && expenses.length > 0) {
                expenses.forEach(expense => {
                    const row = `
                        <tr>
                            <td>${formatDate(expense.date)}</td>
                            <td>${expense.title}</td>
                            <td><span class="badge" style="background-color: ${expense.category.color_code}">${expense.category.name}</span></td>
                            <td>₱${formatNumber(expense.amount)}</td>
                            <td>${formatPaymentMethod(expense.payment_method)}</td>
                            <td>${expense.receipt_number}</td>
                            <td>${expense.creator ? expense.creator.first_name + ' ' + expense.creator.last_name : 'Unknown'}</td>
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
            } else {
                tableBody.html('<tr><td colspan="8" class="text-center">No expenses found</td></tr>');
            }
        }

        // Load filtered expenses
        function loadFilteredExpenses() {
            const category = $('#expensesFilterCategory').val();
            const startDate = $('#expensesFilterStartDate').val();
            const endDate = $('#expensesFilterEndDate').val();
            
            // Validation
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                toastr.error('Start date cannot be after end date');
                return;
            }
            
            $('#allExpensesSpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.filtered") }}', // Changed from 'filter' to 'filtered'
                method: 'GET',
                data: {
                    category_id: category,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    renderAllExpenses(response.expenses);
                },
                error: function() {
                    toastr.error('Failed to filter expenses');
                },
                complete: function() {
                    $('#allExpensesSpinner').addClass('d-none');
                }
            });
        }

        // Load full budget history
        function loadFullBudgetHistory() {
            $('#fullHistorySpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.budget.filtered") }}', // Use the existing filtered route instead
                method: 'GET',
                // No filters means get all budgets
                data: {
                    budget_type_id: '',
                    start_date: '',
                    end_date: ''
                },
                success: function(response) {
                    renderFullBudgetHistory(response.budgets);
                },
                error: function() {
                    toastr.error('Failed to load budget history');
                },
                complete: function() {
                    $('#fullHistorySpinner').addClass('d-none');
                }
            });
        }

        // Render full budget history
        function renderFullBudgetHistory(budgets) {
            const tableBody = $('#fullHistoryBody');
            tableBody.html('');
            
            if (budgets && budgets.length > 0) {
                budgets.forEach(budget => {
                    const row = `
                        <tr>
                            <td>${formatDate(budget.start_date)} to ${formatDate(budget.end_date)}</td>
                            <td class="${budget.amount >= 0 ? 'budget-amount-positive' : 'budget-amount-negative'}">₱${formatNumber(budget.amount)}</td>
                            <td>${budget.budget_type.name}</td>
                            <td>${budget.description || 'No description'}</td>
                            <td>${budget.creator ? budget.creator.first_name + ' ' + budget.creator.last_name : 'Unknown'}</td>
                            <td>${formatDateTime(budget.created_at)}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editBudget(${budget.budget_allocation_id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteBudget(${budget.budget_allocation_id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            } else {
                tableBody.html('<tr><td colspan="7" class="text-center">No budget history found</td></tr>');
            }
        }

        // Load filtered budget history
        function loadFilteredBudgetHistory() {
            const budgetType = $('#budgetFilterType').val();
            const startDate = $('#budgetFilterStartDate').val();
            const endDate = $('#budgetFilterEndDate').val();
            
            // Validation
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                toastr.error('Start date cannot be after end date');
                return;
            }
            
            $('#fullHistorySpinner').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("admin.expense.budget.filtered") }}',
                method: 'GET',
                data: {
                    budget_type_id: budgetType,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    renderFullBudgetHistory(response.budgets);
                },
                error: function() {
                    toastr.error('Failed to filter budget history');
                },
                complete: function() {
                    $('#fullHistorySpinner').addClass('d-none');
                }
            });
        }

        // Export expenses to Excel
        function exportExpensesToExcel() {
            window.location.href = '{{ route("admin.expense.export.excel") }}';
            toastr.success('Export started. The file will download shortly.');
        }

        // Export budgets to Excel
        function exportBudgetsToExcel() {
            window.location.href = '{{ route("admin.expense.budget.export.excel") }}';
            toastr.success('Export started. The file will download shortly.');
        }

        // Export filtered expenses to Excel
        function exportFilteredExpensesToExcel() {
            const category = $('#expensesFilterCategory').val();
            const startDate = $('#expensesFilterStartDate').val();
            const endDate = $('#expensesFilterEndDate').val();
            
            window.location.href = `{{ route("admin.expense.export.excel") }}?category_id=${category || ''}&start_date=${startDate || ''}&end_date=${endDate || ''}`;
            toastr.success('Export started. The file will download shortly.');
        }

        // Export filtered budgets to Excel
        function exportFilteredBudgetsToExcel() {
            const budgetType = $('#budgetFilterType').val();
            const startDate = $('#budgetFilterStartDate').val();
            const endDate = $('#budgetFilterEndDate').val();
            
            window.location.href = `{{ route("admin.expense.budget.export.excel") }}?budget_type_id=${budgetType || ''}&start_date=${startDate || ''}&end_date=${endDate || ''}`;
            toastr.success('Export started. The file will download shortly.');
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

        // Show success alert
        function showSuccessAlert(message) {
            $('#successAlertMessage').text(message);
            $('#successAlert').removeClass('d-none');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('#successAlert').addClass('d-none');
            }, 5000);
        }
    </script>
</body>
</html>
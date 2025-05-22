<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BudgetAllocation;
use App\Models\BudgetType;
use App\Services\LogService;
use App\Models\Notification;
use App\Enums\LogType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;
use App\Exports\BudgetsExport;

class ExpenseTrackerController extends Controller
{
    protected $logService;
    
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display the expense tracker dashboard
     */
    public function index(Request $request)
    {
        // Get categories for dropdowns
        $categories = ExpenseCategory::orderBy('name')->get();
        $budgetTypes = BudgetType::orderBy('name')->get();
        
        // Calculate date ranges for filtering
        $currentMonth = Carbon::now()->format('Y-m');
        $categoryId = $request->input('category_id', '');
        $month = $request->input('month', $currentMonth);
        
        // Create query for expenses
        $expensesQuery = Expense::with(['category', 'creator']);
        
        // Apply category filter if provided
        if ($categoryId) {
            $expensesQuery->where('category_id', $categoryId);
        }
        
        // Apply date filter only if month is provided, otherwise get all data
        if ($month) {
            $dateParts = explode('-', $month);
            if (count($dateParts) == 2) {
                $startDate = Carbon::createFromDate($dateParts[0], $dateParts[1], 1)->startOfMonth();
                $endDate = Carbon::createFromDate($dateParts[0], $dateParts[1], 1)->endOfMonth();
                $expensesQuery->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
            }
        }
        
        // Execute query and order by date
        $expenses = $expensesQuery->orderBy('date', 'desc')->get();
        
        // Get latest budget allocation
        $currentBudget = BudgetAllocation::with('budgetType')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();
            
        // Get recent budgets for the history
        $recentBudgets = BudgetAllocation::with(['budgetType', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate statistics
        $stats = $this->calculateStatistics($expenses, $currentBudget);
        
        // Get the 5 most recent expenses for quick view
        $recentExpenses = $expenses->take(5);
        
        // Prepare chart data
        $chartData = $this->prepareChartData($expenses);
        
        // Log the view
        $this->logService->createLog(
            'expense_tracker', 
            0, 
            LogType::VIEW, 
            Auth::user()->first_name . ' ' . Auth::user()->last_name . ' viewed expense tracker',
            Auth::id()
        );
        
        // Check if JSON format is requested (for AJAX calls)
        if ($request->input('format') === 'json') {
            return response()->json([
                'stats' => $stats,
                'recentExpenses' => $recentExpenses,
                'recentBudgets' => $recentBudgets,
                'currentBudget' => $currentBudget,
                'chartData' => $chartData
            ]);
        }
        
        // Return the view with data for normal page loads
        return view('admin.adminExpenseTracker', compact(
            'categories', 
            'budgetTypes',
            'expenses', 
            'recentExpenses',
            'recentBudgets',
            'currentBudget',
            'stats',
            'categoryId',
            'month',
            'chartData'
        ));
    }

    /**
     * Prepare chart data for the expense breakdown
     */
    private function prepareChartData($expenses)
    {
        // Group expenses by category
        $expensesByCategory = $expenses->groupBy('category_id');
        
        $labels = [];
        $data = [];
        $colors = [];
        
        foreach ($expensesByCategory as $categoryId => $categoryExpenses) {
            $category = $categoryExpenses->first()->category;
            $labels[] = $category->name;
            $data[] = $categoryExpenses->sum('amount');
            $colors[] = $category->color_code;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ];
    }
    
    /**
     * Store a new expense
     */
    public function storeExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'category_id' => 'required|exists:expense_categories,category_id',
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'payment_method' => 'required|in:cash,check,bank_transfer,gcash,paymaya,credit_card,debit_card,other',
            'date' => 'required|date|before_or_equal:today',
            'receipt_number' => 'required|string|max:50',
            'description' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'title.regex' => 'The title contains invalid characters.',
            'description.regex' => 'The description contains invalid characters.',
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $receiptPath = null;
            
            // Handle receipt file upload
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $receiptPath = $file->storeAs('receipts', $fileName, 'public');
            }
            
            // Create the expense record
            $expense = Expense::create([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'date' => $request->date,
                'receipt_number' => $request->receipt_number,
                'description' => $request->description,
                'receipt_path' => $receiptPath,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);
            
            // Create log entry
            $this->logService->createLog(
                'expense',
                $expense->expense_id,
                LogType::CREATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' created a new expense record',
                Auth::id()
            );
            
            // Create notification for admins
            $this->createNotificationForAdmins(
                'New Expense Added',
                'A new expense of ₱' . number_format($expense->amount, 2) . ' for ' . $expense->title . ' has been recorded.'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Expense added successfully',
                'expense' => $expense->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add expense: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get expense details
     */
    public function getExpense($id)
    {
        try {
            $expense = Expense::with(['category', 'creator', 'updater'])->findOrFail($id);
            
            // Add receipt URL if exists
            if ($expense->receipt_path) {
                $expense->receipt_url = asset('storage/' . $expense->receipt_path);
            }
            
            return response()->json([
                'success' => true,
                'expense' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expense details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update an expense
     */
    public function updateExpense(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'category_id' => 'required|exists:expense_categories,category_id',
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'payment_method' => 'required|in:cash,check,bank_transfer,gcash,paymaya,credit_card,debit_card,other',
            'date' => 'required|date|before_or_equal:today',
            'receipt_number' => 'required|string|max:50',
            'description' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'title.regex' => 'The title contains invalid characters.',
            'description.regex' => 'The description contains invalid characters.',
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $expense = Expense::findOrFail($id);
            $oldAmount = $expense->amount;
            
            // Handle receipt file upload
            if ($request->hasFile('receipt')) {
                // Delete old receipt if exists
                if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
                    Storage::disk('public')->delete($expense->receipt_path);
                }
                
                $file = $request->file('receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $receiptPath = $file->storeAs('receipts', $fileName, 'public');
                $expense->receipt_path = $receiptPath;
            }
            
            // Update expense fields
            $expense->title = $request->title;
            $expense->category_id = $request->category_id;
            $expense->amount = $request->amount;
            $expense->payment_method = $request->payment_method;
            $expense->date = $request->date;
            $expense->receipt_number = $request->receipt_number;
            $expense->description = $request->description;
            $expense->updated_by = Auth::id();
            $expense->save();
            
            // Create log entry
            $this->logService->createLog(
                'expense',
                $expense->expense_id,
                LogType::UPDATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' updated expense record',
                Auth::id()
            );
            
            // Create notification if amount changed significantly
            if (abs($expense->amount - $oldAmount) > 100) {
                $this->createNotificationForAdmins(
                    'Expense Amount Modified',
                    'The expense for "' . $expense->title . '" has been updated from ₱' . 
                    number_format($oldAmount, 2) . ' to ₱' . number_format($expense->amount, 2) . '.'
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully',
                'expense' => $expense->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update expense: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete an expense
     */
    public function deleteExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:expenses,expense_id',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect'
            ], 403);
        }

        try {
            $expense = Expense::findOrFail($request->expense_id);
            $expenseTitle = $expense->title;
            $expenseAmount = $expense->amount;
            
            // Delete receipt file if exists
            if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            
            // Delete the expense record
            $expense->delete();
            
            // Create log entry
            $this->logService->createLog(
                'expense',
                $request->expense_id,
                LogType::DELETE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' deleted expense record',
                Auth::id()
            );
            
            // Create notification
            $this->createNotificationForAdmins(
                'Expense Record Deleted',
                'An expense record "' . $expenseTitle . '" (₱' . number_format($expenseAmount, 2) . ') has been deleted.'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete expense: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a new budget allocation
     */
    public function storeBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget_type_id' => 'required|exists:budget_types,budget_type_id',
            'description' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
        ], [
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
            'description.regex' => 'The description contains invalid characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the budget allocation
            $budget = BudgetAllocation::create([
                'amount' => $request->amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'budget_type_id' => $request->budget_type_id,
                'description' => $request->description,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);
            
            // Get budget type name
            $budgetType = BudgetType::find($request->budget_type_id);
            
            // Create log entry
            $this->logService->createLog(
                'budget_allocation',
                $budget->budget_allocation_id,
                LogType::CREATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' created a new budget allocation',
                Auth::id()
            );
            
            // Create notification for admins
            $this->createNotificationForAdmins(
                'New Budget Allocation Added',
                'A new ' . $budgetType->name . ' budget of ₱' . number_format($budget->amount, 2) . 
                ' has been allocated for the period ' . Carbon::parse($budget->start_date)->format('M d, Y') . 
                ' to ' . Carbon::parse($budget->end_date)->format('M d, Y') . '.'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Budget allocation added successfully',
                'budget' => $budget->load('budgetType')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add budget allocation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get budget allocation details
     */
    public function getBudgetAllocation($id)
    {
        try {
            $budget = BudgetAllocation::with(['budgetType', 'creator', 'updater'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'budget' => $budget
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch budget details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a budget allocation
     */
    public function updateBudget(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget_type_id' => 'required|exists:budget_types,budget_type_id',
            'description' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
        ], [
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
            'description.regex' => 'The description contains invalid characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $budget = BudgetAllocation::findOrFail($id);
            $oldAmount = $budget->amount;
            
            // Update budget fields
            $budget->amount = $request->amount;
            $budget->start_date = $request->start_date;
            $budget->end_date = $request->end_date;
            $budget->budget_type_id = $request->budget_type_id;
            $budget->description = $request->description;
            $budget->updated_by = Auth::id();
            $budget->save();
            
            // Create log entry
            $this->logService->createLog(
                'budget_allocation',
                $budget->budget_allocation_id,
                LogType::UPDATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' updated budget allocation',
                Auth::id()
            );
            
            // Create notification if amount changed significantly
            if (abs($budget->amount - $oldAmount) > 1000) {
                $this->createNotificationForAdmins(
                    'Budget Amount Modified',
                    'A budget allocation has been updated from ₱' . 
                    number_format($oldAmount, 2) . ' to ₱' . number_format($budget->amount, 2) . '.'
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Budget allocation updated successfully',
                'budget' => $budget->load('budgetType')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update budget allocation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a budget allocation
     */
    public function deleteBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_id' => 'required|exists:budget_allocations,budget_allocation_id',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect'
            ], 403);
        }

        try {
            $budget = BudgetAllocation::with('budgetType')->findOrFail($request->budget_id);
            $budgetType = $budget->budgetType->name;
            $budgetAmount = $budget->amount;
            $budgetPeriod = Carbon::parse($budget->start_date)->format('M d, Y') . ' to ' . 
                            Carbon::parse($budget->end_date)->format('M d, Y');
            
            // Delete the budget allocation
            $budget->delete();
            
            // Create log entry
            $this->logService->createLog(
                'budget_allocation',
                $request->budget_id,
                LogType::DELETE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' deleted budget allocation',
                Auth::id()
            );
            
            // Create notification
            $this->createNotificationForAdmins(
                'Budget Allocation Deleted',
                'A ' . $budgetType . ' budget of ₱' . number_format($budgetAmount, 2) . 
                ' for the period ' . $budgetPeriod . ' has been deleted.'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Budget allocation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete budget allocation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a filtered list of expenses with pagination
     */
    public function getFilteredExpenses(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        
        try {
            $query = Expense::with(['category', 'creator'])
                ->when($startDate, function($query) use ($startDate) {
                    return $query->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function($query) use ($endDate) {
                    return $query->whereDate('date', '<=', $endDate);
                })
                ->when($categoryId, function($query) use ($categoryId) {
                    return $query->where('category_id', $categoryId);
                })
                ->orderBy('date', 'desc');
            
            // Paginate the results
            $expenses = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Transform expenses to include receipt_path
            $transformedExpenses = $expenses->map(function ($expense) {
                $expense->receipt_path = $expense->receipt ? asset('storage/' . $expense->receipt) : null;
                return $expense;
            });
            
            return response()->json([
                'expenses' => $transformedExpenses,
                'pagination' => [
                    'total' => $expenses->total(),
                    'per_page' => $expenses->perPage(),
                    'current_page' => $expenses->currentPage(),
                    'last_page' => $expenses->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to filter expenses: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get filtered budget history with pagination
     */
    public function getFilteredBudgets(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $budgetTypeId = $request->input('budget_type_id');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        
        try {
            $query = BudgetAllocation::with(['budgetType', 'creator'])
                ->when($startDate, function($query) use ($startDate) {
                    return $query->whereDate('start_date', '>=', $startDate);
                })
                ->when($endDate, function($query) use ($endDate) {
                    return $query->whereDate('end_date', '<=', $endDate);
                })
                ->when($budgetTypeId, function($query) use ($budgetTypeId) {
                    return $query->where('budget_type_id', $budgetTypeId);
                })
                ->orderBy('created_at', 'desc');
            
            // Paginate the results
            $budgets = $query->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json([
                'budgets' => $budgets->items(),
                'pagination' => [
                    'total' => $budgets->total(),
                    'per_page' => $budgets->perPage(),
                    'current_page' => $budgets->currentPage(),
                    'last_page' => $budgets->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to filter budget history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export expenses to Excel
     */
    public function exportExpensesToExcel(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $categoryId = $request->input('category_id');
            
            return Excel::download(
                new ExpensesExport($startDate, $endDate, $categoryId), 
                'expenses_' . Carbon::now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export expenses: ' . $e->getMessage());
        }
    }
    
    /**
     * Export budgets to Excel
     */
    public function exportBudgetsToExcel(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $budgetTypeId = $request->input('budget_type_id');
            
            return Excel::download(
                new BudgetsExport($startDate, $endDate, $budgetTypeId), 
                'budget_history_' . Carbon::now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export budgets: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate statistics for dashboard
     */
    private function calculateStatistics($expenses, $currentBudget)
    {
        // Initialize stats array
        $stats = [
            'totalExpenses' => 0,
            'previousPeriodTotal' => null,
            'percentChange' => null,
            'topCategory' => null,
            'grandTotalExpenses' => 0,  // All-time total expenses
            'grandTotalBudget' => 0     // All-time total budget
        ];
        
        // Calculate total expenses for current period
        $totalExpenses = $expenses->sum('amount');
        $stats['totalExpenses'] = $totalExpenses;
        
        // Calculate expenses from previous period for comparison
        if ($expenses->count() > 0) {
            $currentPeriodStart = $expenses->min('date');
            $currentPeriodEnd = $expenses->max('date');
            
            if ($currentPeriodStart && $currentPeriodEnd) {
                $periodLength = $currentPeriodStart->diffInDays($currentPeriodEnd) + 1;
                $previousPeriodEnd = $currentPeriodStart->copy()->subDay();
                $previousPeriodStart = $previousPeriodEnd->copy()->subDays($periodLength - 1);
                
                $previousExpenses = Expense::whereBetween('date', [
                    $previousPeriodStart->toDateString(), 
                    $previousPeriodEnd->toDateString()
                ])->sum('amount');
                
                $stats['previousPeriodTotal'] = $previousExpenses;
                
                // Calculate percent change if previous period had expenses
                if ($previousExpenses > 0) {
                    $percentChange = (($totalExpenses - $previousExpenses) / $previousExpenses) * 100;
                    $stats['percentChange'] = round($percentChange, 1);
                }
            }
        }
        
        // Find top expense category
        if ($expenses->count() > 0) {
            // Group expenses by category and sum amounts
            $expensesByCategory = $expenses->groupBy('category_id')
                ->map(function ($items) {
                    return [
                        'amount' => $items->sum('amount'),
                        'category' => $items->first()->category
                    ];
                })
                ->sortByDesc('amount')
                ->values();
            
            if ($expensesByCategory->count() > 0) {
                $topCategory = $expensesByCategory->first();
                $stats['topCategory'] = [
                    'name' => $topCategory['category']->name,
                    'amount' => $topCategory['amount'],
                    'percentage' => $totalExpenses > 0 ? round(($topCategory['amount'] / $totalExpenses) * 100) : 0,
                    'color' => $topCategory['category']->color_code
                ];
            }
        }
        
        // Calculate grand total for all expenses (all time)
        $stats['grandTotalExpenses'] = Expense::sum('amount');
        
        // Calculate grand total for all budgets (all time)
        $stats['grandTotalBudget'] = BudgetAllocation::sum('amount');
        
        return $stats;
    }
    
    /**
     * Create notifications for all admin users
     */
    private function createNotificationForAdmins($title, $message)
    {
        try {
            // Get all admin users
            $adminUsers = \App\Models\User::where('role_id', 1)->get();
            
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create admin notifications: ' . $e->getMessage());
        }
    }
}
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
use App\Services\UploadService;

class ExpenseTrackerController extends Controller
{
    protected $logService;
    protected $uploadService;

    public function __construct(LogService $logService, UploadService $uploadService)
    {
        $this->logService = $logService;
        $this->uploadService = $uploadService;
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
            ->orderBy('updated_at', 'desc')  // Primary sort by last updated
            ->orderBy('created_at', 'desc')  // Secondary sort by creation date
            ->take(10)  // Or whatever limit you currently use
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
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
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
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max for receipt
        ], [
            'title.regex' => 'The title must contain at least one letter and only common characters.',
            'description.regex' => 'The description must contain at least one letter and only common characters.',
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

            // Handle receipt file upload using UploadService
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
                $receiptPath = $this->uploadService->upload(
                    $file,
                    'spaces-private',
                    'uploads/expense_receipts',
                    [
                        'filename' => 'expense_' . Auth::id() . '_' . $uniqueIdentifier . '.' . $file->getClientOriginalExtension()
                    ]
                );
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
                'A new expense of ₱' . number_format($expense->amount, 2) . ' for ' . $expense->title . ' has been recorded by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '.'
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
            $expense = Expense::with('category')->findOrFail($id);
            
            // FIX: Get the raw database date value without any timezone conversion
            $rawDate = $expense->getRawOriginal('date'); // Get the raw date from database
            if ($rawDate) {
                $expense->date = substr($rawDate, 0, 10); // Extract YYYY-MM-DD portion
            } else {
                $expense->date = $expense->date->format('Y-m-d');
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
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
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
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max for receipt
        ], [
            'title.regex' => 'The title must contain at least one letter and only common characters.',
            'description.regex' => 'The description must contain at least one letter and only common characters.',
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $expense = Expense::findOrFail($id);
        
        // Convert request values to appropriate types
        $requestAmount = (float)$request->amount;
        $requestCategoryId = (int)$request->category_id;
        $requestDate = date('Y-m-d', strtotime($request->date));
        $requestTitle = trim($request->title ?? '');
        $requestPaymentMethod = trim($request->payment_method ?? '');
        $requestReceiptNumber = trim($request->receipt_number ?? '');
        $requestDescription = trim($request->description ?? '');
        
        // Convert database values to appropriate types
        $dbAmount = (float)$expense->amount;
        $dbCategoryId = (int)$expense->category_id;
        $dbDate = $expense->date->format('Y-m-d');
        $dbTitle = trim($expense->title ?? '');
        $dbPaymentMethod = trim($expense->payment_method ?? '');
        $dbReceiptNumber = trim($expense->receipt_number ?? '');
        $dbDescription = trim($expense->description ?? '');
        
        // Initialize hasChanges to false
        $hasChanges = false;
        $changeDetails = [];
        
        // Log comparison details for debugging
        \Log::debug('Expense update comparison', [
            'title' => ['db' => $dbTitle, 'request' => $requestTitle, 'equal' => $dbTitle === $requestTitle],
            'amount' => ['db' => $dbAmount, 'request' => $requestAmount, 'equal' => $dbAmount === $requestAmount],
            'category_id' => ['db' => $dbCategoryId, 'request' => $requestCategoryId, 'equal' => $dbCategoryId === $requestCategoryId],
            'payment_method' => ['db' => $dbPaymentMethod, 'request' => $requestPaymentMethod, 'equal' => $dbPaymentMethod === $requestPaymentMethod],
            'date' => ['db' => $dbDate, 'request' => $requestDate, 'equal' => $dbDate === $requestDate],
            'receipt_number' => ['db' => $dbReceiptNumber, 'request' => $requestReceiptNumber, 'equal' => $dbReceiptNumber === $requestReceiptNumber],
            'description' => ['db' => $dbDescription, 'request' => $requestDescription, 'equal' => $dbDescription === $requestDescription],
            'hasFile' => $request->hasFile('receipt')
        ]);
        
        // Compare title with case-insensitive comparison
        if (strcasecmp($dbTitle, $requestTitle) !== 0) {
            $hasChanges = true;
            $changeDetails[] = "Title changed from '{$dbTitle}' to '{$requestTitle}'";
        }
        
        // Compare category ID
        if ($dbCategoryId !== $requestCategoryId) {
            $hasChanges = true;
            $newCategory = ExpenseCategory::find($requestCategoryId)->name;
            $oldCategory = ExpenseCategory::find($dbCategoryId)->name;
            $changeDetails[] = "Category changed from '{$oldCategory}' to '{$newCategory}'";
        }
        
        // Compare amount with fixed precision
        if (number_format($dbAmount, 2) !== number_format($requestAmount, 2)) {
            $hasChanges = true;
            $changeDetails[] = "Amount changed from '₱" . number_format($dbAmount, 2) . "' to '₱" . number_format($requestAmount, 2) . "'";
        }
        
        // Compare payment method
        if ($dbPaymentMethod !== $requestPaymentMethod) {
            $hasChanges = true;
            $oldMethod = ucfirst(str_replace('_', ' ', $dbPaymentMethod));
            $newMethod = ucfirst(str_replace('_', ' ', $requestPaymentMethod));
            $changeDetails[] = "Payment method changed from '{$oldMethod}' to '{$newMethod}'";
        }
        
        // Compare date
        if ($dbDate !== $requestDate) {
            $hasChanges = true;
            $oldDate = Carbon::parse($dbDate)->format('M d, Y');
            $newDate = Carbon::parse($requestDate)->format('M d, Y');
            $changeDetails[] = "Date changed from '{$oldDate}' to '{$newDate}'";
        }
        
        // Compare receipt number
        if ($dbReceiptNumber !== $requestReceiptNumber) {
            $hasChanges = true;
            $changeDetails[] = "Receipt number changed from '{$dbReceiptNumber}' to '{$requestReceiptNumber}'";
        }
        
        // Compare description
        if ($dbDescription !== $requestDescription) {
            $hasChanges = true;
            $changeDetails[] = "Description was updated";
        }
        
        // Check for new receipt file
        if ($request->hasFile('receipt')) {
            $hasChanges = true;
            $changeDetails[] = "Receipt file was updated";
        }
        
        // If nothing changed, return an error
        if (!$hasChanges) {
            \Log::info('No changes detected in expense update');
            return response()->json([
                'success' => false,
                'message' => 'No changes were made to the expense.'
            ], 422);
        }
            
            // Handle receipt file upload if provided using UploadService
            if ($request->hasFile('receipt')) {
                // Delete old file if it exists
                if ($expense->receipt_path) {
                    $this->uploadService->delete($expense->receipt_path, 'spaces-private');
                }

                // Store new file
                $file = $request->file('receipt');
                $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
                $receiptPath = $this->uploadService->upload(
                    $file,
                    'spaces-private',
                    'uploads/expense_receipts',
                    [
                        'filename' => 'expense_' . Auth::id() . '_' . $uniqueIdentifier . '.' . $file->getClientOriginalExtension()
                    ]
                );
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
            
            // Create log entry with detailed changes
            $this->logService->createLog(
                'expense',
                $expense->expense_id,
                LogType::UPDATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' updated expense record. ' . implode('. ', $changeDetails),
                Auth::id()
            );
            
            // Create notification for all admins with change details
            $this->createNotificationForAdmins(
                'Expense Updated',
                'An expense record for ₱' . number_format($expense->amount, 2) . ' (' . $expense->title . ') has been updated by ' . 
                Auth::user()->first_name . ' ' . Auth::user()->last_name . '. ' . implode('. ', $changeDetails)
            );
            
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
            
            // Delete receipt file if exists using UploadService
            if ($expense->receipt_path) {
                $this->uploadService->delete($expense->receipt_path, 'spaces-private');
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
            
            // Create notification for all admins
            $this->createNotificationForAdmins(
                'Expense Deleted',
                'An expense record for ₱' . number_format($expenseAmount, 2) . ' (' . $expenseTitle . ') has been deleted by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '.'
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
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/'
            ],
        ], [
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
            'description.regex' => 'The description must contain at least one letter and only common characters.',
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
            
            // Create notification for all admins
            $this->createNotificationForAdmins(
                'New Budget Allocation Added',
                'A new budget allocation of ₱' . number_format($budget->amount, 2) . ' (' . $budget->budgetType->name . ') has been added by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '.'
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
            $budget = BudgetAllocation::with('budgetType')->findOrFail($id);
            
            // FIX: Get the raw database date values without any timezone conversion
            $rawStartDate = $budget->getRawOriginal('start_date');
            $rawEndDate = $budget->getRawOriginal('end_date');
            
            if ($rawStartDate) {
                $budget->start_date = substr($rawStartDate, 0, 10); // Extract YYYY-MM-DD portion
            } else {
                $budget->start_date = $budget->start_date->format('Y-m-d');
            }
            
            if ($rawEndDate) {
                $budget->end_date = substr($rawEndDate, 0, 10); // Extract YYYY-MM-DD portion
            } else {
                $budget->end_date = $budget->end_date->format('Y-m-d');
            }
            
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
            'budget_type_id' => 'required|exists:budget_types,budget_type_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\-_.,;:()\'\"!?&]+$/',
        ], [
            'description.regex' => 'The description must contain at least one letter and only common characters.',
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₱1,000,000.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $budget = BudgetAllocation::with('budgetType')->findOrFail($id);
            
            // CRITICAL FIX: Use raw date values for reliable comparison
            $rawStartDate = substr($budget->getRawOriginal('start_date'), 0, 10);
            $rawEndDate = substr($budget->getRawOriginal('end_date'), 0, 10);
            
            // Format original values for precise comparison
            $originalBudget = [
                'amount' => (float)$budget->amount,
                'budget_type_id' => (int)$budget->budget_type_id,
                'budget_type_name' => $budget->budgetType->name,
                'start_date' => $rawStartDate,
                'end_date' => $rawEndDate,
                'description' => trim($budget->description ?? '')
            ];

            // Format new values for precise comparison
            $newBudget = [
                'amount' => (float)$request->amount,
                'budget_type_id' => (int)$request->budget_type_id,
                'start_date' => date('Y-m-d', strtotime($request->start_date)),
                'end_date' => date('Y-m-d', strtotime($request->end_date)),
                // CRITICAL FIX: Use null coalescing to prevent empty description from becoming null
                'description' => trim($request->description ?? '')
            ];
            
            // Debug log to check values
            \Log::debug('Budget update comparison', [
                'original' => $originalBudget,
                'new' => $newBudget,
                'description_equal' => $originalBudget['description'] === $newBudget['description'],
                'amount_equal' => $originalBudget['amount'] === $newBudget['amount'],
                'original_amount_type' => gettype($originalBudget['amount']),
                'new_amount_type' => gettype($newBudget['amount']),
            ]);
            
            // Initialize tracking variables
            $hasChanges = false;
            $changeDetails = [];
            
            // Check for changes in amount with proper formatting
            if (number_format($originalBudget['amount'], 2) !== number_format($newBudget['amount'], 2)) {
                $hasChanges = true;
                $changeDetails[] = "Amount changed from '₱" . number_format($originalBudget['amount'], 2) . 
                                "' to '₱" . number_format($newBudget['amount'], 2) . "'";
                $budget->amount = $newBudget['amount'];
            }
            
            // Check for changes in budget type
            if ($originalBudget['budget_type_id'] !== $newBudget['budget_type_id']) {
                $hasChanges = true;
                $newBudgetType = BudgetType::find($newBudget['budget_type_id']);
                $changeDetails[] = "Budget type changed from '{$originalBudget['budget_type_name']}' to '{$newBudgetType->name}'";
                $budget->budget_type_id = $newBudget['budget_type_id'];
            }
            
            // Check for changes in start date
            if ($originalBudget['start_date'] !== $newBudget['start_date']) {
                $hasChanges = true;
                $changeDetails[] = "Start date changed from '" . 
                                Carbon::parse($originalBudget['start_date'])->format('M d, Y') . 
                                "' to '" . 
                                Carbon::parse($newBudget['start_date'])->format('M d, Y') . "'";
                $budget->start_date = $newBudget['start_date'];
            }
            
            // Check for changes in end date
            if ($originalBudget['end_date'] !== $newBudget['end_date']) {
                $hasChanges = true;
                $changeDetails[] = "End date changed from '" . 
                                Carbon::parse($originalBudget['end_date'])->format('M d, Y') . 
                                "' to '" . 
                                Carbon::parse($newBudget['end_date'])->format('M d, Y') . "'";
                $budget->end_date = $newBudget['end_date'];
            }
            
            // CRITICAL FIX: Check for changes in description with proper string comparison
            // Use === to ensure exact comparison including empty strings vs null
            if ($originalBudget['description'] !== $newBudget['description']) {
                $hasChanges = true;
                
                $originalDesc = $originalBudget['description'] ?: 'No description';
                $newDesc = $newBudget['description'] ?: 'No description';
                
                $changeDetails[] = "Description changed from '{$originalDesc}' to '{$newDesc}'";
                $budget->description = $newBudget['description'];
            }
            
            // If nothing changed, return an error
            if (!$hasChanges) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes were made to the budget allocation.'
                ], 422);
            }
            
            // Update the modifier and timestamp
            $budget->updated_by = Auth::id();
            $budget->save();
            
            // Log the update
            $this->logService->createLog(
                'budget_allocation',
                $budget->budget_allocation_id,
                LogType::UPDATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' updated budget allocation: ' . implode(', ', $changeDetails),
                Auth::id()
            );
            
            // Create notification for admins
            $this->createNotificationForAdmins(
                'Budget Allocation Updated',
                Auth::user()->first_name . ' updated a budget allocation. Changes: ' . implode(', ', $changeDetails)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Budget allocation updated successfully',
                'changes' => $changeDetails
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
            'budget_id' => 'required|integer|exists:budget_allocations,budget_allocation_id',
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
                'message' => 'Incorrect password'
            ], 403);
        }

        try {
            // Find the budget allocation with its budget type
            $budget = BudgetAllocation::with('budgetType')->findOrFail($request->budget_id);
            
            // Get the budget type name safely (with fallback)
            $budgetTypeName = $budget->budgetType ? $budget->budgetType->name : 'Unknown';
            $amount = $budget->amount;
            
            // Delete the budget allocation
            $budget->delete();
            
            // Log the deletion
            $this->logService->createLog(
                'budget_allocation',
                $request->budget_id,
                LogType::DELETE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' deleted budget allocation of type ' . $budgetTypeName . ' with amount ' . $amount,
                Auth::id()
            );
            
            // Create notification for admins
            $this->createNotificationForAdmins(
                'Budget Allocation Deleted',
                Auth::user()->first_name . ' deleted a ' . $budgetTypeName . ' budget allocation of ₱' . number_format($amount, 2)
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
                if ($expense->receipt_path) {
                    $expense->receipt_url = app(\App\Services\UploadService::class)->getTemporaryPrivateUrl(
                        $expense->receipt_path,
                        30,
                        'spaces-private',
                        ['ResponseContentDisposition' => 'inline']
                    );
                } else {
                    $expense->receipt_url = null;
                }
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
            // Get all admin users (role_id = 1)
            $adminUsers = \App\Models\User::where('role_id', 1)->get();
            
            // Create notification for each admin
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'user_type' => 'cose_staff', // Admin users are staff type
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => Carbon::now(),
                    'is_read' => false
                ]);
            }
            
            \Log::info('Notifications created for all admins: ' . $title);
        } catch (\Exception $e) {
            \Log::error('Failed to create admin notifications: ' . $e->getMessage());
        }
    }
   
}
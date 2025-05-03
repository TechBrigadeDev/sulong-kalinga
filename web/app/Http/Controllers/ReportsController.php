<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\WeeklyCarePlan;
use App\Models\GeneralCarePlan;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get request parameters
        $search = $request->input('search', '');
        $filterType = $request->input('filter', '');
        $sortOrder = $request->input('sort', 'asc'); 

        try {
            // Get current user info
            $user = Auth::user();
            $userRole = $user->role_id ?? 0;
            $userId = $user->id ?? 0;

            // First collect diagnostic data
            $counts = [
                'weekly_plans' => WeeklyCarePlan::count(),
                'general_plans' => GeneralCarePlan::count(),
                'beneficiaries' => Beneficiary::count(),
                'users' => User::count()
            ];
            
            Log::info('Table counts', $counts);
            
            // Collection that will hold all our reports
            $combinedReports = new Collection();
            
            // STEP 1: Get Weekly Care Plans with eager loading
            $weeklyPlans = WeeklyCarePlan::with(['author', 'beneficiary'])
                ->when($userRole == 3, function($query) use ($userId) {
                    // Care worker only sees their authored reports
                    return $query->where('created_by', $userId);
                })
                ->get();
                
            foreach ($weeklyPlans as $plan) {
                // Create a standardized report object
                $reportObj = (object)[
                    'id' => $plan->weekly_care_plan_id,
                    'report_id' => $plan->weekly_care_plan_id,
                    'created_at' => $plan->created_at,
                    'author_id' => $plan->created_by,
                    'author_first_name' => optional($plan->author)->first_name ?? 'Unknown',
                    'author_last_name' => optional($plan->author)->last_name ?? '',
                    'beneficiary_id' => $plan->beneficiary_id,
                    'beneficiary_first_name' => optional($plan->beneficiary)->first_name ?? 'Unknown',
                    'beneficiary_last_name' => optional($plan->beneficiary)->last_name ?? '',
                    'report_type' => 'Weekly Care Plan'
                ];
                
                $combinedReports->push($reportObj);
            }
            
            // STEP 2: Get General Care Plans
            $generalPlans = GeneralCarePlan::all();
            foreach ($generalPlans as $plan) {
                // Find the beneficiary with this general care plan ID
                $beneficiary = Beneficiary::where('general_care_plan_id', $plan->general_care_plan_id)->first();
                
                if ($beneficiary) {
                    // Find the author of the beneficiary record
                    $author = User::find($beneficiary->created_by);
                    
                    // Skip if care worker restriction applies
                    if ($userRole == 3 && $beneficiary->created_by != $userId && $plan->care_worker_id != $userId) {
                        continue;
                    }
                    
                    // Create a standardized report object
                    $reportObj = (object)[
                        'id' => $plan->general_care_plan_id,
                        'report_id' => $plan->general_care_plan_id, 
                        'created_at' => $plan->created_at,
                        'author_id' => $beneficiary->created_by,
                        'author_first_name' => optional($author)->first_name ?? 'Unknown',
                        'author_last_name' => optional($author)->last_name ?? '',
                        'beneficiary_id' => $beneficiary->beneficiary_id,
                        'beneficiary_first_name' => $beneficiary->first_name,
                        'beneficiary_last_name' => $beneficiary->last_name,
                        'report_type' => 'General Care Plan'
                    ];
                    
                    $combinedReports->push($reportObj);
                }
            }
            
            // STEP 3: Apply search and filtering together in PHP
            // Filter by search term if provided
            if (!empty($search)) {
                $combinedReports = $combinedReports->filter(function($report) use ($search) {
                    $authorName = ($report->author_first_name . ' ' . $report->author_last_name);
                    $beneficiaryName = ($report->beneficiary_first_name . ' ' . $report->beneficiary_last_name);
                    
                    return (stripos($authorName, $search) !== false || 
                            stripos($beneficiaryName, $search) !== false);
                });
            }
            
            // Modified STEP 4: Apply sorting logic
            // First apply date sorting if sortOrder is set (meaning the toggle was used)
            if ($request->has('sort')) {
                // Always apply date-based sorting first when toggle is used
                $combinedReports = $combinedReports->sortBy('created_at', SORT_REGULAR, $sortOrder === 'desc');
                
                // Then apply any additional filtering criteria
                if ($filterType) {
                    switch ($filterType) {
                        case 'type':
                            // Secondary sort by report type
                            $combinedReports = $combinedReports->sortBy('report_type', SORT_REGULAR, $sortOrder === 'desc');
                            break;
                            
                        case 'author':
                            // Secondary sort by author name
                            $combinedReports = $combinedReports->sortBy(function($report) {
                                return $report->author_first_name . ' ' . $report->author_last_name;
                            }, SORT_REGULAR, $sortOrder === 'desc');
                            break;
                    }
                }
            } else {
                // If toggle wasn't used, use the filter-based sorting
                switch ($filterType) {
                    case 'type':
                        $combinedReports = $combinedReports->sortBy('report_type', SORT_REGULAR, $sortOrder === 'desc');
                        break;
                        
                    case 'author':
                        $combinedReports = $combinedReports->sortBy(function($report) {
                            return $report->author_first_name . ' ' . $report->author_last_name;
                        }, SORT_REGULAR, $sortOrder === 'desc');
                        break;
                        
                    case 'date':
                        $combinedReports = $combinedReports->sortBy('created_at', SORT_REGULAR, $sortOrder === 'desc');
                        break;
                        
                    default:
                        // Default: Sort alphabetically by author's first name
                        $combinedReports = $combinedReports->sortBy('author_first_name', SORT_REGULAR, $sortOrder === 'desc');
                        break;
                }
            }
            
            // STEP 5: Reset array keys (important for proper display)
            $reports = $combinedReports->values();
            
            // Add complete diagnostic info
            $diagnostic = [
                'raw_counts' => $counts,
                'weekly_plans_raw' => $weeklyPlans->count(), 
                'general_plans_raw' => $generalPlans->count(),
                'final_count' => $reports->count(),
                'sort_method' => $filterType ?: 'Default (Alphabetical by Author\'s First Name)'
            ];
            
            Log::info('Reports generated', [
                'count' => $reports->count(),
                'first_type' => $reports->isEmpty() ? 'none' : $reports->first()->report_type,
                'sort_method' => $filterType ?: 'author_first_name'
            ]);

            // Determine which view to return based on the user's role
            $viewName = match ($userRole) {
                1 => 'admin.reportsManagement',        // Administrator
                2 => 'careManager.reportsManagement',  // Care Manager
                3 => 'careWorker.reportsManagement',   // Care Worker
                default => 'admin.reportsManagement',  // Default fallback
            };

            return view($viewName, [
                'reports' => $reports,
                'search' => $search,
                'filterType' => $filterType,
                'sortOrder' => $sortOrder,
                'userRole' => $userRole,
                'diagnostic' => $diagnostic
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in reports generation: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Determine which view to return based on the user's role
            $viewName = match ($userRole) {
                1 => 'admin.reportsManagement',        // Administrator
                2 => 'careManager.reportsManagement',  // Care Manager
                3 => 'careWorker.reportsManagement',   // Care Worker
                default => 'admin.reportsManagement',  // Default fallback
            };
            
            return view($viewName, [
                'reports' => collect([]),
                'search' => $search,
                'filterType' => $filterType,
                'sortOrder' => $sortOrder,
                'userRole' => $userRole,
                'noRecordsMessage' => 'An error occurred while generating reports: ' . $e->getMessage()
            ]);
        }
    }
}
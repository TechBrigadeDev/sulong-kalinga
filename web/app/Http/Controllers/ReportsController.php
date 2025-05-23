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
    /**
     * Display a listing of the reports with pagination
     */
    public function index(Request $request)
    {
        // Get request parameters
        $search = $request->input('search', '');
        $filterType = $request->input('filter', '');
        $sortOrder = $request->input('sort', 'asc');
        $perPage = $request->input('per_page', 15); // Number of items per page

        try {
            // Get current user info
            $user = Auth::user();
            $userRole = $user->role_id ?? 0;
            $userId = $user->id ?? 0;
            
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
                    'created_at' => $plan->date,
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
            
            // STEP 3: Apply search and filtering together
            if (!empty($search)) {
                $combinedReports = $combinedReports->filter(function($report) use ($search) {
                    $authorName = ($report->author_first_name . ' ' . $report->author_last_name);
                    $beneficiaryName = ($report->beneficiary_first_name . ' ' . $report->beneficiary_last_name);
                    
                    return (stripos($authorName, $search) !== false || 
                            stripos($beneficiaryName, $search) !== false);
                });
            }
            
            // STEP 4: Apply sorting
            if ($request->has('sort')) {
                $combinedReports = $combinedReports->sortBy('created_at', SORT_REGULAR, $sortOrder === 'desc');
                
                if ($filterType) {
                    switch ($filterType) {
                        case 'type':
                            $combinedReports = $combinedReports->sortBy('report_type', SORT_REGULAR, $sortOrder === 'desc');
                            break;
                            
                        case 'author':
                            $combinedReports = $combinedReports->sortBy(function($report) {
                                return $report->author_first_name . ' ' . $report->author_last_name;
                            }, SORT_REGULAR, $sortOrder === 'desc');
                            break;
                    }
                }
            } else {
                // Default sorting
                $combinedReports = $combinedReports->sortBy('created_at', SORT_REGULAR, $sortOrder === 'desc');
            }
            
            // STEP 5: Paginate the results
            $currentPage = $request->input('page', 1);
            $total = $combinedReports->count();
            
            // Slice the collection to get just the items for the current page
            $reports = $combinedReports->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            // Create a paginator manually
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $reports, 
                $total, 
                $perPage, 
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            // Determine which view to return based on the user's role
            $viewName = match ($userRole) {
                1 => 'admin.reportsManagement',
                2 => 'careManager.reportsManagement',
                3 => 'careWorker.reportsManagement',
                default => 'admin.reportsManagement',
            };

            return view($viewName, [
                'reports' => $paginator,
                'search' => $search,
                'filterType' => $filterType,
                'sortOrder' => $sortOrder,
                'userRole' => $userRole
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
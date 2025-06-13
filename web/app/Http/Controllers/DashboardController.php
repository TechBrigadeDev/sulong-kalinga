<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\EmergencyNotice;
use App\Models\ServiceRequest;
use App\Models\GeneralCarePlan;
use App\Models\WeeklyCarePlan;
use App\Models\CareWorkerIntervention;
use App\Models\WeeklyCarePlanInterventions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for admin
     *
     * @param bool $showWelcome Whether to show welcome message
     * @return \Illuminate\View\View
     */
    public function adminDashboard($showWelcome = false)
    {
        // Get beneficiary stats
        $beneficiaryStats = $this->getBeneficiaryStats();
        
        // Get care worker stats
        $careWorkerStats = $this->getCareWorkerStats();
        
        // Get location stats
        $locationStats = $this->getLocationStats();
        
        // Get today's requests
        $requestStats = $this->getTodayRequestStats();
        
        // Get upcoming visitations for all care workers
        $upcomingVisitations = $this->getUpcomingVisitationsForAllCareWorkers();
        
        // Get top care workers performance
        $careWorkerPerformance = $this->getTopCareWorkersPerformance();
        
        // Get recent care plans for all care workers
        $recentCarePlans = $this->getRecentCarePlansForAllCareWorkers();
        
        // Get expense data (using existing expense controller)
        $expenseData = $this->getExpenseData();
        
        return view('admin.admindashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careWorkerStats' => $careWorkerStats,
            'locationStats' => $locationStats,
            'requestStats' => $requestStats,
            'upcomingVisitations' => $upcomingVisitations,
            'careWorkerPerformance' => $careWorkerPerformance,
            'recentCarePlans' => $recentCarePlans,
            'expenseData' => $expenseData
        ]);
    }

    /**
     * Get this month's expense data for admin dashboard
     * 
     * @return array
     */
    private function getExpenseData()
    {
        try {
            // Get current month's start and end dates
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            // Get expenses for current month (up to 5 most recent)
            $recentExpenses = DB::table('expenses as e')
                ->join('expense_categories as ec', 'e.category_id', '=', 'ec.category_id')
                ->select(
                    'e.expense_id',
                    'e.title',
                    'e.amount',
                    'e.date',
                    'ec.name as category_name',
                    'ec.color_code'
                )
                ->whereBetween('e.date', [$startOfMonth, $endOfMonth])
                ->orderBy('e.date', 'desc')
                ->limit(5)
                ->get();
            
            // Calculate total spent this month
            $totalSpent = DB::table('expenses')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
                
            // Get budget breakdown by category
            $budgetBreakdown = DB::table('expenses as e')
                ->join('expense_categories as ec', 'e.category_id', '=', 'ec.category_id')
                ->select(
                    'ec.name as category_name',
                    'ec.color_code',
                    DB::raw('SUM(e.amount) as total_amount')
                )
                ->whereBetween('e.date', [$startOfMonth, $endOfMonth])
                ->groupBy('ec.name', 'ec.color_code')
                ->orderBy('total_amount', 'desc')
                ->limit(4)  // Show top 4 categories
                ->get();
            
            // Format the data for the view
            $formattedExpenses = [];
            foreach ($recentExpenses as $expense) {
                $formattedExpenses[] = [
                    'id' => $expense->expense_id,
                    'title' => $expense->title,
                    'amount' => $expense->amount,
                    'date' => $expense->date,
                    'category' => $expense->category_name,
                    'color' => $expense->color_code
                ];
            }
            
            // Format category breakdown
            $categoryBreakdown = [];
            foreach ($budgetBreakdown as $category) {
                $percentage = $totalSpent > 0 ? round(($category->total_amount / $totalSpent) * 100) : 0;
                $categoryBreakdown[] = [
                    'category' => $category->category_name,
                    'amount' => $category->total_amount,
                    'percentage' => $percentage,
                    'color' => $category->color_code
                ];
            }
            
            return [
                'recent_expenses' => $formattedExpenses,
                'total_spent' => $totalSpent,
                'month' => Carbon::now()->format('F Y'),
                'category_breakdown' => $categoryBreakdown
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting expense data for dashboard: ' . $e->getMessage());
            return [
                'recent_expenses' => [],
                'total_spent' => 0,
                'month' => Carbon::now()->format('F Y'),
                'category_breakdown' => []
            ];
        }
    }
    
    /**
     * Get dashboard data for care manager
     *
     * @param bool $showWelcome Whether to show welcome message
     * @return \Illuminate\View\View
     */
    public function careManagerDashboard($showWelcome = false)
    {
        // Get beneficiary stats
        $beneficiaryStats = $this->getBeneficiaryStats();
        
        // Get care worker stats
        $careWorkerStats = $this->getCareWorkerStats();
        
        // Get today's requests
        $requestStats = $this->getTodayRequestStats();
        
        // Get emergency and service requests
        $emergencyAndServiceRequests = $this->getEmergencyAndServiceRequests();
        
        // Get upcoming visitations for all care workers
        $upcomingVisitations = $this->getUpcomingVisitationsForAllCareWorkers();
        
        // Get top care workers performance
        $careWorkerPerformance = $this->getTopCareWorkersPerformance();
        
        // Get recent care plans for all care workers
        $recentCarePlans = $this->getRecentCarePlansForAllCareWorkers();
        
        return view('careManager.managerdashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careWorkerStats' => $careWorkerStats,
            'requestStats' => $requestStats,
            'emergencyAndServiceRequests' => $emergencyAndServiceRequests,
            'upcomingVisitations' => $upcomingVisitations,
            'careWorkerPerformance' => $careWorkerPerformance,
            'recentCarePlans' => $recentCarePlans,
        ]);
    }

    /**
     * Get the 3 newest emergency and service requests
     * 
     * @return array
     */
    private function getEmergencyAndServiceRequests()
    {
        try {
            // Get 2 newest emergency notices
            $emergencyNotices = DB::table('emergency_notices as en')
                ->join('beneficiaries as b', 'en.beneficiary_id', '=', 'b.beneficiary_id')
                ->join('emergency_types as et', 'en.emergency_type_id', '=', 'et.emergency_type_id')
                ->leftJoin('cose_users as cu', 'en.assigned_to', '=', 'cu.id')
                ->select(
                    'en.notice_id',
                    'en.message',
                    'en.created_at',
                    'en.status',
                    'b.first_name as beneficiary_first_name',
                    'b.last_name as beneficiary_last_name',
                    'et.name as emergency_type',
                    'et.color_code',
                    DB::raw("'emergency' as request_type"),
                    'cu.first_name as assigned_first_name',
                    'cu.last_name as assigned_last_name'
                )
                ->orderBy('en.created_at', 'desc')
                ->limit(2)
                ->get();
            
            // Get 1 newest service request
            $serviceRequests = DB::table('service_requests as sr')
                ->join('beneficiaries as b', 'sr.beneficiary_id', '=', 'b.beneficiary_id')
                ->join('service_request_types as srt', 'sr.service_type_id', '=', 'srt.service_type_id')
                ->leftJoin('cose_users as cu', 'sr.care_worker_id', '=', 'cu.id')
                ->select(
                    'sr.service_request_id as notice_id',
                    'sr.message',
                    'sr.created_at',
                    'sr.status',
                    'b.first_name as beneficiary_first_name',
                    'b.last_name as beneficiary_last_name',
                    'srt.name as emergency_type',
                    'srt.color_code',
                    DB::raw("'service' as request_type"),
                    'cu.first_name as assigned_first_name',
                    'cu.last_name as assigned_last_name'
                )
                ->orderBy('sr.created_at', 'desc')
                ->limit(1)
                ->get();
            
            // Combine and sort by creation date
            $combinedRequests = $emergencyNotices->concat($serviceRequests);
            $combinedRequests = $combinedRequests->sortByDesc('created_at');
            
            // Convert to array with formatted data
            $formattedRequests = $combinedRequests->map(function($request) {
                $timeAgo = Carbon::parse($request->created_at)->diffForHumans();
                
                return [
                    'id' => $request->notice_id,
                    'type' => $request->request_type,
                    'message' => $request->message,
                    'time_ago' => $timeAgo,
                    'status' => $request->status,
                    'beneficiary_name' => $request->beneficiary_first_name . ' ' . $request->beneficiary_last_name,
                    'emergency_type' => $request->emergency_type,
                    'color_code' => $request->color_code,
                    'assigned_to' => ($request->assigned_first_name && $request->assigned_last_name) ? 
                                    $request->assigned_first_name . ' ' . $request->assigned_last_name : 
                                    'Unassigned'
                ];
            })->take(3)->values()->all();
            
            return $formattedRequests;
        } catch (\Exception $e) {
            \Log::error('Error getting emergency and service requests: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the 4 nearest upcoming visitations for all care workers
     * 
     * @return array
     */
    private function getUpcomingVisitationsForAllCareWorkers()
    {
        try {
            $today = Carbon::now()->startOfDay();
            
            // Get the 4 nearest upcoming visitations for all care workers
            $upcomingVisits = DB::table('visitation_occurrences as vo')
                ->join('visitations as v', 'vo.visitation_id', '=', 'v.visitation_id')
                ->join('beneficiaries as b', 'v.beneficiary_id', '=', 'b.beneficiary_id')
                ->join('cose_users as cu', 'v.care_worker_id', '=', 'cu.id')
                ->where('vo.occurrence_date', '>=', $today)
                ->where('vo.status', 'scheduled')
                ->select(
                    'vo.occurrence_id',
                    'vo.occurrence_date',
                    'vo.start_time',
                    'b.first_name as beneficiary_first_name',
                    'b.last_name as beneficiary_last_name',
                    'b.beneficiary_id',
                    'b.street_address',
                    'v.visit_type',
                    'v.confirmed_by_beneficiary',
                    'v.confirmed_by_family',
                    'v.is_flexible_time',
                    'cu.first_name as care_worker_first_name',
                    'cu.last_name as care_worker_last_name'
                )
                ->orderBy('vo.occurrence_date', 'asc')
                ->orderBy('vo.start_time', 'asc')
                ->limit(4)
                ->get();
            
            // Transform data to include formatted time and status
            $upcomingVisits->transform(function($visit) {
                $date = Carbon::parse($visit->occurrence_date);
                $time = $visit->start_time ? Carbon::parse($visit->start_time)->format('g:i A') : 'Flexible Time';
                $displayDate = $date->isToday() ? 'Today' : ($date->isTomorrow() ? 'Tomorrow' : $date->format('M d, Y'));
                
                $status = ($visit->confirmed_by_beneficiary || $visit->confirmed_by_family) ? 'Confirmed' : 'Pending';
                $statusClass = $status === 'Confirmed' ? 'bg-success' : 'bg-info';
                
                return [
                    'id' => $visit->occurrence_id,
                    'date_display' => $displayDate,
                    'time' => $time,
                    'beneficiary_name' => $visit->beneficiary_first_name . ' ' . $visit->beneficiary_last_name,
                    'beneficiary_id' => 'B-' . str_pad($visit->beneficiary_id, 5, '0', STR_PAD_LEFT),
                    'location' => $visit->street_address,
                    'visit_type' => ucwords(str_replace('_', ' ', $visit->visit_type)),
                    'status' => $status,
                    'status_class' => $statusClass,
                    'assigned_to' => $visit->care_worker_first_name . ' ' . $visit->care_worker_last_name
                ];
            });
            
            return $upcomingVisits;
        } catch (\Exception $e) {
            \Log::error('Error getting upcoming visitations for all care workers: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Get monthly hours for top 10 care workers this month
     * 
     * @return array
     */
    private function getTopCareWorkersPerformance()
    {
        try {
            // Get current month's start and end dates
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            // Get top 10 care workers by hours this month
            $careWorkerHours = DB::table('weekly_care_plan_interventions as wpi')
                ->join('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->join('cose_users as cu', 'wcp.care_worker_id', '=', 'cu.id')
                ->whereBetween('wcp.created_at', [$startOfMonth, $endOfMonth])
                ->where('cu.role_id', 3) // Care workers only
                ->select(
                    'cu.id',
                    'cu.first_name',
                    'cu.last_name',
                    DB::raw('SUM(wpi.duration_minutes) as total_minutes')
                )
                ->groupBy('cu.id', 'cu.first_name', 'cu.last_name')
                ->orderBy('total_minutes', 'desc')
                ->limit(10)
                ->get();
            
            // Transform data to include formatted hours
            $careWorkerHours->transform(function($worker) {
                $hours = floor($worker->total_minutes / 60);
                $minutes = $worker->total_minutes % 60;
                
                return [
                    'id' => $worker->id,
                    'name' => $worker->first_name . ' ' . $worker->last_name,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'formatted_time' => $hours . ' hrs' . ($minutes > 0 ? ' ' . $minutes . ' min' : '')
                ];
            });
            
            return $careWorkerHours;
        } catch (\Exception $e) {
            \Log::error('Error getting care worker performance: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Get 10 most recent weekly care plans from all care workers
     * 
     * @return array
     */
    private function getRecentCarePlansForAllCareWorkers()
    {
        try {
            // Get 10 most recent care plans from all care workers
            $recentPlans = DB::table('weekly_care_plans as wcp')
                ->join('beneficiaries as b', 'wcp.beneficiary_id', '=', 'b.beneficiary_id')
                ->join('cose_users as cu', 'wcp.care_worker_id', '=', 'cu.id')
                ->select(
                    'wcp.weekly_care_plan_id',
                    'b.first_name as beneficiary_first_name',
                    'b.last_name as beneficiary_last_name',
                    'cu.first_name as care_worker_first_name',
                    'cu.last_name as care_worker_last_name',
                    'wcp.created_at'
                )
                ->orderBy('wcp.created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Transform data
            $recentPlans->transform(function($plan) {
                return [
                    'id' => $plan->weekly_care_plan_id,
                    'beneficiary_name' => $plan->beneficiary_first_name . ' ' . $plan->beneficiary_last_name,
                    'submitted_by' => $plan->care_worker_first_name . ' ' . $plan->care_worker_last_name,
                    'date' => Carbon::parse($plan->created_at)->format('M d, Y')
                ];
            });
            
            return $recentPlans;
        } catch (\Exception $e) {
            \Log::error('Error getting recent care plans: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }
    
    /**
     * Get dashboard data for care worker
     *
     * @param bool $showWelcome Whether to show welcome message
     * @return \Illuminate\View\View
     */
    public function careWorkerDashboard($showWelcome = false)
    {
        $user = Auth::user();
        
        // For care workers, limit data to their assigned beneficiaries
        $beneficiaryStats = $this->getBeneficiaryStatsForCareWorker($user->id);
        
        // Get care hours statistics for this care worker
        $careHoursStats = $this->getCareHoursStats($user->id);
        
        // Get report statistics for this care worker
        $reportStats = $this->getReportStats($user->id);
        
        // Get recent care plans for this care worker
        $recentCarePlans = $this->getRecentCarePlans($user->id);
        
        // Get upcoming visitations for this care worker
        $upcomingVisitations = $this->getUpcomingVisitations($user->id);
        
        return view('careWorker.workerdashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careHoursStats' => $careHoursStats,
            'reportStats' => $reportStats,
            'recentCarePlans' => $recentCarePlans,
            'upcomingVisitations' => $upcomingVisitations,
        ]);
    }

    /**
     * Get recent care plans for a specific care worker
     * 
     * @param int $careWorkerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentCarePlans($careWorkerId)
    {
        try {
            // Get 7 most recent care plans created by this care worker
            $recentPlans = DB::table('weekly_care_plans as wcp')
                ->join('beneficiaries as b', 'wcp.beneficiary_id', '=', 'b.beneficiary_id')
                ->where('wcp.care_worker_id', $careWorkerId)
                ->select(
                    'wcp.weekly_care_plan_id',
                    'b.first_name',
                    'b.last_name',
                    'wcp.created_at',
                    'wcp.date',
                    'wcp.acknowledged_by_beneficiary',
                    'wcp.acknowledged_by_family'
                )
                ->orderBy('wcp.created_at', 'desc')
                ->limit(7)
                ->get();
                
            // Transform data to include status
            $recentPlans->transform(function($plan) {
                $status = ($plan->acknowledged_by_beneficiary || $plan->acknowledged_by_family) ? 'Acknowledged' : 'Pending Review';
                $statusClass = $status === 'Acknowledged' ? 'badge-reviewed' : 'badge-emergency';
                
                return [
                    'id' => $plan->weekly_care_plan_id,
                    'beneficiary_name' => $plan->first_name . ' ' . $plan->last_name,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'date' => Carbon::parse($plan->date)->format('M d, Y')
                ];
            });
            
            return $recentPlans;
        } catch (\Exception $e) {
            \Log::error('Error getting recent care plans: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Get upcoming visitations for a specific care worker
     * 
     * @param int $careWorkerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUpcomingVisitations($careWorkerId)
    {
        try {
            $today = Carbon::now()->startOfDay();
            
            // Get the 4 nearest upcoming visitations for this care worker
            $upcomingVisits = DB::table('visitation_occurrences as vo')
                ->join('visitations as v', 'vo.visitation_id', '=', 'v.visitation_id')
                ->join('beneficiaries as b', 'v.beneficiary_id', '=', 'b.beneficiary_id')
                ->where('v.care_worker_id', $careWorkerId)
                ->where('vo.occurrence_date', '>=', $today)
                ->where('vo.status', 'scheduled')
                ->select(
                    'vo.occurrence_id',
                    'vo.occurrence_date',
                    'vo.start_time',
                    'b.first_name',
                    'b.last_name',
                    'b.beneficiary_id',
                    'b.street_address',
                    'v.visit_type',
                    'v.confirmed_by_beneficiary',
                    'v.confirmed_by_family',
                    'v.is_flexible_time'
                )
                ->orderBy('vo.occurrence_date', 'asc')
                ->orderBy('vo.start_time', 'asc')
                ->limit(4)
                ->get();
            
            // Transform data to include formatted time and status
            $upcomingVisits->transform(function($visit) {
                $date = Carbon::parse($visit->occurrence_date);
                $time = $visit->start_time ? Carbon::parse($visit->start_time)->format('g:i A') : 'Flexible Time';
                $displayDate = $date->isToday() ? 'Today' : ($date->isTomorrow() ? 'Tomorrow' : $date->format('M d, Y'));
                
                return [
                    'id' => $visit->occurrence_id,
                    'date_display' => $displayDate,
                    'time' => $time,
                    'beneficiary_name' => $visit->first_name . ' ' . $visit->last_name,
                    'beneficiary_id' => 'B-' . str_pad($visit->beneficiary_id, 5, '0', STR_PAD_LEFT),
                    'location' => $visit->street_address,
                    'visit_type' => ucwords(str_replace('_', ' ', $visit->visit_type)),
                ];
            });
            
            return $upcomingVisits;
        } catch (\Exception $e) {
            \Log::error('Error getting upcoming visitations: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Get care hours statistics for a specific care worker
     * 
     * @param int $careWorkerId
     * @return array
     */
    private function getCareHoursStats($careWorkerId)
    {
        try {
            // Get current date and time
            $now = Carbon::now();
            
            // Get all-time data (no date restriction)
            $totalMinutes = DB::table('weekly_care_plan_interventions as wpi')
                ->join('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->where('wcp.care_worker_id', $careWorkerId)
                ->sum(DB::raw('COALESCE(wpi.duration_minutes, 0)'));
            
            // Current month data (bounded to current month only)
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            $monthlyMinutes = DB::table('weekly_care_plan_interventions as wpi')
                ->join('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->where('wcp.care_worker_id', $careWorkerId)
                ->whereBetween('wcp.date', [$startOfMonth, $endOfMonth])
                ->sum(DB::raw('COALESCE(wpi.duration_minutes, 0)'));
            
            // Current week data (bounded to current week only)
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek = $now->copy()->endOfWeek();
            $weeklyMinutes = DB::table('weekly_care_plan_interventions as wpi')
                ->join('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->where('wcp.care_worker_id', $careWorkerId)
                ->whereBetween('wcp.date', [$startOfWeek, $endOfWeek])
                ->sum(DB::raw('COALESCE(wpi.duration_minutes, 0)'));
            
            // Format exactly like CareWorkerPerformanceController
            $totalHours = floor($totalMinutes / 60);
            $totalRemainingMins = $totalMinutes % 60;
            
            $weeklyHours = floor($weeklyMinutes / 60);
            $weeklyRemainingMins = $weeklyMinutes % 60;
            
            $monthlyHours = floor($monthlyMinutes / 60);
            $monthlyRemainingMins = $monthlyMinutes % 60;
            
            return [
                'total' => $totalHours,
                'total_formatted' => $totalHours . ' hrs ' . ($totalRemainingMins > 0 ? $totalRemainingMins . ' min' : ''),
                'week' => $weeklyHours,
                'week_formatted' => $weeklyHours . ' hrs ' . ($weeklyRemainingMins > 0 ? $weeklyRemainingMins . ' min' : ''),
                'month' => $monthlyHours,
                'month_formatted' => $monthlyHours . ' hrs ' . ($monthlyRemainingMins > 0 ? $monthlyRemainingMins . ' min' : '')
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting care hours stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'total_formatted' => '0 hrs',
                'week' => 0,
                'week_formatted' => '0 hrs',
                'month' => 0,
                'month_formatted' => '0 hrs'
            ];
        }
    }

    /**
     * Get report statistics for a specific care worker
     * 
     * @param int $careWorkerId
     * @return array
     */
    private function getReportStats($careWorkerId)
    {
        try {
            // Get total weekly care plans created by this care worker
            $total = DB::table('weekly_care_plans')
                ->where('care_worker_id', $careWorkerId)
                ->count();
            
            // Get pending reports (both acknowledgment fields are null)
            $pending = DB::table('weekly_care_plans')
                ->where('care_worker_id', $careWorkerId)
                ->whereNull('acknowledged_by_beneficiary')
                ->whereNull('acknowledged_by_family')
                ->count();
            
            // Get approved reports (at least one acknowledgment field is not null)
            $approved = DB::table('weekly_care_plans')
                ->where('care_worker_id', $careWorkerId)
                ->where(function($query) {
                    $query->whereNotNull('acknowledged_by_beneficiary')
                        ->orWhereNotNull('acknowledged_by_family');
                })
                ->count();
            
            return [
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting report stats: ' . $e->getMessage());
            return ['total' => 0, 'pending' => 0, 'approved' => 0];
        }
    }
    
    /**
     * Get beneficiary statistics
     * 
     * @return array
     */
    private function getBeneficiaryStats()
    {
        $total = Beneficiary::count();
        $active = Beneficiary::where('beneficiary_status_id', 1)->count();
        $inactive = $total - $active;
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive
        ];
    }
    
    /**
     * Get care worker statistics
     * 
     * @return array
     */
    private function getCareWorkerStats()
    {
        $total = User::where('role_id', 3)->count();
        $active = User::where('role_id', 3)->where('status', 'Active')->count();
        $inactive = User::where('role_id', 3)->where('status', 'Inactive')->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive
        ];
    }
    
    /**
     * Get location statistics
     * 
     * @return array
     */
    private function getLocationStats()
    {
        $municipalityCount = Municipality::count();
        $barangayCount = Barangay::count();
        
        return [
            'municipalities' => $municipalityCount,
            'barangays' => $barangayCount
        ];
    }
    
    /**
     * Get today's request statistics
     * 
     * @return array
     */
    private function getTodayRequestStats()
    {
        $today = Carbon::now()->startOfDay();
        $now = Carbon::now();
        
        // Handle potential database errors gracefully
        try {
            // Get emergency requests created today
            $emergencyCount = EmergencyNotice::whereBetween('created_at', [$today, $now])->count();
            
            // Get service requests created today
            $serviceCount = ServiceRequest::whereBetween('created_at', [$today, $now])->count();
            
            $totalCount = $emergencyCount + $serviceCount;
        } catch (\Exception $e) {
            \Log::error('Error getting request stats: ' . $e->getMessage());
            $emergencyCount = 0;
            $serviceCount = 0;
            $totalCount = 0;
        }
        
        return [
            'total' => $totalCount,
            'emergency' => $emergencyCount,
            'service' => $serviceCount
        ];
    }
    
    /**
     * Get beneficiary statistics for a specific care worker
     * 
     * @param int $careWorkerId
     * @return array
     */
    private function getBeneficiaryStatsForCareWorker($careWorkerId)
    {
        try {
            // Get beneficiaries assigned to this care worker through general care plans
            $total = DB::table('beneficiaries')
                ->join('general_care_plans', 'beneficiaries.general_care_plan_id', '=', 'general_care_plans.general_care_plan_id')
                ->where('general_care_plans.care_worker_id', $careWorkerId)
                ->count();
                
            $active = DB::table('beneficiaries')
                ->join('general_care_plans', 'beneficiaries.general_care_plan_id', '=', 'general_care_plans.general_care_plan_id')
                ->where('general_care_plans.care_worker_id', $careWorkerId)
                ->where('beneficiaries.beneficiary_status_id', 1)
                ->count();
                
            $inactive = $total - $active;
        } catch (\Exception $e) {
            \Log::error('Error getting beneficiary stats for care worker: ' . $e->getMessage());
            $total = 0;
            $active = 0;
            $inactive = 0;
        }
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive
        ];
    }
    
    /**
     * Get today's request statistics for a specific care worker
     * 
     * @param int $careWorkerId
     * @return array
     */
    private function getTodayRequestStatsForCareWorker($careWorkerId)
    {
        $today = Carbon::now()->startOfDay();
        $now = Carbon::now();
        
        try {
            // Get service requests assigned to this care worker today
            $serviceCount = ServiceRequest::where('care_worker_id', $careWorkerId)
                ->whereBetween('created_at', [$today, $now])
                ->count();
                
            // For emergency notices, check assigned_to field
            $emergencyCount = EmergencyNotice::where('assigned_to', $careWorkerId)
                ->whereBetween('created_at', [$today, $now])
                ->count();
                
            $totalCount = $emergencyCount + $serviceCount;
        } catch (\Exception $e) {
            \Log::error('Error getting request stats for care worker: ' . $e->getMessage());
            $serviceCount = 0;
            $emergencyCount = 0;
            $totalCount = 0;
        }
        
        return [
            'total' => $totalCount,
            'emergency' => $emergencyCount,
            'service' => $serviceCount
        ];
    }
}
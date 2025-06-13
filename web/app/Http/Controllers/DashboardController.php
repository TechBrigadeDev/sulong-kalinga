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
        
        return view('admin.admindashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careWorkerStats' => $careWorkerStats,
            'locationStats' => $locationStats,
            'requestStats' => $requestStats,
        ]);
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
        
        return view('careManager.managerdashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careWorkerStats' => $careWorkerStats,
            'requestStats' => $requestStats,
        ]);
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
        
        return view('careWorker.workerdashboard', [
            'showWelcome' => $showWelcome,
            'beneficiaryStats' => $beneficiaryStats,
            'careHoursStats' => $careHoursStats,
            'reportStats' => $reportStats,
        ]);
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
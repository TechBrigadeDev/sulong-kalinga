<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Municipality;
use App\Models\WeeklyCarePlan;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\Beneficiary;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\CareNeed;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CareWorkerPerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Get all care workers (role_id = 3)
        $careWorkers = User::where('role_id', 3)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get all municipalities
        $municipalities = Municipality::orderBy('municipality_name')->get();
        
        // Get available years from weekly care plans
        $availableYears = WeeklyCarePlan::select(DB::raw('EXTRACT(YEAR FROM date) as year'))
        ->distinct()
        ->orderBy('year')
        ->pluck('year')
        ->toArray();

        // If no years available, add current year
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }
        
        // Get filters from request or set defaults
        $selectedCareWorkerId = $request->input('care_worker_id', null);
        $selectedMunicipalityId = $request->input('municipality_id', null);
        $selectedTimeRange = $request->input('time_range', 'weeks');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedStartMonth = $request->input('start_month', 1);
        $selectedEndMonth = $request->input('end_month', 12);
        
        // FIXED: Use current year as default, and only use database years if they're recent
        $currentYear = Carbon::now()->year;
        if (!empty($availableYears) && max($availableYears) >= ($currentYear - 5)) {
            // Only use most recent year from database if it's within 5 years of current
            $selectedYear = $request->input('year', max($availableYears));
        } else {
            // Otherwise default to current year
            $selectedYear = $request->input('year', $currentYear);
            
            // Make sure current year is in the available years list
            if (!in_array($currentYear, $availableYears)) {
                $availableYears[] = $currentYear;
                sort($availableYears);
            }
        }
        
        // Get active care workers count
        $activeCareWorkersCount = User::where('role_id', 3)
            ->where('status', 'Active')
            ->count();
        
        // Calculate date ranges based on time range selection
        $startDate = null;
        $endDate = null;
        $dateRangeLabel = '';
        
        // Get initial year selection based on available years (keep your existing code)
        $currentYear = Carbon::now()->year;
        if (!empty($availableYears) && max($availableYears) >= ($currentYear - 5)) {
            $selectedYear = $request->input('year', max($availableYears));
        } else {
            $selectedYear = $request->input('year', $currentYear);
            
            if (!in_array($currentYear, $availableYears)) {
                $availableYears[] = $currentYear;
                sort($availableYears);
            }
        }

        // IMPORTANT: Update the year value based on the time range
        if ($selectedTimeRange === 'weeks') {
            // For Monthly view, look for monthly_year specifically
            $selectedYear = $request->input('monthly_year', $selectedYear);
        } elseif ($selectedTimeRange === 'months') {
            // For Range of Months, look for range_year specifically
            $selectedYear = $request->input('range_year', $selectedYear);
        }
        // For 'year' time range, we already have the correct year from the initial selection

        // Now continue with your switch statement for the date ranges
        switch ($selectedTimeRange) {
            case 'weeks':
                $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
                $dateRangeLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');
                break;
                
            case 'months':
                $startDate = Carbon::createFromDate($selectedYear, $selectedStartMonth, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($selectedYear, $selectedEndMonth, 1)->endOfMonth();
                $startMonthName = Carbon::createFromDate($selectedYear, $selectedStartMonth, 1)->format('F');
                $endMonthName = Carbon::createFromDate($selectedYear, $selectedEndMonth, 1)->format('F');
                $dateRangeLabel = "$startMonthName - $endMonthName $selectedYear";
                break;
                
            case 'year':
                $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();
                $dateRangeLabel = $selectedYear;
                break;
        }

        // Build query based on filters
        $query = WeeklyCarePlan::query()
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by care worker if selected
        if ($selectedCareWorkerId) {
            $query->where('care_worker_id', $selectedCareWorkerId);
        }

        // Filter by municipality if selected and care worker is not selected
        if ($selectedMunicipalityId && !$selectedCareWorkerId) {
            $careWorkerIds = User::where('assigned_municipality_id', $selectedMunicipalityId)
                ->where('role_id', 3)
                ->pluck('id');
            $query->whereIn('care_worker_id', $careWorkerIds);
        }

        // Get filtered weekly care plans
        $weeklyCarePlanIds = $query->pluck('weekly_care_plan_id')->toArray();

        // Calculate total care hours (in minutes)
        $totalCareMinutes = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
            ->sum('duration_minutes');
        
        // Convert minutes to hours
        $totalHours = floor($totalCareMinutes / 60);
        $totalMinutes = $totalCareMinutes % 60;
        $formattedCareTime = [
            'hours' => $totalHours,
            'minutes' => $totalMinutes
        ];

        // Count total interventions
        $totalInterventions = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
            ->count();

        // Initialize variables before the conditional blocks
        $mostImplementedInterventions = [];
        $hoursPerClient = [];

        // Check if any records exist for the selected filters
        $hasRecords = !empty($weeklyCarePlanIds);

        // Get most implemented interventions 
        if ($hasRecords) {
            // Query that includes both predefined and custom interventions
            $interventionCounts = DB::table('weekly_care_plan_interventions as wpi')
                ->leftJoin('interventions as i', 'wpi.intervention_id', '=', 'i.intervention_id')
                ->whereIn('wpi.weekly_care_plan_id', $weeklyCarePlanIds)
                ->select(
                    DB::raw('COALESCE(i.intervention_description, wpi.intervention_description) as intervention_name'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('intervention_name')
                ->orderBy('count', 'desc')
                ->limit(7) // Changed from 10 to 7 to match requirements
                ->get();
            
            foreach ($interventionCounts as $intervention) {
                $mostImplementedInterventions[] = [
                    'intervention_description' => $intervention->intervention_name,
                    'count' => $intervention->count
                ];
            }

            // Get hours per client
            $hoursPerClient = DB::table('weekly_care_plan_interventions')
                ->join('weekly_care_plans', 'weekly_care_plan_interventions.weekly_care_plan_id', '=', 'weekly_care_plans.weekly_care_plan_id')
                ->join('beneficiaries', 'weekly_care_plans.beneficiary_id', '=', 'beneficiaries.beneficiary_id')
                ->whereIn('weekly_care_plan_interventions.weekly_care_plan_id', $weeklyCarePlanIds)
                ->select(
                    DB::raw("CONCAT(beneficiaries.last_name, ', ', LEFT(beneficiaries.first_name, 1), '.') as beneficiary_name"),
                    DB::raw('SUM(weekly_care_plan_interventions.duration_minutes) / 60 as hours')
                )
                ->groupBy('beneficiaries.beneficiary_id', 'beneficiaries.last_name', 'beneficiaries.first_name')
                ->orderBy('hours', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
        }

        $careCategories = CareCategory::with('interventions')->get();
    
        // For each care category, calculate intervention statistics based on filters
        $categorySummaries = [];
        
        foreach ($careCategories as $category) {
            $interventionStats = [];
            
            // Get all interventions in this category (both from interventions table and custom ones)
            $interventionsInCategory = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
                ->where(function ($query) use ($category) {
                    // Either match by intervention_id + care_category_id
                    $query->whereHas('intervention', function($q) use ($category) {
                        $q->where('care_category_id', $category->care_category_id);
                    })
                    // Or match by direct care_category_id for custom interventions
                    ->orWhere('care_category_id', $category->care_category_id);
                })
                ->select(
                    'intervention_id', 
                    'care_category_id', 
                    'intervention_description', 
                    DB::raw('COUNT(*) as times_implemented'),
                    DB::raw('SUM(duration_minutes) as total_minutes')
                )
                ->groupBy('intervention_id', 'care_category_id', 'intervention_description')
                ->get();
            
            foreach ($interventionsInCategory as $intervention) {
                // Format hours and minutes
                $hours = floor($intervention->total_minutes / 60);
                $minutes = $intervention->total_minutes % 60;
                
                $description = $intervention->intervention_description;
                if (!$description && $intervention->intervention_id) {
                    // If description is empty but we have an intervention_id, get from interventions table
                    $interventionRecord = Intervention::find($intervention->intervention_id);
                    $description = $interventionRecord ? $interventionRecord->intervention_description : 'Unknown';
                }
                
                $interventionStats[] = [
                    'id' => $intervention->intervention_id,
                    'description' => $description,
                    'times_implemented' => $intervention->times_implemented,
                    'total_hours' => $hours,
                    'total_minutes' => $minutes
                ];
            }
            
            $categorySummaries[$category->care_category_id] = [
                'category_name' => $category->care_category_name,
                'interventions' => $interventionStats
            ];
        }

        // Time Per Category Chart Data
        $categoryTimeBreakdown = [];
        if ($hasRecords) {
            $categoryData = DB::table('weekly_care_plan_interventions as wpi')
                ->leftJoin('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->leftJoin('interventions as i', 'wpi.intervention_id', '=', 'i.intervention_id')
                ->leftJoin('care_categories as cc', function($join) {
                    $join->on('i.care_category_id', '=', 'cc.care_category_id')
                        ->orOn('wpi.care_category_id', '=', 'cc.care_category_id');
                })
                ->whereIn('wpi.weekly_care_plan_id', $weeklyCarePlanIds)
                ->select(
                    'cc.care_category_name',
                    'cc.care_category_id',
                    DB::raw('SUM(wpi.duration_minutes) as total_minutes')
                )
                ->groupBy('cc.care_category_id', 'cc.care_category_name')
                ->orderBy('total_minutes', 'desc')
                ->get();
            
            foreach ($categoryData as $category) {
                if ($category->care_category_name) {
                    $hours = floor($category->total_minutes / 60);
                    $minutes = $category->total_minutes % 60;
                    
                    $categoryTimeBreakdown[] = [
                        'category_name' => $category->care_category_name,
                        'total_minutes' => $category->total_minutes,
                        'hours' => $hours,
                        'minutes' => $minutes,
                        'formatted_time' => $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' min' : '')
                    ];
                }
            }
        }

        // Client Care Breakdown - only when care worker is selected
        $clientCareBreakdown = [];
        if ($hasRecords && $selectedCareWorkerId) {
            $clientData = DB::table('weekly_care_plan_interventions as wpi')
                ->join('weekly_care_plans as wcp', 'wpi.weekly_care_plan_id', '=', 'wcp.weekly_care_plan_id')
                ->join('beneficiaries as b', 'wcp.beneficiary_id', '=', 'b.beneficiary_id')
                ->whereIn('wpi.weekly_care_plan_id', $weeklyCarePlanIds)
                ->select(
                    'b.beneficiary_id',
                    DB::raw("CONCAT(b.last_name, ', ', LEFT(b.first_name, 1), '.') as beneficiary_name"),
                    DB::raw('SUM(wpi.duration_minutes) as total_minutes')
                )
                ->groupBy('b.beneficiary_id', 'b.last_name', 'b.first_name')
                ->orderBy('total_minutes', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($clientData as $client) {
                $hours = floor($client->total_minutes / 60);
                $minutes = $client->total_minutes % 60;
                
                $clientCareBreakdown[] = [
                    'beneficiary_name' => $client->beneficiary_name,
                    'total_minutes' => $client->total_minutes,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'formatted_time' => $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' min' : '')
                ];
            }
        }

        // Care Worker Performance table data
        $careWorkerPerformance = [];
        $performanceData = DB::table('cose_users as cu')
            ->leftJoin('weekly_care_plans as wcp', function($join) use ($startDate, $endDate) {
                $join->on('cu.id', '=', 'wcp.care_worker_id')
                    ->whereBetween('wcp.date', [$startDate, $endDate]);
            })
            ->leftJoin('weekly_care_plan_interventions as wpi', 'wcp.weekly_care_plan_id', '=', 'wpi.weekly_care_plan_id')
            ->where('cu.role_id', 3) // Care workers only
            ->when($selectedMunicipalityId, function($query) use ($selectedMunicipalityId) {
                return $query->where('cu.assigned_municipality_id', $selectedMunicipalityId);
            })
            ->select(
                'cu.id as care_worker_id',
                'cu.first_name',
                'cu.last_name',
                DB::raw('COUNT(DISTINCT wcp.weekly_care_plan_id) as beneficiary_visits'),
                DB::raw('COUNT(wpi.wcp_intervention_id) as interventions_performed'),
                DB::raw('SUM(COALESCE(wpi.duration_minutes, 0)) as total_minutes')
            )
            ->groupBy('cu.id', 'cu.first_name', 'cu.last_name')
            ->orderBy('cu.last_name')
            ->get();

        foreach ($performanceData as $worker) {
            $hours = floor($worker->total_minutes / 60);
            $minutes = $worker->total_minutes % 60;
            
            $careWorkerPerformance[] = [
                'id' => $worker->care_worker_id,
                'name' => $worker->last_name . ', ' . $worker->first_name,
                'hours_worked' => [
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'formatted_time' => $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' min' : '')
                ],
                'beneficiary_visits' => $worker->beneficiary_visits ?: 0,
                'interventions_performed' => $worker->interventions_performed ?: 0,
                'is_selected' => $selectedCareWorkerId == $worker->care_worker_id
            ];
        }


        
        return view('admin.careWorkerPerformance', compact(
            'careWorkers',
            'municipalities',
            'availableYears',
            'selectedCareWorkerId',
            'selectedMunicipalityId',
            'selectedTimeRange',
            'selectedMonth',
            'selectedStartMonth',
            'selectedEndMonth',
            'selectedYear',
            'formattedCareTime',
            'activeCareWorkersCount',
            'totalInterventions',
            'dateRangeLabel',
            'hasRecords',
            'mostImplementedInterventions',
            'hoursPerClient',
            'careCategories',
            'categorySummaries',
            'categoryTimeBreakdown',
            'clientCareBreakdown',
            'careWorkerPerformance'
        ));

    }

    public function careManagerIndex(Request $request)
    {
        // Get the same data as admin index method
        $data = $this->index($request);
        
        // Return care manager view with the same data
        return view('careManager.careWorkerPerformance', $data->getData());
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WeeklyCarePlan;
use App\Models\Beneficiary;
use App\Models\CareCategory;
use App\Models\HealthHistory;
use App\Models\WeeklyCarePlanInterventions;
use Carbon\Carbon;

class FamilyPortalCarePlanController extends Controller
{
    /**
     * Display the weekly care plans for the beneficiary
     */
    public function index(Request $request)
    {
        try {
            // Determine if the user is a beneficiary or family member
            $beneficiaryId = null;
            $userType = null;
            $userId = null;
            
            if (Auth::guard('beneficiary')->check()) {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
                $userType = 'beneficiary';
                $userId = $user->beneficiary_id;
            } elseif (Auth::guard('family')->check()) {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
                $userType = 'family';
                $userId = $user->family_member_id;
            }
            
            // Get filter parameters
            $search = $request->input('search');
            $filter = $request->input('filter', 'all');
            $perPage = $request->input('per_page', 10);
            
            // Build the query for weekly care plans related to this beneficiary
            $query = WeeklyCarePlan::with(['beneficiary', 'author', 'careWorker'])
                ->where('beneficiary_id', $beneficiaryId)
                ->orderBy('created_at', 'desc');
            
            // Apply search filter if provided - IMPROVED
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('author', function($authorQuery) use ($search) {
                        $authorQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    })
                    // Use PostgreSQL's TO_CHAR instead of MySQL's DATE_FORMAT
                    ->orWhereRaw("TO_CHAR(created_at, 'Month') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'DD') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'YYYY') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'MM/DD/YYYY') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'Mon') ILIKE ?", ["%{$search}%"]);
                });
            }
            
            // Apply status filter
            if ($filter == 'pending') {
                $query->where(function($q) {
                    $q->whereNull('acknowledged_by_beneficiary')
                    ->whereNull('acknowledged_by_family')
                    ->whereNull('acknowledgement_signature');
                });
            } elseif ($filter == 'acknowledged') {
                $query->where(function($q) {
                    $q->whereNotNull('acknowledged_by_beneficiary')
                    ->orWhereNotNull('acknowledged_by_family')
                    ->orWhereNotNull('acknowledgement_signature');
                });
            }
            
            // Get paginated results
            $carePlans = $query->paginate($perPage);

            // For AJAX requests, return only the necessary HTML
            if ($request->ajax() || $request->input('ajax') == 1) {
                $view = $userType === 'beneficiary' 
                    ? 'beneficiaryPortal.partials.carePlansTable' 
                    : 'familyPortal.partials.carePlansTable';
                    
                return view($view, [
                    'carePlans' => $carePlans, 
                    'search' => $search, 
                    'filter' => $filter,
                    'userType' => $userType,
                    'userId' => $userId
                ])->render();
            }
            
            // For regular requests, return the full view
            $view = $userType === 'beneficiary' 
                ? 'beneficiaryPortal.viewAllCarePlan' 
                : 'familyPortal.viewAllCarePlan';
            
            return view($view, [
                'carePlans' => $carePlans, 
                'search' => $search, 
                'filter' => $filter,
                'userType' => $userType,
                'userId' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error in care plan index: ' . $e->getMessage());
            
            if ($request->ajax() || $request->input('ajax') == 1) {
                return '<div class="alert alert-danger">Error loading care plans. Please refresh the page.</div>';
            }
            
            // Determine which view to use based on the user type for the error page
            $view = Auth::guard('beneficiary')->check()
                ? 'beneficiaryPortal.viewAllCarePlan'
                : 'familyPortal.viewAllCarePlan';
                
            return view($view, [
                'carePlans' => collect(),
                'search' => '',
                'filter' => 'all',
                'error' => 'An error occurred while loading care plans. Please try again later.'
            ]);
        }
    }

    /**
     * Show statistics view with beneficiary-specific data
     */
    public function statistics(Request $request)
    {
        try {
            // Determine the user type and get beneficiary ID
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            
            if ($userType === 'beneficiary') {
                $beneficiaryId = Auth::guard('beneficiary')->user()->beneficiary_id;
                $userId = $beneficiaryId;
            } else {
                $familyMember = Auth::guard('family')->user();
                $beneficiaryId = $familyMember->related_beneficiary_id;
                $userId = $familyMember->family_member_id;
            }
            
            // Get the beneficiary
            $beneficiary = Beneficiary::with(['category', 'municipality', 'barangay'])->findOrFail($beneficiaryId);
            
            // Get filter parameters
            $selectedTimeRange = $request->input('time_range', 'weeks');
            $selectedMonth = $request->input('month', Carbon::now()->month);
            $selectedYear = $request->input('year', Carbon::now()->year);
            $selectedStartMonth = $request->input('start_month', 1);
            $selectedEndMonth = $request->input('end_month', 12);
            
            // Calculate date ranges based on time range selection
            $startDate = null;
            $endDate = null;
            $dateRangeLabel = '';
            
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
            
            // Get vital signs history for charts
            $vitalSignsHistory = DB::table('vital_signs as vs')
                ->join('weekly_care_plans as wcp', 'vs.vital_signs_id', '=', 'wcp.vital_signs_id')
                ->where('wcp.beneficiary_id', $beneficiaryId)
                ->whereBetween('wcp.date', [$startDate, $endDate])
                ->orderBy('wcp.date', 'asc')
                ->select(
                    'wcp.date',
                    'vs.blood_pressure',
                    'vs.body_temperature as temperature',
                    'vs.pulse_rate as heart_rate',
                    'vs.respiratory_rate'
                )
                ->get();
            
            // Prepare chart data
            $chartLabels = [];
            $bloodPressureData = ['systolic' => [], 'diastolic' => []];
            $heartRateData = [];
            $respiratoryRateData = [];
            $temperatureData = [];
            
            foreach ($vitalSignsHistory as $record) {
                $date = Carbon::parse($record->date)->format('M d');
                $chartLabels[] = $date;
                
                // Process blood pressure (format: "120/80")
                if ($record->blood_pressure) {
                    $bpParts = explode('/', $record->blood_pressure);
                    $bloodPressureData['systolic'][] = isset($bpParts[0]) ? intval($bpParts[0]) : null;
                    $bloodPressureData['diastolic'][] = isset($bpParts[1]) ? intval($bpParts[1]) : null;
                } else {
                    $bloodPressureData['systolic'][] = null;
                    $bloodPressureData['diastolic'][] = null;
                }
                
                $heartRateData[] = $record->heart_rate;
                $respiratoryRateData[] = $record->respiratory_rate;
                $temperatureData[] = $record->temperature;
            }
            
            // Get care services summary data - restructured to group by category with interventions
            $careCategories = CareCategory::orderBy('care_category_name')->get();
            $careServicesSummary = [];

            // Query base - weekly care plans filtered by date range and beneficiary
            $wcpQuery = WeeklyCarePlan::whereBetween('date', [$startDate, $endDate])
                ->where('beneficiary_id', $beneficiaryId);

            // Get the filtered weekly care plan IDs
            $filteredWcpIds = $wcpQuery->pluck('weekly_care_plan_id')->toArray();

            // Calculate total care hours by category
            $totalCareMinutes = 0;

            // Get all interventions for the filtered weekly care plans
            $allInterventions = DB::table('weekly_care_plan_interventions as wcpi')
            ->leftJoin('interventions as i', 'wcpi.intervention_id', '=', 'i.intervention_id')
            ->whereIn('wcpi.weekly_care_plan_id', $filteredWcpIds)
            ->select(
                'wcpi.intervention_id',
                'wcpi.care_category_id',
                'wcpi.intervention_description',
                'wcpi.duration_minutes',
                'i.intervention_description as standard_intervention_description'
            )
            ->get();

            // Calculate category totals and organize data
            foreach ($careCategories as $category) {
                $categoryId = $category->care_category_id;
                $categoryInterventions = $allInterventions->where('care_category_id', $categoryId);
                
                if ($categoryInterventions->count() > 0) {
                    // Calculate category total minutes
                    $categoryTotalMinutes = $categoryInterventions->sum('duration_minutes');
                    $totalCareMinutes += $categoryTotalMinutes;
                    
                    // Format category totals
                    $categoryHours = floor($categoryTotalMinutes / 60);
                    $categoryMins = $categoryTotalMinutes % 60;
                    $categoryDurationDisplay = $categoryHours > 0 
                        ? $categoryHours . ' hrs ' . ($categoryMins > 0 ? $categoryMins . ' min' : '') 
                        : $categoryTotalMinutes . ' min';
                    
                    // Group interventions
                    $interventions = [];
                    $processedIds = [];
                    
                    foreach ($categoryInterventions as $intervention) {
                        $name = '';
                        $isCustom = false;
                        
                        if ($intervention->intervention_id) {
                            // Skip duplicate intervention IDs
                            if (in_array($intervention->intervention_id, $processedIds)) {
                                continue;
                            }
                            
                            // Get the intervention description from the joined interventions table
                            // Join is needed in the query to properly fetch this data
                            $interventionDescription = DB::table('interventions')
                                ->where('intervention_id', $intervention->intervention_id)
                                ->value('intervention_description');
                            
                            $name = $interventionDescription ?? 'Unnamed Intervention';
                            $processedIds[] = $intervention->intervention_id;
                        } else {
                            $name = $intervention->intervention_description ?? 'Custom Intervention';
                            $isCustom = true;
                        }
                        
                        // Calculate intervention minutes
                        $minutes = $isCustom 
                            ? $intervention->duration_minutes 
                            : $categoryInterventions->where('intervention_id', $intervention->intervention_id)->sum('duration_minutes');
                        
                        // Format duration display
                        $hours = floor($minutes / 60);
                        $mins = $minutes % 60;
                        $durationDisplay = $hours > 0 
                            ? $hours . ' hrs ' . ($mins > 0 ? $mins . ' min' : '') 
                            : $minutes . ' min';
                        
                        // Calculate percentage of category
                        $percentage = $categoryTotalMinutes > 0 ? ($minutes / $categoryTotalMinutes) * 100 : 0;
                        
                        $interventions[] = [
                            'id' => $intervention->intervention_id,
                            'name' => $name . ($isCustom ? ' (Custom)' : ''),
                            'is_custom' => $isCustom,
                            'duration_minutes' => $minutes,
                            'duration_display' => $durationDisplay,
                            'percentage' => $percentage
                        ];
                    }
                    
                    // Add to summary - this is where the issue was occurring
                    $careServicesSummary[$categoryId] = [
                        'category_name' => $category->care_category_name,
                        'total_minutes' => $categoryTotalMinutes,
                        'total_duration_display' => $categoryDurationDisplay,
                        'overall_percentage' => 0, // Will calculate after getting total
                        'interventions' => $interventions
                    ];
                }
            }

            // Calculate overall percentages
            if ($totalCareMinutes > 0) {
                foreach ($careServicesSummary as $categoryId => $data) {
                    $careServicesSummary[$categoryId]['overall_percentage'] = 
                        ($data['total_minutes'] / $totalCareMinutes) * 100;
                }
            }

            // Calculate total care hours and format for display
            $totalCareHours = floor($totalCareMinutes / 60);
            $remainingMinutes = $totalCareMinutes % 60;
            $totalCareTime = $totalCareHours . ' hrs ' . ($remainingMinutes > 0 ? $remainingMinutes . ' min' : '');
            
            // Get total count of care plans in the date range
            $totalCarePlans = $wcpQuery->count();
            
            // Process medical conditions and illnesses
            $medicalConditions = [];
            $illnesses = [];
            
            // Get medical conditions from health history
            if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->healthHistory) {
                $medicalConditionsText = $beneficiary->generalCarePlan->healthHistory->medical_conditions;
                if ($medicalConditionsText) {
                    if (is_string($medicalConditionsText) && is_array(json_decode($medicalConditionsText, true))) {
                        $medicalConditions = json_decode($medicalConditionsText, true);
                    } else {
                        $medicalConditions = [$medicalConditionsText];
                    }
                }
            }
            
            // Get illnesses from weekly care plans
            $weeklyPlansWithIllnesses = WeeklyCarePlan::where('beneficiary_id', $beneficiaryId)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('illnesses')
                ->get();
                
            foreach ($weeklyPlansWithIllnesses as $plan) {
                if (is_string($plan->illnesses) && is_array(json_decode($plan->illnesses, true))) {
                    $planIllnesses = json_decode($plan->illnesses, true);
                    foreach ($planIllnesses as $illness) {
                        if (!in_array($illness, $illnesses)) {
                            $illnesses[] = $illness;
                        }
                    }
                }
            }
            
            // Get available years for filters
            $availableYears = WeeklyCarePlan::where('beneficiary_id', $beneficiaryId)
                ->select(DB::raw('EXTRACT(YEAR FROM date) as year'))
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->toArray();
            
            // If no years available, add current year
            if (empty($availableYears)) {
                $availableYears = [Carbon::now()->year];
            }
            
            // Determine which view to use
            $view = $userType === 'beneficiary' ? 'beneficiaryPortal.carePlan' : 'familyPortal.carePlan';
            
            return view($view, [
                'beneficiary' => $beneficiary,
                'chartLabels' => $chartLabels,
                'bloodPressureData' => $bloodPressureData,
                'heartRateData' => $heartRateData,
                'respiratoryRateData' => $respiratoryRateData,
                'temperatureData' => $temperatureData,
                'careServicesSummary' => $careServicesSummary,
                'careCategories' => $careCategories,
                'totalCareTime' => $totalCareTime,
                'totalCarePlans' => $totalCarePlans,
                'medicalConditions' => $medicalConditions,
                'illnesses' => $illnesses,
                'selectedTimeRange' => $selectedTimeRange,
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear,
                'selectedStartMonth' => $selectedStartMonth,
                'selectedEndMonth' => $selectedEndMonth,
                'dateRangeLabel' => $dateRangeLabel,
                'availableYears' => $availableYears
            ]);
        } catch (\Exception $e) {
            Log::error('Error in beneficiary/family care plan statistics: ' . $e->getMessage());
            
            return redirect()
                ->route($userType === 'beneficiary' ? 'beneficiary.care.plan.index' : 'family.care.plan.index')
                ->with('error', 'An error occurred while loading statistics. Please try again later.');
        }
    }

    /**
     * View detailed weekly care plan
     */
    public function viewWeeklyCarePlan($id)
    {
        try {
            // Determine the user type
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $userId = Auth::guard('beneficiary')->check() 
                ? Auth::guard('beneficiary')->user()->beneficiary_id
                : Auth::guard('family')->user()->family_member_id;
            
            // Get the weekly care plan with related data
            $weeklyCareplan = WeeklyCarePlan::with([
                'beneficiary',
                'careWorker',
                'vitalSigns',
                'interventions.intervention',
                'interventions.careCategory',
                'acknowledgedByBeneficiary',
                'acknowledgedByFamily'
            ])->findOrFail($id);
            
            // Security check: ensure the user has access to this care plan
            if ($userType === 'beneficiary' && $weeklyCareplan->beneficiary_id !== $userId) {
                abort(403, 'You do not have permission to view this care plan.');
            } elseif ($userType === 'family') {
                $familyMember = Auth::guard('family')->user();
                if ($familyMember->related_beneficiary_id !== $weeklyCareplan->beneficiary_id) {
                    abort(403, 'You do not have permission to view this care plan.');
                }
            }
            
            // Get all care categories
            $categories = CareCategory::orderBy('care_category_name')->get();
            
            // Organize standard interventions by category (with intervention_id)
            $interventionsByCategory = collect();
            foreach ($weeklyCareplan->interventions as $intervention) {
                if ($intervention->intervention_id) {
                    $categoryId = $intervention->care_category_id;
                    if (!$interventionsByCategory->has($categoryId)) {
                        $interventionsByCategory->put($categoryId, collect());
                    }
                    $interventionsByCategory[$categoryId]->push($intervention);
                }
            }
            
            // Get custom interventions (without intervention_id)
            $customInterventions = $weeklyCareplan->interventions
                ->whereNull('intervention_id')
                ->whereNotNull('care_category_id')
                ->whereNotNull('intervention_description');
            
            // Determine which view to use based on the user type
            $view = $userType === 'beneficiary' 
                ? 'beneficiaryPortal.viewWeeklyCarePlan' 
                : 'familyPortal.viewWeeklyCarePlan';
            
            return view($view, [
                'weeklyCareplan' => $weeklyCareplan,
                'categories' => $categories,
                'interventionsByCategory' => $interventionsByCategory,
                'customInterventions' => $customInterventions,
                'userId' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing weekly care plan: ' . $e->getMessage());
            
            return redirect()
                ->route($userType === 'beneficiary' ? 'beneficiary.care.plan.index' : 'family.care.plan.index')
                ->with('error', 'An error occurred while loading the care plan: ' . $e->getMessage());
        }
    }

    /**
     * Process acknowledgment of weekly care plan
     */
    public function acknowledgeWeeklyCarePlan(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'confirmation' => 'required|in:confirmed',
            ]);
            
            // Determine the user type and ID
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $userId = Auth::guard('beneficiary')->check() 
                ? Auth::guard('beneficiary')->user()->beneficiary_id
                : Auth::guard('family')->user()->family_member_id;
            
            // Get the weekly care plan
            $weeklyCareplan = WeeklyCarePlan::findOrFail($id);
            
            // Security check: ensure the user has access to this care plan
            if ($userType === 'beneficiary' && $weeklyCareplan->beneficiary_id !== $userId) {
                abort(403, 'You do not have permission to acknowledge this care plan.');
            } elseif ($userType === 'family' && 
                     Auth::guard('family')->user()->related_beneficiary_id !== $weeklyCareplan->beneficiary_id) {
                abort(403, 'You do not have permission to acknowledge this care plan.');
            }
            
            // Record the acknowledgment
            if ($userType === 'beneficiary') {
                $weeklyCareplan->acknowledged_by_beneficiary = $userId;
            } else {
                $weeklyCareplan->acknowledged_by_family = $userId;
            }
            
            // Record signature information
            $signature = [
                'acknowledged_by' => $userType === 'beneficiary' ? 'Beneficiary' : 'Family Member',
                'user_id' => $userId,
                'name' => Auth::guard($userType === 'beneficiary' ? 'beneficiary' : 'family')->user()->first_name . ' ' . 
                         Auth::guard($userType === 'beneficiary' ? 'beneficiary' : 'family')->user()->last_name,
                'date' => Carbon::now()->toDateTimeString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];
            
            $weeklyCareplan->acknowledgement_signature = json_encode($signature);
            $weeklyCareplan->save();
            
            // Redirect back with success message
            $routeName = $userType === 'beneficiary' 
                ? 'beneficiary.care.plan.view' 
                : 'family.care.plan.view';
                
            return redirect()
                ->route($routeName, ['id' => $id])
                ->with('success', 'Care plan acknowledged successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error acknowledging care plan: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'An error occurred while processing your acknowledgment. Please try again.');
        }
    }
}
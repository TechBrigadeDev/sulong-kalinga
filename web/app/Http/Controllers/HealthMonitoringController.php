<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\HealthMonitoringController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\Municipality;
use App\Models\BeneficiaryCategory;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\CareNeed;
use App\Models\BeneficiaryStatus;
use App\Models\WeeklyCarePlan;
use App\Models\VitalSigns;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HealthMonitoringController extends Controller
{
    public function index(Request $request, $forExport = false)
    {
        // Get all active beneficiaries (status_id = 1)
        $beneficiaries = Beneficiary::orderBy('last_name')
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
        $selectedBeneficiaryId = $request->input('beneficiary_id', null);
        $selectedMunicipalityId = $request->input('municipality_id', null);
        $selectedTimeRange = $request->input('time_range', 'weeks');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedStartMonth = $request->input('start_month', 1);
        $selectedEndMonth = $request->input('end_month', 12);
        
        // Use current year as default, and only use database years if they're recent
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
        
        // Calculate date ranges based on time range selection
        $startDate = null;
        $endDate = null;
        $dateRangeLabel = '';

        // Get initial year selection based on available years
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

        // Get selected beneficiary details if one is selected
        $selectedBeneficiary = null;
        if ($selectedBeneficiaryId) {
            $selectedBeneficiary = Beneficiary::with(['category', 'municipality', 'barangay'])
                ->find($selectedBeneficiaryId);
        }
        
        // Compute health statistics when no specific beneficiary is selected
        $healthStatistics = [];
        $totalPopulation = 0;
        // Initialize totals with default values so it's always defined
        $totals = [
            'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0, 
            'male' => 0, 'female' => 0, 
            'single' => 0, 'married' => 0, 'widowed' => 0
        ];

        // Get vital signs history if a beneficiary is selected
        $vitalSignsHistory = [];
            
        if (!$selectedBeneficiary) {
            // Start with beneficiary query for active beneficiaries
            $beneficiaryQuery = Beneficiary::where('beneficiary_status_id', 1);
            
            // Filter by municipality if selected
            if ($selectedMunicipalityId) {
                $beneficiaryQuery->where('municipality_id', $selectedMunicipalityId);
            }
            
            // Get total population count
            $totalPopulation = $beneficiaryQuery->count();
            
            // Get statistics by category
            $categoryStats = $beneficiaryQuery->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('category_id') // Order by the grouped column
            ->get();
                
            $categories = BeneficiaryCategory::all()->keyBy('category_id');
            
            // Format category statistics
            foreach ($categoryStats as $stat) {
                if (isset($categories[$stat->category_id])) {
                    $healthStatistics[] = [
                        'category' => $categories[$stat->category_id]->category_name,
                        'count' => $stat->count,
                        'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                    ];
                }
            }
            
            // Get statistics by gender
            $genderStats = $beneficiaryQuery->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->orderBy('gender') // Order by the grouped column
            ->get();
                
            foreach ($genderStats as $stat) {
                $healthStatistics[] = [
                    'category' => $stat->gender,
                    'count' => $stat->count,
                    'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                ];
            }
            
            // Get statistics by civil status
            $civilStatusStats = $beneficiaryQuery->select('civil_status', DB::raw('count(*) as count'))
                ->groupBy('civil_status')
                ->orderBy('civil_status') // Change ordering to use the grouped column instead
                ->get();
                
            foreach ($civilStatusStats as $stat) {
                $healthStatistics[] = [
                    'category' => $stat->civil_status,
                    'count' => $stat->count,
                    'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                ];
            }
            
            // Calculate age groups
            $now = Carbon::now();
            $ageGroups = [
                '60-69' => [0, 0], // [count, percentage]
                '70-79' => [0, 0],
                '80-89' => [0, 0],
                '90+' => [0, 0]
            ];
            
            $beneficiaryQuery->chunk(100, function ($beneficiaries) use (&$ageGroups, $now, $totalPopulation) {
                foreach ($beneficiaries as $beneficiary) {
                    $birthday = Carbon::parse($beneficiary->birthday);
                    $age = $birthday->diffInYears($now);
                    
                    if ($age >= 90) {
                        $ageGroups['90+'][0]++;
                    } elseif ($age >= 80) {
                        $ageGroups['80-89'][0]++;
                    } elseif ($age >= 70) {
                        $ageGroups['70-79'][0]++;
                    } else {
                        $ageGroups['60-69'][0]++;
                    }
                }
            });
            
            // Calculate percentages for age groups
            foreach ($ageGroups as $range => $data) {
                $count = $data[0];
                $percentage = $totalPopulation > 0 ? round(($count / $totalPopulation) * 100, 2) : 0;
                
                $healthStatistics[] = [
                    'category' => $range . ' years old',
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }
        }

        // If a specific beneficiary is selected, get their vital signs history
        $vitalSignsHistory = [];
        if ($selectedBeneficiary) {
            $vitalSignsHistory = DB::table('vital_signs as vs')
                ->join('weekly_care_plans as wcp', 'vs.vital_signs_id', '=', 'wcp.vital_signs_id')
                ->where('wcp.beneficiary_id', $selectedBeneficiaryId)
                ->whereBetween('wcp.date', [$startDate, $endDate])
                ->orderBy('wcp.date', 'asc')
                ->select(
                    'wcp.date',
                    'vs.blood_pressure',
                    'vs.body_temperature',
                    'vs.pulse_rate',
                    'vs.respiratory_rate'
                )
                ->get();
        }

        if (!$selectedBeneficiary) {
            // Initialize statistics array and totals
            $healthStatistics = [];
            $totals = [
                'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0, 
                'male' => 0, 'female' => 0, 
                'single' => 0, 'married' => 0, 'widowed' => 0
            ];

            // Get beneficiaries filtered by municipality if selected
            $beneficiaryQuery = Beneficiary::where('beneficiary_status_id', 1);
            if ($selectedMunicipalityId) {
                $beneficiaryQuery->where('municipality_id', $selectedMunicipalityId);
            }
            $totalPopulation = $beneficiaryQuery->count();

            // Get category statistics and build matrix
            $categories = BeneficiaryCategory::all();
            foreach ($categories as $category) {
                $query = clone $beneficiaryQuery;
                $query->where('category_id', $category->category_id);
                $categoryCount = $query->count();
                
                if ($categoryCount > 0) {
                    // Initialize category statistics array
                    $healthStatistics[$category->category_name] = [
                        'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0,
                        'male' => 0, 'female' => 0, 
                        'single' => 0, 'married' => 0, 'widowed' => 0,
                        'percentage' => round(($categoryCount / $totalPopulation) * 100, 1)
                    ];
                    
                    // Get age distribution
                    $now = Carbon::now();
                    $query->chunk(100, function ($beneficiaries) use (&$healthStatistics, $category, $now, &$totals) {
                        foreach ($beneficiaries as $beneficiary) {
                            $birthday = Carbon::parse($beneficiary->birthday);
                            $age = $birthday->diffInYears($now);
                            
                            // Age group
                            if ($age >= 90) {
                                $healthStatistics[$category->category_name]['age_90_plus']++;
                                $totals['age_90_plus']++;
                            } elseif ($age >= 80) {
                                $healthStatistics[$category->category_name]['age_80_89']++;
                                $totals['age_80_89']++;
                            } elseif ($age >= 70) {
                                $healthStatistics[$category->category_name]['age_70_79']++;
                                $totals['age_70_79']++;
                            } else {
                                $healthStatistics[$category->category_name]['age_60_69']++;
                                $totals['age_60_69']++;
                            }
                            
                            // Gender
                            if ($beneficiary->gender === 'Male') {
                                $healthStatistics[$category->category_name]['male']++;
                                $totals['male']++;
                            } else if ($beneficiary->gender === 'Female') {
                                $healthStatistics[$category->category_name]['female']++;
                                $totals['female']++;
                            }
                            
                            // Civil status
                            if ($beneficiary->civil_status === 'Single') {
                                $healthStatistics[$category->category_name]['single']++;
                                $totals['single']++;
                            } else if ($beneficiary->civil_status === 'Married') {
                                $healthStatistics[$category->category_name]['married']++;
                                $totals['married']++;
                            } else if ($beneficiary->civil_status === 'Widowed') {
                                $healthStatistics[$category->category_name]['widowed']++;
                                $totals['widowed']++;
                            }
                        }
                    });
                }
            }
        }
        
        $careCategories = CareCategory::orderBy('care_category_name')->get();
        $careServicesSummary = [];
        
        // Query base - weekly care plans filtered by date range
        $wcpQuery = WeeklyCarePlan::whereBetween('date', [$startDate, $endDate]);
        
        // Filter by municipality if selected
        if ($selectedMunicipalityId) {
            $wcpQuery->whereHas('beneficiary', function($query) use ($selectedMunicipalityId) {
                $query->where('municipality_id', $selectedMunicipalityId);
            });
        }
        
        // Filter by beneficiary if selected
        if ($selectedBeneficiaryId) {
            $wcpQuery->where('beneficiary_id', $selectedBeneficiaryId);
        }
        
        // Get the filtered weekly care plan IDs
        $filteredWcpIds = $wcpQuery->pluck('weekly_care_plan_id')->toArray();
        
        // Prepare care services summary data
        foreach ($careCategories as $category) {
            $interventions = Intervention::where('care_category_id', $category->care_category_id)->get();
            $interventionData = [];
            $hasInterventions = false;
            
            foreach ($interventions as $intervention) {
                // Get implementation data for this intervention
                $implementations = DB::table('weekly_care_plan_interventions')
                    ->whereIn('weekly_care_plan_id', $filteredWcpIds)
                    ->where('intervention_id', $intervention->intervention_id)
                    ->count();
                    
                // Get total duration in minutes
                $totalMinutes = DB::table('weekly_care_plan_interventions')
                    ->whereIn('weekly_care_plan_id', $filteredWcpIds)
                    ->where('intervention_id', $intervention->intervention_id)
                    ->sum('duration_minutes');
                    
                // Only include interventions that were actually implemented
                if ($implementations > 0) {
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    
                    $interventionData[] = [
                        'description' => $intervention->intervention_description,
                        'implementations' => $implementations,
                        'hours' => $hours,
                        'minutes' => $minutes,
                        'formatted_duration' => $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' min' : '')
                    ];
                    
                    $hasInterventions = true;
                }
            }
            
            // Add to care services summary
            $careServicesSummary[$category->care_category_id] = [
                'category_name' => $category->care_category_name,
                'interventions' => $interventionData,
                'has_interventions' => $hasInterventions
            ];
        }

        $totalCareHours = 0;
        $totalCareMinutes = 0;

        // Loop through all categories and their interventions to sum up hours
        foreach ($careServicesSummary as $categoryData) {
            if ($categoryData['has_interventions']) {
                foreach ($categoryData['interventions'] as $intervention) {
                    $totalCareHours += $intervention['hours'];
                    $totalCareMinutes += $intervention['minutes'];
                }
            }
        }

        // Convert excess minutes to hours
        $additionalHours = floor($totalCareMinutes / 60);
        $totalCareHours += $additionalHours;
        $remainingMinutes = $totalCareMinutes % 60;

        // Format total care time
        $totalCareTime = $totalCareHours . ' hrs ' . ($remainingMinutes > 0 ? $remainingMinutes . ' min' : '');

        // Vital Signs Charts Data
        $bloodPressureData = [];
        $heartRateData = [];
        $respiratoryRateData = [];
        $temperatureData = [];
        $chartLabels = [];

        // Get vital signs based on filters (date range, municipality, beneficiary)
        $vitalSignsQuery = DB::table('vital_signs as vs')
        ->join('weekly_care_plans as wcp', 'vs.vital_signs_id', '=', 'wcp.vital_signs_id')
        ->whereBetween('wcp.date', [$startDate, $endDate]);

        // Apply municipality filter if selected
        if ($selectedMunicipalityId) {
            $vitalSignsQuery->join('beneficiaries as b', 'wcp.beneficiary_id', '=', 'b.beneficiary_id')
                        ->where('b.municipality_id', $selectedMunicipalityId);
        }

        // Apply beneficiary filter if selected
        if ($selectedBeneficiaryId) {
            $vitalSignsQuery->where('wcp.beneficiary_id', $selectedBeneficiaryId);
            
            // Handle different time ranges for individual beneficiary
            if ($selectedTimeRange === 'year') {
                // For yearly view, use hardcoded month labels for individual beneficiary
                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                // Initialize arrays with nulls for all months
                $bloodPressureData = array_fill(0, 12, null);
                $heartRateData = array_fill(0, 12, null);
                $respiratoryRateData = array_fill(0, 12, null);
                $temperatureData = array_fill(0, 12, null);
                
                // Get monthly data for individual beneficiary
                $vitalSigns = $vitalSignsQuery->select(
                    DB::raw('EXTRACT(MONTH FROM wcp.date) as month_num'),
                    DB::raw('AVG(CAST(SPLIT_PART(vs.blood_pressure, \'/\', 1) AS INTEGER)) as avg_systolic'),
                    DB::raw('AVG(vs.pulse_rate) as avg_pulse_rate'),
                    DB::raw('AVG(vs.respiratory_rate) as avg_respiratory_rate'),
                    DB::raw('AVG(vs.body_temperature) as avg_temperature')
                )
                ->where(DB::raw('EXTRACT(YEAR FROM wcp.date)'), '=', $selectedYear)
                ->groupBy('month_num')
                ->orderBy('month_num')
                ->get();
                
                // Fill data into the arrays at correct month positions
                foreach ($vitalSigns as $reading) {
                    $monthIndex = (int)$reading->month_num - 1; // Convert 1-12 to 0-11 index
                    if ($monthIndex >= 0 && $monthIndex < 12) {
                        $bloodPressureData[$monthIndex] = round($reading->avg_systolic, 1);
                        $heartRateData[$monthIndex] = round($reading->avg_pulse_rate, 1);
                        $respiratoryRateData[$monthIndex] = round($reading->avg_respiratory_rate, 1);
                        $temperatureData[$monthIndex] = round($reading->avg_temperature, 1);
                    }
                }
            } else if ($selectedTimeRange === 'months') {
                // Get daily data for the date range (instead of monthly aggregation)
                $vitalSigns = $vitalSignsQuery->select(
                    'wcp.date', // Use the actual date
                    DB::raw('AVG(CAST(SPLIT_PART(vs.blood_pressure, \'/\', 1) AS INTEGER)) as avg_systolic'),
                    DB::raw('AVG(vs.pulse_rate) as avg_pulse_rate'),
                    DB::raw('AVG(vs.respiratory_rate) as avg_respiratory_rate'),
                    DB::raw('AVG(vs.body_temperature) as avg_temperature')
                )
                ->groupBy('wcp.date') // Group by exact date
                ->orderBy('wcp.date') // Order by date chronologically
                ->get();
                
                // Process data to show exact dates
                foreach ($vitalSigns as $reading) {
                    $chartLabels[] = Carbon::parse($reading->date)->format('M d'); // Format as "Jan 05"
                    $bloodPressureData[] = round($reading->avg_systolic, 1);
                    $heartRateData[] = round($reading->avg_pulse_rate, 1);
                    $respiratoryRateData[] = round($reading->avg_respiratory_rate, 1);
                    $temperatureData[] = round($reading->avg_temperature, 1);
                }
            } else { // weeks
                // For weekly view (single month), show daily data points
                $vitalSigns = $vitalSignsQuery->select(
                    'wcp.date',
                    'vs.blood_pressure',
                    'vs.body_temperature',
                    'vs.pulse_rate',
                    'vs.respiratory_rate'
                )
                ->orderBy('wcp.date')
                ->get();
                
                // Process for daily data display
                foreach ($vitalSigns as $reading) {
                    $chartLabels[] = Carbon::parse($reading->date)->format('M d');
                    
                    // Extract systolic value from blood pressure (format: "120/80")
                    $bpParts = explode('/', $reading->blood_pressure);
                    $systolic = isset($bpParts[0]) ? (int)$bpParts[0] : 0;
                    
                    $bloodPressureData[] = $systolic;
                    $heartRateData[] = $reading->pulse_rate;
                    $respiratoryRateData[] = $reading->respiratory_rate;
                    $temperatureData[] = $reading->body_temperature;
                }
            }
        } else {
            // For aggregate view
            if ($selectedTimeRange === 'year') {
                // For yearly view, use hardcoded month labels
                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                // Initialize arrays with nulls for all months
                $bloodPressureData = array_fill(0, 12, null);
                $heartRateData = array_fill(0, 12, null);
                $respiratoryRateData = array_fill(0, 12, null);
                $temperatureData = array_fill(0, 12, null);
                
                // Get monthly averages
                $vitalSigns = $vitalSignsQuery->select(
                    DB::raw('EXTRACT(MONTH FROM wcp.date) as month_num'),
                    DB::raw('AVG(CAST(SPLIT_PART(vs.blood_pressure, \'/\', 1) AS INTEGER)) as avg_systolic'),
                    DB::raw('AVG(vs.body_temperature) as avg_temperature'),
                    DB::raw('AVG(vs.pulse_rate) as avg_pulse_rate'),
                    DB::raw('AVG(vs.respiratory_rate) as avg_respiratory_rate')
                )
                ->where(DB::raw('EXTRACT(YEAR FROM wcp.date)'), '=', $selectedYear)
                ->groupBy('month_num')
                ->orderBy('month_num')
                ->get();
                
                // Fill data into the arrays at correct month positions
                foreach ($vitalSigns as $reading) {
                    $monthIndex = (int)$reading->month_num - 1; // Convert 1-12 to 0-11 index
                    if ($monthIndex >= 0 && $monthIndex < 12) {
                        $bloodPressureData[$monthIndex] = round($reading->avg_systolic, 1);
                        $heartRateData[$monthIndex] = round($reading->avg_pulse_rate, 1);
                        $respiratoryRateData[$monthIndex] = round($reading->avg_respiratory_rate, 1);
                        $temperatureData[$monthIndex] = round($reading->avg_temperature, 1);
                    }
                }
            } else if ($selectedTimeRange === 'months') {
                // Get data for each day in the selected date range
                $vitalSigns = $vitalSignsQuery->select(
                    'wcp.date', // Use the actual date
                    DB::raw('AVG(CAST(SPLIT_PART(vs.blood_pressure, \'/\', 1) AS INTEGER)) as avg_systolic'),
                    DB::raw('AVG(vs.body_temperature) as avg_temperature'),
                    DB::raw('AVG(vs.pulse_rate) as avg_pulse_rate'),
                    DB::raw('AVG(vs.respiratory_rate) as avg_respiratory_rate')
                )
                ->groupBy('wcp.date') // Group by exact date
                ->orderBy('wcp.date') // Order by date chronologically
                ->get();
                
                // Process data to show exact dates
                foreach ($vitalSigns as $reading) {
                    $chartLabels[] = Carbon::parse($reading->date)->format('M d'); // Format as "Jan 05"
                    $bloodPressureData[] = round($reading->avg_systolic, 1);
                    $heartRateData[] = round($reading->avg_pulse_rate, 1);
                    $respiratoryRateData[] = round($reading->avg_respiratory_rate, 1);
                    $temperatureData[] = round($reading->avg_temperature, 1);
                }
            } else { // weeks
                // Get data for each day in the selected month
                $vitalSigns = $vitalSignsQuery->select(
                    DB::raw('wcp.date::date as date'),
                    DB::raw('AVG(CAST(SPLIT_PART(vs.blood_pressure, \'/\', 1) AS INTEGER)) as avg_systolic'),
                    DB::raw('AVG(vs.body_temperature) as avg_temperature'),
                    DB::raw('AVG(vs.pulse_rate) as avg_pulse_rate'),
                    DB::raw('AVG(vs.respiratory_rate) as avg_respiratory_rate')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
                // Process data for weekly view - show each date that has data
                foreach ($vitalSigns as $reading) {
                    $chartLabels[] = Carbon::parse($reading->date)->format('M d'); // Format as "Jan 01"
                    $bloodPressureData[] = round($reading->avg_systolic, 1);
                    $heartRateData[] = round($reading->avg_pulse_rate, 1);
                    $respiratoryRateData[] = round($reading->avg_respiratory_rate, 1);
                    $temperatureData[] = round($reading->avg_temperature, 1);
                }
            }
        }

        // Process medical conditions data for chart
        $medicalConditionStats = [];
        if (!$selectedBeneficiary) {
            // Start with beneficiary query for active beneficiaries with a general care plan
            $beneficiaryQuery = Beneficiary::where('beneficiary_status_id', 1)
                ->whereNotNull('general_care_plan_id');
            
            // Filter by municipality if selected
            if ($selectedMunicipalityId) {
                $beneficiaryQuery->where('municipality_id', $selectedMunicipalityId);
            }
            
            // Get beneficiaries with their health histories
            $beneficiaries = $beneficiaryQuery->with('generalCarePlan.healthHistory')->get();
            
            // Process medical conditions
            foreach ($beneficiaries as $beneficiary) {
                if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->healthHistory) {
                    $medicalConditions = json_decode($beneficiary->generalCarePlan->healthHistory->medical_conditions, true);
                    
                    if (is_array($medicalConditions)) {
                        foreach ($medicalConditions as $condition) {
                            if (!isset($medicalConditionStats[$condition])) {
                                $medicalConditionStats[$condition] = 0;
                            }
                            $medicalConditionStats[$condition]++;
                        }
                    }
                }
            }
            
            // Sort by count (descending)
            arsort($medicalConditionStats);
            
            // Take top 10 conditions
            $medicalConditionStats = array_slice($medicalConditionStats, 0, 10, true);
        }

        // Process illnesses data for chart
        $illnessStats = [];
        if (!$selectedBeneficiary) {
            // Get weekly care plans within date range and optional municipality filter
            $weeklyCarePlansQuery = WeeklyCarePlan::whereBetween('date', [$startDate, $endDate]);
            
            // Filter by municipality if selected
            if ($selectedMunicipalityId) {
                $weeklyCarePlansQuery->whereHas('beneficiary', function($query) use ($selectedMunicipalityId) {
                    $query->where('municipality_id', $selectedMunicipalityId);
                });
            }
            
            // Get weekly care plans
            $weeklyCarePlans = $weeklyCarePlansQuery->get();
            
            // Process illnesses
            foreach ($weeklyCarePlans as $wcPlan) {
                if ($wcPlan->illnesses) {
                    $illnesses = json_decode($wcPlan->illnesses, true);
                    
                    if (is_array($illnesses)) {
                        foreach ($illnesses as $illness) {
                            if (!isset($illnessStats[$illness])) {
                                $illnessStats[$illness] = 0;
                            }
                            $illnessStats[$illness]++;
                        }
                    }
                }
            }
            
            // Sort by count (descending)
            arsort($illnessStats);
            
            // Take top 10 illnesses
            $illnessStats = array_slice($illnessStats, 0, 10, true);
        }

         // At the end of the method, before the return statements:
        $returnData = compact(
            'beneficiaries',
            'municipalities',
            'availableYears',
            'selectedBeneficiaryId',
            'selectedMunicipalityId',
            'selectedTimeRange',
            'selectedMonth',
            'selectedStartMonth',
            'selectedEndMonth',
            'selectedYear',
            'dateRangeLabel',
            'selectedBeneficiary',
            'healthStatistics',
            'totalPopulation',
            'vitalSignsHistory',
            'totals',
            'careCategories',
            'careServicesSummary',
            'chartLabels',
            'bloodPressureData',
            'heartRateData',
            'respiratoryRateData',
            'temperatureData',
            'medicalConditionStats',
            'illnessStats',
            'totalCareTime'
        );
        
        // If this is for export, return the data
        if ($forExport) {
            return view('admin.healthMonitoring', $returnData);
        }
        
        // Otherwise render the view as usual
        return view('admin.healthMonitoring', $returnData);
    }

    public function careManagerIndex(Request $request)
    {
        // Use the exact same implementation as the admin index method
        $data = $this->index($request);
        
        // Just change the view that gets returned
        return view('careManager.healthMonitoring', $data->getData());
    }
}
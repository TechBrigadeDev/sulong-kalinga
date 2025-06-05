<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\GeneralCarePlan;
use App\Models\HealthHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PortalMedicationScheduleController extends Controller
{
    /**
     * Display the medication schedule
     */
    public function index()
    {
        try {
            $beneficiaryId = null;
            $userType = 'unknown';
            
            // Determine the authenticated user type
            if (Auth::guard('beneficiary')->check()) {
                $beneficiary = Auth::guard('beneficiary')->user();
                $beneficiaryId = $beneficiary->beneficiary_id;
                $userType = 'beneficiary';
            } elseif (Auth::guard('family')->check()) {
                $familyMember = Auth::guard('family')->user();
                $beneficiary = $familyMember->beneficiary;
                $beneficiaryId = $beneficiary->beneficiary_id;
                $userType = 'family';
            } else {
                return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
            }
            
            // Get beneficiary details
            $beneficiaryDetails = $this->getBeneficiaryDetails($beneficiaryId);
            
            // Get medication information
            $activeMedications = $this->getActiveMedications($beneficiaryId);
            $prnMedications = $this->getPRNMedications($beneficiaryId);
            
            // Get health information
            $healthConditions = $this->getHealthConditions($beneficiaryId);
            $immunizations = $this->getImmunizations($beneficiaryId); // Changed from currentIllnesses to immunizations
            
            // Get last updated date
            $lastUpdated = $this->getLastUpdated($beneficiaryId);
            
            // Return the appropriate view based on user type
            if ($userType === 'beneficiary') {
                return view('beneficiaryPortal.medicationSchedule', [
                    'beneficiary' => $beneficiaryDetails,
                    'activeMedications' => $activeMedications,
                    'prnMedications' => $prnMedications,
                    'healthConditions' => $healthConditions,
                    'immunizations' => $immunizations, // Changed from currentIllnesses to immunizations
                    'lastUpdated' => $lastUpdated
                ]);
            } else {
                return view('familyPortal.medicationSchedule', [
                    'beneficiary' => $beneficiaryDetails,
                    'activeMedications' => $activeMedications,
                    'prnMedications' => $prnMedications,
                    'healthConditions' => $healthConditions,
                    'immunizations' => $immunizations, // Changed from currentIllnesses to immunizations
                    'lastUpdated' => $lastUpdated
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error displaying medication schedule: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'An error occurred while loading the medication schedule. Please try again later.');
        }
    }
    
    /**
     * Get beneficiary details
     */
    private function getBeneficiaryDetails($beneficiaryId)
    {
        $beneficiary = Beneficiary::findOrFail($beneficiaryId);
        
        // Calculate age dynamically from birthday
        $age = Carbon::parse($beneficiary->birthday)->age;
        
        return [
            'id' => $beneficiary->beneficiary_id,
            'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name,
            'age' => $age, // Age calculated dynamically
            'gender' => $beneficiary->gender, // Gender from database
            'photo' => $beneficiary->photo
        ];
    }
    
    /**
     * Get active medications (not "as needed")
     */
    private function getActiveMedications($beneficiaryId)
    {
        $medications = MedicationSchedule::where('beneficiary_id', $beneficiaryId)
            ->where('as_needed', false)
            ->where('status', 'active')
            ->where(function($query) {
                // Only include medications that haven't ended yet or don't have an end date
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->format('Y-m-d'));
            })
            ->with(['creator', 'updater'])
            ->orderBy('medication_name')
            ->get();
        
        return $this->formatMedications($medications);
    }
    
    /**
     * Get PRN medications (as needed)
     */
    private function getPRNMedications($beneficiaryId)
    {
        $medications = MedicationSchedule::where('beneficiary_id', $beneficiaryId)
            ->where('as_needed', true)
            ->where('status', 'active')
            ->where(function($query) {
                // Only include medications that haven't ended yet or don't have an end date
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->format('Y-m-d'));
            })
            ->with(['creator', 'updater'])
            ->orderBy('medication_name')
            ->get();
        
        return $this->formatMedications($medications);
    }
    
    /**
     * Format medication data for display
     */
    private function formatMedications($medications)
    {
        $formattedMedications = [];
        
        foreach ($medications as $med) {
            // Create structured dosage time information
            $dosageTimes = [];
            $times = []; // Add this line to define the times array
            
            if ($med->morning_time) {
                $times[] = Carbon::parse($med->morning_time)->format('g:i A');
                $dosageTimes[] = [
                    'time' => Carbon::parse($med->morning_time)->format('g:i A') . ' (Morning)',
                    'with_food' => $med->with_food_morning,
                    'period' => 'morning'
                ];
            }
            
            if ($med->noon_time) {
                $times[] = Carbon::parse($med->noon_time)->format('g:i A');
                $dosageTimes[] = [
                    'time' => Carbon::parse($med->noon_time)->format('g:i A') . ' (Noon)',
                    'with_food' => $med->with_food_noon,
                    'period' => 'noon'
                ];
            }
            
            if ($med->evening_time) {
                $times[] = Carbon::parse($med->evening_time)->format('g:i A');
                $dosageTimes[] = [
                    'time' => Carbon::parse($med->evening_time)->format('g:i A') . ' (Evening)',
                    'with_food' => $med->with_food_evening,
                    'period' => 'evening'
                ];
            }
            
            if ($med->night_time) {
                $times[] = Carbon::parse($med->night_time)->format('g:i A');
                $dosageTimes[] = [
                    'time' => Carbon::parse($med->night_time)->format('g:i A') . ' (Night)',
                    'with_food' => $med->with_food_night,
                    'period' => 'night'
                ];
            }
            
            // Try to find matching medication in the medications table
            $frequencyText = '';
            $adminInstructions = '';
            
            // Get the beneficiary's general care plan ID first
            if ($med->beneficiary && $med->beneficiary->general_care_plan_id) {
                $medication = \App\Models\Medication::where('general_care_plan_id', $med->beneficiary->general_care_plan_id)
                    ->where('medication', 'like', '%' . $med->medication_name . '%')
                    ->first();
                
                if ($medication) {
                    $frequencyText = $medication->frequency;
                    $adminInstructions = $medication->administration_instructions;
                }
            }
            
            // Use defaults if no matching medication found
            if (empty($frequencyText)) {
                $frequencyText = $this->getDefaultFrequency($times, $med->as_needed);
            }
            
            if (empty($adminInstructions)) {
                $adminInstructions = $this->getDefaultAdminInstructions($med);
            }
            
            // Format the medication for the view
            $formattedMedications[] = [
                'id' => $med->medication_schedule_id,
                'name' => $med->medication_name,
                'dosage' => $med->dosage,
                'type' => ucfirst($med->medication_type),
                'times' => $times,
                'times_string' => implode(', ', $times),
                'start_date' => Carbon::parse($med->start_date)->format('d M Y'),
                'administration' => $adminInstructions,
                'special_instructions' => $med->special_instructions,
                'recorded_by' => ($med->creator ? $med->creator->first_name . ' ' . $med->creator->last_name : "Unknown"),
                'remaining' => $med->dosage, // Using dosage for remaining pills
                'frequency' => $frequencyText, // Use frequency from medications table
                'as_needed' => $med->as_needed,
                'dosage_times' => $dosageTimes
            ];
        }
        
        return $formattedMedications;
    }

    /**
     * Get default frequency string if not available in medications table
     */
    private function getDefaultFrequency($times, $asNeeded) {
        if ($asNeeded) {
            return 'As needed';
        }
        
        switch (count($times)) {
            case 1:
                return 'Once daily';
            case 2:
                return 'Twice daily';
            case 3:
                return 'Three times daily';
            case 4:
                return 'Four times daily';
            default:
                return 'As directed';
        }
    }

    /**
     * Get default administration instructions if not available in medications table
     */
    private function getDefaultAdminInstructions($med) {
        $instructions = [];
        
        // Add route of administration based on medication type
        switch (strtolower($med->medication_type)) {
            case 'tablet':
            case 'capsule':
                $instructions[] = 'Take by mouth';
                break;
            case 'injection':
                $instructions[] = 'Injectable as directed';
                break;
            case 'liquid':
                $instructions[] = 'Take orally with measuring cup';
                break;
            case 'topical':
                $instructions[] = 'Apply to affected area';
                break;
            default:
                $instructions[] = 'Use as directed';
        }
        
        // Add food requirements
        $withFood = [];
        if ($med->with_food_morning && $med->morning_time) $withFood[] = 'morning';
        if ($med->with_food_noon && $med->noon_time) $withFood[] = 'noon';
        if ($med->with_food_evening && $med->evening_time) $withFood[] = 'evening';
        if ($med->with_food_night && $med->night_time) $withFood[] = 'night';
        
        if (count($withFood) > 0) {
            if (count($withFood) == count(array_filter([$med->morning_time, $med->noon_time, $med->evening_time, $med->night_time]))) {
                $instructions[] = 'Take with food';
            } else {
                $instructions[] = 'Take with food in the ' . implode(', ', $withFood);
            }
        } else if (!$med->as_needed) {
            $instructions[] = 'Take on empty stomach';
        }
        
        return implode(', ', $instructions);
    }


   /**
     * Get health conditions for the beneficiary
     */
    private function getHealthConditions($beneficiaryId)
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        $conditions = [];
        
        if ($beneficiary && $beneficiary->general_care_plan_id) {
            $healthHistory = \App\Models\HealthHistory::where('general_care_plan_id', $beneficiary->general_care_plan_id)->first();
            
            if ($healthHistory && $healthHistory->medical_conditions) {
                // Try to parse as JSON first
                if ($this->isJson($healthHistory->medical_conditions)) {
                    $medicalConditions = json_decode($healthHistory->medical_conditions, true);
                    
                    // Handle structured JSON format
                    if (is_array($medicalConditions)) {
                        foreach ($medicalConditions as $condition) {
                            if (is_array($condition) && isset($condition['name'])) {
                                $conditions[] = [
                                    'name' => $condition['name'],
                                ];
                            } elseif (is_string($condition)) {
                                $conditions[] = [
                                    'name' => $condition,
                                ];
                            }
                        }
                    }
                } else {
                    // Handle as plain text - split by commas or line breaks
                    $textConditions = preg_split("/[,;\n]+/", $healthHistory->medical_conditions);
                    foreach ($textConditions as $condition) {
                        $condition = trim($condition);
                        if (!empty($condition)) {
                            $conditions[] = [
                                'name' => $condition,
                            ];
                        }
                    }
                }
            }
        }
        
        return $conditions;
    }
    
    /**
     * Get the last update timestamp
     */
    private function getLastUpdated($beneficiaryId)
    {
        $latestMedication = MedicationSchedule::where('beneficiary_id', $beneficiaryId)
            ->latest('updated_at')
            ->first();
            
        if ($latestMedication) {
            return [
                'date' => $latestMedication->updated_at->format('F d, Y'),
                'time' => $latestMedication->updated_at->format('g:i A')
            ];
        }
        
        // Default if no medications found
        return [
            'date' => now()->format('F d, Y'),
            'time' => now()->format('g:i A')
        ];
    }
    
    /**
     * Infer the medical condition based on medication name
     */
    private function getMedicationCondition($medicationName)
    {
        $medicationName = strtolower($medicationName);
        
        if (strpos($medicationName, 'metformin') !== false || 
            strpos($medicationName, 'insulin') !== false || 
            strpos($medicationName, 'glipizide') !== false) {
            return 'For Type 2 Diabetes';
        }
        
        if (strpos($medicationName, 'lisinopril') !== false || 
            strpos($medicationName, 'amlodipine') !== false || 
            strpos($medicationName, 'hydrochlorothiazide') !== false ||
            strpos($medicationName, 'losartan') !== false) {
            return 'For Hypertension';
        }
        
        if (strpos($medicationName, 'atorvastatin') !== false || 
            strpos($medicationName, 'simvastatin') !== false || 
            strpos($medicationName, 'rosuvastatin') !== false || 
            strpos($medicationName, 'statin') !== false) {
            return 'For Hyperlipidemia';
        }
        
        if (strpos($medicationName, 'cetirizine') !== false || 
            strpos($medicationName, 'loratadine') !== false || 
            strpos($medicationName, 'allegra') !== false || 
            strpos($medicationName, 'zyrtec') !== false || 
            strpos($medicationName, 'claritin') !== false) {
            return 'For Seasonal Allergies';
        }
        
        if (strpos($medicationName, 'ibuprofen') !== false || 
            strpos($medicationName, 'naproxen') !== false || 
            strpos($medicationName, 'acetaminophen') !== false || 
            strpos($medicationName, 'tylenol') !== false || 
            strpos($medicationName, 'advil') !== false) {
            return 'For Pain/Inflammation';
        }
        
        return 'General Medication';
    }
    
    /**
     * Check if a string is valid JSON
     */
    private function isJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Get immunization records for the beneficiary
     */
    private function getImmunizations($beneficiaryId)
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        $immunizations = [];
        
        if ($beneficiary && $beneficiary->general_care_plan_id) {
            $healthHistory = \App\Models\HealthHistory::where('general_care_plan_id', $beneficiary->general_care_plan_id)->first();
            
            if ($healthHistory && $healthHistory->immunizations) {
                // Try to parse as JSON first
                if ($this->isJson($healthHistory->immunizations)) {
                    $immunizationData = json_decode($healthHistory->immunizations, true);
                    
                    // Handle structured JSON format
                    if (is_array($immunizationData)) {
                        foreach ($immunizationData as $immunization) {
                            if (is_array($immunization) && isset($immunization['name'])) {
                                $immunizations[] = [
                                    'name' => $immunization['name'],
                                    'details' => isset($immunization['date']) ? 
                                        'Administered on ' . $immunization['date'] : 
                                        (isset($immunization['details']) ? $immunization['details'] : 'No date information')
                                ];
                            } elseif (is_string($immunization)) {
                                $immunizations[] = [
                                    'name' => $immunization,
                                    'details' => 'Vaccination record on file'
                                ];
                            }
                        }
                    }
                } else {
                    // Handle as plain text - split by commas or line breaks
                    $textImmunizations = preg_split("/[,;\n]+/", $healthHistory->immunizations);
                    foreach ($textImmunizations as $immunization) {
                        $immunization = trim($immunization);
                        if (!empty($immunization)) {
                            $immunizations[] = [
                                'name' => $immunization,
                                'details' => 'Vaccination record on file'
                            ];
                        }
                    }
                }
            }
        }
        
        // If no immunizations found, add default
        if (empty($immunizations)) {
            $immunizations[] = [
                'name' => 'No Immunization Records',
                'details' => 'No immunization records currently available'
            ];
        }
        
        return $immunizations;
    }

    // Creates a method to find the next medication scheduled for a beneficiary
    // Looks for medications due today after the current time
    // If none are found today, checks for the first medication scheduled tomorrow
    // Shows medication name, dosage, time, and whether it should be taken with food
    // Indicates if the medication is due tomorrow
    // Shows a fallback message if no medications are scheduled
    
    /**
     * Get the next medication due for a beneficiary
     * 
     * @param int|null $beneficiaryId Optional beneficiary ID (for family members)
     * @return array|null Returns medication details or null if none found
     */
    public function getNextMedication($beneficiaryId = null)
    {
        try {
            if (Auth::guard('beneficiary')->check()) {
                $beneficiaryId = Auth::guard('beneficiary')->id();
            } elseif (Auth::guard('family')->check() && !$beneficiaryId) {
                $familyMember = Auth::guard('family')->user();
                $beneficiaryId = $familyMember->related_beneficiary_id;
            }

            if (!$beneficiaryId) {
                return null;
            }
            
            // Get the current time
            $now = Carbon::now();
            $currentTime = $now->format('H:i:s');
            $today = $now->format('Y-m-d');
            
            // Get all active medications for today
            $medications = MedicationSchedule::where('beneficiary_id', $beneficiaryId)
                ->where('status', 'active')
                ->where(function($query) use ($today) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $today);
                })
                ->get();
                
            // No medications found
            if ($medications->isEmpty()) {
                return null;
            }
            
            $nextMedication = null;
            $earliestTime = null;
            
            // Process each medication to find the next due time
            foreach ($medications as $med) {
                $times = [
                    'morning' => $med->morning_time ? $med->morning_time : null,
                    'noon' => $med->noon_time ? $med->noon_time : null,
                    'evening' => $med->evening_time ? $med->evening_time : null,
                    'night' => $med->night_time ? $med->night_time : null
                ];
                
                foreach ($times as $period => $time) {
                    if (!$time) continue;
                    
                    // Convert to Carbon for easier comparison
                    $timeObj = Carbon::parse($time);
                    $timeStr = $timeObj->format('H:i:s');
                    
                    // Only consider times that are later than current time
                    if ($timeStr > $currentTime) {
                        // If this is the first time we found or is earlier than what we found before
                        if ($earliestTime === null || $timeStr < $earliestTime) {
                            $earliestTime = $timeStr;
                            $nextMedication = $med;
                            $nextPeriod = $period;
                        }
                    }
                }
            }
            
            // Found medication for today
            if ($nextMedication && $earliestTime) {
                return [
                    'name' => $nextMedication->medication_name,
                    'dosage' => $nextMedication->dosage,
                    'time' => Carbon::parse($earliestTime)->format('g:i A'),
                    'period' => $nextPeriod,
                    'day' => 'today',
                    'with_food' => $this->checkWithFood($nextMedication, $nextPeriod),
                    'medication_type' => $nextMedication->medication_type
                ];
            }
            
            // If no medication found for today, check for tomorrow's first medication
            $tomorrowMedication = $this->getFirstMedicationForTomorrow($beneficiaryId);
            if ($tomorrowMedication) {
                return $tomorrowMedication;
            }
            
            // If we couldn't find any upcoming medication
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting next medication: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'beneficiary_id' => $beneficiaryId
            ]);
            return null;
        }
    }

    /**
     * Get first medication scheduled for tomorrow
     */
    private function getFirstMedicationForTomorrow($beneficiaryId)
    {
        try {
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');
            
            // Get all active medications
            $medications = MedicationSchedule::where('beneficiary_id', $beneficiaryId)
                ->where('status', 'active')
                ->where(function($query) use ($tomorrow) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $tomorrow);
                })
                ->get();
                
            if ($medications->isEmpty()) {
                return null;
            }
            
            $earliestMed = null;
            $earliestTime = null;
            $earliestPeriod = null;
            
            // Find the earliest time among all medications
            foreach ($medications as $med) {
                $times = [
                    'morning' => $med->morning_time ? $med->morning_time : null,
                    'noon' => $med->noon_time ? $med->noon_time : null,
                    'evening' => $med->evening_time ? $med->evening_time : null,
                    'night' => $med->night_time ? $med->night_time : null
                ];
                
                foreach ($times as $period => $time) {
                    if (!$time) continue;
                    
                    $timeStr = Carbon::parse($time)->format('H:i:s');
                    
                    if ($earliestTime === null || $timeStr < $earliestTime) {
                        $earliestTime = $timeStr;
                        $earliestMed = $med;
                        $earliestPeriod = $period;
                    }
                }
            }
            
            if ($earliestMed && $earliestTime) {
                return [
                    'name' => $earliestMed->medication_name,
                    'dosage' => $earliestMed->dosage,
                    'time' => Carbon::parse($earliestTime)->format('g:i A'),
                    'period' => $earliestPeriod,
                    'day' => 'tomorrow',
                    'with_food' => $this->checkWithFood($earliestMed, $earliestPeriod),
                    'medication_type' => $earliestMed->medication_type
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting tomorrow medication: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if medication should be taken with food for a given period
     * 
     * @param MedicationSchedule $medication The medication record
     * @param string $period The period (morning, noon, evening, night)
     * @return bool Whether the medication should be taken with food
     */
    private function checkWithFood($medication, $period)
    {
        switch ($period) {
            case 'morning':
                return $medication->with_food_morning;
            case 'noon':
                return $medication->with_food_noon;
            case 'evening':
                return $medication->with_food_evening;
            case 'night':
                return $medication->with_food_night;
            default:
                return false;
        }
    }

    
}
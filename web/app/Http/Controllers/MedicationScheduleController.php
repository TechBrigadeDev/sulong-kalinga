<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\HealthHistory;
use App\Models\GeneralCarePlan;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MedicationScheduleController extends Controller
{
    /**
     * Display the medication schedule management page based on user role
     */
    public function index(Request $request)
    {
        // Get current user and role
        $user = Auth::user();
        $role = $user->role_id;
        
        // Determine role name for routing and views
        $roleName = 'admin';
        if ($role === 2) {
            $roleName = 'careManager';
        } elseif ($role === 3) {
            $roleName = 'careWorker';
        }

        // Apply filters
        $query = MedicationSchedule::with([
            'beneficiary', 
            'beneficiary.generalCarePlan.healthHistory',
            'creator',
            'updater'
        ]);

        // Filter by search term (beneficiary name or medication name)
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('medication_name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('beneficiary', function($q) use ($searchTerm) {
                      $q->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$searchTerm}%"]);
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by time period
        if ($request->has('period') && $request->period && $request->period !== 'all') {
            switch ($request->period) {
                case 'morning':
                    $query->whereNotNull('morning_time');
                    break;
                case 'afternoon':
                    $query->whereNotNull('noon_time');
                    break;
                case 'evening':
                    $query->whereNotNull('evening_time');
                    break;
                case 'night':
                    $query->whereNotNull('night_time');
                    break;
                case 'as_needed':
                    $query->where('as_needed', true);
                    break;
            }
        }

        // Get all beneficiaries for the dropdown with their health history for allergies
        $beneficiaries = Beneficiary::with(['generalCarePlan.healthHistory'])->orderBy('first_name')->get();
        
        // Format beneficiary data for the dropdown with health info
        $formattedBeneficiaries = [];
        foreach ($beneficiaries as $beneficiary) {
            $id = $beneficiary->beneficiary_id;
            $name = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            $allergies = null;
            
            if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->healthHistory) {
                $healthHistory = $beneficiary->generalCarePlan->healthHistory;
                if ($healthHistory->allergies) {
                    $allergies = $healthHistory->allergies;
                    // Convert JSON string to array if needed
                    if (is_string($allergies) && $this->isJson($allergies)) {
                        $allergies = json_decode($allergies, true);
                        $allergies = is_array($allergies) ? implode(", ", $allergies) : $allergies;
                    }
                }
            }

            $formattedBeneficiaries[] = [
                'id' => $id,
                'name' => $name,
                'allergies' => $allergies,
                // Include additional health data that might be useful
                'medical_conditions' => $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->healthHistory 
                    ? $beneficiary->generalCarePlan->healthHistory->medical_conditions 
                    : null,
                'immunizations' => $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->healthHistory 
                    ? $beneficiary->generalCarePlan->healthHistory->immunizations 
                    : null
            ];
        }
        
        // Get paginated results
        $medicationSchedules = $query->orderBy('created_at', 'desc')->paginate(10);

        // Format health information for each medication schedule
        foreach ($medicationSchedules as $schedule) {
            // Capitalize medication type
            $schedule->medication_type = ucfirst($schedule->medication_type);
            
            // Format health history data if available
            if ($schedule->beneficiary && $schedule->beneficiary->generalCarePlan && 
                $schedule->beneficiary->generalCarePlan->healthHistory) {
                
                $healthHistory = $schedule->beneficiary->generalCarePlan->healthHistory;
                
                // Format conditions
                if ($healthHistory->medical_conditions) {
                    $conditions = json_decode($healthHistory->medical_conditions, true);
                    $healthHistory->formatted_conditions = is_array($conditions) ? 
                        implode(", ", $conditions) : $healthHistory->medical_conditions;
                }
                
                // Format immunizations
                if ($healthHistory->immunizations) {
                    $immunizations = json_decode($healthHistory->immunizations, true);
                    $healthHistory->formatted_immunizations = is_array($immunizations) ? 
                        implode(", ", $immunizations) : $healthHistory->immunizations;
                }
                
                // Format allergies
                if ($healthHistory->allergies) {
                    $allergies = json_decode($healthHistory->allergies, true);
                    $healthHistory->formatted_allergies = is_array($allergies) ? 
                        implode(", ", $allergies) : $healthHistory->allergies;
                }
            }
        }

        // Return the appropriate view based on user role
        return view("$roleName.{$roleName}MedicationSchedule", [
            'medicationSchedules' => $medicationSchedules,
            'beneficiaries' => $formattedBeneficiaries,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'periodFilter' => $request->period ?? 'all'
        ]);
    }
    
    /**
     * Store a newly created medication schedule in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'medication_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Reject if medication name is purely numeric
                    if (is_numeric($value)) {
                        $fail('The medication name cannot be purely numeric.');
                    }
                },
            ],
            'dosage' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    // Dosage should not be purely numeric - must include units
                    if (is_numeric($value)) {
                        $fail('The dosage must include units (e.g., 500mg, 10ml).');
                    }
                },
            ],
            'medication_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'special_instructions' => 'nullable|string',
        ]);
        
        // Custom validation for schedule times
        $validator->after(function ($validator) use ($request) {
            if (!$request->has('as_needed') && 
                !$request->has('morning_time') && 
                !$request->has('noon_time') && 
                !$request->has('evening_time') && 
                !$request->has('night_time')) {
                $validator->errors()->add('schedule_time', 'At least one schedule time or "As Needed" must be selected.');
            }
        });
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_modal', true);  // This will tell the page to show the modal
        }
        
        // Get the current user
        $user = Auth::user();
        
        // Create the medication schedule
        $medicationSchedule = new MedicationSchedule();
        $medicationSchedule->beneficiary_id = $request->beneficiary_id;
        $medicationSchedule->medication_name = $request->medication_name;
        $medicationSchedule->dosage = $request->dosage;
        $medicationSchedule->medication_type = strtolower($request->medication_type);
        $medicationSchedule->special_instructions = $request->special_instructions;
        $medicationSchedule->start_date = $request->start_date;
        $medicationSchedule->end_date = $request->end_date;
        $medicationSchedule->status = 'active';
        $medicationSchedule->created_by = $user->id;
        $medicationSchedule->updated_by = $user->id;
        
        // Set schedule times
        $medicationSchedule->as_needed = $request->has('as_needed');
        
        // Only set time fields if not "as needed"
        if (!$medicationSchedule->as_needed) {
            if ($request->has('morning_time')) {
                $medicationSchedule->morning_time = $request->morning_time;
                $medicationSchedule->with_food_morning = $request->has('with_food_morning');
            }
            
            if ($request->has('noon_time')) {
                $medicationSchedule->noon_time = $request->noon_time;
                $medicationSchedule->with_food_noon = $request->has('with_food_noon');
            }
            
            if ($request->has('evening_time')) {
                $medicationSchedule->evening_time = $request->evening_time;
                $medicationSchedule->with_food_evening = $request->has('with_food_evening');
            }
            
            if ($request->has('night_time')) {
                $medicationSchedule->night_time = $request->night_time;
                $medicationSchedule->with_food_night = $request->has('with_food_night');
            }
        }
        
        // Save the medication schedule
        $medicationSchedule->save();
        
        // Send notifications
        $this->sendMedicationScheduleNotifications($medicationSchedule);
        
        // Redirect with success message
        return redirect()->back()->with('success', 'Medication schedule created successfully!');
    }
    
    /**
     * Send notifications about the new medication schedule to relevant users.
     */
    private function sendMedicationScheduleNotifications($medicationSchedule)
    {
        $beneficiary = Beneficiary::find($medicationSchedule->beneficiary_id);
        if (!$beneficiary) {
            return;
        }
        
        $beneficiaryName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
        $medicationName = $medicationSchedule->medication_name;
        $dosage = $medicationSchedule->dosage;
        
        // Generate schedule information for the notification
        $scheduleInfo = $this->getScheduleInfoForNotification($medicationSchedule);
        
        // Create notification message
        $messageTitle = 'New Medication Schedule';
        $message = "A new medication schedule has been created for {$beneficiaryName}: {$medicationName} ({$dosage}). {$scheduleInfo}";
        
        // 1. Notify the beneficiary
        $this->createNotification($beneficiary->beneficiary_id, 'beneficiary', $messageTitle, $message);
        
        // 2. Notify all family members related to the beneficiary
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
            $this->createNotification($familyMember->family_member_id, 'family_member', $messageTitle, $message);
        }
        
        // 3. Notify the care worker assigned to the beneficiary (if they exist and are not the creator)
        if ($beneficiary->generalCarePlan) {
            $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
            // Only notify if the care worker exists and is not the one creating the medication schedule
            if ($careWorkerId && $careWorkerId != $medicationSchedule->created_by) {
                $this->createNotification($careWorkerId, 'cose_staff', $messageTitle, $message);
            }
        }
    }
    
    /**
     * Create a notification record.
     */
    private function createNotification($userId, $userType, $title, $message)
    {
        Notification::create([
            'user_id' => $userId,
            'user_type' => $userType,
            'message_title' => $title,
            'message' => $message,
            'date_created' => now(),
            'is_read' => false
        ]);
    }
    
    /**
     * Generate a human-readable schedule description for notifications.
     */
    private function getScheduleInfoForNotification($medicationSchedule)
    {
        if ($medicationSchedule->as_needed) {
            return "To be taken as needed.";
        }
        
        $times = [];
        
        if ($medicationSchedule->morning_time) {
            $time = Carbon::parse($medicationSchedule->morning_time)->format('g:i A');
            $times[] = "morning at {$time}" . ($medicationSchedule->with_food_morning ? ' with food' : '');
        }
        
        if ($medicationSchedule->noon_time) {
            $time = Carbon::parse($medicationSchedule->noon_time)->format('g:i A');
            $times[] = "afternoon at {$time}" . ($medicationSchedule->with_food_noon ? ' with food' : '');
        }
        
        if ($medicationSchedule->evening_time) {
            $time = Carbon::parse($medicationSchedule->evening_time)->format('g:i A');
            $times[] = "evening at {$time}" . ($medicationSchedule->with_food_evening ? ' with food' : '');
        }
        
        if ($medicationSchedule->night_time) {
            $time = Carbon::parse($medicationSchedule->night_time)->format('g:i A');
            $times[] = "night at {$time}" . ($medicationSchedule->with_food_night ? ' with food' : '');
        }
        
        if (empty($times)) {
            return "No specific schedule times set.";
        }
        
        if (count($times) == 1) {
            return "To be taken in the " . $times[0] . ".";
        }
        
        $lastTime = array_pop($times);
        return "To be taken in the " . implode(', ', $times) . " and " . $lastTime . ".";
    }
    
    /**
     * Check if a string is valid JSON.
     */
    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
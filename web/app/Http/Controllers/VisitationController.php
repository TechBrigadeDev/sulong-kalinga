<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\VisitationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\VisitationOccurrence;
use App\Models\VisitationArchive;
use App\Models\VisitationException;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\RecurringPattern;
use App\Models\Notification;
use App\Models\GeneralCarePlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitationController extends Controller
{
    /**
     * Display the care worker appointments page with appropriate view based on user role
     */
    public function index(Request $request)
    {
        // Run status update whenever the page loads
        $this->updatePastAppointmentsStatus();

        $user = Auth::user();
        $viewPath = $this->getViewPathForRole($user);
        
        // Process query filters for beneficiary search
        $query = Beneficiary::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }
        
        // Get care workers for admin/care manager views
        $careWorkers = [];
        if ($user->role_id <= 2) { // Admin or Care Manager
            $careWorkers = User::where('role_id', 3)->get(); // Care Workers
        }
        // For care workers, show only their own appointments
        elseif ($user->isCareWorker()) {
            $careWorkers = User::where('id', $user->id)->get();
        }
        
        return view($viewPath, [
            'careWorkers' => $careWorkers,
        ]);
    }
    
    /**
     * Get visitations for the calendar view using the occurrence-based approach
     */
    public function getVisitations(Request $request)
    {

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        // Set a reasonable timeout
        set_time_limit(30); 
        
        $viewType = $request->input('view_type', 'dayGridMonth');
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $user = Auth::user();
        $today = Carbon::today();
        
        // For month view, limit to 3 months of data at most
        if ($viewType === 'dayGridMonth') {
            $calendarStartDate = Carbon::parse($startDate);
            $calendarEndDate = Carbon::parse($endDate);
            
            // Limit date range to prevent timeout
            $maxEndDate = $calendarStartDate->copy()->addMonths(3);
            if ($calendarEndDate->gt($maxEndDate)) {
                $calendarEndDate = $maxEndDate;
                $endDate = $maxEndDate->format('Y-m-d');
            }
        }

        try {
            // APPROACH 1: For visitations with occurrences
            $occurrenceQuery = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
                ->whereBetween('occurrence_date', [$startDate, $endDate]);
                
            // APPROACH 2: For non-recurring visitations without occurrences yet (transitional)
            // FIXED: using doesntHave('occurrences') instead of whereNull('occurrences')
            $visitations = Visitation::with(['beneficiary', 'careWorker', 'recurringPattern', 'exceptions'])
                ->doesntHave('occurrences')  // CORRECTED LINE
                ->where(function($query) use ($startDate, $endDate) {
                    // Direct date match
                    $query->whereBetween('visitation_date', [$startDate, $endDate]);
                    
                    // Or recurring appointment that might have occurrences in our range
                    $query->orWhereHas('recurringPattern', function($q) use ($startDate) {
                        // Get recurring appointments where:
                        // 1. Start date is before our end range
                        // 2. Either has no end date, or end date is after our start range
                        $q->where(function($dateQuery) use ($startDate) {
                            $dateQuery->whereNull('recurrence_end')
                                    ->orWhere('recurrence_end', '>=', $startDate);
                        });
                    });
                });
            
            // Filter by role
            if ($user->isCareWorker()) {
                $occurrenceQuery->whereHas('visitation', function($q) use ($user) {
                    $q->where('care_worker_id', $user->id);
                });
                
                $visitations->where('care_worker_id', $user->id);
            } 
            /*elseif ($user->isCareManager()) {
                // Care managers see visitations for care workers they manage
                $careWorkerIds = User::where('assigned_care_manager_id', $user->id)->pluck('id');
                
                $occurrenceQuery->whereHas('visitation', function($q) use ($careWorkerIds) {
                    $q->whereIn('care_worker_id', $careWorkerIds);
                });
                
                $visitations->whereIn('care_worker_id', $careWorkerIds);
            }*/ 
            // Give the two care managers mutual access to all appointments
            
            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                
                $occurrenceQuery->whereHas('visitation', function($q) use ($search) {
                    $q->whereHas('beneficiary', function($bq) use ($search) {
                        // Use ILIKE for case-insensitive search in PostgreSQL
                        $bq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ILIKE', "%{$search}%");
                    })->orWhereHas('careWorker', function($cq) use ($search) {
                        $cq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ILIKE', "%{$search}%");
                    });
                });
                
                // Similarly update the visitations query for search
                $visitations->where(function($q) use ($search) {
                    $q->whereHas('beneficiary', function($bq) use ($search) {
                        $bq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ILIKE', "%{$search}%");
                    })->orWhereHas('careWorker', function($cq) use ($search) {
                        $cq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ILIKE', "%{$search}%");
                    });
                });
            }
            
            // Apply care worker filter if provided
            if ($request->has('care_worker_id') && !empty($request->care_worker_id)) {
                $occurrenceQuery->whereHas('visitation', function($q) use ($request) {
                    $q->where('care_worker_id', $request->care_worker_id);
                });
                
                $visitations->where('care_worker_id', $request->care_worker_id);
            }
            
            // Execute queries with limits to prevent timeouts
            $occurrences = $occurrenceQuery->limit(1000)->get();
            $visitations = $visitations->limit(500)->get();
            
            // DEBUG: Log the query counts
            \Log::info('Visitation queries executed', [
                'occurrences_count' => $occurrences->count(),
                'visitations_count' => $visitations->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            $events = [];
            
            // Process occurrences from the occurrences table
            foreach ($occurrences as $occurrence) {
                // Skip if the parent visitation is missing
                if (!$occurrence->visitation) continue;
                
                $visitation = $occurrence->visitation;
                
                // Create title
                $title = $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name;
                if ($user->role_id <= 2) { // Admin or care manager
                    $title .= ' (' . $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name . ')';
                }
                
                // Build the event object
                $event = [
                    'id' => 'occ-' . $occurrence->occurrence_id,
                    'title' => $title,
                    'start' => $occurrence->occurrence_date . 
                            ($visitation->is_flexible_time ? '' : 'T' . Carbon::parse($occurrence->start_time)->format('H:i:s')),
                    'end' => $occurrence->occurrence_date . 
                            ($visitation->is_flexible_time ? '' : 'T' . Carbon::parse($occurrence->end_time)->format('H:i:s')),
                    'backgroundColor' => $this->getStatusColor($occurrence->status),
                    'borderColor' => $this->getStatusColor($occurrence->status),
                    'textColor' => '#fff',
                    'allDay' => $visitation->is_flexible_time,
                    'extendedProps' => [
                        'visitation_id' => $visitation->visitation_id,
                        'occurrence_id' => $occurrence->occurrence_id,
                        'care_worker' => $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name,
                        'care_worker_id' => $visitation->care_worker_id,
                        'beneficiary' => $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name,
                        'beneficiary_id' => $visitation->beneficiary_id,
                        'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                        'status' => ucfirst($occurrence->status),
                        'is_flexible_time' => $visitation->is_flexible_time,
                        'notes' => $occurrence->notes ?? $visitation->notes,
                        'phone' => $visitation->beneficiary->mobile ?? 'Not Available',
                        'address' => $visitation->beneficiary->street_address,
                        'recurring' => $visitation->recurringPattern ? true : false,
                    ]
                ];
                
                $events[] = $event;
            }
            
            // Process legacy visitations (should become fewer over time as you migrate)
            foreach ($visitations as $visitation) {
                // For visitations not yet migrated to occurrences system
                // Create base event properties
                $color = $this->getStatusColor($visitation->status);
                
                $title = $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name;
                if ($user->role_id <= 2) { // Admin or care manager
                    $title .= ' (' . $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name . ')';
                }
                
                $baseEvent = [
                    'title' => $title,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#fff',
                    'allDay' => $visitation->is_flexible_time,
                    'extendedProps' => [
                        'visitation_id' => $visitation->visitation_id,
                        'care_worker' => $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name,
                        'care_worker_id' => $visitation->care_worker_id,
                        'beneficiary' => $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name,
                        'beneficiary_id' => $visitation->beneficiary_id,
                        'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                        'status' => ucfirst($visitation->status),
                        'is_flexible_time' => $visitation->is_flexible_time,
                        'notes' => $visitation->notes,
                        'phone' => $visitation->beneficiary->mobile ?? 'Not Available',
                        'address' => $visitation->beneficiary->street_address,
                        'recurring' => $visitation->recurringPattern ? true : false,
                    ]
                ];
                
                // Handle recurring events (legacy approach)
                if ($visitation->recurringPattern) {
                    // Process recurring pattern later
                    
                    // Generate on-the-fly for legacy visitations
                    // This block would be your existing code for handling recurring patterns
                    // We'll leave it in place during the transition period
                } else {
                    // Non-recurring event
                    $event = $baseEvent;
                    $event['id'] = $visitation->visitation_id;
                    $event['start'] = $visitation->visitation_date->format('Y-m-d') . 
                                    ($visitation->is_flexible_time ? '' : 'T' . $visitation->start_time->format('H:i:s'));
                    $event['end'] = $visitation->visitation_date->format('Y-m-d') . 
                                ($visitation->is_flexible_time ? '' : 'T' . $visitation->end_time->format('H:i:s'));
                    
                    $events[] = $event;
                }
                
                // Generate occurrences for this visitation for next time
                // This helps gradually migrate to the occurrence-based system
                if (!$visitation->occurrences()->exists()) {
                    $visitation->generateOccurrences(3);
                }
            }
            
            return response()->json($events);
        } catch (\Exception $e) {
            \Log::error('Error in getVisitations: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An error occurred while fetching visitations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status of past appointments to "completed"
     * This will run during key user interactions instead of as a scheduled task
     */
    private function updatePastAppointmentsStatus()
    {
        // Only update non-recurring appointments that are in the past
        $updated = Visitation::where('status', 'scheduled')
            ->where('visitation_date', '<', Carbon::today())
            ->whereDoesntHave('recurringPattern') // Exclude recurring appointments
            ->update(['status' => 'completed']);
        
        if ($updated > 0) {
            \Log::info("Automatically marked $updated past non-recurring appointments as completed");
        }
        
        return $updated;
    }

    /**
     * Get beneficiary details for a specific beneficiary
     */
    public function getBeneficiaryDetails($id)
    {
        try {
            $beneficiary = Beneficiary::find($id);
            
            if (!$beneficiary) {
                return response()->json(['success' => false, 'message' => 'Beneficiary not found'], 404);
            }
            
            // Format the phone number safely
            $phone = $beneficiary->mobile ?? 'Not Available';
            
            // Default address if relationships are missing
            $fullAddress = $beneficiary->street_address ?? 'Not Available';
            
            // Safely load relationships
            try {
                if ($beneficiary->barangay) {
                    $fullAddress .= ', ' . $beneficiary->barangay->barangay_name;
                }
                
                if ($beneficiary->municipality) {
                    $fullAddress .= ', ' . $beneficiary->municipality->municipality_name;
                }
            } catch (\Exception $e) {
                // If there's an issue with the relationships, just use the street address
                $fullAddress = $beneficiary->street_address ?? 'Not Available';
            }
            
            // Safely get care worker information
            $assignedCareWorker = null;
            try {
                if ($beneficiary->general_care_plan_id) {
                    $generalCarePlan = GeneralCarePlan::find($beneficiary->general_care_plan_id);
                    if ($generalCarePlan && $generalCarePlan->care_worker_id) {
                        $careWorker = User::find($generalCarePlan->care_worker_id);
                        if ($careWorker) {
                            $assignedCareWorker = [
                                'id' => $careWorker->id,
                                'name' => $careWorker->first_name . ' ' . $careWorker->last_name
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // If there's an issue getting the care worker, leave it as null
            }
            
            return response()->json([
                'success' => true,
                'beneficiary' => [
                    'id' => $beneficiary->beneficiary_id,
                    'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name,
                    'phone' => $phone,
                    'address' => $fullAddress,
                    'care_worker' => $assignedCareWorker
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading beneficiary details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all beneficiaries for dropdown
     */
    public function getBeneficiaries(Request $request)
    {
        $user = Auth::user();
        $query = Beneficiary::query();
        
        // Filter by search term if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                  ->orWhere('beneficiary_id', 'LIKE', "%{$search}%");
            });
        }
        
        // For care workers, only show assigned beneficiaries
        if ($user->isCareWorker()) {
            $beneficiaryIds = GeneralCarePlan::where('care_worker_id', $user->id)
                                           ->pluck('general_care_plan_id');
            $query->whereIn('general_care_plan_id', $beneficiaryIds);
        }
        
        $beneficiaries = $query->select('beneficiary_id', 'first_name', 'last_name')
                              ->orderBy('last_name')
                              ->get()
                              ->map(function($beneficiary) {
                                  return [
                                      'id' => $beneficiary->beneficiary_id,
                                      'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name
                                  ];
                              });
        
        return response()->json(['success' => true, 'beneficiaries' => $beneficiaries]);
    }
    
    /**
     * Helper function to determine the correct view path based on user role
     */
    private function getViewPathForRole($user)
    {
        if ($user->isAdministrator() || $user->isExecutiveDirector()) {
            return 'admin.adminCareworkerAppointments';
        } elseif ($user->isCareManager()) {
            return 'careManager.careManagerCareworkerAppointments';
        } elseif ($user->isCareWorker()) {
            return 'careWorker.careWorkerAppointments';
        }
        
        // Default fallback
        return 'admin.adminCareworkerAppointments';
    }
    
    /**
     * Helper function to get color based on visitation status
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'scheduled':
                return '#4e73df'; // Blue
            case 'completed':
                return '#1cc88a'; // Green
            case 'canceled':
                return '#e74a3b'; // Red
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Store a new appointment
     */
    public function storeAppointment(Request $request)
    {
        // First validate that either times are specified or is_flexible_time is checked
        if (!$request->has('is_flexible_time') && (!$request->has('start_time') || !$request->has('end_time') || 
            empty($request->start_time) || empty($request->end_time))) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'time' => ['Either specific times or flexible time must be selected.']
                ]
            ], 422);
        }
        
        // Check recurring pattern requirements if it's recurring
        if ($request->has('is_recurring') && $request->is_recurring) {
            $recurringValidator = Validator::make($request->all(), [
                'pattern_type' => 'required|in:daily,weekly,monthly', 
                'day_of_week' => 'required_if:pattern_type,weekly|array|min:1',
                'recurrence_end' => 'required|date|after:visitation_date'
            ], [
                'pattern_type.required' => 'Please specify the recurring pattern type.',
                'day_of_week.required_if' => 'Please select at least one day of the week for weekly patterns.',
                'day_of_week.array' => 'Days of week must be selected for weekly patterns.',
                'day_of_week.min' => 'At least one day must be selected for weekly patterns.',
                'recurrence_end.required' => 'End date is required for recurring appointments.',
                'recurrence_end.after' => 'End date must be after the visit date.'
            ]);
            
            if ($recurringValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $recurringValidator->errors()
                ], 422);
            }

            $isOccurrenceUpdate = $request->has('occurrence_id') && $request->occurrence_id;
        }
        
        // Validate main appointment data
        $validator = Validator::make($request->all(), [
            'care_worker_id' => 'required|exists:cose_users,id',
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'visitation_date' => 'required|date|after_or_equal:today',
            'visit_type' => 'required|in:routine_care_visit,service_request,emergency_visit',
            'start_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i',
            'end_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i|after:start_time',
            'is_flexible_time' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'start_time.required_if' => 'Start time is required when flexible time is not checked.',
            'end_time.required_if' => 'End time is required when flexible time is not checked.',
            'end_time.after' => 'End time must be after start time.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
            
        try {
            // Create the appointment
            $visitation = new Visitation();
            $visitation->care_worker_id = $request->care_worker_id;
            $visitation->beneficiary_id = $request->beneficiary_id;
            $visitation->visitation_date = $request->visitation_date;
            $visitation->visit_type = $request->visit_type;
            $visitation->is_flexible_time = $request->has('is_flexible_time');
            $visitation->start_time = $request->is_flexible_time ? null : $request->start_time;
            $visitation->end_time = $request->is_flexible_time ? null : $request->end_time;
            $visitation->notes = $request->notes;
            $visitation->status = 'scheduled';
            $visitation->date_assigned = now();
            $visitation->assigned_by = Auth::id();
            $visitation->save();
            
            // NOW create the recurring pattern if needed, AFTER creating the visitation
            if ($request->has('is_recurring')) {
                $pattern = new RecurringPattern();
                $pattern->visitation_id = $visitation->visitation_id;
                $pattern->pattern_type = $request->pattern_type;
                
                // Convert day_of_week array to comma-separated string
                if (is_array($request->day_of_week)) {
                    $pattern->day_of_week = implode(',', $request->day_of_week);
                } else {
                    $pattern->day_of_week = $request->day_of_week;
                }
                
                $pattern->recurrence_end = $request->recurrence_end;
                $pattern->save();
            }
            
            // Generate occurrences
            $visitation->generateOccurrences(3);

            $this->sendAppointmentNotifications($visitation, 'created', null, Auth::id());
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'visitation' => $visitation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to split a recurring appointment into past and future parts
     * 
     * @param Visitation $originalVisitation The original visitation record
     * @param Carbon $splitDate The date to split at (usually today)
     * @param array $newData New data for the future occurrences
     * @return Visitation The newly created visitation record for future occurrences
     */
    private function splitRecurringAppointment(Visitation $originalVisitation, Carbon $splitDate, array $newData)
    {
        // STEP 1: Create a new visitation for future occurrences using direct SQL
        $newVisitationId = DB::table('visitations')->insertGetId([
            'care_worker_id' => $newData['care_worker_id'],
            'beneficiary_id' => $newData['beneficiary_id'],
            'visitation_date' => $newData['visitation_date'],
            'visit_type' => $newData['visit_type'],
            'is_flexible_time' => $newData['is_flexible_time'] ? 1 : 0,
            'start_time' => $newData['is_flexible_time'] ? null : $newData['start_time'],
            'end_time' => $newData['is_flexible_time'] ? null : $newData['end_time'],
            'notes' => $newData['notes'],
            'status' => 'scheduled',
            'date_assigned' => now(),
            'assigned_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now()
        ], 'visitation_id');  // Specify the primary key column name here
        
        // STEP 2: Create the new pattern for future occurrences if needed
        if ($newData['is_recurring']) {
            $newPatternId = DB::table('recurring_patterns')->insertGetId([
                'visitation_id' => $newVisitationId,
                'pattern_type' => $newData['pattern_type'] ?? 'weekly',
                'day_of_week' => is_array($newData['day_of_week']) 
                    ? implode(',', $newData['day_of_week']) 
                    : $newData['day_of_week'],
                'recurrence_end' => $newData['recurrence_end'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ], 'pattern_id');  // Specify the primary key column name here
            
        }

       // STEP 2.5: Create exceptions for the original pattern for all dates that will be covered by the new pattern
        if ($newData['is_recurring']) {
            $newStartDate = Carbon::parse($newData['visitation_date']);
            
            // Get the original pattern FIRST
            $originalPattern = $originalVisitation->recurringPattern;
            
            // Now use the pattern
            $originalEndDate = $newData['original_recurrence_end'] ?? $originalPattern->recurrence_end;
            $originalDate = Carbon::parse($originalVisitation->visitation_date);
            $patternType = $originalPattern->pattern_type;
            $currentDate = $originalDate->copy();
            
            // Calculate the end date for exception creation 
            $exceptionEndDate = null;
            if ($originalEndDate) {
                $exceptionEndDate = Carbon::parse($originalEndDate);
            } else {
                // If no end date, use a reasonable future date (e.g., 1 year from now)
                $exceptionEndDate = Carbon::now()->addYear();
            }
            
            // Create exceptions for all future occurrences from the new start date
            while ($currentDate <= $exceptionEndDate) {
                // Only create exceptions for dates on or after the new pattern start date
                if ($currentDate >= $newStartDate) {
                    // Add an exception for this date
                    DB::table('visitation_exceptions')->insert([
                        'visitation_id' => $originalVisitation->visitation_id,
                        'exception_date' => $currentDate->format('Y-m-d'),
                        'status' => 'skipped',  // Use 'skipped' instead of 'canceled' to hide it without showing as canceled
                        'reason' => 'Modified - see updated appointment',
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                // Advance to next occurrence based on pattern type
                if ($patternType === 'daily') {
                    $currentDate->addDay();
                } elseif ($patternType === 'weekly') {
                    if ($originalVisitation->recurringPattern->day_of_week) {
                        // Handle weekly pattern with specific days
                        // (Use the same logic as in your getVisitations method)
                        $dayArray = is_string($originalVisitation->recurringPattern->day_of_week) ? 
                            array_map('intval', explode(',', $originalVisitation->recurringPattern->day_of_week)) : 
                            [intval($originalVisitation->recurringPattern->day_of_week)];
                        
                        // Sort days to find the next one
                        sort($dayArray);
                        
                        $currentDayOfWeek = $currentDate->dayOfWeek;
                        $nextDay = null;
                        
                        foreach ($dayArray as $day) {
                            if ($day > $currentDayOfWeek) {
                                $nextDay = $day;
                                break;
                            }
                        }
                        
                        if ($nextDay === null) {
                            // No days left in this week, move to first day of next week
                            $nextDay = $dayArray[0];
                            $daysToAdd = 7 - $currentDayOfWeek + $nextDay;
                            $currentDate->addDays($daysToAdd);
                        } else {
                            // Move to next day in the same week
                            $daysToAdd = $nextDay - $currentDayOfWeek;
                            $currentDate->addDays($daysToAdd);
                        }
                    } else {
                        // Simple weekly pattern (same day each week)
                        $currentDate->addWeek();
                    }
                } elseif ($patternType === 'monthly') {
                    $currentDate->addMonth();
                }
            }
        }
        
        // STEP 3: Update the original pattern - ACTUALLY preserve the original end date
        $originalPattern = $originalVisitation->recurringPattern;
        $originalEndDate = $newData['original_recurrence_end'] ?? $originalPattern->recurrence_end;
        
        // RESTORE the original end date 
        DB::table('recurring_patterns')
            ->where('pattern_id', $originalPattern->pattern_id)
            ->update([
                'recurrence_end' => $originalEndDate,
                'updated_at' => now()
            ]);
        
        // STEP 4: Verify both records exist through direct SQL
        $originalStillExists = DB::table('visitations')
            ->where('visitation_id', $originalVisitation->visitation_id)
            ->exists();
        
        $newExists = DB::table('visitations')
            ->where('visitation_id', $newVisitationId)
            ->exists();
        
        // Return a new Visitation model instance for the new record
        return Visitation::find($newVisitationId);
    }

    /**
     * Update an existing appointment
     */
    public function updateAppointment(Request $request)
    {
        if ($request->has('is_recurring') && $request->is_recurring) {
            $recurringValidator = Validator::make($request->all(), [
                'pattern_type' => 'required|in:daily,weekly,monthly', 
                'day_of_week' => 'required_if:pattern_type,weekly|array|min:1',
                'recurrence_end' => 'required|date|after:visitation_date'
            ], [
                'pattern_type.required' => 'Please specify the recurring pattern type.',
                'day_of_week.required_if' => 'Please select at least one day of the week for weekly patterns.',
                'day_of_week.array' => 'Days of week must be selected for weekly patterns.',
                'day_of_week.min' => 'At least one day must be selected for weekly patterns.',
                'recurrence_end.required' => 'End date is required for recurring appointments.',
                'recurrence_end.after' => 'End date must be after the visit date.'
            ]);
            
            if ($recurringValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $recurringValidator->errors()
                ], 422);
            }

        }

        // Check if we're updating a specific occurrence or the entire series
        $isOccurrenceUpdate = $request->has('occurrence_id') && $request->occurrence_id;
        
        DB::beginTransaction();
        
        try {
            // Get the visitation
            $visitation = Visitation::findOrFail($request->visitation_id);
            
            // Check if user is trying to change between recurring and non-recurring
            $wasRecurring = $visitation->recurringPattern ? true : false;
            $wantsRecurring = $request->has('is_recurring') ? true : false;
            
            if ($wasRecurring !== $wantsRecurring) {
                return response()->json([
                    'success' => false,
                    'message' => 'Converting between recurring and non-recurring appointments is not allowed. Please cancel this appointment and create a new one instead.'
                ], 422);
            }
            
            $originalVisitationDate = $visitation->visitation_date->format('Y-m-d'); // Store original date
            $newVisitationDate = null; // Initialize the variable here

            // For occurrence-specific updates
            if ($isOccurrenceUpdate) {
                $occurrence = VisitationOccurrence::findOrFail($request->occurrence_id);
                
                // Update just this occurrence
                $occurrence->start_time = $request->is_flexible_time ? null : $request->start_time;
                $occurrence->end_time = $request->is_flexible_time ? null : $request->end_time;
                $occurrence->notes = $request->notes;
                $occurrence->is_modified = true; // Mark as modified from the series
                $occurrence->save();

                $this->sendAppointmentNotifications($visitation, 'updated', null, Auth::id());
                
                DB::commit();
                
                // Log the update
                \Log::info('Updated specific occurrence', [
                    'occurrence_id' => $occurrence->occurrence_id,
                    'visitation_id' => $visitation->visitation_id,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment occurrence updated successfully',
                    'visitation' => $visitation,
                    'occurrence' => $occurrence
                ]);
            }
            
            // For full visitation updates
            
            // Check if this is recurring and we need to update this & future occurrences
            $updateFuture = $request->has('update_future') && $request->update_future && $visitation->recurringPattern;
            
            // 1. Archive the current visitation before modifying
            $archive = $visitation->archive('Updated', Auth::id());
            
            // 2. Update the base visitation record
            $visitation->care_worker_id = $request->care_worker_id;
            $visitation->beneficiary_id = $request->beneficiary_id;
            $visitation->visitation_date = $request->visitation_date;
            $visitation->visit_type = $request->visit_type;
            $visitation->is_flexible_time = $request->has('is_flexible_time') && $request->is_flexible_time;
            $visitation->start_time = $request->is_flexible_time ? null : $request->start_time;
            $visitation->end_time = $request->is_flexible_time ? null : $request->end_time;
            $visitation->notes = $request->notes;
            $visitation->updated_at = now();
            $visitation->save();
            
            // Store the new date here to ensure it's defined for all code paths
            $newVisitationDate = $visitation->visitation_date->format('Y-m-d');

            // IMPORTANT: Delete the old base occurrence if date changed, regardless of recurrence
            if ($originalVisitationDate !== $newVisitationDate) {
                // Delete the occurrence for the old date
                $this->forceDeleteOccurrencesByDate($visitation->visitation_id, $originalVisitationDate);
                
                \Log::info('Base date changed for appointment, deleted old occurrence', [
                    'visitation_id' => $visitation->visitation_id,
                    'old_date' => $originalVisitationDate,
                    'new_date' => $newVisitationDate
                ]);
            }
            
            // 3. Handle recurring pattern updates
            if ($request->has('is_recurring')) {
                // Is this set to be recurring?
                if ($request->is_recurring) {
                    $pattern = $visitation->recurringPattern;
                    
                    // Update existing pattern or create new one
                    if ($pattern) {
                        $pattern->pattern_type = $request->pattern_type;
                        
                        // Handle day_of_week field properly
                        if (is_array($request->day_of_week)) {
                            $pattern->day_of_week = implode(',', $request->day_of_week);
                        } else {
                            $pattern->day_of_week = $request->day_of_week;
                        }
                        
                        $pattern->recurrence_end = $request->recurrence_end ?? null;
                        $pattern->save();
                    } else {
                        // Create new pattern if one doesn't exist
                        $pattern = new RecurringPattern();
                        $pattern->visitation_id = $visitation->visitation_id;
                        $pattern->pattern_type = $request->pattern_type;
                        
                        // Handle day_of_week field properly
                        if (is_array($request->day_of_week)) {
                            $pattern->day_of_week = implode(',', $request->day_of_week);
                        } else {
                            $pattern->day_of_week = $request->day_of_week;
                        }
                        
                        $pattern->recurrence_end = $request->recurrence_end ?? null;
                        $pattern->save();
                    }
                    
                    // Get update date - determines which occurrences to update
                    $updateDate = Carbon::parse($request->effective_date ?? $visitation->visitation_date);
                    
                    // Delete future occurrences
                    VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                        ->where('occurrence_date', '>=', $updateDate->format('Y-m-d'))
                        ->delete();
                        
                    // Regenerate occurrences
                    $months = 3; // Generate for 3 months
                    $occurrenceIds = $visitation->generateOccurrences($months);
                    
                    \Log::info('Updated recurring visitation and regenerated occurrences', [
                        'visitation_id' => $visitation->visitation_id,
                        'generated_occurrences' => count($occurrenceIds)
                    ]);
                } else {
                    // It was recurring but now it's not
                    if ($visitation->recurringPattern) {
                        // Remove the pattern
                        $visitation->recurringPattern->delete();
                        
                        // Delete all occurrences except the first one
                        $firstOccurrence = $visitation->occurrences()->orderBy('occurrence_date')->first();
                        
                        if ($firstOccurrence) {
                            VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                                ->where('occurrence_id', '!=', $firstOccurrence->occurrence_id)
                                ->delete();
                        } else {
                            // No occurrences found, create one for the base visitation
                            VisitationOccurrence::create([
                                'visitation_id' => $visitation->visitation_id,
                                'occurrence_date' => $visitation->visitation_date,
                                'start_time' => $visitation->start_time,
                                'end_time' => $visitation->end_time,
                                'status' => $visitation->status
                            ]);
                        }
                        
                        \Log::info('Visitation changed from recurring to non-recurring', [
                            'visitation_id' => $visitation->visitation_id
                        ]);
                    }
                }
            } else {
                // Not dealing with recurrence, just update the single occurrence
                
                // For non-recurring appointments, handle date changes properly
                $newVisitationDate = $visitation->visitation_date->format('Y-m-d');
                
                // If the date was changed, delete the old occurrence
                // Inside the date change block for non-recurring appointments:
                if ($originalVisitationDate !== $newVisitationDate) {
                    // Delete the old occurrence with a forced approach
                    $this->forceDeleteOccurrencesByDate($visitation->visitation_id, $originalVisitationDate);
                    
                    // Force the removal through a direct SQL query as a backup
                    DB::statement('DELETE FROM visitation_occurrences WHERE visitation_id = ? AND occurrence_date = ?', 
                        [$visitation->visitation_id, $originalVisitationDate]);
                        
                    \Log::info('Date change detected - deleted old occurrence', [
                        'visitation_id' => $visitation->visitation_id,
                        'old_date' => $originalVisitationDate,
                        'new_date' => $newVisitationDate
                    ]);
                }

                // Now create/update the occurrence for the new date
                $occurrence = VisitationOccurrence::updateOrCreate(
                    ['visitation_id' => $visitation->visitation_id, 'occurrence_date' => $newVisitationDate],
                    [
                        'start_time' => $visitation->start_time,
                        'end_time' => $visitation->end_time,
                        'status' => $visitation->status
                    ]
                );
            }
            
            // Send notifications
            $this->sendAppointmentNotifications($visitation, 'updated', null, Auth::id()); 

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'visitation' => $visitation,
                'should_refresh' => true,  // Add this flag
                'date_changed' => ($originalVisitationDate !== $newVisitationDate)  // Add this info
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forcefully delete occurrences by date and visitation ID with direct SQL
     * 
     * @param int $visitationId The visitation ID
     * @param string $date The date to delete occurrences for
     * @return int Number of records deleted
     */
    private function forceDeleteOccurrencesByDate($visitationId, $date)
    {
        try {
            // Log what we're attempting to delete
            \Log::info('Attempting to delete occurrence', [
                'visitation_id' => $visitationId,
                'date' => $date
            ]);
            
            // Method 1: Eloquent deletion
            $deleteCount1 = VisitationOccurrence::where('visitation_id', $visitationId)
                ->where('occurrence_date', $date)
                ->delete();
            
            // Method 2: Direct SQL deletion
            $deleteCount2 = DB::delete('DELETE FROM visitation_occurrences WHERE visitation_id = ? AND occurrence_date = ?', 
                [$visitationId, $date]);
            
            // Verify deletion worked
            $remaining = VisitationOccurrence::where('visitation_id', $visitationId)
                ->where('occurrence_date', $date)
                ->count();
            
            \Log::info('Deletion results', [
                'eloquent_deleted' => $deleteCount1,
                'sql_deleted' => $deleteCount2,
                'remaining_count' => $remaining
            ]);
            
            // If there are still occurrences, try one more approach
            if ($remaining > 0) {
                DB::statement('DELETE FROM visitation_occurrences WHERE visitation_id = ? AND occurrence_date = ?', 
                    [$visitationId, $date]);
                    
                $remaining = VisitationOccurrence::where('visitation_id', $visitationId)
                    ->where('occurrence_date', $date)
                    ->count();
                    
                \Log::info('Final deletion check', [
                    'remaining_after_final_attempt' => $remaining
                ]);
            }
            
            return $deleteCount1 + $deleteCount2;
        } catch (\Exception $e) {
            \Log::error('Error in force delete:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Cancel an appointment with enhanced options for recurring events
     */
    public function cancelAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitation_id' => 'required|exists:visitations,visitation_id',
            'reason' => 'required|string|max:500',
            'password' => 'required|string',
            'cancel_option' => 'sometimes|in:single,future', // Changed from 'this,future'
            'occurrence_id' => 'sometimes|exists:visitation_occurrences,occurrence_id',
            'occurrence_date' => 'sometimes|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify user password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'passwordError' => 'The password is incorrect.'
            ], 401);
        }
        
        
        DB::beginTransaction();
                
        try {
            // Determine if we're canceling a specific occurrence or the entire series
            if ($request->has('occurrence_id')) {
                // Cancel a specific occurrence
                $occurrence = VisitationOccurrence::findOrFail($request->occurrence_id);
                $occurrence->cancel($request->reason);
                
                $message = 'The appointment on ' . $occurrence->occurrence_date->format('M j, Y') . ' has been canceled.';
            } else {
                $visitation = Visitation::findOrFail($request->visitation_id);
                
                // Archive the current visitation
                $archive = $visitation->archive('Canceled: ' . $request->reason, Auth::id());
                
                if ($visitation->recurringPattern && $request->has('cancel_option') && $request->cancel_option === 'future') {
                    // Get the occurrence date from the request
                    $occurrenceDate = $request->has('occurrence_date') ? 
                        Carbon::parse($request->occurrence_date) : 
                        Carbon::today();
                    
                    // Cancel this and all future occurrences
                    $this->cancelFutureOccurrences($visitation, $occurrenceDate, $request->reason);
                    
                    $message = 'The appointment and all future occurrences have been canceled.';
                } else {
                    // For non-recurring appointments or cancel_option = 'single'
                    $visitation->status = 'canceled';
                    $visitation->save();
                    
                    // Only cancel the specific occurrence
                    $occurrenceDate = $request->has('occurrence_date') ? 
                        $request->occurrence_date : 
                        $visitation->visitation_date->format('Y-m-d');
                    
                    VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                        ->where('occurrence_date', $occurrenceDate)
                        ->update([
                            'status' => 'canceled',
                            'notes' => $request->reason
                        ]);
                    
                    $message = 'The appointment has been canceled.';
                }
            }
            
            // If canceling a specific occurrence
            if (isset($occurrence)) {
                // Get the parent visitation to include in notifications
                $visitation = $occurrence->visitation;
                $this->sendAppointmentNotifications($visitation, 'canceled', $request->reason, Auth::id());
            } else if (isset($visitation)) {
                // For full visitation cancellations
                $this->sendAppointmentNotifications($visitation, 'canceled', $request->reason, Auth::id());
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'should_refresh' => true // Add refresh flag
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error canceling appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a single occurrence by creating an exception
     */
    private function cancelSingleOccurrence(Visitation $visitation, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception("Occurrence date is required for cancelling a single occurrence");
        }
        
        // Format the date for consistency
        $formattedDate = $occurrenceDate->format('Y-m-d');
        
        \Log::info('Creating exception for single occurrence', [
            'visitation_id' => $visitation->visitation_id,
            'date' => $formattedDate
        ]);
        
        // Delete any existing exception for this date to avoid conflicts
        VisitationException::where('visitation_id', $visitation->visitation_id)
            ->where('exception_date', $formattedDate)
            ->delete();
        
        // Create a new exception record
        $exception = new VisitationException();
        $exception->visitation_id = $visitation->visitation_id;
        $exception->exception_date = $formattedDate;
        $exception->status = 'canceled';
        $exception->reason = $reason;
        $exception->created_by = Auth::id();
        $exception->save();
        
        \Log::info('Exception created successfully', [
            'exception_id' => $exception->exception_id,
            'status' => $exception->status
        ]);
        
        return $exception;
    }

    /**
     * Cancel this and all future occurrences
     */
    private function cancelFutureOccurrences(Visitation $visitation, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception("Occurrence date is required for cancelling future occurrences");
        }
        
        $recurringPattern = $visitation->recurringPattern;
        
        if (!$recurringPattern) {
            throw new \Exception("No recurring pattern found for this visitation");
        }
        
        // Format both dates as strings for proper comparison
        $originalDate = $visitation->visitation_date->format('Y-m-d');
        $occurrenceDateStr = $occurrenceDate->format('Y-m-d');
        
        \Log::info('Processing cancel future occurrences', [
            'visitation_id' => $visitation->visitation_id,
            'original_date' => $originalDate,
            'occurrence_date' => $occurrenceDateStr,
            'are_equal' => ($originalDate == $occurrenceDateStr)
        ]);
        
        // If the occurrence date is the original start date of the visitation
        if ($originalDate == $occurrenceDateStr) {
            // Mark the visitation as canceled but DO NOT update past occurrences
            $visitation->status = 'canceled';
            $visitation->notes = ($visitation->notes ? $visitation->notes . "\n\n" : '') . 
                            "Canceled: " . $reason;
            $visitation->save();
            
            // Find and cancel the selected occurrence
            $selectedOccurrence = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                ->where('occurrence_date', $occurrenceDateStr)
                ->first();
                
            if ($selectedOccurrence) {
                $selectedOccurrence->status = 'canceled';
                $selectedOccurrence->notes = $reason;
                $selectedOccurrence->save();
            }
                
            // Delete future occurrences to clean up the calendar
            $deletedCount = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                ->where('occurrence_date', '>', $occurrenceDateStr)
                ->delete();
                
            \Log::info('Canceled recurring appointment and removed future occurrences', [
                'selected_date' => $occurrenceDateStr,
                'deleted_future_count' => $deletedCount
            ]);
        } else {
            // This is a mid-series cancellation
            
            // 1. Cancel the selected occurrence
            $selectedOccurrence = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                ->where('occurrence_date', $occurrenceDateStr)
                ->first();
                
            if ($selectedOccurrence) {
                $selectedOccurrence->status = 'canceled';
                $selectedOccurrence->notes = $reason;
                $selectedOccurrence->save();
            }
            
            // 2. Delete occurrences after the selected date
            $deletedCount = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                ->where('occurrence_date', '>', $occurrenceDateStr)
                ->delete();
                
            // 3. Update the recurring pattern end date to be the day before the selected date
            $dayBefore = Carbon::parse($occurrenceDateStr)->subDay();
            $recurringPattern->recurrence_end = $dayBefore;
            $recurringPattern->save();
            
            \Log::info('Canceled recurring appointment from middle of series', [
                'selected_date' => $occurrenceDateStr,
                'new_end_date' => $dayBefore->format('Y-m-d'),
                'deleted_future_count' => $deletedCount
            ]);
        }
        
        return true;
    }

    /**
     * Send notifications to relevant stakeholders about appointment changes
     * 
     * @param Visitation $visitation The appointment being modified
     * @param string $action The action performed (created, updated, canceled)
     * @param string|null $reason Reason for cancellation if applicable
     * @param int|null $authorId User ID of the person who performed the action
     */
    private function sendAppointmentNotifications(Visitation $visitation, string $action, string $reason = null, int $authorId = null)
    {
        // Set author ID to current user if not provided
        if ($authorId === null) {
            $authorId = Auth::id();
        }
        
        // Get all stakeholders
        $beneficiary = Beneficiary::find($visitation->beneficiary_id);
        $careWorker = User::find($visitation->care_worker_id);
        $familyMembers = FamilyMember::where('related_beneficiary_id', $visitation->beneficiary_id)->get();
        
        // Get care manager of the care worker
        $careManager = null;
        if ($careWorker && $careWorker->assigned_care_manager_id) {
            $careManager = User::find($careWorker->assigned_care_manager_id);
        }
        
        // Format date and time information
        $dateFormatted = Carbon::parse($visitation->visitation_date)->format('l, F j, Y');
        $timeInfo = $visitation->is_flexible_time ? 
            'flexible time (to be determined)' : 
            'from ' . Carbon::parse($visitation->start_time)->format('g:i A') . ' to ' . 
            Carbon::parse($visitation->end_time)->format('g:i A');
        
        $visitType = ucwords(str_replace('_', ' ', $visitation->visit_type));
        
        // Prepare notification content based on action type
        switch ($action) {
            case 'created':
                $title = "New Appointment Scheduled";
                $message = "A new appointment has been scheduled for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} on {$dateFormatted} at {$timeInfo}. " .
                        "Visit type: {$visitType}.";
                break;
                
            case 'updated':
                $title = "Appointment Updated";
                $message = "The appointment for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} has been updated. " .
                        "New schedule: {$dateFormatted} at {$timeInfo}. " .
                        "Visit type: {$visitType}.";
                break;
                
            case 'canceled':
                $title = "Appointment Canceled";
                $message = "The appointment for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} on {$dateFormatted} " .
                        "has been canceled." . ($reason ? " Reason: {$reason}" : "");
                break;
                
            default:
                return;
        }
        
        // Send notification to care worker (if not the author)
        if ($careWorker && $careWorker->id != $authorId) {
            Notification::create([
                'user_id' => $careWorker->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Send notification to care manager (if not the author)
        if ($careManager && $careManager->id != $authorId) {
            Notification::create([
                'user_id' => $careManager->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify beneficiary if they have portal access
        if ($beneficiary->portal_account_id) {
            Notification::create([
                'user_id' => $beneficiary->beneficiary_id,
                'user_type' => 'beneficiary',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify all family members
        foreach ($familyMembers as $familyMember) {
            if ($familyMember->portal_account_id) {
                Notification::create([
                    'user_id' => $familyMember->family_member_id,
                    'user_type' => 'family_member',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        }

        // Administrator notification code removed
        
        \Log::info("Appointment notifications sent", [
            'action' => $action,
            'visitation_id' => $visitation->visitation_id,
            'author_id' => $authorId
        ]);
    }

    

}
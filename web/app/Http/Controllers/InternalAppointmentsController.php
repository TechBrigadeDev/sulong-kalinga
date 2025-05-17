<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentOccurrence;
use App\Models\AppointmentParticipant;
use App\Models\AppointmentType;
use App\Models\User;
use App\Models\RecurringPattern;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\AppointmentException;
use App\Models\AppointmentArchive;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Notification;

class InternalAppointmentsController extends Controller
{
    /**
     * Display the internal appointments page based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Auto-update status of past appointments
        $this->updatePastAppointmentsStatus();
        
        // Get all appointment types for the form
        $appointmentTypes = AppointmentType::all();
        
        // Get staff users for participants selection (for admin and care manager)
        $staffUsers = User::where('role_id', '<=', 3)
                          ->orderBy('first_name')
                          ->get(['id', 'first_name', 'last_name', 'role_id']);
        
        // Group users by role for easier selection in the form
        $usersByRole = [
            'administrators' => $staffUsers->where('role_id', 1)->values(),
            'care_managers' => $staffUsers->where('role_id', 2)->values(),
            'care_workers' => $staffUsers->where('role_id', 3)->values(),
        ];

        // Get beneficiaries and family members for admin/care manager views
        $beneficiaries = [];
        $familyMembers = [];

        if ($user->role_id <= 2) {
            $beneficiaries = Beneficiary::orderBy('first_name')
                ->get(['beneficiary_id', 'first_name', 'last_name']);
                
            $familyMembers = FamilyMember::orderBy('first_name')
                ->get(['family_member_id', 'first_name', 'last_name', 'related_beneficiary_id']);
        }
        if ($user->role_id === 3) {
            $appointmentsQuery->whereHas('participants', function ($query) use ($user) {
                $query->where('participant_type', 'cose_user')
                    ->where('participant_id', $user->id);
            });
        }
        
        // Base view data
        $viewData = [
            'appointmentTypes' => $appointmentTypes,
            'usersByRole' => $usersByRole,
        ];
        
        // Return the appropriate view based on user role
        if ($user->role_id === 1) {
            $viewData['beneficiaries'] = $beneficiaries;
            $viewData['familyMembers'] = $familyMembers;
            return view('admin.adminInternalAppointments', $viewData);
        } elseif ($user->role_id === 2) {
            $viewData['beneficiaries'] = $beneficiaries;
            $viewData['familyMembers'] = $familyMembers;
            return view('careManager.careManagerInternalAppointments', $viewData);
        } else {
            // For care workers, they only need to see their own related appointments
            return view('careWorker.careWorkerInternalAppointments', [
                'appointmentTypes' => $appointmentTypes,
                'careWorkerId' => $user->id
            ]);
        }
    }
    
    /**
     * Update status of past appointments to "completed"
     */
    private function updatePastAppointmentsStatus()
    {
        $today = Carbon::today();
        
        // Update appointment occurrences that are in the past and still scheduled
        $updated = AppointmentOccurrence::where('status', 'scheduled')
            ->where('occurrence_date', '<', $today)
            ->update(['status' => 'completed']);
            
        if ($updated > 0) {
            \Log::info("Updated $updated past appointments to completed status");
        }
        
        return $updated;
    }
    
    /**
     * Get appointments for the calendar view
     */
    public function getAppointments(Request $request)
    {
        $startDateStr = $request->input('start');
        $endDateStr = $request->input('end');
        $viewType = $request->input('view_type', 'dayGridMonth');
        $user = Auth::user();
        
        // Fix timezone format issue by extracting just the date part if there's an issue
        try {
            $startDate = Carbon::parse($startDateStr);
        } catch (\Exception $e) {
            // Extract just the date part before T
            $startDate = Carbon::parse(explode('T', $startDateStr)[0]);
        }
        
        try {
            $endDate = Carbon::parse($endDateStr);
        } catch (\Exception $e) {
            // Extract just the date part before T
            $endDate = Carbon::parse(explode('T', $endDateStr)[0]);
        }
        
        // For month view, limit data range to prevent performance issues
        if ($viewType === 'dayGridMonth') {
            $maxEndDate = $startDate->copy()->addMonths(3);
            if ($endDate > $maxEndDate) {
                $endDate = $maxEndDate;
            }
        }
        
        // Format dates for database query
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
        
        try {
            $events = [];
            
        $appointmentsQuery = Appointment::with([
            'type',                   
            'participants',          
            'occurrences',          
            'recurringPattern'
        ])
        ->whereHas('occurrences', function ($query) use ($startDateFormatted, $endDateFormatted) {
            $query->whereBetween('occurrence_date', [$startDateFormatted, $endDateFormatted]);
        });
            
            // If user is a care worker, only show appointments they're participating in
            if ($user->role_id === 3) {
                $appointmentsQuery->whereHas('participants', function ($query) use ($user) {
                    $query->where('participant_type', 'cose_user')
                        ->where('participant_id', $user->id);
                });
            }
            
            $appointments = $appointmentsQuery->get();
                
            foreach ($appointments as $appointment) {
                // Get all occurrences for this appointment in the date range
                $occurrences = $appointment->occurrences()
                    ->whereBetween('occurrence_date', [$startDate, $endDate])
                    ->get();
                    
                foreach ($occurrences as $occurrence) {
                    // Create an event for the calendar
                    $event = [
                        'id' => 'occ_' . $occurrence->occurrence_id,
                        'title' => $appointment->title,
                        'start' => $occurrence->occurrence_date->format('Y-m-d') . 
                                ($occurrence->start_time ? 'T' . Carbon::parse($occurrence->start_time)->format('H:i:s') : ''),
                        'end' => $occurrence->occurrence_date->format('Y-m-d') . 
                                ($occurrence->end_time ? 'T' . Carbon::parse($occurrence->end_time)->format('H:i:s') : ''),
                        'allDay' => $appointment->is_flexible_time,
                        'classNames' => ['event-' . $this->getEventClass($appointment->appointment_type_id)],
                        'backgroundColor' => $appointment->type->color_code,
                        'borderColor' => $appointment->type->color_code,
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'appointment_id' => $appointment->appointment_id,
                            'occurrence_id' => $occurrence->occurrence_id,
                            'type' => $appointment->type->type_name,
                            'type_id' => $appointment->appointment_type_id,
                            'description' => $appointment->description,
                            'other_type_details' => $appointment->other_type_details,
                            'meeting_location' => $appointment->meeting_location,
                            'status' => ucfirst($occurrence->status),
                            'is_flexible_time' => $appointment->is_flexible_time,
                            'notes' => $occurrence->notes ?? $appointment->notes,
                            'participants' => $this->formatParticipants($appointment->participants),
                            'recurring' => $appointment->recurringPattern ? true : false,
                            'recurring_pattern' => $appointment->recurringPattern ? [
                                'type' => $appointment->recurringPattern->pattern_type,
                                'day_of_week' => $appointment->recurringPattern->day_of_week,
                                'recurrence_end' => $appointment->recurringPattern->recurrence_end ? 
                                    $appointment->recurringPattern->recurrence_end->format('Y-m-d') : null,
                            ] : null,
                            'can_edit' => $user->role_id <= 2 || $this->isOrganizer($appointment->participants, $user->id),
                        ]
                    ];
                    
                    $events[] = $event;
                }
            }
            
            return response()->json($events);
            
        } catch (\Exception $e) {
            \Log::error("Error loading appointments: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load appointments: ' . $e->getMessage()], 500);
        }
    }

    
    
    /**
     * Get appointment details for viewing
     */
    public function getAppointmentDetails(Request $request)
    {
        // Method stub for retrieving detailed appointment information
        // Will be implemented in future updates
    }
    
    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        
        // Check recurring pattern requirements if it's recurring
        if ($request->has('is_recurring') && $request->is_recurring) {
            if (!$request->has('pattern_type') || !in_array($request->pattern_type, ['daily', 'weekly', 'monthly'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A valid recurrence pattern must be specified'
                ], 422);
            }
            
            // For weekly pattern, day of week is required
            if ($request->pattern_type === 'weekly' && (!$request->has('day_of_week') || empty($request->day_of_week))) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one day of the week must be selected for weekly recurrence'
                ], 422);
            }
        }

        // Check that at least one staff participant is selected
        if (!$request->has('participants') || 
            !isset($request->participants['cose_user']) || 
            !is_array($request->participants['cose_user']) || 
            count($request->participants['cose_user']) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'At least one staff attendee is required'
            ], 422);
        }

        // Validate beneficiary/family participants for specific appointment types
        $typeRequiresBeneficiary = in_array($request->appointment_type_id, [7, 11]); // 7=Assessment, 11=Others
        if ($typeRequiresBeneficiary) {
            // Validate that beneficiaries exist in our system
            if (isset($request->participants['beneficiary']) && is_array($request->participants['beneficiary'])) {
                foreach ($request->participants['beneficiary'] as $id) {
                    if (!Beneficiary::where('beneficiary_id', $id)->exists()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'One or more selected beneficiaries are invalid'
                        ], 422);
                    }
                }
            }
            
            // Validate that family members exist in our system
            if (isset($request->participants['family_member']) && is_array($request->participants['family_member'])) {
                foreach ($request->participants['family_member'] as $id) {
                    if (!FamilyMember::where('family_member_id', $id)->exists()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'One or more selected family members are invalid'
                        ], 422);
                    }
                }
            }
        }
        
        // Validate main appointment data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'appointment_type_id' => 'required|exists:appointment_types,appointment_type_id',
            'date' => 'required|date|after_or_equal:today',
            'meeting_location' => 'required|string|max:200|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'is_flexible_time' => 'sometimes|boolean',
            'start_time' => 'nullable|required_unless:is_flexible_time,1|date_format:H:i',
            'end_time' => 'nullable|required_unless:is_flexible_time,1|date_format:H:i|after:start_time',
            'is_recurring' => 'sometimes|boolean',
            'pattern_type' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_end' => 'required_if:is_recurring,true|nullable|date|after:date',
            'notes' => 'nullable|string|max:500|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'other_type_details' => 'required_if:appointment_type_id,11|nullable|string|max:200|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
        ], [
            'date.after_or_equal' => 'The appointment date cannot be in the past.',
            'end_time.after' => 'End time must be after start time.',
            'other_type_details.required_if' => 'Please specify the meeting type.',
            'recurrence_end.required_if' => 'An end date is required for recurring appointments.',
            'recurrence_end.after' => 'The recurrence end date must be after the appointment date.',
            'title.regex' => 'The title contains invalid characters.',
            'meeting_location.regex' => 'The location contains invalid characters.',
            'notes.regex' => 'The notes contain invalid characters.',
            'other_type_details.regex' => 'The meeting type details contain invalid characters.',
            'start_time.required_unless' => 'Start time is required when flexible time is not selected.',
            'end_time.required_unless' => 'End time is required when flexible time is not selected.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
            
        try {
            $user = Auth::user();
            
            // Create appointment record
            $appointment = new Appointment();
            $appointment->appointment_type_id = $request->appointment_type_id;
            $appointment->title = $request->title;
            $appointment->description = $request->description ?? null;
            $appointment->other_type_details = $request->other_type_details;
            $appointment->date = $request->date;
            $appointment->start_time = $request->is_flexible_time ? null : $request->start_time;
            $appointment->end_time = $request->is_flexible_time ? null : $request->end_time;
            $appointment->is_flexible_time = (bool) $request->is_flexible_time;
            $appointment->meeting_location = $request->meeting_location;
            $appointment->status = 'scheduled';
            $appointment->notes = $request->notes;
            $appointment->created_by = $user->id;
            $appointment->save();
            
            // Process participants
            if ($request->has('participants') && is_array($request->participants)) {
                foreach ($request->participants as $type => $ids) {
                    if (is_array($ids)) {
                        foreach ($ids as $id) {
                            $participant = new AppointmentParticipant();
                            $participant->appointment_id = $appointment->appointment_id;
                            $participant->participant_type = $type;
                            $participant->participant_id = $id;
                            // The first user (creator) is set as organizer
                            $participant->is_organizer = ($type === 'cose_user' && (int)$id === $user->id);
                            $participant->save();
                        }
                    }
                }
            }
            
            // Ensure the current user is always added as a participant/organizer
            $userParticipantExists = AppointmentParticipant::where('appointment_id', $appointment->appointment_id)
                ->where('participant_type', 'cose_user')
                ->where('participant_id', $user->id)
                ->exists();
                
            if (!$userParticipantExists) {
                $participant = new AppointmentParticipant();
                $participant->appointment_id = $appointment->appointment_id;
                $participant->participant_type = 'cose_user';
                $participant->participant_id = $user->id;
                $participant->is_organizer = true;
                $participant->save();
            }
            
            // Create recurring pattern if needed
            if ($request->has('is_recurring') && $request->is_recurring) {
                $pattern = new RecurringPattern();
                $pattern->appointment_id = $appointment->appointment_id;
                $pattern->pattern_type = $request->pattern_type;
                
                if ($request->pattern_type === 'weekly' && $request->has('day_of_week')) {
                    // Make sure we're handling the day_of_week values correctly
                    // First, make sure we have an array of unique values
                    $daysOfWeek = is_array($request->day_of_week) 
                        ? array_unique($request->day_of_week) 
                        : [$request->day_of_week];
                    
                    // Then implode to create the comma-separated list
                    $pattern->day_of_week = implode(',', $daysOfWeek);
                } else {
                    // Ensure null for other pattern types
                    $pattern->day_of_week = null;
                }
                
                if ($request->has('recurrence_end') && $request->recurrence_end) {
                    $pattern->recurrence_end = $request->recurrence_end;
                }
                
                $pattern->save();
            }
            
            // Generate occurrences
            $appointment->generateOccurrences(3); // Generate for 3 months
            
            DB::commit();

            // Send notifications
            $this->notifyNewAppointment($appointment);
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'appointment_id' => $appointment->appointment_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update an existing appointment
     */
    public function update(Request $request)
    {
        // Create a fresh copy of the request data that we can safely modify
        $data = $request->all();
        
        // Explicitly check flexible time value in ANY format
        $isFlexibleTime = false;
        if (isset($data['is_flexible_time'])) {
            // Accept any truthy value: '1', 1, 'true', true, 'on', etc.
            $isFlexibleTime = filter_var($data['is_flexible_time'], FILTER_VALIDATE_BOOLEAN);
        }

        // First, check recurring options
        if ($request->has('is_recurring') && $request->is_recurring) {
            $recurringValidator = Validator::make($request->all(), [
                'pattern_type' => 'required|in:daily,weekly,monthly', 
                'day_of_week' => 'required_if:pattern_type,weekly',
                'recurrence_end' => 'required|date|after:date'
            ], [
                'pattern_type.required' => 'Please specify the recurring pattern type.',
                'day_of_week.required_if' => 'Please select at least one day of the week for weekly patterns.',
                'recurrence_end.required' => 'End date is required for recurring appointments.',
                'recurrence_end.after' => 'End date must be after the appointment date.'
            ]);
            
            if ($recurringValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $recurringValidator->errors()
                ], 422);
            }
        }

        // Check that at least one staff participant is selected
        if (!$request->has('participants') || 
            !isset($request->participants['cose_user']) || 
            !is_array($request->participants['cose_user']) || 
            count($request->participants['cose_user']) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'At least one staff attendee is required'
            ], 422);
        }

        // Validate beneficiary/family participants for specific appointment types
        $typeRequiresBeneficiary = in_array($request->appointment_type_id, [7, 11]); // 7=Assessment, 11=Others
        if ($typeRequiresBeneficiary) {
            // Validate that beneficiaries exist in our system
            if (isset($request->participants['beneficiary']) && is_array($request->participants['beneficiary'])) {
                foreach ($request->participants['beneficiary'] as $id) {
                    if (!Beneficiary::where('beneficiary_id', $id)->exists()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'One or more selected beneficiaries are invalid'
                        ], 422);
                    }
                }
            }
            
            // Validate that family members exist in our system
            if (isset($request->participants['family_member']) && is_array($request->participants['family_member'])) {
                foreach ($request->participants['family_member'] as $id) {
                    if (!FamilyMember::where('family_member_id', $id)->exists()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'One or more selected family members are invalid'
                        ], 422);
                    }
                }
            }
        }

        // Validate main appointment data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'appointment_type_id' => 'required|exists:appointment_types,appointment_type_id',
            'date' => 'required|date',
            'meeting_location' => 'required|string|max:200|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'is_flexible_time' => 'sometimes|boolean',
            'is_recurring' => 'sometimes|boolean',
            'pattern_type' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_end' => 'required_if:is_recurring,true|nullable|date|after:date',
            'notes' => 'nullable|string|max:500|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'other_type_details' => 'required_if:appointment_type_id,11|nullable|string|max:200|regex:/^[\pL\pN\s\-\_\.\,\:\;\!\?\(\)\'\"]+$/u',
            'occurrence_id' => 'sometimes|exists:appointment_occurrences,occurrence_id',
            'appointment_id' => 'required|exists:appointments,appointment_id'
        ], [
            'title.required' => 'Please enter a title for the appointment.',
            'end_time.after' => 'End time must be after start time.',
            'other_type_details.required_if' => 'Please specify the meeting type.',
            'title.regex' => 'The title contains invalid characters.',
            'meeting_location.regex' => 'The location contains invalid characters.',
            'notes.regex' => 'The notes contain invalid characters.',
            'other_type_details.regex' => 'The meeting type details contain invalid characters.',
        ]);

            // Only add time validation if flexible time is NOT checked
            if (!$isFlexibleTime) {
                $validationRules['start_time'] = 'required|date_format:H:i';
                $validationRules['end_time'] = 'required|date_format:H:i|after:start_time';
            }
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $appointmentId = $request->appointment_id;
            
            $appointment = Appointment::with(['recurringPattern', 'occurrences', 'participants'])
                ->findOrFail($appointmentId);
                
            // Check permissions
            if (!$this->hasManagePermission($appointmentId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this appointment.'
                ], 403);
            }
            
            // Determine if we're editing a specific occurrence or the entire series
            $isOccurrenceUpdate = $request->has('occurrence_id') && $request->occurrence_id;
            
            // Check if this was recurring before
            $wasRecurring = $appointment->recurringPattern ? true : false;
            $wantsRecurring = $request->has('is_recurring') ? true : false;

            // If converting between recurring and non-recurring, prevent it
            if ($wasRecurring !== $wantsRecurring) {
                return response()->json([
                    'success' => false,
                    'message' => 'Converting between recurring and non-recurring appointments is not supported. Please cancel this appointment and create a new one instead.'
                ], 422);
            }
            
            // Store original date for comparison
            $originalDate = Carbon::parse($appointment->date)->format('Y-m-d');
            $newDate = Carbon::parse($request->date)->format('Y-m-d');
            $dateChanged = $originalDate !== $newDate;
            
            // If converting between recurring and non-recurring, prevent it
            if ($wasRecurring !== $wantsRecurring) {
                return response()->json([
                    'success' => false,
                    'message' => 'Converting between recurring and non-recurring appointments is not supported. Please cancel this appointment and create a new one instead.'
                ], 422);
            }

            // 1. Handle single occurrence update
            if ($isOccurrenceUpdate) {
                $occurrenceId = $request->occurrence_id;
                $occurrence = AppointmentOccurrence::findOrFail($occurrenceId);
                
                // If this is a recurring appointment, we need to split the series
                if ($wasRecurring && $occurrence) {
                    // Get the occurrence date to use as a split point
                    $splitDate = $occurrence->occurrence_date;
                    
                    // Create exceptions for all future occurrences
                    $this->createExceptionsAfterDate($appointment, $splitDate);
                    
                    // Create a new appointment for the future with the modified data
                    $newAppointment = $this->createAppointmentFromRequest($request, $user->id);
                    
                    // Copy the appropriate participants
                    $this->copyParticipantsToAppointment($appointment, $newAppointment, $request);
                    
                    // Generate occurrences for the new appointment
                    $newAppointment->generateOccurrences(3);
                    
                    DB::commit();

                    // Send notifications about the update
                    $this->notifyUpdatedAppointment($appointment);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Occurrence updated successfully',
                        'appointment_id' => $newAppointment->appointment_id
                    ]);
                }
                // Just update the single occurrence
                else {
                    $occurrence->start_time = $request->is_flexible_time ? null : Carbon::parse($request->start_time);
                    $occurrence->end_time = $request->is_flexible_time ? null : Carbon::parse($request->end_time);
                    $occurrence->is_modified = true;
                    $occurrence->save();

                    // Add this notification call
                    $this->notifyUpdatedAppointment($appointment);
                    
                    DB::commit();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Occurrence updated successfully',
                        'appointment_id' => $appointment->appointment_id
                    ]);
                }
            }
            // 2. Handle entire appointment update
            else {
                // If this is recurring, create new appointment for future occurrences
                if ($wasRecurring && $dateChanged) {
                    // Get today as the split date
                    $splitDate = Carbon::today();
                    
                    // Create exceptions for all future occurrences of the original appointment
                    $this->createExceptionsAfterDate($appointment, $splitDate);
                    
                    // Create a new appointment for the future with the modified data
                    $newAppointment = $this->createAppointmentFromRequest($request, $user->id);
                    
                    // Copy the appropriate participants
                    $this->copyParticipantsToAppointment($appointment, $newAppointment, $request);
                    
                    // Generate occurrences for the new appointment
                    $newAppointment->generateOccurrences(3);

                    // Add this notification call
                    $this->notifyUpdatedAppointment($newAppointment);
                    
                    DB::commit();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Appointment series updated successfully. Past occurrences remain unchanged.',
                        'appointment_id' => $newAppointment->appointment_id
                    ]);
                }
                // Regular update for non-recurring or recurring without date change
                else {
                    // Update main appointment data
                    $appointment->appointment_type_id = $request->appointment_type_id;
                    $appointment->title = $request->title;
                    $appointment->description = $request->description ?? null;
                    $appointment->other_type_details = $request->other_type_details;

                    // Explicitly parse and format the date to ensure it's saved correctly
                    $appointment->date = Carbon::parse($request->date)->format('Y-m-d');

                    $appointment->date = $request->date;
                    $appointment->start_time = $request->is_flexible_time ? null : $request->start_time;
                    $appointment->end_time = $request->is_flexible_time ? null : $request->end_time;
                    $appointment->is_flexible_time = (bool) $request->is_flexible_time;
                    $appointment->meeting_location = $request->meeting_location;
                    $appointment->notes = $request->notes;
                    $appointment->updated_by = $user->id;
                    $appointment->save();

                    // IMPORTANT: Update occurrence dates for non-recurring appointments
                    if (!$wasRecurring) {
                        $appointment->occurrences()->update([
                            'occurrence_date' => $appointment->date,
                            'start_time' => $appointment->start_time,
                            'end_time' => $appointment->end_time
                        ]);
                    }
                    
                    // Update recurring pattern if needed
                    if ($wasRecurring) {
                        $pattern = $appointment->recurringPattern;
                        $pattern->pattern_type = $request->pattern_type;
                        
                        // Handle day_of_week field properly - UPDATED for multiple days
                        if ($request->pattern_type === 'weekly' && $request->has('day_of_week')) {
                            // Fix error handling for array or string inputs
                            try {
                                if (is_array($request->day_of_week)) {
                                    $uniqueDays = array_unique($request->day_of_week);
                                    $pattern->day_of_week = implode(',', $uniqueDays);
                                } else if (is_string($request->day_of_week) && strpos($request->day_of_week, ',') !== false) {
                                    // Already a comma-separated string, keep as is
                                    $pattern->day_of_week = $request->day_of_week;
                                } else {
                                    // Single value
                                    $pattern->day_of_week = (string)$request->day_of_week;
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error processing day_of_week field:', [
                                    'error' => $e->getMessage(),
                                    'value' => $request->day_of_week
                                ]);
                                $pattern->day_of_week = (string)Carbon::parse($appointment->date)->dayOfWeek;
                            }
                        } else {
                            $pattern->day_of_week = null;
                        }
                        
                        $pattern->recurrence_end = $request->recurrence_end;
                        $pattern->save();
                        
                        // Update future occurrences
                        $this->updateFutureOccurrences($appointment);
                    }
                    
                    // Update participants
                    $this->updateAppointmentParticipants($appointment, $request);
                    $this->notifyUpdatedAppointment($appointment);
                    
                    DB::commit();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Appointment updated successfully',
                        'appointment_id' => $appointment->appointment_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete only future appointment occurrences for dates on or after the given date
     */
    private function createExceptionsAfterDate($appointment, $date)
    {
        $formattedDate = $date instanceof Carbon ? $date->format('Y-m-d') : Carbon::parse($date)->format('Y-m-d');
        
        // Get today's date to preserve past occurrences
        $today = Carbon::today()->format('Y-m-d');
        
        // Log what we're about to do
        \Log::info("Handling occurrences for appointment ID {$appointment->appointment_id}, preserving dates before {$today}");
        
        // Get count of occurrences before making changes
        $totalCount = $appointment->occurrences()->count();
        $futureCount = $appointment->occurrences()->where('occurrence_date', '>=', $formattedDate)->count();
        $pastCount = $totalCount - $futureCount;
        
        \Log::info("Total occurrences: {$totalCount}, Future: {$futureCount}, Past: {$pastCount}");
        
        // FIXED: Only delete occurrences on or after the given date
        // AND ensure the date is not in the past (this is the key fix)
        $deleted = $appointment->occurrences()
            ->where('occurrence_date', '>=', $formattedDate)
            ->where('occurrence_date', '>=', $today) // CRITICAL: Only delete future occurrences
            ->delete();
        
        \Log::info("Deleted {$deleted} future occurrences for appointment ID {$appointment->appointment_id}");
        
        // No need to create exceptions - we're fully deleting the occurrences
    }

    /**
     * Create a new appointment from the request data
     */
    private function createAppointmentFromRequest(Request $request, $userId)
    {
        $appointment = new Appointment();
        $appointment->appointment_type_id = $request->appointment_type_id;
        $appointment->title = $request->title;
        $appointment->description = $request->description ?? null;
        $appointment->other_type_details = $request->other_type_details;
        $appointment->date = $request->date;
        $appointment->start_time = $request->is_flexible_time ? null : $request->start_time;
        $appointment->end_time = $request->is_flexible_time ? null : $request->end_time;
        $appointment->is_flexible_time = (bool) $request->is_flexible_time;
        $appointment->meeting_location = $request->meeting_location;
        $appointment->status = 'scheduled';
        $appointment->notes = $request->notes;
        $appointment->created_by = $userId;
        $appointment->save();
        
        // Create recurring pattern if needed
        if ($request->has('is_recurring') && $request->is_recurring) {
            $pattern = new RecurringPattern();
            $pattern->appointment_id = $appointment->appointment_id;
            $pattern->pattern_type = $request->pattern_type;
            
            if ($request->pattern_type === 'weekly' && $request->has('day_of_week')) {
                // Make sure we're handling the day_of_week values correctly
                if (is_array($request->day_of_week)) {
                    $uniqueDays = array_unique($request->day_of_week);
                    $pattern->day_of_week = implode(',', $uniqueDays);
                } else if (is_string($request->day_of_week) && strpos($request->day_of_week, ',') !== false) {
                    $pattern->day_of_week = $request->day_of_week;
                } else {
                    $pattern->day_of_week = (string)$request->day_of_week;
                }
            } else {
                $pattern->day_of_week = null;
            }
            
            $pattern->recurrence_end = $request->recurrence_end;
            $pattern->save();
        }
        
        return $appointment;
    }

    /**
     * Update future occurrences of an appointment
     */
    private function updateFutureOccurrences($appointment)
    {
        // Delete all future occurrences
        $today = Carbon::today()->format('Y-m-d');
        $appointment->occurrences()
            ->where('occurrence_date', '>=', $today)
            ->delete();
        
        // Generate new occurrences
        $appointment->generateOccurrences(3);
    }

    /**
     * Copy participants from one appointment to another with optional updates from request
     */
    private function copyParticipantsToAppointment($sourceAppointment, $targetAppointment, $request = null)
    {
        // If request contains participants, use those
        if ($request && $request->has('participants') && is_array($request->participants)) {
            foreach ($request->participants as $type => $ids) {
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        $participant = new AppointmentParticipant();
                        $participant->appointment_id = $targetAppointment->appointment_id;
                        $participant->participant_type = $type;
                        $participant->participant_id = $id;
                        // The current user is set as organizer
                        $participant->is_organizer = ($type === 'cose_user' && (int)$id === Auth::id());
                        $participant->save();
                    }
                }
            }
            
            // Ensure the current user is always added as a participant/organizer
            $userParticipantExists = AppointmentParticipant::where('appointment_id', $targetAppointment->appointment_id)
                ->where('participant_type', 'cose_user')
                ->where('participant_id', Auth::id())
                ->exists();
                
            if (!$userParticipantExists) {
                $participant = new AppointmentParticipant();
                $participant->appointment_id = $targetAppointment->appointment_id;
                $participant->participant_type = 'cose_user';
                $participant->participant_id = Auth::id();
                $participant->is_organizer = true;
                $participant->save();
            }
        }
        // Otherwise copy from source appointment
        else {
            foreach ($sourceAppointment->participants as $sourceParticipant) {
                $participant = new AppointmentParticipant();
                $participant->appointment_id = $targetAppointment->appointment_id;
                $participant->participant_type = $sourceParticipant->participant_type;
                $participant->participant_id = $sourceParticipant->participant_id;
                $participant->is_organizer = $sourceParticipant->is_organizer;
                $participant->save();
            }
        }
    }

    /**
     * Update the participants for an appointment
     */
    private function updateAppointmentParticipants($appointment, $request)
    {
        // Delete all existing participants EXCEPT the currently logged-in user
        AppointmentParticipant::where('appointment_id', $appointment->appointment_id)
            ->where(function($query) {
                $query->where('participant_type', '!=', 'cose_user')
                    ->orWhere('participant_id', '!=', Auth::id());
            })
            ->delete();
        
        // Add new participants from the request
        if ($request->has('participants') && is_array($request->participants)) {
            foreach ($request->participants as $type => $ids) {
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        // Skip if this is the current user (we kept their entry)
                        if ($type === 'cose_user' && (int)$id === Auth::id()) {
                            continue;
                        }
                        
                        $participant = new AppointmentParticipant();
                        $participant->appointment_id = $appointment->appointment_id;
                        $participant->participant_type = $type;
                        $participant->participant_id = $id;
                        $participant->is_organizer = false; // Only the creator is organizer
                        $participant->save();
                    }
                }
            }
        }
        
        // Ensure the current user is always a participant/organizer
        $userParticipantExists = AppointmentParticipant::where('appointment_id', $appointment->appointment_id)
            ->where('participant_type', 'cose_user')
            ->where('participant_id', Auth::id())
            ->exists();
            
        if (!$userParticipantExists) {
            $participant = new AppointmentParticipant();
            $participant->appointment_id = $appointment->appointment_id;
            $participant->participant_type = 'cose_user';
            $participant->participant_id = Auth::id();
            $participant->is_organizer = true;
            $participant->save();
        } else {
            // Make sure the current user is marked as organizer
            AppointmentParticipant::where('appointment_id', $appointment->appointment_id)
                ->where('participant_type', 'cose_user')
                ->where('participant_id', Auth::id())
                ->update(['is_organizer' => true]);
        }
    }
    
    /**
     * Cancel an appointment with enhanced options for recurring events
     */
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'occurrence_date' => 'sometimes|date',
            'cancel_type' => 'required|in:single,future',
            'reason' => 'nullable|string|max:500',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify user password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'error' => 'invalid_password',
                'message' => 'The provided password is incorrect.'
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $appointmentId = $request->appointment_id;
            $appointment = Appointment::with(['recurringPattern', 'occurrences'])->findOrFail($appointmentId);
            
            // Check if user has permission to cancel this appointment
            if (!$this->hasManagePermission($appointmentId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this appointment.'
                ], 403);
            }
            
            $isRecurring = $appointment->recurringPattern ? true : false;
            $occurrenceDate = $request->has('occurrence_date') ? Carbon::parse($request->occurrence_date) : null;
            $reason = $request->reason ?: 'Canceled by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            
            \Log::info('Canceling appointment', [
                'appointment_id' => $appointmentId,
                'is_recurring' => $isRecurring,
                'cancel_type' => $request->cancel_type,
                'occurrence_date' => $occurrenceDate ? $occurrenceDate->format('Y-m-d') : null
            ]);
            
            if ($isRecurring) {
                // Handle recurring appointment cancellation
                if ($request->cancel_type === 'single') {
                    // Cancel only this occurrence
                    $this->cancelSingleOccurrence($appointment, $occurrenceDate, $reason);
                    $message = 'The selected occurrence has been canceled.';

                    $this->notifyCanceledAppointment($appointment, $occurrenceDate, false);
                } else {
                    // Cancel this and all future occurrences
                    $this->cancelFutureOccurrences($appointment, $occurrenceDate, $reason);
                    $message = 'This and all future occurrences have been canceled.';

                    $this->notifyCanceledAppointment($appointment, $occurrenceDate, true);
                }
            } else {
                // Handle non-recurring appointment cancellation
                $appointment->status = 'canceled';
                $appointment->notes = $appointment->notes . "\n\nCanceled: " . $reason;
                $appointment->save();
                
                // Also update the occurrence status
                if ($appointment->occurrences()->exists()) {
                    $appointment->occurrences()->update([
                        'status' => 'canceled'
                    ]);
                }
                
                $message = 'Appointment has been canceled.';

                // Send notification for appointment cancellation
                $this->notifyCanceledAppointment($appointment);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error canceling appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a single occurrence by creating an exception
     */
    private function cancelSingleOccurrence(Appointment $appointment, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception('Occurrence date is required');
        }
        
        // Format the date for consistency
        $formattedDate = $occurrenceDate->format('Y-m-d');
        
        \Log::info('Creating exception for single occurrence', [
            'date' => $formattedDate
        ]);
        
        // Delete any existing exception for this date to avoid conflicts
        AppointmentException::where('appointment_id', $appointment->appointment_id)
            ->where('exception_date', $formattedDate)
            ->delete();
        
        // Create a new exception record
        $exception = new AppointmentException();
        $exception->appointment_id = $appointment->appointment_id;
        $exception->exception_date = $formattedDate;
        $exception->status = 'canceled';
        $exception->reason = $reason;
        $exception->created_by = Auth::id();
        $exception->save();
        
        // Also update the occurrence status if it exists
        $occurrence = $appointment->occurrences()
            ->whereDate('occurrence_date', $formattedDate)
            ->first();
        
        if ($occurrence) {
            $occurrence->status = 'canceled';
            $occurrence->save();
        }
        
        \Log::info('Exception created successfully', [
            'status' => $exception->status
        ]);
        
        return $exception;
    }

    /**
     * Cancel this and all future occurrences
     * Keeps the selected occurrence marked as canceled in the database
     */
    private function cancelFutureOccurrences(Appointment $appointment, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception('Occurrence date is required');
        }
        
        $recurringPattern = $appointment->recurringPattern;
        
        if (!$recurringPattern) {
            throw new \Exception('Appointment is not recurring');
        }
        
        // Format both dates as strings for proper comparison
        $originalDate = $appointment->date->format('Y-m-d');
        $occurrenceDateStr = $occurrenceDate->format('Y-m-d');
        
        \Log::info('Processing cancel future occurrences', [
            'original_date' => $originalDate,
            'occurrence_date' => $occurrenceDateStr,
            'are_equal' => ($originalDate == $occurrenceDateStr)
        ]);
        
        // If canceling from the very first occurrence:
        if ($originalDate == $occurrenceDateStr) {
            // Update the main appointment status
            $appointment->status = 'canceled';
            $appointment->notes = $appointment->notes . "\n\nCanceled: " . $reason;
            $appointment->save();
            
            // Update all occurrences to canceled
            $appointment->occurrences()->update([
                'status' => 'canceled'
            ]);
            
            \Log::info('Canceled entire recurring appointment', [
                'appointment_id' => $appointment->appointment_id
            ]);
        } else {
            // We're canceling from a middle occurrence
            
            // 1. Create exception/cancel the selected occurrence
            $this->cancelSingleOccurrence($appointment, $occurrenceDate, $reason);
            
            // 2. Handle all future dates - update the recurrence end date to the day before
            $newEndDate = $occurrenceDate->copy()->subDay();
            $recurringPattern->recurrence_end = $newEndDate;
            $recurringPattern->save();
            
            // 3. Delete all occurrences AFTER the target date (not including the selected date)
            $nextDay = $occurrenceDate->copy()->addDay()->format('Y-m-d'); 
            $deletedCount = $appointment->occurrences()
                ->whereDate('occurrence_date', '>', $occurrenceDateStr)
                ->delete();
            
            \Log::info('Canceled future occurrences', [
                'appointment_id' => $appointment->appointment_id,
                'selected_date' => $occurrenceDateStr,
                'new_end_date' => $newEndDate->format('Y-m-d'),
                'deleted_future_occurrences' => $deletedCount
            ]);
        }
        
        return true;
    }
    
    /**
     * Generate occurrences for a recurring appointment
     */
    public function generateOccurrences(Appointment $appointment)
    {
        // Method stub for generating appointment occurrences
        // Will be implemented in future updates
    }
    
    /**
     * Check if the current user is an organizer for this appointment
     */
    private function isOrganizer($participants, $userId)
    {
        foreach ($participants as $participant) {
            if ($participant->participant_type === 'cose_user' && 
                $participant->participant_id === $userId && 
                $participant->is_organizer) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Format participants for event display
     */
    private function formatParticipants($participants)
    {
        $formatted = [];
        
        foreach ($participants as $participant) {
            // Skip if participant doesn't exist
            if (!$participant->participant) {
                continue;
            }
            
            $name = '';
            
            // Format name based on participant type
            switch ($participant->participant_type) {
                case 'cose_user':
                    $name = $participant->participant->first_name . ' ' . $participant->participant->last_name;
                    break;
                case 'beneficiary':
                    $name = $participant->participant->first_name . ' ' . $participant->participant->last_name;
                    break;
                case 'family_member':
                    $name = $participant->participant->first_name . ' ' . $participant->participant->last_name;
                    break;
            }
            
            $formatted[] = [
                'id' => $participant->participant_id,
                'type' => $participant->participant_type,
                'name' => $name,
                'is_organizer' => $participant->is_organizer
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Get CSS class for an event based on appointment type
     */
    private function getEventClass($typeId)
    {
        $typeClasses = [
            1 => 'quarterly-feedback',
            2 => 'skills-enhancement',
            3 => 'council',
            4 => 'health-board',
            5 => 'liga',
            6 => 'hmo',
            7 => 'assessment',
            8 => 'careplan',
            9 => 'team',
            10 => 'mentoring',
            11 => 'other',
        ];
        
        return 'event-' . ($typeClasses[$typeId] ?? 'other');
    }
    
    /**
     * Check if user has permission to manage appointments
     * 
     * Admins and care managers have full access
     * Care workers can only manage appointments they created
     */
    private function hasManagePermission($appointmentId = null)
    {
        $user = Auth::user();
        
        // Admin and care managers can manage all appointments
        if ($user->role_id <= 2) {
            return true;
        }
        
        // Care workers can only manage appointments they created/organized
        if ($appointmentId) {
            return AppointmentParticipant::where('appointment_id', $appointmentId)
                ->where('participant_type', 'cose_user')
                ->where('participant_id', $user->id)
                ->where('is_organizer', true)
                ->exists();
        }
        
        return false;
    }

    /**
     * Send notification to all participants of an appointment
     */
    private function notifyAppointmentParticipants($appointment, $type, $message, $title = null)
    {
        // Get all participants
        $participants = $appointment->participants;
        $notifiedUsers = [];
        
        foreach ($participants as $participant) {
            $userId = $participant->participant_id;
            $userType = $participant->participant_type;
            
            // Convert participant type for notification system
            if ($userType === 'cose_user') {
                $userType = 'cose_staff';
            }
            
            // Skip duplicates
            $key = $userType . '-' . $userId;
            if (in_array($key, $notifiedUsers)) {
                continue;
            }
            
            // Create notification with ALL required fields
            Notification::create([
                'user_id' => $userId,
                'user_type' => $userType,
                'message_title' => $title ?? 'Appointment Notification',
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
            
            $notifiedUsers[] = $key;
        }
        
        \Log::info("Sent notifications about appointment {$appointment->appointment_id}", [
            'type' => $type,
            'recipient_count' => count($notifiedUsers)
        ]);
    }

    /**
     * Notify participants about a new appointment
     */
    private function notifyNewAppointment($appointment)
    {
        $formattedDate = Carbon::parse($appointment->date)->format('F j, Y');
        $timeInfo = $appointment->is_flexible_time ? 
            "with flexible timing" : 
            "from " . Carbon::parse($appointment->start_time)->format('g:i A') . " to " . 
            Carbon::parse($appointment->end_time)->format('g:i A');
        
        $location = $appointment->meeting_location;
        $type = $appointment->type ? $appointment->type->type_name : "Meeting";
        
        $message = "You have a new {$type}: \"{$appointment->title}\" scheduled on {$formattedDate} {$timeInfo} at {$location}. Please mark your calendar!";
        $title = "New Appointment Scheduled";
        
        $this->notifyAppointmentParticipants(
            $appointment, 
            'internal_appointment_created',
            $message,
            $title
        );
    }

    /**
     * Notify participants about an updated appointment
     */
    private function notifyUpdatedAppointment($appointment)
    {
        $formattedDate = Carbon::parse($appointment->date)->format('F j, Y');
        $timeInfo = $appointment->is_flexible_time ? 
            "with flexible timing" : 
            "from " . Carbon::parse($appointment->start_time)->format('g:i A') . " to " . 
            Carbon::parse($appointment->end_time)->format('g:i A');
        
        $location = $appointment->meeting_location;
        
        $message = "Important: Your appointment \"{$appointment->title}\" on {$formattedDate} has been updated. The meeting is now scheduled for {$formattedDate} {$timeInfo} at {$location}. Please update your calendar!";
        $title = "Appointment Details Updated";
        
        $this->notifyAppointmentParticipants(
            $appointment, 
            'internal_appointment_updated',
            $message,
            $title
        );
    }

    /**
     * Notify participants about a canceled appointment
     */
    private function notifyCanceledAppointment($appointment, $occurrenceDate = null, $isFuture = false)
    {
        $formattedDate = $occurrenceDate ? 
            Carbon::parse($occurrenceDate)->format('F j, Y') : 
            Carbon::parse($appointment->date)->format('F j, Y');
        
        $timeInfo = $appointment->is_flexible_time ? 
            "with flexible timing" : 
            "scheduled for " . Carbon::parse($appointment->start_time)->format('g:i A');
            
        if ($isFuture) {
            $message = "CANCELLATION NOTICE: Your appointment \"{$appointment->title}\" on {$formattedDate} {$timeInfo} has been canceled, along with all future occurrences of this series. Please update your calendar accordingly.";
        } else {
            $message = "CANCELLATION NOTICE: Your appointment \"{$appointment->title}\" on {$formattedDate} {$timeInfo} has been canceled. Please update your calendar.";
        }
        
        $title = "Appointment Canceled";
        
        $this->notifyAppointmentParticipants(
            $appointment, 
            'internal_appointment_canceled',
            $message,
            $title
        );
    }

    /**
     * Send reminder notifications for upcoming appointments
     * This is called by the scheduled command SendInternalAppointmentReminders
     *
     * @return int Number of notifications sent
     */
    public function sendAppointmentReminders()
    {
        // Get appointments occurring tomorrow
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        // Find all occurrences scheduled for tomorrow
        $occurrences = AppointmentOccurrence::with(['appointment', 'appointment.participants', 'appointment.type'])
            ->where('occurrence_date', $tomorrow)
            ->where('status', 'scheduled')
            ->get();
        
        $count = 0;
        
        foreach ($occurrences as $occurrence) {
            $appointment = $occurrence->appointment;
            
            if (!$appointment) {
                continue;
            }
            
            // Format date and time for notification
            $formattedDate = Carbon::parse($occurrence->occurrence_date)->format('F j, Y');
            $timeInfo = $appointment->is_flexible_time ? 
                "with flexible timing" : 
                "from " . Carbon::parse($occurrence->start_time)->format('g:i A') . " to " . 
                Carbon::parse($occurrence->end_time)->format('g:i A');
            
            $location = $appointment->meeting_location;
            $type = $appointment->type ? $appointment->type->type_name : "Meeting";
            
            $message = "REMINDER: You have a {$type} tomorrow - \"{$appointment->title}\" on {$formattedDate} {$timeInfo} at {$location}. We look forward to your participation!";
            $title = "Tomorrow's Appointment Reminder";
            
            // Send notifications to all participants
            $this->notifyAppointmentParticipants(
                $appointment, 
                'internal_appointment_reminder',
                $message,
                $title
            );
            
            $count++;
        }
        
        \Log::info("Sent {$count} internal appointment reminders for tomorrow");
        return $count;
    }
    
}
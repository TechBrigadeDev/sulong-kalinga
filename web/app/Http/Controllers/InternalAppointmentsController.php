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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        // Method stub for updating existing appointments
        // Will be implemented in future updates
    }
    
    /**
     * Cancel an appointment or occurrences
     */
    public function cancel(Request $request)
    {
        // Method stub for cancelling appointments/occurrences
        // Will be implemented in future updates
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
    
}
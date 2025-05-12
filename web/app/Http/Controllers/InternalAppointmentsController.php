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
        // Method stub for creating new appointments
        // Will be implemented in future updates
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
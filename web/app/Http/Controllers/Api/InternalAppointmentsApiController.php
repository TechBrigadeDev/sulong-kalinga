<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentOccurrence;
use App\Models\AppointmentParticipant;
use App\Models\AppointmentType;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InternalAppointmentsApiController extends Controller
{
    /**
     * List internal appointments with occurrences and participants (for calendar/mobile).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Base query: eager load type, participants, and occurrences
        $query = Appointment::with([
            'appointmentType',
            'participants',
            'occurrences'
        ]);

        // Care Worker: only see appointments they organize or participate in
        if ($user->role_id == 3) {
            $query->whereHas('participants', function($q) use ($user) {
                $q->where('participant_type', 'cose_user')
                  ->where('participant_id', $user->id);
            });
        }

        // Optional: filter by date range for calendar
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('occurrences', function($q) use ($request) {
                $q->whereBetween('occurrence_date', [$request->start_date, $request->end_date]);
            });
        }

        // Optional: filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Show a single internal appointment with occurrences and participants.
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $appointment = Appointment::with([
            'appointmentType',
            'participants',
            'occurrences'
        ])->find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        // Role-based access check
        if ($user->role_id == 3) {
            $isParticipant = $appointment->participants->where('participant_type', 'cose_user')
                ->where('participant_id', $user->id)->count() > 0;
            if (!$isParticipant) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    /**
     * Store a newly created internal appointment (with occurrences and participants).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_type_id' => 'required|exists:appointment_types,appointment_type_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'other_type_details' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_flexible_time' => 'boolean',
            'meeting_location' => 'nullable|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*.participant_type' => 'required|in:cose_user,beneficiary,family_member',
            'participants.*.participant_id' => 'required|integer',
            'participants.*.is_organizer' => 'boolean',
            'notes' => 'nullable|string',
            // Recurrence fields (optional)
            'recurrence' => 'nullable|array',
            'recurrence.pattern_type' => 'nullable|in:daily,weekly,monthly',
            'recurrence.day_of_week' => 'nullable|array',
            'recurrence.recurrence_end' => 'nullable|date|after_or_equal:date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Create the appointment
            $appointment = Appointment::create([
                'appointment_type_id' => $request->appointment_type_id,
                'title' => $request->title,
                'description' => $request->description,
                'other_type_details' => $request->other_type_details,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_flexible_time' => $request->is_flexible_time ?? false,
                'meeting_location' => $request->meeting_location,
                'status' => 'scheduled',
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            // 2. Add participants
            foreach ($request->participants as $p) {
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_type' => $p['participant_type'],
                    'participant_id' => $p['participant_id'],
                    'is_organizer' => $p['is_organizer'] ?? false,
                ]);
            }

            // 3. Create occurrences (single or recurring)
            $occurrences = [];
            if ($request->filled('recurrence.pattern_type')) {
                // Recurring logic (simple version, expand as needed)
                $start = Carbon::parse($request->date);
                $end = Carbon::parse($request->recurrence['recurrence_end']);
                $pattern = $request->recurrence['pattern_type'];
                $daysOfWeek = $request->recurrence['day_of_week'] ?? [];

                while ($start->lte($end)) {
                    $add = false;
                    if ($pattern === 'daily') {
                        $add = true;
                    } elseif ($pattern === 'weekly' && in_array($start->dayOfWeek, $daysOfWeek)) {
                        $add = true;
                    } elseif ($pattern === 'monthly' && $start->day === Carbon::parse($request->date)->day) {
                        $add = true;
                    }
                    if ($add) {
                        $occurrences[] = [
                            'appointment_id' => $appointment->appointment_id,
                            'occurrence_date' => $start->toDateString(),
                            'start_time' => $request->start_time,
                            'end_time' => $request->end_time,
                            'status' => 'scheduled',
                        ];
                    }
                    $start->addDay();
                }
            } else {
                // Single occurrence
                $occurrences[] = [
                    'appointment_id' => $appointment->appointment_id,
                    'occurrence_date' => $request->date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'status' => 'scheduled',
                ];
            }
            foreach ($occurrences as $occ) {
                AppointmentOccurrence::create($occ);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $appointment->fresh(['appointmentType', 'participants', 'occurrences'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update an existing internal appointment (with occurrences and participants).
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_type_id' => 'required|exists:appointment_types,appointment_type_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'other_type_details' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_flexible_time' => 'boolean',
            'meeting_location' => 'nullable|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*.participant_type' => 'required|in:cose_user,beneficiary,family_member',
            'participants.*.participant_id' => 'required|integer',
            'participants.*.is_organizer' => 'boolean',
            'notes' => 'nullable|string',
            // Recurrence fields (optional)
            'recurrence' => 'nullable|array',
            'recurrence.pattern_type' => 'nullable|in:daily,weekly,monthly',
            'recurrence.day_of_week' => 'nullable|array',
            'recurrence.recurrence_end' => 'nullable|date|after_or_equal:date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Update the appointment
            $appointment->update([
                'appointment_type_id' => $request->appointment_type_id,
                'title' => $request->title,
                'description' => $request->description,
                'other_type_details' => $request->other_type_details,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_flexible_time' => $request->is_flexible_time ?? false,
                'meeting_location' => $request->meeting_location,
                'notes' => $request->notes,
                'updated_by' => $request->user()->id,
            ]);

            // 2. Update participants (delete old, add new)
            AppointmentParticipant::where('appointment_id', $appointment->appointment_id)->delete();
            foreach ($request->participants as $p) {
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_type' => $p['participant_type'],
                    'participant_id' => $p['participant_id'],
                    'is_organizer' => $p['is_organizer'] ?? false,
                ]);
            }

            // 3. Update occurrences (delete old, add new)
            AppointmentOccurrence::where('appointment_id', $appointment->appointment_id)->delete();
            $occurrences = [];
            if ($request->filled('recurrence.pattern_type')) {
                $start = Carbon::parse($request->date);
                $end = Carbon::parse($request->recurrence['recurrence_end']);
                $pattern = $request->recurrence['pattern_type'];
                $daysOfWeek = $request->recurrence['day_of_week'] ?? [];

                while ($start->lte($end)) {
                    $add = false;
                    if ($pattern === 'daily') {
                        $add = true;
                    } elseif ($pattern === 'weekly' && in_array($start->dayOfWeek, $daysOfWeek)) {
                        $add = true;
                    } elseif ($pattern === 'monthly' && $start->day === Carbon::parse($request->date)->day) {
                        $add = true;
                    }
                    if ($add) {
                        $occurrences[] = [
                            'appointment_id' => $appointment->appointment_id,
                            'occurrence_date' => $start->toDateString(),
                            'start_time' => $request->start_time,
                            'end_time' => $request->end_time,
                            'status' => 'scheduled',
                        ];
                    }
                    $start->addDay();
                }
            } else {
                $occurrences[] = [
                    'appointment_id' => $appointment->appointment_id,
                    'occurrence_date' => $request->date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'status' => 'scheduled',
                ];
            }
            foreach ($occurrences as $occ) {
                AppointmentOccurrence::create($occ);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $appointment->fresh(['appointmentType', 'participants', 'occurrences'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Cancel (archive) an appointment.
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Password check (for security, similar to web)
        $user = $request->user();
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Archive the appointment
            \App\Models\AppointmentArchive::create([
                'appointment_id' => $appointment->appointment_id,
                'original_appointment_id' => $appointment->appointment_id,
                'title' => $appointment->title,
                'appointment_type_id' => $appointment->appointment_type_id,
                'description' => $appointment->description,
                'other_type_details' => $appointment->other_type_details,
                'date' => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'is_flexible_time' => $appointment->is_flexible_time,
                'meeting_location' => $appointment->meeting_location,
                'status' => 'cancelled',
                'notes' => $appointment->notes,
                'created_by' => $appointment->created_by,
                'reason' => $request->reason,
                'archived_by' => $user->id,
            ]);

            // Delete the appointment (cascade deletes occurrences/participants)
            $appointment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled and archived.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get a flat list of appointment events for calendar (like getAppointments in web).
     */
    public function calendarEvents(Request $request)
    {
        $user = $request->user();

        $query = Appointment::with(['appointmentType', 'participants', 'occurrences']);

        // Care Worker: only see appointments they organize or participate in
        if ($user->role_id == 3) {
            $query->whereHas('participants', function($q) use ($user) {
                $q->where('participant_type', 'cose_user')
                  ->where('participant_id', $user->id);
            });
        }

        // Optional: filter by date range for calendar
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('occurrences', function($q) use ($request) {
                $q->whereBetween('occurrence_date', [$request->start_date, $request->end_date]);
            });
        }

        $appointments = $query->get();

        // Flatten occurrences into calendar events
        $events = [];
        foreach ($appointments as $appointment) {
            foreach ($appointment->occurrences as $occ) {
                $events[] = [
                    'id' => $occ->occurrence_id,
                    'appointment_id' => $appointment->appointment_id,
                    'title' => $appointment->title,
                    'start' => $occ->occurrence_date . 'T' . ($occ->start_time ?? '00:00:00'),
                    'end' => $occ->occurrence_date . 'T' . ($occ->end_time ?? '00:00:00'),
                    'status' => $occ->status,
                    'type' => $appointment->appointmentType->type_name ?? null,
                    'color' => $this->getStatusColor($occ->status),
                    'location' => $appointment->meeting_location,
                    'notes' => $occ->notes ?? $appointment->notes,
                    'extendedProps' => [
                        'participants' => $appointment->participants,
                        'is_flexible_time' => $appointment->is_flexible_time,
                        'appointment_type_id' => $appointment->appointment_type_id,
                    ]
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Helper: Get color for status (for calendar).
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'scheduled': return '#007bff';
            case 'completed': return '#28a745';
            case 'cancelled': return '#dc3545';
            default: return '#6c757d';
        }
    }

    /**
     * Get all appointment types for dropdowns/search.
     */
    public function listAppointmentTypes()
    {
        $types = AppointmentType::all();
        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Get all staff users grouped by role (for participant selection).
     */
    public function listStaff()
    {
        $staff = \App\Models\User::whereIn('role_id', [1, 2, 3])
            ->orderBy('role_id')
            ->orderBy('last_name')
            ->get()
            ->groupBy('role_id');
        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    /**
     * Get all beneficiaries (for admin/care manager).
     */
    public function listBeneficiaries(Request $request)
    {
        $query = Beneficiary::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%$search%")
                  ->orWhere('last_name', 'ILIKE', "%$search%");
            });
        }
        $beneficiaries = $query->limit(50)->get();
        return response()->json([
            'success' => true,
            'data' => $beneficiaries
        ]);
    }

    /**
     * Get all family members (for admin/care manager).
     */
    public function listFamilyMembers(Request $request)
    {
        $query = FamilyMember::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%$search%")
                  ->orWhere('last_name', 'ILIKE', "%$search%");
            });
        }
        $family = $query->limit(50)->get();
        return response()->json([
            'success' => true,
            'data' => $family
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentType;

class InternalAppointmentsApiController extends Controller
{
    /**
     * List internal appointments with occurrences and participants (for calendar/mobile).
     */
    public function index(Request $request)
    {
        $user = $request->user();

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
    public function show($id)
    {
        $appointment = \App\Models\Appointment::with([
            'appointmentType',
            'participants',
            'occurrences'
        ])->findOrFail($id);

        // Add full name to each participant
        $participantsWithNames = $appointment->participants->map(function ($participant) {
            $fullName = null;
            if ($participant->participant_type === 'cose_user') {
                $user = \App\Models\User::find($participant->participant_id);
                if ($user) {
                    $fullName = trim($user->first_name . ' ' . $user->last_name);
                }
            } elseif ($participant->participant_type === 'beneficiary') {
                $beneficiary = \App\Models\Beneficiary::find($participant->participant_id);
                if ($beneficiary) {
                    $fullName = trim($beneficiary->first_name . ' ' . ($beneficiary->middle_name ? $beneficiary->middle_name . ' ' : '') . $beneficiary->last_name);
                }
            } elseif ($participant->participant_type === 'family_member') {
                $family = \App\Models\FamilyMember::find($participant->participant_id);
                if ($family) {
                    $fullName = trim($family->first_name . ' ' . $family->last_name);
                }
            }
            $data = $participant->toArray();
            $data['full_name'] = $fullName;
            return $data;
        });

        $data = $appointment->toArray();
        $data['participants'] = $participantsWithNames;

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get a flat list of appointment events for calendar (like getAppointments in web).
     */
    public function calendarEvents(Request $request)
    {
        $user = $request->user();

        $query = Appointment::with(['appointmentType', 'participants', 'occurrences']);

        if ($user->role_id == 3) {
            $query->whereHas('participants', function($q) use ($user) {
                $q->where('participant_type', 'cose_user')
                  ->where('participant_id', $user->id);
            });
        }

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
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VisitationApiController extends Controller
{
    /**
     * List visitations with occurrences and related info (read-only for mobile).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Visitation::with([
            'beneficiary',
            'careWorker',
            'occurrences'
        ]);

        // Care Worker: only their own visitations
        if ($user->role_id == 3) {
            $query->where('care_worker_id', $user->id);
        }
        // Family or Beneficiary: only visitations of their assigned care worker
        elseif (in_array($user->role_id, [4, 5])) {
            // Find the beneficiary_id for this user (if family, get related beneficiary)
            $beneficiaryId = null;
            if ($user->role_id == 5) { // Beneficiary
                $beneficiaryId = $user->beneficiary_id ?? null;
            } elseif ($user->role_id == 4) { // Family
                $beneficiaryId = $user->familyMember->beneficiary_id ?? null;
            }
            if ($beneficiaryId) {
                $query->where('beneficiary_id', $beneficiaryId);
            } else {
                // No beneficiary assigned, return empty
                return response()->json(['success' => true, 'data' => []]);
            }
        }
        // Care Manager/Admin: see all

        // Optional: filter by date range
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereHas('occurrences', function($q) use ($request) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $q->whereBetween('occurrence_date', [$request->start_date, $request->end_date]);
                } elseif ($request->filled('start_date')) {
                    $q->where('occurrence_date', '>=', $request->start_date);
                } else {
                    $q->where('occurrence_date', '<=', $request->end_date);
                }
            });
        }

        // Optional: filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visitations = $query->orderBy('visitation_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $visitations
        ]);
    }

    /**
     * Show a single visitation with all details (read-only for mobile).
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $visitation = Visitation::with([
            'beneficiary',
            'careWorker',
            'occurrences'
        ])->find($id);

        if (!$visitation) {
            return response()->json([
                'success' => false,
                'message' => 'Visitation not found.'
            ], 404);
        }

        // Role-based access check
        if ($user->role_id == 3 && $visitation->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        if (in_array($user->role_id, [4, 5])) {
            $beneficiaryId = null;
            if ($user->role_id == 5) { // Beneficiary
                $beneficiaryId = $user->beneficiary_id ?? null;
            } elseif ($user->role_id == 4) { // Family
                $beneficiaryId = $user->familyMember->beneficiary_id ?? null;
            }
            if ($visitation->beneficiary_id != $beneficiaryId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $visitation
        ]);
    }

    /**
     * Get a flat list of visitation events for calendar (like getVisitations in web).
     */
    public function calendarEvents(Request $request)
    {
        $user = $request->user();

        $query = Visitation::with(['beneficiary', 'careWorker', 'occurrences']);

        // Role-based filtering (same as index)
        if ($user->role_id == 3) {
            $query->where('care_worker_id', $user->id);
        } elseif (in_array($user->role_id, [4, 5])) {
            $beneficiaryId = null;
            if ($user->role_id == 5) {
                $beneficiaryId = $user->beneficiary_id ?? null;
            } elseif ($user->role_id == 4) {
                $beneficiaryId = $user->familyMember->beneficiary_id ?? null;
            }
            if ($beneficiaryId) {
                $query->where('beneficiary_id', $beneficiaryId);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        }

        // Date range filter
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereHas('occurrences', function($q) use ($request) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $q->whereBetween('occurrence_date', [$request->start_date, $request->end_date]);
                } elseif ($request->filled('start_date')) {
                    $q->where('occurrence_date', '>=', $request->start_date);
                } else {
                    $q->where('occurrence_date', '<=', $request->end_date);
                }
            });
        }

        $visitations = $query->get();

        // Flatten occurrences into calendar events
        $events = [];
        foreach ($visitations as $visitation) {
            foreach ($visitation->occurrences as $occ) {
                $events[] = [
                    'id' => $occ->occurrence_id,
                    'visitation_id' => $visitation->visitation_id,
                    'title' => $visitation->beneficiary->full_name ?? 'Visitation',
                    'start' => $occ->occurrence_date . 'T' . ($occ->start_time ?? '00:00:00'),
                    'end' => $occ->occurrence_date . 'T' . ($occ->end_time ?? '00:00:00'),
                    'status' => $occ->status,
                    'visit_type' => $visitation->visit_type,
                    'care_worker' => $visitation->careWorker->full_name ?? null,
                    'beneficiary' => $visitation->beneficiary->full_name ?? null,
                    'notes' => $occ->notes ?? $visitation->notes,
                    'color' => $this->getStatusColor($occ->status),
                    'extendedProps' => [
                        'care_worker_id' => $visitation->care_worker_id,
                        'beneficiary_id' => $visitation->beneficiary_id,
                        'is_flexible_time' => $visitation->is_flexible_time,
                        'work_shift_id' => $visitation->work_shift_id,
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
     * Get beneficiary details for a given beneficiary_id.
     */
    public function showBeneficiary($id)
    {
        $beneficiary = Beneficiary::with(['generalCarePlan', 'assignedCareWorker'])->find($id);

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $beneficiary
        ]);
    }

    /**
     * Get a list of beneficiaries for dropdowns/search.
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

        // Optional: filter by care worker
        if ($request->filled('care_worker_id')) {
            $query->where('assigned_care_worker_id', $request->care_worker_id);
        }

        $beneficiaries = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'data' => $beneficiaries
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
}

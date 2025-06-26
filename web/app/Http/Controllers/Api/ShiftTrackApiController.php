<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;
use App\Services\NotificationService;
use App\Models\VisitationOccurrence;

class ShiftTrackApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all arrival/departure shift tracks for a specific shift.
     */
    // Like the show method of ShiftApiController but only for arrival/departure tracks.
    // DO NOT USE THIS METHOD IN MOBILE, USE SHOW METHOD IN ShiftApiController
    // public function index($shiftId)
    // {
    //     $shift = Shift::with(['tracks.visitation' => function ($query) {
    //         $query->whereIn('arrival_status', ['arrived', 'departed']);
    //     }])->findOrFail($shiftId);

    //     // Only return arrival/departure tracks
    //     $tracks = $shift->tracks->whereIn('arrival_status', ['arrived', 'departed'])->values();

    //     return response()->json($tracks);
    // }

    /**
     * Add an arrival or departure event to a shift track.
     */
    public function event(Request $request, $shiftId)
    {
        $visitation = \App\Models\Visitation::findOrFail($request->visitation_id);

        // Do not allow event if visitation is already completed (for non-recurring only)
        // For recurring, allow as long as the occurrence is not completed
        $hasRecurring = \App\Models\RecurringPattern::where('visitation_id', $visitation->visitation_id)->exists();

        if (!$hasRecurring && $visitation->status === 'completed') {
            return response()->json([
                'message' => 'Cannot log event for a visitation that is already completed.'
            ], 422);
        }

        $request->validate([
            'care_worker_id' => 'required|exists:cose_users,id',
            'track_coordinates' => 'required|array',
            'track_coordinates.lat' => 'required|numeric',
            'track_coordinates.lng' => 'required|numeric',
            'recorded_at' => 'required|date',
            'visitation_id' => 'required|exists:visitations,visitation_id',
            'arrival_status' => 'required|in:arrived,departed',
            // 'address' => 'nullable|string', // address not part of the request validation, will be filled by reverse geocoding after timing out of shift
        ]);

        $shift = Shift::findOrFail($shiftId);
        if ($shift->status !== 'in_progress') {
            return response()->json(['message' => 'Cannot log event for a shift that is not in progress.'], 422);
        }

        if ($shift->care_worker_id != $request->care_worker_id) {
            return response()->json(['message' => 'Care worker does not match shift.'], 422);
        }

        // Always check occurrence, not parent visitation date
        $occurrenceDate = date('Y-m-d', strtotime($request->recorded_at));
        $occurrence = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
            ->where('occurrence_date', $occurrenceDate)
            ->first();

        if (!$occurrence) {
            return response()->json([
                'message' => 'No visitation occurrence found for this date.'
            ], 422);
        }

        // 1. Check if there is any "arrived" entry for this shift (any visitation) without a matching "departed"
        $openArrived = ShiftTrack::where('shift_id', $shift->id)
            ->where('arrival_status', 'arrived')
            ->get()
            ->filter(function ($track) use ($shift) {
                // For each arrived, check if there is a departed for the same visitation
                $departed = ShiftTrack::where('shift_id', $shift->id)
                    ->where('visitation_id', $track->visitation_id)
                    ->where('arrival_status', 'departed')
                    ->exists();
                return !$departed;
            });

        // 2. If there is an open "arrived" entry for a different visitation, block any new "arrived" or "departed" for other visitations
        if ($openArrived->count() > 0) {
            $openVisitationIds = $openArrived->pluck('visitation_id')->unique()->values();
            // If trying to "arrived" or "departed" for a different visitation, block
            if (
                !$openVisitationIds->contains($request->visitation_id)
            ) {
                return response()->json([
                    'message' => 'You have an ongoing "arrived" entry for another visitation that has not been departed. Complete it before proceeding to another visitation.',
                    'open_visitation_ids' => $openVisitationIds
                ], 422);
            }
        }

        // 3. Prevent "departed" if there is no "arrived" for this visitation
        if ($request->arrival_status === 'departed') {
            $hasArrived = ShiftTrack::where('shift_id', $shift->id)
                ->where('visitation_id', $request->visitation_id)
                ->where('arrival_status', 'arrived')
                ->exists();

            if (!$hasArrived) {
                return response()->json([
                    'message' => 'Cannot log "departed" for this visitation because there is no "arrived" entry yet.'
                ], 422);
            }

            // Prevent multiple "departed" for the same visitation
            $alreadyDeparted = ShiftTrack::where('shift_id', $shift->id)
                ->where('visitation_id', $request->visitation_id)
                ->where('arrival_status', 'departed')
                ->exists();

            if ($alreadyDeparted) {
                return response()->json([
                    'message' => 'This visitation already has a "departed" entry.'
                ], 422);
            }
        }

        // 4. Prevent multiple "arrived" for the same visitation without a matching "departed"
        if ($request->arrival_status === 'arrived') {
            $hasOpenArrived = ShiftTrack::where('shift_id', $shift->id)
                ->where('visitation_id', $request->visitation_id)
                ->where('arrival_status', 'arrived')
                ->whereNotIn('visitation_id', function($query) use ($shift, $request) {
                    $query->select('visitation_id')
                        ->from('shift_tracks')
                        ->where('shift_id', $shift->id)
                        ->where('arrival_status', 'departed');
                })
                ->exists();

            if ($hasOpenArrived) {
                return response()->json([
                    'message' => 'You already have an "arrived" entry for this visitation that has not been departed.'
                ], 422);
            }
        }


        $track = ShiftTrack::create([
            'shift_id' => $shift->id,
            'care_worker_id' => $request->care_worker_id,
            'track_coordinates' => $request->track_coordinates,
            'address' => null, // Always null, filled by geocoding later
            'recorded_at' => $request->recorded_at,
            'visitation_id' => $request->visitation_id,
            'arrival_status' => $request->arrival_status,
            'synced' => true,
        ]);

        // If departed, mark occurrence as completed, and parent only if not recurring
        if ($request->arrival_status === 'departed') {
            $occurrence->status = 'completed';
            $occurrence->save();

            if (!$hasRecurring) {
                // Non-recurring: mark parent as completed
                $visitation->status = 'completed';
                $visitation->save();
            } else {
                // Recurring: check if all occurrences are completed or recurrence has ended
                $remaining = \App\Models\VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                    ->where('status', 'scheduled')
                    ->count();

                $recurringPattern = \App\Models\RecurringPattern::where('visitation_id', $visitation->visitation_id)->first();
                $recurrenceEnd = $recurringPattern ? $recurringPattern->recurrence_end : null;
                $today = now()->toDateString();

                if ($remaining === 0 || ($recurrenceEnd && $today > $recurrenceEnd)) {
                    $visitation->status = 'completed';
                    $visitation->save();
                }
            }
        }

        // --- Notify all care managers ---
        $careWorkerName = $shift->careWorker->first_name . ' ' . $shift->careWorker->last_name;
        $beneficiaryName = $visitation->beneficiary ? ($visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name) : '';
        $statusText = ucfirst($request->arrival_status);

        $title = "Care Worker {$statusText} at Visitation";
        $message = "Care worker {$careWorkerName} has {$statusText} for beneficiary {$beneficiaryName}.";

        $this->notificationService->notifyAllCareManagers($title, $message);

        return response()->json($track, 201);
    }
}

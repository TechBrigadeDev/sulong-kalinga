<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;

class ShiftTrackApiController extends Controller
{
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

        // Add check: Do not allow event if visitation is already completed
        if ($visitation->status === 'completed') {
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

        if ($visitation->visitation_date->format('Y-m-d') !== date('Y-m-d', strtotime($request->recorded_at))) {
            return response()->json([
                'message' => 'Arrival/departure date does not match visitation date.'
            ], 422);
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

        // If departed, mark visitation as completed
        if ($request->arrival_status === 'departed') {
            $visitation->status = 'completed';
            $visitation->save();
        }

        return response()->json($track, 201);
    }
}

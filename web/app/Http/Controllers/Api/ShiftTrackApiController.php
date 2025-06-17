<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;

class ShiftTrackApiController extends Controller
{
    /**
     * Get all arrival/departure tracks for a shift.
     */
    public function index($shiftId)
    {
        $shift = Shift::with(['tracks.visitation' => function ($query) {
            $query->whereIn('arrival_status', ['arrived', 'departed']);
        }])->findOrFail($shiftId);

        // Only return arrival/departure tracks
        $tracks = $shift->tracks->whereIn('arrival_status', ['arrived', 'departed'])->values();

        return response()->json($tracks);
    }

    /**
     * Add an arrival or departure event to a shift track.
     */
    public function event(Request $request, $shiftId)
    {
        $request->validate([
            'care_worker_id' => 'required|exists:cose_users,id',
            'track_coordinates' => 'required|array',
            'track_coordinates.lat' => 'required|numeric',
            'track_coordinates.lng' => 'required|numeric',
            'recorded_at' => 'required|date',
            'visitation_id' => 'required|exists:visitations,id',
            'arrival_status' => 'required|in:arrived,departed',
            'address' => 'nullable|string',
        ]);

        $shift = Shift::findOrFail($shiftId);

        if ($shift->care_worker_id != $request->care_worker_id) {
            return response()->json(['message' => 'Care worker does not match shift.'], 422);
        }

        $track = ShiftTrack::create([
            'shift_id' => $shift->id,
            'care_worker_id' => $request->care_worker_id,
            'track_coordinates' => $request->track_coordinates,
            'address' => $request->address,
            'recorded_at' => $request->recorded_at,
            'visitation_id' => $request->visitation_id,
            'arrival_status' => $request->arrival_status,
            'synced' => true,
        ]);

        return response()->json($track, 201);
    }
}

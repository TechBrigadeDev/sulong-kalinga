<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;
use App\Models\User;

class ShiftApiTrackController extends Controller
{
    /**
     * Get all tracks for a shift.
     */
    public function index($shiftId)
    {
        $shift = Shift::with('tracks')->findOrFail($shiftId);
        return response()->json($shift->tracks);
    }

    /**
     * Add a single location track to a shift.
     */
    public function store(Request $request, $shiftId)
    {
        $request->validate([
            'care_worker_id' => 'required|exists:cose_users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
            'recorded_at' => 'required|date',
        ]);

        $shift = Shift::findOrFail($shiftId);

        // Optionally, check that care_worker_id matches the shift's care_worker_id
        if ($shift->care_worker_id != $request->care_worker_id) {
            return response()->json(['message' => 'Care worker does not match shift.'], 422);
        }

        $track = ShiftTrack::create([
            'shift_id' => $shift->id,
            'care_worker_id' => $request->care_worker_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'recorded_at' => $request->recorded_at,
            'synced' => true,
        ]);

        return response()->json($track, 201);
    }

    /**
     * Bulk add tracks to a shift (for offline sync).
     */
    public function bulkStore(Request $request, $shiftId)
    {
        $request->validate([
            'care_worker_id' => 'required|exists:cose_users,id',
            'tracks' => 'required|array|min:1',
            'tracks.*.latitude' => 'required|numeric',
            'tracks.*.longitude' => 'required|numeric',
            'tracks.*.address' => 'nullable|string',
            'tracks.*.recorded_at' => 'required|date',
        ]);

        $shift = Shift::findOrFail($shiftId);

        if ($shift->care_worker_id != $request->care_worker_id) {
            return response()->json(['message' => 'Care worker does not match shift.'], 422);
        }

        $created = [];
        foreach ($request->tracks as $trackData) {
            $created[] = ShiftTrack::create([
                'shift_id' => $shift->id,
                'care_worker_id' => $request->care_worker_id,
                'latitude' => $trackData['latitude'],
                'longitude' => $trackData['longitude'],
                'address' => $trackData['address'] ?? null,
                'recorded_at' => $trackData['recorded_at'],
                'synced' => true,
            ]);
        }

        return response()->json(['created' => $created], 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;
use App\Models\User;

class ShiftController extends Controller
{
    /**
     * List/filter shifts.
     */
    public function index(Request $request)
    {
        $query = Shift::with('careWorker');

        // Optional filters
        if ($request->filled('care_worker_id')) {
            $query->where('care_worker_id', $request->care_worker_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }

        $shifts = $query->orderBy('time_in', 'desc')->paginate(20);

        return response()->json($shifts);
    }

    /**
     * Start a shift (time in).
     */
    public function store(Request $request)
    {
        $request->validate([
            'care_worker_id' => 'required|exists:cose_users,id',
            'time_in' => 'nullable|date', // Optional, defaults to now
        ]);

        // Only allow care workers (role_id == 3)
        $user = User::where('id', $request->care_worker_id)->where('role_id', 3)->first();
        if (!$user) {
            return response()->json(['message' => 'User is not a care worker.'], 422);
        }

        // Prevent duplicate in-progress shift
        $existing = Shift::where('care_worker_id', $user->id)->where('status', 'in_progress')->first();
        if ($existing) {
            return response()->json(['message' => 'Care worker already has an in-progress shift.'], 409);
        }

        $shift = Shift::create([
            'care_worker_id' => $user->id,
            'time_in' => $request->time_in ?? now(),
            'status' => 'in_progress',
        ]);

        return response()->json($shift, 201);
    }

    /**
     * End a shift (time out).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'time_out' => 'nullable|date', // Optional, defaults to now
        ]);

        $shift = Shift::findOrFail($id);

        if ($shift->status === 'completed') {
            return response()->json(['message' => 'Shift already completed.'], 409);
        }

        $shift->update([
            'time_out' => $request->time_out ?? now(),
            'status' => 'completed',
        ]);

        return response()->json($shift);
    }

    /**
     * Show shift details (with tracks).
     */
    public function show($id)
    {
        $shift = Shift::with(['careWorker', 'tracks'])->findOrFail($id);
        return response()->json($shift);
    }
}

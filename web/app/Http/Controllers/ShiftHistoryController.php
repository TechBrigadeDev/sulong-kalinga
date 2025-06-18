<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;

class ShiftHistoryController extends Controller
{
    /**
     * Display a listing of the current (in-progress) shift histories.
     */
    public function index(Request $request)
    {
        $query = Shift::with('careWorker');

        // Filtering by care worker name
        if ($request->filled('search')) {
            $query->whereHas('careWorker', function ($q) use ($request) {
                $q->where('first_name', 'ilike', '%' . $request->search . '%')
                ->orWhere('last_name', 'ilike', '%' . $request->search . '%');
            });
        }
        // Filtering by date
        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }
        // Show both in_progress and completed
        $query->whereIn('status', ['completed']);

        $shifts = $query->orderBy('time_in', 'desc')->paginate(20);

        // No need to pass municipality or status anymore
        return view('admin.shiftHistories', [
            'shifts' => $shifts,
            'search' => $request->search,
            'date' => $request->date
        ]);
    }
    

    /**
     * Display the details and location history for a specific shift.
     */
    public function shiftDetails($shiftId)
    {
        $shift = Shift::with([
            'careWorker',
            'tracks.visitation' // eager load tracks and their visitations
        ])->findOrFail($shiftId);

        // Optionally, sort tracks by recorded_at
        $tracks = $shift->tracks
            ->whereIn('arrival_status', ['arrived', 'departed'])
            ->sortBy('recorded_at')
            ->values();

        return view('admin.shiftHistoryDetails', [
            'shift' => $shift,
            'tracks' => $tracks
        ]);
    }
}
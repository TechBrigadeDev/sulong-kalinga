<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;

class ShiftHistoryController extends Controller
{
    /**
     * Display a listing of the current shift histories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Shift::with('careWorker');

        // Filtering
        if ($request->filled('search')) {
            $query->whereHas('careWorker', function ($q) use ($request) {
                $q->where('first_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('last_name', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }
        $query->where('status', 'in_progress');

        $shifts = $query->orderBy('time_in', 'desc')->paginate(20);

        return view('admin.shiftHistories', [
            'shifts' => $shifts,
            'search' => $request->search,
            'date' => $request->date
        ]);
    }
    
    /**
     * Display a listing of archived shift histories.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived(Request $request)
    {
        $query = Shift::with('careWorker');

        // Filtering
        if ($request->filled('search')) {
            $query->whereHas('careWorker', function ($q) use ($request) {
                $q->where('first_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('last_name', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }
        $query->where('status', 'completed');

        $shifts = $query->orderBy('time_in', 'desc')->paginate(20);

        return view('admin.archivedShiftHistories', [
            'shifts' => $shifts,
            'search' => $request->search,
            'date' => $request->date
        ]);
    }

    public function shiftDetails()
    {
        return view('admin.shiftHistoryDetails');
    }
}
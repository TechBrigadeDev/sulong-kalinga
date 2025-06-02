<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftTrack;

class CareWorkerTrackingController extends Controller
{
    public function index()
    {
        // Get all care workers currently on shift
        $onShift = Shift::with(['careWorker', 'tracks' => function($q) {
            $q->orderBy('recorded_at', 'desc');
        }])
        ->where('status', 'in_progress')
        ->get();

        // Optionally, get the latest track for each care worker
        $careWorkers = $onShift->map(function($shift) {
            $latestTrack = $shift->tracks->first();
            return [
                'care_worker' => $shift->careWorker,
                'shift_id' => $shift->id,
                'latest_track' => $latestTrack,
            ];
        });

        return view('admin.adminCareWorkerTracking', [
            'careWorkers' => $careWorkers
        ]);
    }
}

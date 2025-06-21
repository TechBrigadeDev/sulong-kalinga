<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftTrack;
use App\Models\User;
use App\Models\Visitation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Services\NotificationService;

class ShiftApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List upcoming scheduled visitations for the authenticated care worker.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $today = now()->toDateString();

        $visitations = Visitation::with('beneficiary')
            ->where('care_worker_id', $user->id)
            ->where('status', 'scheduled')
            ->whereDate('visitation_date', $today)
            ->orderBy('visitation_date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($visitation) {
                $beneficiary = $visitation->beneficiary;

                // Find the latest shift track for this visitation
                $latestTrack = \App\Models\ShiftTrack::where('visitation_id', $visitation->visitation_id)
                    ->orderByDesc('recorded_at')
                    ->first();

                if (!$latestTrack) {
                    $currentStatus = null;
                } else {
                    $currentStatus = $latestTrack->arrival_status === 'arrived'
                        ? 'arrived'
                        : ($latestTrack->arrival_status === 'departed' ? 'departed' : null);
                }

                return [
                    'visitation_id' => $visitation->visitation_id,
                    'care_worker_id' => $visitation->care_worker_id,
                    'beneficiary_id' => $visitation->beneficiary_id,
                    'beneficiary_name' => trim(
                        ($beneficiary->first_name ?? '') . ' ' .
                        ($beneficiary->middle_name ?? '') . ' ' .
                        ($beneficiary->last_name ?? '')
                    ),
                    'visit_type' => $visitation->visit_type,
                    'address' => $beneficiary->street_address ?? '',
                    'start_time' => $visitation->start_time,
                    'is_flexible_time' => $visitation->is_flexible_time,
                    'actions' => ['Arrived', 'Departed'],
                    'current_status' => $currentStatus,
                ];
            });

        return response()->json($visitations);
    }

    /**
     * Start a shift (time in) for the authenticated care worker.
     */
    // ADD IN MOBILE AN "ARE YOU SURE" PROMPT
    public function timeIn(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'time_in' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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

        // Notify all care managers
        $careWorkerName = $user->first_name . ' ' . $user->last_name;
        $title = "Care Worker Timed In";
        $message = "Care worker {$careWorkerName} has started their shift.";
        $this->notificationService->notifyAllCareManagers($title, $message);

        return response()->json($shift, 201);
    }

    /**
     * End a shift (time out) for the authenticated care worker and trigger geocoding for all arrival/departure tracks.
     */
    // ADD IN MOBILE AN "ARE YOU SURE" PROMPT
    public function timeOut(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'time_out' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shift = Shift::where('id', $id)->where('care_worker_id', $user->id)->firstOrFail();

        if ($shift->status === 'completed') {
            return response()->json(['message' => 'Shift already completed.'], 409);
        }

        // Prevent time out if any visitation has "arrived" but no "departed" event
        $arrivedVisitations = ShiftTrack::where('shift_id', $shift->id)
            ->where('arrival_status', 'arrived')
            ->pluck('visitation_id')
            ->unique();

        $departedVisitations = ShiftTrack::where('shift_id', $shift->id)
            ->where('arrival_status', 'departed')
            ->pluck('visitation_id')
            ->unique();

        // Find visitations with arrived but no departed
        $incompleteVisitations = $arrivedVisitations->diff($departedVisitations);

        if ($incompleteVisitations->count() > 0) {
            return response()->json([
                'message' => 'Cannot time out. Some visitations have an "arrived" event but no "departed" event.',
                'incomplete_visitations' => $incompleteVisitations->values()
            ], 422);
        }

        $shift->update([
            'time_out' => $request->time_out ?? now(),
            'status' => 'completed',
        ]);

        $this->batchGeocodeShiftTracks($shift);

        // Notify all care managers
        $careWorkerName = $user->first_name . ' ' . $user->last_name;
        $title = "Care Worker Timed Out";
        $message = "Care worker {$careWorkerName} has ended their shift.";
        $this->notificationService->notifyAllCareManagers($title, $message);

        return response()->json($shift);
    }

    /**
     * Show the current in-progress shift for the authenticated care worker.
     */
    public function current(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $shift = Shift::where('care_worker_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => true,
                'message' => 'No in-progress shift found.',
                'shift' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'shift' => $shift
        ]);
    }

    /**
     * Show shift details (with tracks and visitations) for the authenticated care worker.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $shift = Shift::with([
            'careWorker',
            'tracks.visitation'
        ])->where('id', $id)->where('care_worker_id', $user->id)->firstOrFail();

        // Only include arrival/departure tracks
        $shift->tracks = $shift->tracks->whereIn('arrival_status', ['arrived', 'departed'])->values();

        return response()->json($shift);
    }

    /**
     * Show all completed shifts (archived) for the authenticated care worker.
     */
    public function archived(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can access this resource.'
            ], 403);
        }

        $query = Shift::with('careWorker')
            ->where('status', 'completed')
            ->where('care_worker_id', $user->id);

        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }

        $shifts = $query->orderBy('time_in', 'desc')->paginate(20);

        return response()->json($shifts);
    }

    /**
     * Batch geocode all arrival/departure tracks for a shift and update their addresses.
     */
    protected function batchGeocodeShiftTracks(Shift $shift)
    {
        $tracks = $shift->tracks()
            ->whereNull('address')
            ->whereIn('arrival_status', ['arrived', 'departed'])
            ->get();
        $apiKey = config('services.google_maps.key');

        foreach ($tracks as $track) {
            $coords = $track->track_coordinates;
            if (is_array($coords) && isset($coords['lat'], $coords['lng'])) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
                $address = $this->reverseGeocode($lat, $lng, $apiKey);
                if ($address) {
                    $track->address = $address;
                    $track->save();
                }
            }
        }
    }

    /**
     * Reverse geocode a lat/lng to an address using Google Maps API.
     */
    protected function reverseGeocode($lat, $lng, $apiKey)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";
        $response = Http::get($url);

        if ($response->ok() && isset($response['results'])) {
            // Prefer establishment or premise over plus_code
            foreach ($response['results'] as $result) {
                if (
                    in_array('establishment', $result['types']) ||
                    in_array('point_of_interest', $result['types']) ||
                    in_array('premise', $result['types'])
                ) {
                    return $result['formatted_address'];
                }
            }
            // Fallback to first formatted_address
            if (isset($response['results'][0]['formatted_address'])) {
                return $response['results'][0]['formatted_address'];
            }
        }
        return null;
    }
}

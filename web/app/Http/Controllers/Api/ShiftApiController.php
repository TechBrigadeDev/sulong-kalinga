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

class ShiftApiController extends Controller
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
        $validator = Validator::make($request->all(), [
            'care_worker_id' => 'required|exists:cose_users,id',
            'time_in' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
     * End a shift (time out) and trigger geocoding for all arrival/departure tracks.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'time_out' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shift = Shift::findOrFail($id);

        if ($shift->status === 'completed') {
            return response()->json(['message' => 'Shift already completed.'], 409);
        }

        $shift->update([
            'time_out' => $request->time_out ?? now(),
            'status' => 'completed',
        ]);

        // Only geocode arrival/departure tracks (no interval tracks anymore)
        $this->batchGeocodeShiftTracks($shift);

        return response()->json($shift);
    }

    /**
     * Show shift details (with tracks and visitations).
     */
    public function show($id)
    {
        $shift = Shift::with([
            'careWorker',
            'tracks.visitation'
        ])->findOrFail($id);

        // Only include arrival/departure tracks
        $shift->tracks = $shift->tracks->whereIn('arrival_status', ['arrived', 'departed'])->values();

        return response()->json($shift);
    }

    /**
     * Fetch all assigned visitations for a care worker for a given date.
     */
    public function getAssignedVisitations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'care_worker_id' => 'required|exists:cose_users,id',
            'date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date ?? now()->toDateString();

        $visitations = Visitation::with(['beneficiary', 'careWorker'])
            ->where('care_worker_id', $request->care_worker_id)
            ->whereDate('visitation_date', $date)
            ->whereIn('status', ['scheduled', 'active']) // adjust as needed
            ->orderBy('visitation_date')
            ->orderBy('start_time')
            ->get();

        return response()->json($visitations);
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
        if ($response->ok() && isset($response['results'][0]['formatted_address'])) {
            return $response['results'][0]['formatted_address'];
        }
        return null;
    }
}

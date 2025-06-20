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

        // Determine which view to use based on the route prefix
        $view = request()->is('care-manager/*')
            ? 'careManager.shiftHistories'
            : 'admin.shiftHistories';

        return view($view, [
            'shifts' => $shifts,
            'search' => $request->search,
            'date' => $request->date
        ]);
    }
    

    /**
     * Display the details and location history for a specific shift,
     * including proximity check to beneficiary's map_location.
     */
    public function shiftDetails($shiftId)
    {
        $shift = Shift::with([
            'careWorker',
            'tracks.visitation.beneficiary' // eager load tracks, visitations, and beneficiaries
        ])->findOrFail($shiftId);

        // Optionally, sort tracks by recorded_at
        $tracks = $shift->tracks
            ->whereIn('arrival_status', ['arrived', 'departed'])
            ->sortBy('recorded_at')
            ->values();

        // Define the radius (in meters) for "near beneficiary"
        $radiusMeters = 700; // Reasonable for rural PH, see note below

        // Add proximity check for each track
        $tracks = $tracks->map(function ($track) use ($radiusMeters) {
            $proximity = 'N/A';
            $beneficiary = $track->visitation->beneficiary ?? null;

            if (
                $beneficiary &&
                !empty($beneficiary->map_location) &&
                isset($track->track_coordinates['lat'], $track->track_coordinates['lng'])
            ) {
                if (is_array($beneficiary->map_location)) {
                    $beneficiaryCoords = $beneficiary->map_location;
                } else {
                    $beneficiaryCoords = json_decode($beneficiary->map_location, true);
                }
                $lat = $beneficiaryCoords['lat'] ?? $beneficiaryCoords['latitude'] ?? null;
                $lng = $beneficiaryCoords['lng'] ?? $beneficiaryCoords['longitude'] ?? null;

                if ($lat !== null && $lng !== null) {
                    $distance = $this->haversineDistance(
                        $track->track_coordinates['lat'],
                        $track->track_coordinates['lng'],
                        $lat,
                        $lng
                    );
                    $proximity = $distance <= $radiusMeters ? 'Near Beneficiary (within 700 meters)' : 'Not Near Beneficiary (within 700 meters)';
                }
            }

            // Format visit_type for display
            if ($track->visitation && isset($track->visitation->visit_type)) {
                $track->visitation->visit_type_display = ucwords(str_replace('_', ' ', $track->visitation->visit_type));
            } else {
                $track->visitation->visit_type_display = '';
            }

            $track->proximity = $proximity;
            return $track;
        });

        // Determine which view to use based on the route prefix
        $view = request()->is('care-manager/*')
            ? 'careManager.shiftHistoryDetails'
            : 'admin.shiftHistoryDetails';

        return view($view, [
            'shift' => $shift,
            'tracks' => $tracks
        ]);
    }

    /**
     * Calculate the distance between two coordinates using the Haversine formula.
     * Returns distance in meters.
     */
    protected function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;

        $a = sin($dLat/2) * sin($dLat/2) +
            cos($lat1) * cos($lat2) *
            sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
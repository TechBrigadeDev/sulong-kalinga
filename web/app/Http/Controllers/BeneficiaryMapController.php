<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\GeneralCarePlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BeneficiaryMapController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin and Care Manager: show all beneficiaries
        if ($user->role_id == 1 || $user->role_id == 2) {
            $beneficiaries = Beneficiary::with('category')->whereNotNull('map_location')->get();
        }
        // Care Worker: show only assigned beneficiaries
        else if ($user->role_id == 3) {
            // Get all general care plan IDs assigned to this care worker
            $carePlanIds = \App\Models\GeneralCarePlan::where('care_worker_id', $user->id)->pluck('general_care_plan_id');
            // Get all beneficiaries with those care plan IDs
            $beneficiaries = Beneficiary::with('category')
                ->whereNotNull('map_location')
                ->whereIn('general_care_plan_id', $carePlanIds)
                ->get();
        } else {
            // Default: show none
            $beneficiaries = collect();
        }

        // Prepare data for JS (flatten map_location)
        $beneficiariesForMap = $beneficiaries->map(function ($b) {
            $location = is_array($b->map_location) ? $b->map_location : json_decode($b->map_location, true);

            // Use the correct primary key
            $id = $b->beneficiary_id ?? $b->id ?? null;

            // Use category name if available, fallback to empty string
            $category = '';
            if (isset($b->category) && is_object($b->category) && isset($b->category->category_name)) {
                $category = $b->category->category_name;
            } elseif (is_string($b->category)) {
                $category = $b->category;
            }

            // Compose address if needed (adjust as per your schema)
            $address = $b->address ?? $b->street_address ?? '';

            // --- Assigned Care Worker Lookup ---
            $caregiver = '';
            $caregiverContact = '';
            if ($b->general_care_plan_id) {
                $carePlan = GeneralCarePlan::find($b->general_care_plan_id);
                if ($carePlan && $carePlan->care_worker_id) {
                    $careWorker = User::find($carePlan->care_worker_id);
                    if ($careWorker) {
                        $caregiver = trim(($careWorker->first_name ?? '') . ' ' . ($careWorker->last_name ?? ''));
                        $caregiverContact = $careWorker->mobile ?? '';
                    }
                }
            }

            return [
                'id' => $id,
                'name' => trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? '')),
                'address' => $address,
                'contact' => $b->contact_number ?? $b->mobile ?? '',
                'category' => $category,
                'caregiver' => $caregiver,
                'caregiverContact' => $caregiverContact,
                'lat' => isset($location['lat']) ? $location['lat'] : (isset($location['latitude']) ? $location['latitude'] : null),
                'lng' => isset($location['lng']) ? $location['lng'] : (isset($location['longitude']) ? $location['longitude'] : null),
            ];
        })->filter(function ($b) {
            // Only include if id, lat, and lng are present
            return $b['id'] && $b['lat'] && $b['lng'];
        })->values();

        $firstBeneficiaryId = $beneficiariesForMap->first()['id'] ?? null;

        // Role-based view selection
        if ($user->role_id == 1) {
            $view = 'admin.beneficiaryMap';
        } elseif ($user->role_id == 2) {
            $view = 'careManager.beneficiaryMap';
        } elseif ($user->role_id == 3) {
            $view = 'careWorker.beneficiaryMap';
        } else {
            // fallback to admin for now, or show a 404/unauthorized
            $view = 'admin.beneficiaryMap';
        }

        return view($view, [
            'beneficiariesForMap' => $beneficiariesForMap,
            'firstBeneficiaryId' => $firstBeneficiaryId
        ]);
    }
}

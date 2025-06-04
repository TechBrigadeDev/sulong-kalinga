<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WeeklyCarePlan;
use App\Models\Beneficiary;
use App\Models\CareCategory;
use App\Models\HealthHistory;
use App\Models\WeeklyCarePlanInterventions;
use Carbon\Carbon;

class FamilyPortalCarePlanController extends Controller
{
    /**
     * Display the weekly care plans for the beneficiary
     */
    public function index(Request $request)
    {
        try {
            // Determine if the user is a beneficiary or family member
            $beneficiaryId = null;
            $userType = null;
            $userId = null;
            
            if (Auth::guard('beneficiary')->check()) {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
                $userType = 'beneficiary';
                $userId = $user->beneficiary_id;
            } elseif (Auth::guard('family')->check()) {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
                $userType = 'family';
                $userId = $user->family_member_id;
            }
            
            // Get filter parameters
            $search = $request->input('search');
            $filter = $request->input('filter', 'all');
            $perPage = $request->input('per_page', 10);
            
            // Build the query for weekly care plans related to this beneficiary
            $query = WeeklyCarePlan::with(['beneficiary', 'author', 'careWorker'])
                ->where('beneficiary_id', $beneficiaryId)
                ->orderBy('created_at', 'desc');
            
            // Apply search filter if provided - IMPROVED
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('author', function($authorQuery) use ($search) {
                        $authorQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    })
                    // Use PostgreSQL's TO_CHAR instead of MySQL's DATE_FORMAT
                    ->orWhereRaw("TO_CHAR(created_at, 'Month') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'DD') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'YYYY') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'MM/DD/YYYY') ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("TO_CHAR(created_at, 'Mon') ILIKE ?", ["%{$search}%"]);
                });
            }
            
            // Apply status filter
            if ($filter == 'pending') {
                $query->where(function($q) {
                    $q->whereNull('acknowledged_by_beneficiary')
                    ->whereNull('acknowledged_by_family')
                    ->whereNull('acknowledgement_signature');
                });
            } elseif ($filter == 'acknowledged') {
                $query->where(function($q) {
                    $q->whereNotNull('acknowledged_by_beneficiary')
                    ->orWhereNotNull('acknowledged_by_family')
                    ->orWhereNotNull('acknowledgement_signature');
                });
            }
            
            // Get paginated results
            $carePlans = $query->paginate($perPage);

            // For AJAX requests, return only the necessary HTML
            if ($request->ajax() || $request->input('ajax') == 1) {
                $view = $userType === 'beneficiary' 
                    ? 'beneficiaryPortal.partials.carePlansTable' 
                    : 'familyPortal.partials.carePlansTable';
                    
                return view($view, [
                    'carePlans' => $carePlans, 
                    'search' => $search, 
                    'filter' => $filter,
                    'userType' => $userType,
                    'userId' => $userId
                ])->render();
            }
            
            // For regular requests, return the full view
            $view = $userType === 'beneficiary' 
                ? 'beneficiaryPortal.viewAllCarePlan' 
                : 'familyPortal.viewAllCarePlan';
            
            return view($view, [
                'carePlans' => $carePlans, 
                'search' => $search, 
                'filter' => $filter,
                'userType' => $userType,
                'userId' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error in care plan index: ' . $e->getMessage());
            
            if ($request->ajax() || $request->input('ajax') == 1) {
                return '<div class="alert alert-danger">Error loading care plans. Please refresh the page.</div>';
            }
            
            // Determine which view to use based on the user type for the error page
            $view = Auth::guard('beneficiary')->check()
                ? 'beneficiaryPortal.viewAllCarePlan'
                : 'familyPortal.viewAllCarePlan';
                
            return view($view, [
                'carePlans' => collect(),
                'search' => '',
                'filter' => 'all',
                'error' => 'An error occurred while loading care plans. Please try again later.'
            ]);
        }
    }

    /**
     * Show statistics view
     */
    public function statistics()
    {
        // Determine which view to use based on the user type
        $view = Auth::guard('beneficiary')->check()
            ? 'beneficiaryPortal.carePlan'
            : 'familyPortal.carePlan';
            
        return view($view);
    }

    /**
     * View detailed weekly care plan
     */
    public function viewWeeklyCarePlan($id)
    {
        try {
            // Determine the user type
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $userId = Auth::guard('beneficiary')->check() 
                ? Auth::guard('beneficiary')->user()->beneficiary_id
                : Auth::guard('family')->user()->family_member_id;
            
            // Get the weekly care plan with related data
            $weeklyCareplan = WeeklyCarePlan::with([
                'beneficiary',
                'careWorker',
                'vitalSigns',
                'interventions.intervention',
                'interventions.careCategory',
                'acknowledgedByBeneficiary',
                'acknowledgedByFamily'
            ])->findOrFail($id);
            
            // Security check: ensure the user has access to this care plan
            if ($userType === 'beneficiary' && $weeklyCareplan->beneficiary_id !== $userId) {
                abort(403, 'You do not have permission to view this care plan.');
            } elseif ($userType === 'family') {
                $familyMember = Auth::guard('family')->user();
                if ($familyMember->related_beneficiary_id !== $weeklyCareplan->beneficiary_id) {
                    abort(403, 'You do not have permission to view this care plan.');
                }
            }
            
            // Get all care categories
            $categories = CareCategory::orderBy('care_category_name')->get();
            
            // Organize standard interventions by category (with intervention_id)
            $interventionsByCategory = collect();
            foreach ($weeklyCareplan->interventions as $intervention) {
                if ($intervention->intervention_id) {
                    $categoryId = $intervention->care_category_id;
                    if (!$interventionsByCategory->has($categoryId)) {
                        $interventionsByCategory->put($categoryId, collect());
                    }
                    $interventionsByCategory[$categoryId]->push($intervention);
                }
            }
            
            // Get custom interventions (without intervention_id)
            $customInterventions = $weeklyCareplan->interventions
                ->whereNull('intervention_id')
                ->whereNotNull('care_category_id')
                ->whereNotNull('intervention_description');
            
            // Determine which view to use based on the user type
            $view = $userType === 'beneficiary' 
                ? 'beneficiaryPortal.viewWeeklyCarePlan' 
                : 'familyPortal.viewWeeklyCarePlan';
            
            return view($view, [
                'weeklyCareplan' => $weeklyCareplan,
                'categories' => $categories,
                'interventionsByCategory' => $interventionsByCategory,
                'customInterventions' => $customInterventions,
                'userId' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing weekly care plan: ' . $e->getMessage());
            
            return redirect()
                ->route($userType === 'beneficiary' ? 'beneficiary.care.plan.index' : 'family.care.plan.index')
                ->with('error', 'An error occurred while loading the care plan: ' . $e->getMessage());
        }
    }

    /**
     * Process acknowledgment of weekly care plan
     */
    public function acknowledgeWeeklyCarePlan(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'confirmation' => 'required|in:confirmed',
            ]);
            
            // Determine the user type and ID
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $userId = Auth::guard('beneficiary')->check() 
                ? Auth::guard('beneficiary')->user()->beneficiary_id
                : Auth::guard('family')->user()->family_member_id;
            
            // Get the weekly care plan
            $weeklyCareplan = WeeklyCarePlan::findOrFail($id);
            
            // Security check: ensure the user has access to this care plan
            if ($userType === 'beneficiary' && $weeklyCareplan->beneficiary_id !== $userId) {
                abort(403, 'You do not have permission to acknowledge this care plan.');
            } elseif ($userType === 'family' && 
                     Auth::guard('family')->user()->related_beneficiary_id !== $weeklyCareplan->beneficiary_id) {
                abort(403, 'You do not have permission to acknowledge this care plan.');
            }
            
            // Record the acknowledgment
            if ($userType === 'beneficiary') {
                $weeklyCareplan->acknowledged_by_beneficiary = $userId;
            } else {
                $weeklyCareplan->acknowledged_by_family = $userId;
            }
            
            // Record signature information
            $signature = [
                'acknowledged_by' => $userType === 'beneficiary' ? 'Beneficiary' : 'Family Member',
                'user_id' => $userId,
                'name' => Auth::guard($userType === 'beneficiary' ? 'beneficiary' : 'family')->user()->first_name . ' ' . 
                         Auth::guard($userType === 'beneficiary' ? 'beneficiary' : 'family')->user()->last_name,
                'date' => Carbon::now()->toDateTimeString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];
            
            $weeklyCareplan->acknowledgement_signature = json_encode($signature);
            $weeklyCareplan->save();
            
            // Redirect back with success message
            $routeName = $userType === 'beneficiary' 
                ? 'beneficiary.care.plan.view' 
                : 'family.care.plan.view';
                
            return redirect()
                ->route($routeName, ['id' => $id])
                ->with('success', 'Care plan acknowledged successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error acknowledging care plan: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'An error occurred while processing your acknowledgment. Please try again.');
        }
    }
}
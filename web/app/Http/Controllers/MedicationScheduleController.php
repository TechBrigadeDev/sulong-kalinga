<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\HealthHistory;
use App\Models\GeneralCarePlan;
use Illuminate\Support\Facades\Auth;

class MedicationScheduleController extends Controller
{
    /**
     * Display the medication schedule management page based on user role
     */
    public function index(Request $request)
    {
        // Get current user and role
        $user = Auth::user();
        $role = $user->role_id;
        
        // Determine role name for routing and views
        $roleName = 'admin';
        if ($role === 2) {
            $roleName = 'careManager';
        } elseif ($role === 3) {
            $roleName = 'careWorker';
        }

        // Apply filters
        $query = MedicationSchedule::with([
            'beneficiary', 
            'beneficiary.generalCarePlan.healthHistory',
            'creator',
            'updater'
        ]);

        // Filter by search term (beneficiary name or medication name)
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('medication_name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('beneficiary', function($q) use ($searchTerm) {
                      $q->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$searchTerm}%"]);
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by time period
        if ($request->has('period') && $request->period && $request->period !== 'all') {
            switch ($request->period) {
                case 'morning':
                    $query->whereNotNull('morning_time');
                    break;
                case 'afternoon':
                    $query->whereNotNull('noon_time');
                    break;
                case 'evening':
                    $query->whereNotNull('evening_time');
                    break;
                case 'night':
                    $query->whereNotNull('night_time');
                    break;
                case 'as_needed':
                    $query->where('as_needed', true);
                    break;
            }
        }

        // Get all beneficiaries for the dropdown
        $beneficiaries = Beneficiary::orderBy('first_name')->get();
        
        // Get paginated results
        $medicationSchedules = $query->orderBy('created_at', 'desc')->paginate(10);

        // Format health information for each medication schedule
        foreach ($medicationSchedules as $schedule) {
            // Capitalize medication type
            $schedule->medication_type = ucfirst($schedule->medication_type);
            
            // Format health history data if available
            if ($schedule->beneficiary && $schedule->beneficiary->generalCarePlan && 
                $schedule->beneficiary->generalCarePlan->healthHistory) {
                
                $healthHistory = $schedule->beneficiary->generalCarePlan->healthHistory;
                
                // Format conditions
                if ($healthHistory->medical_conditions) {
                    $conditions = json_decode($healthHistory->medical_conditions, true);
                    $healthHistory->formatted_conditions = is_array($conditions) ? 
                        implode(", ", $conditions) : $healthHistory->medical_conditions;
                }
                
                // Format immunizations
                if ($healthHistory->immunizations) {
                    $immunizations = json_decode($healthHistory->immunizations, true);
                    $healthHistory->formatted_immunizations = is_array($immunizations) ? 
                        implode(", ", $immunizations) : $healthHistory->immunizations;
                }
                
                // Format allergies
                if ($healthHistory->allergies) {
                    $allergies = json_decode($healthHistory->allergies, true);
                    $healthHistory->formatted_allergies = is_array($allergies) ? 
                        implode(", ", $allergies) : $healthHistory->allergies;
                }
            }
        }

        // Return the appropriate view based on user role
        return view("$roleName.{$roleName}MedicationSchedule", [
            'medicationSchedules' => $medicationSchedules,
            'beneficiaries' => $beneficiaries,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'periodFilter' => $request->period ?? 'all'
        ]);
    }
}
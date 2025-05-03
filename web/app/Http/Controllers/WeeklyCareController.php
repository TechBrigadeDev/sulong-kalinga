<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Beneficiary;
use App\Models\WeeklyCarePlan;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\VitalSigns;
use App\Models\Notification;
use App\Models\FamilyMember;
use App\Models\User;

class WeeklyCareController extends Controller
{

    protected function getRolePrefixRoute()
    {
        $user = Auth::user();
        
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'care-manager';
        } elseif ($user->role_id == 3) {
            return 'care-worker';
        }
        
        return 'admin'; // Default fallback
    }

    protected function getRolePrefixView()
    {
        $user = Auth::user();
        
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'careManager';
        } elseif ($user->role_id == 3) {
            return 'careWorker';
        }
        
        return 'admin'; // Default fallback
    }

    /**
     * Show the form for creating a new weekly care plan
     */
    public function create()
    {
        $rolePrefix = $this->getRolePrefixView();
        
        // Fetch all beneficiaries for admins and care managers
        // For care workers, only fetch their assigned beneficiaries
        if (Auth::user()->role_id == 3) {
            $beneficiaries = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        } else {
            $beneficiaries = Beneficiary::orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }
        
        // Get all care categories with their interventions
        $careCategories = CareCategory::with('interventions')->get();
        
        // Return view based on user role
        return view($rolePrefix . '.weeklyCareplan', compact('beneficiaries', 'careCategories'));
    }
    
    /**
     * Get beneficiary details via AJAX
     */
    public function getBeneficiaryDetails($id)
    {
        try {
            $beneficiary = Beneficiary::with('generalCarePlan.healthHistory')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $beneficiary
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching beneficiary details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve beneficiary details'
            ], 500);
        }
    }
    
    /**
     * Store a new weekly care plan
     */
    public function store(Request $request)
    {
        $rolePrefix = $this->getRolePrefixRoute();
        $user = Auth::user();

        // Validate the request
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'selected_interventions' => 'required|array',
            'duration_minutes' => 'required|array',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',

            // Custom interventions validations
            'custom_category.*' => 'sometimes|nullable|exists:care_categories,care_category_id',
            'custom_description.*' => 'sometimes|nullable|required_with:custom_category.*|string|min:5|max:255|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'\"]+$/',
            'custom_duration.*' => 'sometimes|nullable|required_with:custom_category.*|numeric|min:0.01|max:999.99',
        ], [
            'beneficiary_id.required' => 'Please select a beneficiary',
            'beneficiary_id.exists' => 'The selected beneficiary is invalid',

            'assessment.required' => 'Please provide an assessment',
            'assessment.min' => 'Assessment must be at least 20 characters',
            'assessment.max' => 'Assessment cannot exceed 5000 characters',
            'assessment.regex' => 'Assessment must contain some text and cannot consist of only numbers or symbols',

            'blood_pressure.required' => 'Blood pressure is required',
            'blood_pressure.regex' => 'Blood pressure must be in format 120/80',

            'body_temperature.required' => 'Body temperature is required',
            'body_temperature.between' => 'Body temperature must be between 35°C and 42°C',

            'pulse_rate.required' => 'Pulse rate is required',
            'pulse_rate.between' => 'Pulse rate must be between 40 and 200 bpm',

            'respiratory_rate.required' => 'Respiratory rate is required',
            'respiratory_rate.between' => 'Respiratory rate must be between 8 and 40 bpm',

            'evaluation_recommendations.required' => 'Please provide evaluation recommendations',
            'evaluation_recommendations.min' => 'Evaluation recommendations must be at least 20 characters',
            'evaluation_recommendations.max' => 'Evaluation recommendations cannot exceed 5000 characters',
            'evaluation_recommendations.regex' => 'Evaluation recommendations must contain some text and cannot consist of only numbers or symbols',

            'selected_interventions.required' => 'Please select at least one intervention',

            'duration_minutes.*.required' => 'Please specify the duration for all selected interventions',
            'duration_minutes.*.numeric' => 'Duration must be a number',
            'duration_minutes.*.min' => 'Duration must be greater than 0',
            'duration_minutes.*.max' => 'Duration cannot exceed 999.99 minutes',

            'custom_category.*.exists' => 'Invalid care category selected',

            'custom_description.*.required_with' => 'Please provide a description for custom interventions',
            'custom_description.*.min' => 'Custom intervention description must be at least 5 characters',
            'custom_description.*.max' => 'Custom intervention description cannot exceed 255 characters',
            'custom_description.*.regex' => 'Custom intervention description must contain text and can only include letters, numbers, and basic punctuation',

            'custom_duration.*.required_with' => 'Please provide a duration for custom interventions',
            'custom_duration.*.numeric' => 'Custom intervention duration must be a number',
            'custom_duration.*.min' => 'Custom intervention duration must be greater than 0',
            'custom_duration.*.max' => 'Custom intervention duration cannot exceed 999.99 minutes',
        ]);
        
        Log::info('Validation passed');

        try {
            if ($user->role_id == 3) {
                $assignedBeneficiaryIds = Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                    $query->where('care_worker_id', $user->id);
                })->pluck('beneficiary_id');
                
                if (!$assignedBeneficiaryIds->contains($request->beneficiary_id)) {
                    return redirect()->route($rolePrefix . '.weeklycareplans.create')
                        ->with('error', 'You can only create plans for your assigned beneficiaries.');
                }
            }
            
            DB::beginTransaction();

            // 1. Create vital signs record
            $vitalSigns = VitalSigns::create([
                'blood_pressure' => $request->blood_pressure,
                'body_temperature' => $request->body_temperature,
                'pulse_rate' => $request->pulse_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'created_by' => Auth::id(),
            ]);
            Log::info('Vital signs created: ' . $vitalSigns->id);

            // 2. Create weekly care plan
            $weeklyCarePlan = WeeklyCarePlan::create([
                'beneficiary_id' => $request->beneficiary_id,
                'care_worker_id' => Auth::id(),
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'date' => now(),
                'assessment' => $request->assessment,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            Log::info('Weekly care plan created: ' . $weeklyCarePlan->id);

            // 3. Save ONLY selected interventions with valid durations
            if ($request->has('selected_interventions') && is_array($request->selected_interventions)) {
                foreach ($request->selected_interventions as $interventionId) {
                    // Only save if there's a valid duration for this intervention
                    if (isset($request->duration_minutes[$interventionId]) && 
                        $request->duration_minutes[$interventionId] > 0) {
                        
                        // Get intervention record to determine care category
                        $intervention = Intervention::find($interventionId);

                        if ($intervention) {
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'intervention_id' => $interventionId,
                                'care_category_id' => $intervention->care_category_id,
                                'intervention_description' => null, // This is a pre-defined intervention
                                'duration_minutes' => $request->duration_minutes[$interventionId],
                                'implemented' => true
                            ]);
                            Log::info('Intervention saved: ' . $interventionId . ' with duration: ' . $request->duration_minutes[$interventionId]);
                        } else {
                            Log::warning('Intervention not found: ' . $interventionId);
                        }
                    } else {
                        Log::info('Skipping intervention ' . $interventionId . ' - no valid duration provided');
                    }
                }
            }

            // 4. Save ONLY custom interventions with valid descriptions AND durations
            if ($request->has('custom_category') && is_array($request->custom_category)) {
                foreach ($request->custom_category as $index => $categoryId) {
                    // Only save if there's both a description AND a valid duration
                    if (!empty($categoryId) && 
                        !empty($request->custom_description[$index]) &&
                        isset($request->custom_duration[$index]) && 
                        $request->custom_duration[$index] > 0) {
                        
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => null, // Custom intervention doesn't have an ID
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index],
                            'implemented' => true
                        ]);
                        Log::info('Custom intervention saved: ' . $categoryId . ' with duration: ' . $request->custom_duration[$index]);
                    } else {
                        Log::info('Skipping custom intervention for category ' . $categoryId . ' - missing description or valid duration');
                    }
                }
            }

            DB::commit();

            // Send notifications
            try {
                // Get necessary details for notifications
                $beneficiary = Beneficiary::findOrFail($request->beneficiary_id);
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
                
                // 1. Notify the beneficiary if they have a portal account
                if ($beneficiary->portal_account_id) {
                    $beneficiaryTitle = 'New Weekly Care Plan Created';
                    $beneficiaryMessage = $actor . ' (' . $actorRole . ') has created a new weekly care plan for you.';
                    $this->sendNotificationToBeneficiary($beneficiary->beneficiary_id, $beneficiaryTitle, $beneficiaryMessage);
                }
                
                // 2. Notify all family members of the beneficiary
                $familyTitle = 'New Weekly Care Plan for Your Relative';
                $familyMessage = 'A new weekly care plan has been created for ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                            ' by ' . $actor . ' (' . $actorRole . ').';
                $this->sendNotificationToFamilyMembers($beneficiary->beneficiary_id, $familyTitle, $familyMessage);
                
                // 3. Notify the care manager if the creator is a care worker
                if (Auth::user()->role_id == 3) { // Care Worker
                    $careWorker = User::find(Auth::id());
                    
                    // Only notify care manager if one is assigned and they're not the one creating the plan
                    if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                        $careManagerTitle = 'New Weekly Care Plan Created by Your Care Worker';
                        $careManagerMessage = 'Your care worker ' . $actor . ' has created a new weekly care plan for beneficiary ' . 
                                            $beneficiary->first_name . ' ' . $beneficiary->last_name . '.';
                        $this->sendNotificationToCareManager($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the main process
                \Log::warning('Error sending notifications for new weekly care plan: ' . $notifyEx->getMessage());
            }

            return redirect()->route($rolePrefix . '.weeklycareplans.create')
                ->with('success', 'Weekly care plan created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating weekly care plan: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        }
    }
    
    /**
     * Display a listing of the weekly care plans
     */
    public function index()
    {
        $rolePrefix = $this->getRolePrefixView();
        $user = Auth::user();
        
        // For care workers, only show their own plans
        if ($user->role_id == 3) {
            $weeklyCarePlans = WeeklyCarePlan::with(['beneficiary', 'vitalSigns', 'interventions'])
                ->where('care_worker_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // For admins and care managers, show all plans
            $weeklyCarePlans = WeeklyCarePlan::with(['beneficiary', 'vitalSigns', 'interventions'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        return view($rolePrefix . '.weeklyCarePlansList', compact('weeklyCarePlans'));
    }

    public function customInterventions()
    {
        return $this->hasMany(WeeklyCarePlanInterventions::class, 'weekly_care_plan_id')
                    ->whereNull('intervention_id');
    }

    public function show($id)
    {
        $rolePrefix = $this->getRolePrefixView();
        $user = Auth::user();
        
        // Get the weekly care plan with related data
        $weeklyCareplan = WeeklyCarePlan::with('beneficiary', 
            'beneficiary.generalCarePlan.healthHistory', 
            'vitalSigns',
            'acknowledgedByBeneficiary',
            'acknowledgedByFamily')
            ->findOrFail($id);
        
        // For care workers, check if they are the author
        if ($user->role_id == 3 && $weeklyCareplan->care_worker_id != $user->id) {
            return redirect()->route('care-worker.reports')
                ->with('error', 'You do not have permission to view this care plan.');
        }
        
        // Get standard and custom interventions (rest of your existing code)
        $standardInterventions = DB::table('weekly_care_plan_interventions as wpi')
            ->join('interventions as i', 'wpi.intervention_id', '=', 'i.intervention_id')
            ->where('wpi.weekly_care_plan_id', $id)
            ->whereNotNull('wpi.intervention_id')
            ->select('i.intervention_description', 'wpi.duration_minutes', 'i.care_category_id')
            ->get();
        
        $interventionsByCategory = $standardInterventions->groupBy('care_category_id');
        
        $customInterventions = DB::table('weekly_care_plan_interventions')
            ->where('weekly_care_plan_id', $id)
            ->whereNull('intervention_id')
            ->get();
        
        $categories = CareCategory::all();
        
        // Return view based on user role
        return view($rolePrefix . '.viewWeeklyCareplan', compact(
            'weeklyCareplan',
            'interventionsByCategory',
            'customInterventions',
            'categories'
        ));
    }

    /*  public function edit($id)
        {
            $weeklyCarePlan = WeeklyCarePlan::findOrFail($id);
            
            // Only allow editing if the user is an admin, care manager or the original author
            if (Auth::user()->role_id > 2 && $weeklyCarePlan->created_by != Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to edit this care plan');
            }
            
            // Proceed with editing
            return view('weeklycareplans.edit', compact('weeklyCarePlan'));
        } 
    */

    public function edit($id)
    {
        $rolePrefix = $this->getRolePrefixView();
        $user = Auth::user();
        
        // Get the weekly care plan with all related data
        $weeklyCarePlan = WeeklyCarePlan::with([
            'beneficiary', 
            'beneficiary.generalCarePlan.healthHistory',
            'vitalSigns',
            'interventions'
        ])->findOrFail($id);
        
        // Check authorization:
        // - Admins and care managers can edit all plans
        // - Care workers can only edit their own plans
        if ($user->role_id == 3 && $weeklyCarePlan->care_worker_id != $user->id) {
            return redirect()->route($this->getRolePrefixRoute() . '.weeklycareplans.index')
                ->with('error', 'You do not have permission to edit this care plan.');
        }
        
        // Get all relevant data for the form
        if ($user->role_id == 3) {
            $beneficiaries = Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                $query->where('care_worker_id', $user->id);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        } else {
            $beneficiaries = Beneficiary::orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        // Set the selected beneficiary explicitly
        $selectedBeneficiary = $weeklyCarePlan->beneficiary;
        
        $careCategories = CareCategory::with('interventions')->get();
        
        // Rest of your existing code for preparing form data
        $selectedInterventions = [];
        $interventionDurations = [];
        
        foreach ($weeklyCarePlan->interventions as $intervention) {
            if ($intervention->intervention_id) {
                $selectedInterventions[] = $intervention->intervention_id;
                $interventionDurations[$intervention->intervention_id] = $intervention->duration_minutes;
            }
        }
        
        $customInterventions = $weeklyCarePlan->interventions()
            ->whereNull('intervention_id')
            ->get();

        $customInterventionsByCategory = [];
        foreach ($customInterventions as $intervention) {
            if (!isset($customInterventionsByCategory[$intervention->care_category_id])) {
                $customInterventionsByCategory[$intervention->care_category_id] = [];
            }
            $customInterventionsByCategory[$intervention->care_category_id][] = $intervention;
        }
        
        // Return view based on user role
        return view($rolePrefix . '.editWeeklyCareplan', compact(
            'weeklyCarePlan',
            'beneficiaries',
            'careCategories',
            'selectedInterventions',
            'interventionDurations',
            'customInterventions',
            'customInterventionsByCategory'
        ));
    }

    public function update(Request $request, $id)
    {

        $rolePrefix = $this->getRolePrefixRoute();
        $user = Auth::user();

        // Find the weekly care plan
        $weeklyCarePlan = WeeklyCarePlan::findOrFail($id);
        
        // Check authorization:
        // - Admins and care managers can update all plans
        // - Care workers can only update their own plans
        if ($user->role_id == 3 && $weeklyCarePlan->care_worker_id != $user->id) {
            return redirect()->route('care-worker.weeklycareplans.index')
                ->with('error', 'You do not have permission to update this care plan.');
        }

        // Validate the request (same validation as store method)
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'selected_interventions' => 'required|array',
            'duration_minutes' => 'required|array',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',
            'custom_category.*' => 'sometimes|nullable|exists:care_categories,care_category_id',
            'custom_description.*' => 'sometimes|nullable|required_with:custom_category.*|string|min:5|max:255|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'\"]+$/',
            'custom_duration.*' => 'sometimes|nullable|required_with:custom_category.*|numeric|min:0.01|max:999.99',
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Update vital signs
            $vitalSigns = VitalSigns::findOrFail($weeklyCarePlan->vital_signs_id);
            $vitalSigns->update([
                'blood_pressure' => $request->blood_pressure,
                'body_temperature' => $request->body_temperature,
                'pulse_rate' => $request->pulse_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'updated_by' => Auth::id(),
            ]);
            
            // 2. Update weekly care plan
            $weeklyCarePlan->update([
                'beneficiary_id' => $request->beneficiary_id,
                'assessment' => $request->assessment,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
            
            // 3. Delete all existing interventions
            $weeklyCarePlan->interventions()->delete();
            
            // 4. Add selected interventions
            if ($request->has('selected_interventions') && is_array($request->selected_interventions)) {
                foreach ($request->selected_interventions as $interventionId) {
                    if (isset($request->duration_minutes[$interventionId]) && 
                        $request->duration_minutes[$interventionId] > 0) {
                        
                        $intervention = Intervention::find($interventionId);
                        
                        if ($intervention) {
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'intervention_id' => $interventionId,
                                'care_category_id' => $intervention->care_category_id,
                                'intervention_description' => null,
                                'duration_minutes' => $request->duration_minutes[$interventionId],
                                'implemented' => true
                            ]);
                        }
                    }
                }
            }
            
            // 5. Add custom interventions
            if ($request->has('custom_category') && is_array($request->custom_category)) {
                foreach ($request->custom_category as $index => $categoryId) {
                    if (!empty($categoryId) && 
                        !empty($request->custom_description[$index]) &&
                        isset($request->custom_duration[$index]) && 
                        $request->custom_duration[$index] > 0) {
                        
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => null,
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index],
                            'implemented' => true
                        ]);
                    }
                }
            }
            
            DB::commit();

            // Send notifications about the update
            try {
                // Get necessary details for notifications
                $beneficiary = Beneficiary::findOrFail($weeklyCarePlan->beneficiary_id);
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
                
                // 1. Notify the beneficiary about the update
                if ($beneficiary->portal_account_id) {
                    $beneficiaryTitle = 'Weekly Care Plan Updated';
                    $beneficiaryMessage = $actor . ' (' . $actorRole . ') has updated your weekly care plan.';
                    $this->sendNotificationToBeneficiary($beneficiary->beneficiary_id, $beneficiaryTitle, $beneficiaryMessage);
                }
                
                // 2. Notify all family members about the update
                $familyTitle = 'Weekly Care Plan Updated for Your Relative';
                $familyMessage = 'The weekly care plan for ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                            ' has been updated by ' . $actor . ' (' . $actorRole . ').';
                $this->sendNotificationToFamilyMembers($beneficiary->beneficiary_id, $familyTitle, $familyMessage);
                
                // 3. If the editor is not the original care worker, notify them about the update
                if ($weeklyCarePlan->care_worker_id && $weeklyCarePlan->care_worker_id != Auth::id()) {
                    $careWorkerTitle = 'Your Weekly Care Plan Was Updated';
                    $careWorkerMessage = 'The weekly care plan you created for ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                                    ' has been updated by ' . $actor . ' (' . $actorRole . ').';
                    $this->sendNotificationToCareWorker($weeklyCarePlan->care_worker_id, $careWorkerTitle, $careWorkerMessage);
                }
                
                // 4. Notify the care manager if the editor is a care worker
                if (Auth::user()->role_id == 3) { // Care Worker
                    $careWorker = User::find(Auth::id());
                    
                    // Only notify care manager if one is assigned and they're not the one editing the plan
                    if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                        $careManagerTitle = 'Weekly Care Plan Updated by Your Care Worker';
                        $careManagerMessage = 'Your care worker ' . $actor . ' has updated a weekly care plan for beneficiary ' . 
                                            $beneficiary->first_name . ' ' . $beneficiary->last_name . '.';
                        $this->sendNotificationToCareManager($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the main process
                \Log::warning('Error sending notifications for updated weekly care plan: ' . $notifyEx->getMessage());
            }
            
            //This is the one telling it where to go (Note for Nadine)
            return redirect()->route($rolePrefix . '.reports')
            ->with('success', 'Weekly care plan updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating weekly care plan: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        
        // Only admins and care managers can delete plans
        if ($user->role_id == 3) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete care plans.'
            ], 403);
        }

        try {
            // Log incoming request for debugging
            Log::info('Delete request received for weekly care plan ID: ' . $id);
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is required to confirm deletion.'
                ]);
            }

            // Verify the password
            if (!Hash::check($request->password, auth()->user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The password you entered is incorrect.'
                ]);
            }

            // Find the weekly care plan and store details before deletion
            $weeklyCarePlan = WeeklyCarePlan::with('beneficiary')->findOrFail($id);
            $beneficiaryId = $weeklyCarePlan->beneficiary_id;
            $beneficiaryName = $weeklyCarePlan->beneficiary->first_name . ' ' . $weeklyCarePlan->beneficiary->last_name;
            $careWorkerId = $weeklyCarePlan->care_worker_id;

            // Begin database transaction
            DB::beginTransaction();
            
            try {
                // 1. Delete interventions first
                $weeklyCarePlan->interventions()->delete();
                Log::info('Deleted interventions for weekly care plan: ' . $id);
                
                // 2. Get the vital signs ID before deleting the care plan
                $vitalSignsId = $weeklyCarePlan->vital_signs_id;
                
                // 3. Delete the weekly care plan
                $weeklyCarePlan->delete();
                Log::info('Deleted weekly care plan: ' . $id);
                
                // 4. Delete vital signs
                if ($vitalSignsId) {
                    VitalSigns::where('vital_signs_id', $vitalSignsId)->delete();
                    Log::info('Deleted vital signs: ' . $vitalSignsId);
                }

                // Process notifications before committing the transaction
                // (so we have the data we need before it's gone)
                try {
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : 'Care Manager';
                    
                    // 1. Notify the beneficiary about the deletion
                    $beneficiary = Beneficiary::find($beneficiaryId);
                    if ($beneficiary && $beneficiary->portal_account_id) {
                        $beneficiaryTitle = 'Weekly Care Plan Deleted';
                        $beneficiaryMessage = 'Your weekly care plan has been deleted by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToBeneficiary($beneficiaryId, $beneficiaryTitle, $beneficiaryMessage);
                    }
                    
                    // 2. Notify family members about the deletion
                    $familyTitle = 'Weekly Care Plan Deleted for Your Relative';
                    $familyMessage = 'The weekly care plan for ' . $beneficiaryName . ' has been deleted by ' . $actor . ' (' . $actorRole . ').';
                    $this->sendNotificationToFamilyMembers($beneficiaryId, $familyTitle, $familyMessage);
                    
                    // 3. Notify the care worker who created the plan (if they're not the one deleting it)
                    if ($careWorkerId && $careWorkerId != Auth::id()) {
                        $careWorkerTitle = 'Your Weekly Care Plan Was Deleted';
                        $careWorkerMessage = 'The weekly care plan you created for ' . $beneficiaryName . ' has been deleted by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToCareWorker($careWorkerId, $careWorkerTitle, $careWorkerMessage);
                        
                        // 4. Notify the care manager assigned to this care worker (if not the one deleting)
                        $careWorker = User::find($careWorkerId);
                        if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                            $careManagerTitle = 'Weekly Care Plan Deleted';
                            $careManagerMessage = 'A weekly care plan created by your care worker ' . $careWorker->first_name . ' ' . $careWorker->last_name . 
                                                ' for beneficiary ' . $beneficiaryName . ' has been deleted by ' . $actor . ' (' . $actorRole . ').';
                            $this->sendNotificationToCareManager($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                        }
                    }
                } catch (\Exception $notifyEx) {
                    // Log but don't fail the transaction if notifications fail
                    \Log::warning('Failed to send weekly care plan deletion notifications: ' . $notifyEx->getMessage());
                }
                
                DB::commit();
                
                $reportsRoute = auth()->user()->role_id == 2 
                    ? route('care-manager.reports') 
                    : route('admin.reports');

                return response()->json([
                    'success' => true,
                    'message' => 'Weekly care plan deleted successfully.',
                    'redirectTo' => $reportsRoute  // Add this line
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction error: ' . $e->getMessage());
                throw $e; // Re-throw to be caught by outer catch
            }
        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Weekly care plan not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Weekly care plan not found.'
            ], 404);
        }
        catch (\Exception $e) {
            // Log detailed error information
            Log::error('Weekly care plan deletion error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $reportsRoute = auth()->user()->role_id == 2 
            ? route('care-manager.reports') 
            : route('admin.reports');

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request: ' . $e->getMessage(),
                'redirectTo' => $reportsRoute  // Add this line
            ], 500);
        }
    }

    /**
     * Send notification to a beneficiary
     *
     * @param int $beneficiaryId ID of the beneficiary to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToBeneficiary($beneficiaryId, $title, $message)
    {
        try {
            // Check if beneficiary has an associated portal account
            $beneficiary = Beneficiary::find($beneficiaryId);
            if (!$beneficiary || !$beneficiary->portal_account_id) {
                \Log::warning('Cannot send notification to beneficiary: No portal account found for beneficiary ID ' . $beneficiaryId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $beneficiary->portal_account_id;
            $notification->user_type = 'beneficiary';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for beneficiary ' . $beneficiaryId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to beneficiary ' . $beneficiaryId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to all family members of a beneficiary
     *
     * @param int $beneficiaryId ID of the beneficiary
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToFamilyMembers($beneficiaryId, $title, $message)
    {
        try {
            // Get all family members with portal accounts
            $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiaryId)
                ->whereNotNull('portal_account_id')
                ->get();
                
            if ($familyMembers->isEmpty()) {
                \Log::info('No family members with portal accounts found for beneficiary ' . $beneficiaryId);
                return;
            }
            
            foreach ($familyMembers as $member) {
                // Create notification for each family member
                $notification = new Notification();
                $notification->user_id = $member->portal_account_id;
                $notification->user_type = 'family_member';
                $notification->message_title = $title;
                $notification->message = $message;
                $notification->date_created = now();
                $notification->is_read = false;
                $notification->save();
                
                \Log::info('Created notification for family member ' . $member->family_member_id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send notifications to family members of beneficiary ' . $beneficiaryId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a care worker
     *
     * @param int $careWorkerId ID of the care worker to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToCareWorker($careWorkerId, $title, $message)
    {
        try {
            // Ensure care worker exists and is active
            $careWorker = User::where('id', $careWorkerId)
                ->where('role_id', 3)
                ->where('status', 'Active')
                ->first();
                
            if (!$careWorker) {
                \Log::warning('Cannot send notification: No active care worker found with ID ' . $careWorkerId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $careWorkerId;
            $notification->user_type = 'cose_staff';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for care worker ' . $careWorkerId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to care worker ' . $careWorkerId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a care manager
     *
     * @param int $careManagerId ID of the care manager to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToCareManager($careManagerId, $title, $message)
    {
        try {
            // Ensure care manager exists and is active
            $careManager = User::where('id', $careManagerId)
                ->where('role_id', 2)
                ->where('status', 'Active')
                ->first();
                
            if (!$careManager) {
                \Log::warning('Cannot send notification: No active care manager found with ID ' . $careManagerId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $careManagerId;
            $notification->user_type = 'cose_staff';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for care manager ' . $careManagerId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to care manager ' . $careManagerId . ': ' . $e->getMessage());
        }
    }
}
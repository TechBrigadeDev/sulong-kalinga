<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCarePlan;
use App\Models\VitalSigns;
use App\Models\WeeklyCarePlanInterventions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\UploadService;
use App\Services\NotificationService;
use App\Services\LogService;

class WeeklyCarePlanApiController extends Controller
{
    protected $uploadService;
    protected $notificationService;
    protected $logService;

    public function __construct(UploadService $uploadService, NotificationService $notificationService, LogService $logService)
    {
        $this->uploadService = $uploadService;
        $this->notificationService = $notificationService;
        $this->logService = $logService;
    }

    public function store(Request $request)
    {
        set_time_limit(120);
        // Authorization: Only allow Care Workers (role_id == 3)
        if (!$request->user() || $request->user()->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Care Workers can create Weekly Care Plans.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:29,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'photo' => 'required|file|image|max:4096',
            'selected_interventions' => 'required|array|min:1',
            'duration_minutes' => 'required|array|min:1',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',

            // Illness validation now matches updateWeekly:
            'illness' => 'nullable|array|max:20',
            'illness.*' => 'string|max:100',

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

            'blood_pressure.required' => 'Blood pressure is required',
            'blood_pressure.regex' => 'Blood pressure must be in format 120/80',

            'body_temperature.required' => 'Body temperature is required',
            'body_temperature.between' => 'Body temperature must be between 29°C and 42°C',

            'pulse_rate.required' => 'Pulse rate is required',
            'pulse_rate.between' => 'Pulse rate must be between 40 and 200',

            'respiratory_rate.required' => 'Respiratory rate is required',
            'respiratory_rate.between' => 'Respiratory rate must be between 8 and 40',

            'evaluation_recommendations.required' => 'Please provide evaluation and recommendations',
            'evaluation_recommendations.min' => 'Evaluation and recommendations must be at least 20 characters',
            'evaluation_recommendations.max' => 'Evaluation and recommendations cannot exceed 5000 characters',

            'photo.required' => 'A photo is required for documentation purposes',
            'photo.image' => 'The uploaded file must be an image',
            'photo.max' => 'The photo should not exceed 4MB',

            'selected_interventions.required' => 'Please select at least one intervention',
            'duration_minutes.required' => 'Please specify the duration for all selected interventions',
            'duration_minutes.*.required' => 'Please specify the duration for all selected interventions',
            'duration_minutes.*.numeric' => 'Duration must be a number',
            'duration_minutes.*.min' => 'Duration must be greater than 0',
            'duration_minutes.*.max' => 'Duration cannot exceed 999.99 minutes',

            'custom_category.*.exists' => 'Invalid care category selected',

            'custom_description.*.required_with' => 'Please provide a description for custom interventions',
            'custom_description.*.min' => 'Custom intervention description must be at least 5 characters',
            'custom_description.*.max' => 'Custom intervention description must not exceed 255 characters',
            'custom_description.*.regex' => 'Custom intervention description must contain text and can only include letters, numbers, and basic punctuation',

            'custom_duration.*.required_with' => 'Please provide a duration for custom interventions',
            'custom_duration.*.numeric' => 'Custom intervention duration must be a number',
            'custom_duration.*.min' => 'Custom intervention duration must be greater than 0',
            'custom_duration.*.max' => 'Custom intervention duration cannot exceed 999.99 minutes',

            // Illness error messages
            'illness.array' => 'Illnesses must be an array.',
            'illness.*.string' => 'Each illness must be a string.',
            'illness.*.max' => 'Each illness cannot exceed 100 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Save vital signs
            $vitalSigns = VitalSigns::create([
                'blood_pressure' => $request->blood_pressure,
                'body_temperature' => $request->body_temperature,
                'pulse_rate' => $request->pulse_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            // 2. Prepare illnesses as JSON (trim, filter, only if not empty)
            $illnesses = null;
            if ($request->has('illness') && !empty($request->illness)) {
                $illnessesArray = array_filter(array_map('trim', explode(',', $request->illness)), function($value) {
                    return !empty($value);
                });
                if (count($illnessesArray) > 0) {
                    $illnesses = json_encode($illnessesArray);
                }
            }

            // 3. Handle photo upload using UploadService
            $beneficiary = \App\Models\Beneficiary::find($request->beneficiary_id);
            $firstName = $beneficiary ? $beneficiary->first_name : 'unknown';
            $lastName = $beneficiary ? $beneficiary->last_name : 'unknown';
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            // $photoPath = "testing";
            $photoPath = $this->uploadService->upload(
                $request->file('photo'),
                'spaces-private',
                'uploads/weekly_care_plan_photos',
                [
                    'filename' => $firstName . '_' . $lastName . '_weeklycare_' . $uniqueIdentifier . '.' . $request->file('photo')->getClientOriginalExtension()
                ]
            );

            // 4. Save Weekly Care Plan
            $wcp = WeeklyCarePlan::create([
                'beneficiary_id' => $request->beneficiary_id,
                'care_worker_id' => $request->user()->id, 
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'date' => now()->toDateString(),
                'assessment' => $request->assessment,
                'illnesses' => $illnesses,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'photo_path' => $photoPath,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            // 5. Save interventions
            foreach ($request->selected_interventions as $idx => $interventionId) {
                if (
                    isset($request->duration_minutes[$idx]) &&
                    !is_null($request->duration_minutes[$idx]) &&
                    $request->duration_minutes[$idx] > 0
                ) {
                    $intervention = \App\Models\Intervention::find($interventionId);
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $wcp->weekly_care_plan_id,
                        'intervention_id' => $interventionId,
                        'care_category_id' => $intervention ? $intervention->care_category_id : null,
                        'intervention_description' => $intervention ? $intervention->intervention_description : null,
                        'duration_minutes' => $request->duration_minutes[$idx],
                        'implemented' => true,
                    ]);
                }
            }

            // 6. Save custom interventions if provided (strict: only if all fields present and valid)
            if (
                $request->has('custom_category') && is_array($request->custom_category) &&
                $request->has('custom_description') && is_array($request->custom_description) &&
                $request->has('custom_duration') && is_array($request->custom_duration)
            ) {
                foreach ($request->custom_category as $index => $categoryId) {
                    if (
                        !empty($categoryId) &&
                        !empty($request->custom_description[$index]) &&
                        isset($request->custom_duration[$index]) &&
                        $request->custom_duration[$index] > 0
                    ) {
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $wcp->weekly_care_plan_id,
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index],
                            'implemented' => true
                        ]);
                    }
                }
            }

            DB::commit();

            // --- Notification Service Implementation ---
            $beneficiaryName = $beneficiary ? trim($beneficiary->first_name . ' ' . $beneficiary->last_name) : '';

            // Notify the beneficiary
            if ($beneficiary) {
                $this->notificationService->notifyBeneficiary(
                    $beneficiary->beneficiary_id,
                    'New Weekly Care Plan',
                    'A new weekly care plan has been created for you.'
                );
            }

            // Notify all related family members
            if ($beneficiary && $beneficiary->familyMembers) {
                foreach ($beneficiary->familyMembers as $familyMember) {
                    $this->notificationService->notifyFamilyMember(
                        $familyMember->family_member_id,
                        'New Weekly Care Plan',
                        'A new weekly care plan has been created for your beneficiary.'
                    );
                }
            }

            // Notify the actor (care worker)
            $this->notificationService->notifyStaff(
                $request->user()->id,
                'Weekly Care Plan Created',
                'Your weekly care plan creation was successful.'
            );

            // Notify all care managers
            $this->notificationService->notifyAllCareManagers(
                'New Weekly Care Plan Created',
                "A new weekly care plan has been created for beneficiary {$beneficiaryName} by care worker {$request->user()->id}."
            );

            // Log the creation
            $this->logService->createLog(
                'weekly_care_plan',
                $wcp->weekly_care_plan_id,
                'weekly_care_plan_created',
                "Weekly Care Plan for {$beneficiaryName} created by user {$request->user()->id}",
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'data' => $wcp->fresh(['vitalSigns', 'beneficiary', 'interventions']),
                'photo_url' => $photoPath ? $this->uploadService->getTemporaryPrivateUrl($photoPath, 30) : null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Weekly Care Plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all interventions grouped by care category.
     */
    public function getInterventionsByCategory()
    {
        $categories = \App\Models\CareCategory::with(['interventions' => function($query) {
            $query->select('intervention_id', 'care_category_id', 'intervention_description');
        }])->get(['care_category_id', 'care_category_name']);

        // Format as array grouped by care_category_id
        $result = [];
        foreach ($categories as $category) {
            $result[] = [
                'care_category_id' => $category->care_category_id,
                'care_category_name' => $category->care_category_name,
                'interventions' => $category->interventions->map(function($intervention) {
                    return [
                        'intervention_id' => $intervention->intervention_id,
                        'intervention_description' => $intervention->intervention_description,
                    ];
                })->toArray()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}

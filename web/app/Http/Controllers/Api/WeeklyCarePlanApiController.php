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

// Create only
class WeeklyCarePlanApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'photo' => 'required|image|max:2048', // Now expects an uploaded file
            'selected_interventions' => 'required|array|min:1',
            'duration_minutes' => 'required|array|min:1',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',
            'illness' => 'nullable|string|max:500',

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
            'body_temperature.between' => 'Body temperature must be between 35°C and 42°C',

            'pulse_rate.required' => 'Pulse rate is required',
            'pulse_rate.between' => 'Pulse rate must be between 40 and 200',

            'respiratory_rate.required' => 'Respiratory rate is required',
            'respiratory_rate.between' => 'Respiratory rate must be between 8 and 40',

            'evaluation_recommendations.required' => 'Please provide evaluation and recommendations',
            'evaluation_recommendations.min' => 'Evaluation and recommendations must be at least 20 characters',
            'evaluation_recommendations.max' => 'Evaluation and recommendations cannot exceed 5000 characters',

            'photo.required' => 'A photo is required for documentation purposes',
            'photo.image' => 'The uploaded file must be an image',
            'photo.max' => 'The photo should not exceed 2MB',

            'selected_interventions.required' => 'Please select at least one intervention',
            'duration_minutes.required' => 'Please specify the duration for all selected interventions',
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

            'illness.max' => 'The illnesses list cannot exceed 500 characters',
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
            ]);

            // 2. Prepare illnesses as JSON
            $illness = $request->illness ? json_encode(explode(',', $request->illness)) : null;

            // 3. Handle photo upload
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $photoPath = $this->uploadService->upload(
                $request->file('photo'),
                'spaces-private',
                'uploads/weekly_care_plan_photos',
                'wcp_' . $request->user()->id . '_' . $uniqueIdentifier . '.' . $request->file('photo')->getClientOriginalExtension()
            );

            // 4. Save Weekly Care Plan
            $wcp = WeeklyCarePlan::create([
                'beneficiary_id' => $request->beneficiary_id,
                'care_worker_id' => $request->user()->id,
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'date' => now()->toDateString(),
                'assessment' => $request->assessment,
                'illnesses' => $illness,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'photo_path' => $photoPath,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            // 5. Save interventions
            foreach ($request->selected_interventions as $idx => $interventionId) {
                WeeklyCarePlanInterventions::create([
                    'weekly_care_plan_id' => $wcp->weekly_care_plan_id,
                    'intervention_id' => $interventionId,
                    'duration_minutes' => $request->duration_minutes[$idx] ?? null,
                ]);
            }

            // 6. Save custom interventions if provided
            if ($request->custom_category && $request->custom_description && $request->custom_duration) {
                foreach ($request->custom_category as $idx => $cat) {
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $wcp->weekly_care_plan_id,
                        'custom_category' => $cat,
                        'custom_description' => $request->custom_description[$idx] ?? '',
                        'duration_minutes' => $request->custom_duration[$idx] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $wcp->fresh(['vitalSigns', 'beneficiary'])
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
}

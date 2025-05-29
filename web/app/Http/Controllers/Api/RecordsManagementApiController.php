<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklyCarePlan;
use App\Models\GeneralCarePlan;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\VitalSigns;
use App\Models\WeeklyCarePlanInterventions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\UploadService;

class RecordsManagementApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    // --- WEEKLY CARE PLANS ---

    // List all weekly care plans (role-based)
    public function listWeekly(Request $request)
    {
        $user = $request->user();
        $query = WeeklyCarePlan::with(['beneficiary', 'author', 'vitalSigns']);

        // Role-based filtering
        if ($user->role_id == 3) { // Care Worker
            $query->where('care_worker_id', $user->id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $plans = $query->orderBy('date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->weekly_care_plan_id,
                    'date' => $plan->date,
                    'beneficiary' => $plan->beneficiary ? $plan->beneficiary->full_name : null,
                    'care_worker' => $plan->author ? $plan->author->full_name : null,
                    'assessment' => $plan->assessment,
                    'photo_url' => $plan->photo_path
                        ? Storage::disk('spaces-private')->temporaryUrl($plan->photo_path, now()->addMinutes(30))
                        : null,
                ];
            }),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ]
        ]);
    }

    // View specific weekly care plan
    public function showWeekly($id, Request $request)
    {
        $user = $request->user();
        $plan = WeeklyCarePlan::with([
            'beneficiary',
            'author',
            'vitalSigns',
            'interventions'
        ])->findOrFail($id);

        // Role-based access
        if ($user->role_id == 3 && $plan->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->weekly_care_plan_id,
                'date' => $plan->date,
                'beneficiary' => $plan->beneficiary,
                'care_worker' => $plan->author,
                'assessment' => $plan->assessment,
                'evaluation_recommendations' => $plan->evaluation_recommendations,
                'illnesses' => $plan->illnesses ? json_decode($plan->illnesses) : [],
                'vital_signs' => $plan->vitalSigns,
                'interventions' => $plan->interventions,
                'photo_url' => $plan->photo_path
                    ? Storage::disk('spaces-private')->temporaryUrl($plan->photo_path, now()->addMinutes(30))
                    : null,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]
        ]);
    }

    // Edit weekly care plan
    public function updateWeekly($id, Request $request)
    {
        $user = $request->user();
        $plan = WeeklyCarePlan::with(['vitalSigns', 'interventions'])->findOrFail($id);

        // Role-based access
        if ($user->role_id == 3 && $plan->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'assessment' => 'sometimes|required|string|min:20|max:5000',
            'evaluation_recommendations' => 'sometimes|required|string|min:20|max:5000',
            'illness' => 'nullable|string|max:500',
            'photo' => 'sometimes|nullable|image|max:2048',
            // Vital signs
            'blood_pressure' => 'sometimes|required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'sometimes|required|numeric|between:35,42',
            'pulse_rate' => 'sometimes|required|integer|between:40,200',
            'respiratory_rate' => 'sometimes|required|integer|between:8,40',
            // Interventions
            'selected_interventions' => 'sometimes|array|min:1',
            'duration_minutes' => 'sometimes|array|min:1',
            'duration_minutes.*' => 'sometimes|numeric|min:0.01|max:999.99',
            // Custom interventions
            'custom_category.*' => 'sometimes|nullable|exists:care_categories,care_category_id',
            'custom_description.*' => 'sometimes|nullable|required_with:custom_category.*|string|min:5|max:255|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'\"]+$/',
            'custom_duration.*' => 'sometimes|nullable|required_with:custom_category.*|numeric|min:0.01|max:999.99',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Update main fields
            if ($request->has('assessment')) $plan->assessment = $request->assessment;
            if ($request->has('evaluation_recommendations')) $plan->evaluation_recommendations = $request->evaluation_recommendations;
            if ($request->has('illness')) $plan->illnesses = $request->illness ? json_encode(explode(',', $request->illness)) : null;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                if ($plan->photo_path && Storage::disk('spaces-private')->exists($plan->photo_path)) {
                    Storage::disk('spaces-private')->delete($plan->photo_path);
                }
                $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
                $photoPath = $this->uploadService->upload(
                    $request->file('photo'),
                    'spaces-private',
                    'uploads/weekly_care_plan_photos',
                    'wcp_' . $user->id . '_' . $uniqueIdentifier . '.' . $request->file('photo')->getClientOriginalExtension()
                );
                $plan->photo_path = $photoPath;
            }

            // Update vital signs
            $vital = $plan->vitalSigns;
            if ($vital) {
                if ($request->has('blood_pressure')) $vital->blood_pressure = $request->blood_pressure;
                if ($request->has('body_temperature')) $vital->body_temperature = $request->body_temperature;
                if ($request->has('pulse_rate')) $vital->pulse_rate = $request->pulse_rate;
                if ($request->has('respiratory_rate')) $vital->respiratory_rate = $request->respiratory_rate;
                $vital->save();
            }

            // Update interventions
            if ($request->has('selected_interventions') && $request->has('duration_minutes')) {
                // Remove old interventions
                WeeklyCarePlanInterventions::where('weekly_care_plan_id', $plan->weekly_care_plan_id)->delete();
                foreach ($request->selected_interventions as $idx => $interventionId) {
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $plan->weekly_care_plan_id,
                        'intervention_id' => $interventionId,
                        'duration_minutes' => $request->duration_minutes[$idx] ?? null,
                    ]);
                }
            }
            // Custom interventions
            if ($request->has('custom_category') && $request->has('custom_description') && $request->has('custom_duration')) {
                foreach ($request->custom_category as $idx => $cat) {
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $plan->weekly_care_plan_id,
                        'custom_category' => $cat,
                        'custom_description' => $request->custom_description[$idx] ?? '',
                        'duration_minutes' => $request->custom_duration[$idx] ?? null,
                    ]);
                }
            }

            $plan->save();
            DB::commit();

            return response()->json(['success' => true, 'data' => $plan->fresh(['beneficiary', 'author', 'vitalSigns', 'interventions'])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update Weekly Care Plan.', 'error' => $e->getMessage()], 500);
        }
    }

    // --- GENERAL CARE PLANS ---

    // List all general care plans (role-based)
    public function listGeneral(Request $request)
    {
        $user = $request->user();
        $query = GeneralCarePlan::with([
            'beneficiary',
            'careNeeds',
            'healthHistory',
            'medications',
            'mobility',
            'cognitiveFunction',
            'emotionalWellbeing',
            'careWorkerResponsibility',
            'careWorker',
            'careManager'
        ]);

        // Role-based filtering
        if ($user->role_id == 3) { // Care Worker
            $query->where('care_worker_id', $user->id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $plans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->general_care_plan_id,
                    'beneficiary' => $plan->beneficiary ? $plan->beneficiary->full_name : null,
                    'care_worker' => $plan->careWorker ? $plan->careWorker->full_name : null,
                    'care_manager' => $plan->careManager ? $plan->careManager->full_name : null,
                    'created_at' => $plan->created_at,
                    'updated_at' => $plan->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ]
        ]);
    }

    // View specific general care plan
    public function showGeneral($id, Request $request)
    {
        $user = $request->user();
        $plan = GeneralCarePlan::with([
            'beneficiary',
            'careNeeds',
            'healthHistory',
            'medications',
            'mobility',
            'cognitiveFunction',
            'emotionalWellbeing',
            'careWorkerResponsibility',
            'careWorker',
            'careManager'
        ])->findOrFail($id);

        // Role-based access
        if ($user->role_id == 3 && $plan->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->general_care_plan_id,
                'beneficiary' => $plan->beneficiary,
                'care_worker' => $plan->careWorker,
                'care_manager' => $plan->careManager,
                'care_needs' => $plan->careNeeds,
                'health_history' => $plan->healthHistory,
                'medications' => $plan->medications,
                'mobility' => $plan->mobility,
                'cognitive_function' => $plan->cognitiveFunction,
                'emotional_wellbeing' => $plan->emotionalWellbeing,
                'care_worker_responsibility' => $plan->careWorkerResponsibility,
                'general_care_plan_doc_url' => $plan->general_care_plan_doc
                    ? Storage::disk('spaces-private')->temporaryUrl($plan->general_care_plan_doc, now()->addMinutes(30))
                    : null,
                'beneficiary_signature_url' => $plan->beneficiary_signature
                    ? Storage::disk('spaces-private')->temporaryUrl($plan->beneficiary_signature, now()->addMinutes(30))
                    : null,
                'care_worker_signature_url' => $plan->care_worker_signature
                    ? Storage::disk('spaces-private')->temporaryUrl($plan->care_worker_signature, now()->addMinutes(30))
                    : null,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]
        ]);
    }

    // Edit general care plan
    public function updateGeneral($id, Request $request)
    {
        $user = $request->user();
        $plan = GeneralCarePlan::findOrFail($id);

        // Role-based access
        if ($user->role_id == 3 && $plan->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'general_care_plan_doc' => 'sometimes|nullable|file|mimes:pdf,doc,docx|max:5120',
            'beneficiary_signature' => 'sometimes|nullable|file|mimes:jpeg,png|max:2048',
            'care_worker_signature' => 'sometimes|nullable|file|mimes:jpeg,png|max:2048',
            // Add other fields as needed for editing
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Handle file uploads
        if ($request->hasFile('general_care_plan_doc')) {
            if ($plan->general_care_plan_doc && Storage::disk('spaces-private')->exists($plan->general_care_plan_doc)) {
                Storage::disk('spaces-private')->delete($plan->general_care_plan_doc);
            }
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $plan->general_care_plan_doc = $this->uploadService->upload(
                $request->file('general_care_plan_doc'),
                'spaces-private',
                'uploads/general_care_plan_docs',
                'gcp_' . $user->id . '_' . $uniqueIdentifier . '.' . $request->file('general_care_plan_doc')->getClientOriginalExtension()
            );
        }
        if ($request->hasFile('beneficiary_signature')) {
            if ($plan->beneficiary_signature && Storage::disk('spaces-private')->exists($plan->beneficiary_signature)) {
                Storage::disk('spaces-private')->delete($plan->beneficiary_signature);
            }
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $plan->beneficiary_signature = $this->uploadService->upload(
                $request->file('beneficiary_signature'),
                'spaces-private',
                'uploads/general_care_plan_signatures',
                'gcp_bsig_' . $user->id . '_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature')->getClientOriginalExtension()
            );
        }
        if ($request->hasFile('care_worker_signature')) {
            if ($plan->care_worker_signature && Storage::disk('spaces-private')->exists($plan->care_worker_signature)) {
                Storage::disk('spaces-private')->delete($plan->care_worker_signature);
            }
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $plan->care_worker_signature = $this->uploadService->upload(
                $request->file('care_worker_signature'),
                'spaces-private',
                'uploads/general_care_plan_signatures',
                'gcp_cwsig_' . $user->id . '_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature')->getClientOriginalExtension()
            );
        }

        // Update other editable fields as needed
        // Example:
        // if ($request->has('some_field')) $plan->some_field = $request->some_field;

        $plan->save();

        return response()->json(['success' => true, 'data' => $plan->fresh()]);
    }
}
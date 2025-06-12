<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklyCarePlan;
use App\Models\WeeklyCarePlanInterventions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\UploadService;
use App\Models\Beneficiary;
use App\Models\User;
use App\Services\NotificationService;

class RecordsManagementApiController extends Controller
{
    protected $uploadService;
    protected $notificationService;

    public function __construct(UploadService $uploadService, NotificationService $notificationService)
    {
        $this->uploadService = $uploadService;
        $this->notificationService = $notificationService;
    }

    // --- WEEKLY CARE PLANS ONLY ---

    // List all weekly care plans (role-based)
    public function listWeekly(Request $request)
    {
        $user = $request->user();
        $query = WeeklyCarePlan::query();

        if ($user->role_id == 3) { // Care Worker
            $query->where('care_worker_id', $user->id);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $plans = $query->orderBy('date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $plans->map(function ($plan) {
                // Fetch beneficiary
                $beneficiary = Beneficiary::find($plan->beneficiary_id);
                $beneficiaryFullName = $beneficiary
                    ? trim("{$beneficiary->first_name} {$beneficiary->middle_name} {$beneficiary->last_name}")
                    : null;

                // Fetch care worker (author)
                $careWorker = User::find($plan->care_worker_id);
                $careWorkerFullName = $careWorker
                    ? trim("{$careWorker->first_name} {$careWorker->last_name}")
                    : null;

                return [
                    'id' => $plan->weekly_care_plan_id,
                    'date' => $plan->date,
                    'beneficiary' => $beneficiaryFullName,
                    'care_worker' => $careWorkerFullName,
                    // 'assessment' => $plan->assessment,
                    'photo_url' => $plan->photo_path
                        ? $this->uploadService->getTemporaryPrivateUrl($plan->photo_path, 30)
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

    // View specific weekly care plan (GET for show/edit)
    public function showWeekly($id, Request $request)
    {
        $user = $request->user();
        $plan = WeeklyCarePlan::with([
            'vitalSigns',
            'interventions'
        ])->findOrFail($id);

        if ($user->role_id == 3 && $plan->care_worker_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // Fetch beneficiary
        $beneficiary = Beneficiary::find($plan->beneficiary_id);
        $beneficiaryFullName = $beneficiary
            ? trim("{$beneficiary->first_name} {$beneficiary->middle_name} {$beneficiary->last_name}")
            : null;

        // Fetch care worker (author)
        $careWorker = User::find($plan->care_worker_id);
        $careWorkerFullName = $careWorker
            ? trim("{$careWorker->first_name} {$careWorker->last_name}")
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->weekly_care_plan_id,
                'date' => $plan->date,
                'beneficiary' => $beneficiaryFullName,
                'care_worker' => $careWorkerFullName,
                'assessment' => $plan->assessment,
                'evaluation_recommendations' => $plan->evaluation_recommendations,
                'illnesses' => $plan->illnesses ? json_decode($plan->illnesses) : [],
                'vital_signs' => $plan->vitalSigns,
                'interventions' => $plan->interventions,
                'photo_url' => $plan->photo_path
                    ? $this->uploadService->getTemporaryPrivateUrl($plan->photo_path, 30)
                    : null,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]
        ]);
    }

    // Edit/Update weekly care plan (PATCH)
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
            'illness' => 'nullable|array|max:20',
            'illness.*' => 'string|max:100',
            'photo' => 'sometimes|nullable|image|max:4096',
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
        ], [
            'illness.array' => 'Illnesses must be an array.',
            'illness.*.string' => 'Each illness must be a string.',
            'illness.*.max' => 'Each illness cannot exceed 100 characters.',
            'photo.max' => 'The photo should not exceed 4MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Update main fields
            if ($request->has('assessment')) $plan->assessment = $request->assessment;
            if ($request->has('evaluation_recommendations')) $plan->evaluation_recommendations = $request->evaluation_recommendations;
            if ($request->has('illness')) {
                $illnesses = array_filter(array_map('trim', $request->illness));
                $plan->illnesses = count($illnesses) ? json_encode($illnesses) : null;
            }

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
                    [
                        'filename' => 'wcp_' . $user->id . '_' . $uniqueIdentifier . '.' . $request->file('photo')->getClientOriginalExtension()
                    ]
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
            if (
                $request->has('custom_category') && is_array($request->custom_category) &&
                $request->has('custom_description') && is_array($request->custom_description) &&
                $request->has('custom_duration') && is_array($request->custom_duration)
            ) {
                foreach ($request->custom_category as $idx => $cat) {
                    if (
                        !empty($cat) &&
                        !empty($request->custom_description[$idx]) &&
                        isset($request->custom_duration[$idx]) &&
                        $request->custom_duration[$idx] > 0
                    ) {
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $plan->weekly_care_plan_id,
                            'care_category_id' => $cat,
                            'intervention_description' => $request->custom_description[$idx] ?? '',
                            'duration_minutes' => $request->custom_duration[$idx] ?? null,
                            'implemented' => true
                        ]);
                    }
                }
            }

            // Reset acknowledgments
            $plan->acknowledged_by_beneficiary = null;
            $plan->acknowledged_by_family = null;

            $plan->save();
            DB::commit();

            // --- NOTIFICATIONS (after successful update) ---
            $beneficiary = Beneficiary::find($plan->beneficiary_id);
            $beneficiaryName = $beneficiary
                ? trim("{$beneficiary->first_name} {$beneficiary->middle_name} {$beneficiary->last_name}")
                : null;

            // Notify beneficiary
            if ($beneficiary) {
                $this->notificationService->notifyBeneficiary(
                    $beneficiary->beneficiary_id,
                    'Weekly Care Plan Updated',
                    'Your weekly care plan was updated.'
                );
            }

            // Notify all related family members
            if ($beneficiary && $beneficiary->familyMembers) {
                foreach ($beneficiary->familyMembers as $familyMember) {
                    $this->notificationService->notifyFamilyMember(
                        $familyMember->family_member_id,
                        'Weekly Care Plan Updated',
                        "The weekly care plan for {$beneficiaryName} was updated."
                    );
                }
            }

            // Notify assigned care worker (if not the actor)
            if ($plan->care_worker_id && $plan->care_worker_id != $user->id) {
                $this->notificationService->notifyStaff(
                    $plan->care_worker_id,
                    'Weekly Care Plan Updated',
                    "The weekly care plan for {$beneficiaryName} was updated."
                );
            }

            // Notify the actor
            $this->notificationService->notifyStaff(
                $user->id,
                'Weekly Care Plan Updated',
                'Your weekly care plan update was successful.'
            );

            return response()->json([
                'success' => true,
                'data' => $plan->fresh(['beneficiary', 'author', 'vitalSigns', 'interventions'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error in production, do not expose details
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Weekly Care Plan.'
            ], 500);
        }
    }
}
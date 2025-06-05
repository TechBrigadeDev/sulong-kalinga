<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicationSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MedicationScheduleApiController extends Controller
{
    /**
     * Store a newly created medication schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'medication_name' => 'required|string|max:255|not_regex:/^\d+$/',
            'dosage' => 'required|string|max:100|not_regex:/^\d+$/',
            'medication_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'morning_time' => 'nullable|date_format:H:i',
            'noon_time' => 'nullable|date_format:H:i',
            'evening_time' => 'nullable|date_format:H:i',
            'night_time' => 'nullable|date_format:H:i',
            'as_needed' => 'required_without_all:morning_time,noon_time,evening_time,night_time|boolean',
            'with_food_morning' => 'boolean',
            'with_food_noon' => 'boolean',
            'with_food_evening' => 'boolean',
            'with_food_night' => 'boolean',
            'special_instructions' => 'nullable|string|max:1000',
            'status' => 'required|in:active,completed,paused',
        ], [
            'beneficiary_id.required' => 'Please select a beneficiary.',
            'medication_name.not_regex' => 'Medication name cannot be purely numeric.',
            'dosage.not_regex' => 'Dosage cannot be purely numeric.',
            'as_needed.required_without_all' => 'At least one time or "as needed" must be set.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if ($user->role_id == 3) { // Care Worker
            $assignedBeneficiaryIds = \App\Models\Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                $query->where('care_worker_id', $user->id);
            })->pluck('beneficiary_id');
            if (!$assignedBeneficiaryIds->contains($request->beneficiary_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create a schedule for this beneficiary.'
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            $schedule = MedicationSchedule::create([
                'beneficiary_id' => $request->beneficiary_id,
                'medication_name' => $request->medication_name,
                'dosage' => $request->dosage,
                'medication_type' => $request->medication_type,
                'morning_time' => $request->morning_time,
                'noon_time' => $request->noon_time,
                'evening_time' => $request->evening_time,
                'night_time' => $request->night_time,
                'as_needed' => $request->as_needed ?? false,
                'with_food_morning' => $request->with_food_morning ?? false,
                'with_food_noon' => $request->with_food_noon ?? false,
                'with_food_evening' => $request->with_food_evening ?? false,
                'with_food_night' => $request->with_food_night ?? false,
                'special_instructions' => $request->special_instructions,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(['beneficiary'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create medication schedule.'
            ], 500);
        }
    }

    /**
     * Display a listing of the medication schedules.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = MedicationSchedule::with([
            'beneficiary.generalCarePlan.healthHistory',
            'beneficiary',
        ]);

        if ($user->role_id == 3) {
            $assignedBeneficiaryIds = \App\Models\Beneficiary::whereHas('generalCarePlan', function($q) use ($user) {
                $q->where('care_worker_id', $user->id);
            })->pluck('beneficiary_id');
            $query->whereIn('beneficiary_id', $assignedBeneficiaryIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('medication_name', 'ILIKE', "%$search%")
                  ->orWhere('dosage', 'ILIKE', "%$search%")
                  ->orWhereHas('beneficiary', function($b) use ($search) {
                      $b->where('first_name', 'ILIKE', "%$search%")
                        ->orWhere('last_name', 'ILIKE', "%$search%");
                  });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'completed', 'paused'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('beneficiary_id')) {
            $query->where('beneficiary_id', $request->beneficiary_id);
        }

        if ($request->filled('period')) {
            $today = now()->toDateString();
            if ($request->period === 'today') {
                $query->where('start_date', '<=', $today)
                      ->where(function($q) use ($today) {
                          $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                      });
            } elseif ($request->period === 'upcoming') {
                $query->where('start_date', '>', $today);
            } elseif ($request->period === 'past') {
                $query->whereNotNull('end_date')->where('end_date', '<', $today);
            }
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate(15);

        foreach ($schedules as $schedule) {
            if (
                $schedule->beneficiary &&
                $schedule->beneficiary->generalCarePlan &&
                $schedule->beneficiary->generalCarePlan->healthHistory
            ) {
                $healthHistory = $schedule->beneficiary->generalCarePlan->healthHistory;

                if ($healthHistory->medical_conditions) {
                    $conditions = json_decode($healthHistory->medical_conditions, true);
                    $healthHistory->formatted_conditions = is_array($conditions)
                        ? implode(", ", $conditions)
                        : $healthHistory->medical_conditions;
                }

                if ($healthHistory->immunizations) {
                    $immunizations = json_decode($healthHistory->immunizations, true);
                    $healthHistory->formatted_immunizations = is_array($immunizations)
                        ? implode(", ", $immunizations)
                        : $healthHistory->immunizations;
                }

                if ($healthHistory->allergies) {
                    $allergies = json_decode($healthHistory->allergies, true);
                    $healthHistory->formatted_allergies = is_array($allergies)
                        ? implode(", ", $allergies)
                        : $healthHistory->allergies;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    /**
     * Display the specified medication schedule.
     */
    public function show($id)
    {
        $schedule = MedicationSchedule::with([
            'beneficiary.generalCarePlan.healthHistory',
            'beneficiary',
        ])->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Medication schedule not found.'
            ], 404);
        }

        if (
            $schedule->beneficiary &&
            $schedule->beneficiary->generalCarePlan &&
            $schedule->beneficiary->generalCarePlan->healthHistory
        ) {
            $healthHistory = $schedule->beneficiary->generalCarePlan->healthHistory;

            if ($healthHistory->medical_conditions) {
                $conditions = json_decode($healthHistory->medical_conditions, true);
                $healthHistory->formatted_conditions = is_array($conditions)
                    ? implode(", ", $conditions)
                    : $healthHistory->medical_conditions;
            }
            if ($healthHistory->immunizations) {
                $immunizations = json_decode($healthHistory->immunizations, true);
                $healthHistory->formatted_immunizations = is_array($immunizations)
                    ? implode(", ", $immunizations)
                    : $healthHistory->immunizations;
            }
            if ($healthHistory->allergies) {
                $allergies = json_decode($healthHistory->allergies, true);
                $healthHistory->formatted_allergies = is_array($allergies)
                    ? implode(", ", $allergies)
                    : $healthHistory->allergies;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    /**
     * Update the specified medication schedule.
     */
    public function update(Request $request, $id)
    {
        $schedule = MedicationSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Medication schedule not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'medication_name' => 'required|string|max:255|not_regex:/^\d+$/',
            'dosage' => 'required|string|max:100|not_regex:/^\d+$/',
            'medication_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'morning_time' => 'nullable|date_format:H:i',
            'noon_time' => 'nullable|date_format:H:i',
            'evening_time' => 'nullable|date_format:H:i',
            'night_time' => 'nullable|date_format:H:i',
            'as_needed' => 'required_without_all:morning_time,noon_time,evening_time,night_time|boolean',
            'with_food_morning' => 'boolean',
            'with_food_noon' => 'boolean',
            'with_food_evening' => 'boolean',
            'with_food_night' => 'boolean',
            'special_instructions' => 'nullable|string|max:1000',
            'status' => 'required|in:active,completed,paused',
        ], [
            'beneficiary_id.required' => 'Please select a beneficiary.',
            'medication_name.not_regex' => 'Medication name cannot be purely numeric.',
            'dosage.not_regex' => 'Dosage cannot be purely numeric.',
            'as_needed.required_without_all' => 'At least one time or "as needed" must be set.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if ($user->role_id == 3) { // Care Worker
            $assignedBeneficiaryIds = \App\Models\Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                $query->where('care_worker_id', $user->id);
            })->pluck('beneficiary_id');
            if (!$assignedBeneficiaryIds->contains($request->beneficiary_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this schedule for this beneficiary.'
                ], 403);
            }
        }

        // Change detection: block update if no changes
        $input = $request->only([
            'beneficiary_id', 'medication_name', 'dosage', 'medication_type',
            'morning_time', 'noon_time', 'evening_time', 'night_time', 'as_needed',
            'with_food_morning', 'with_food_noon', 'with_food_evening', 'with_food_night',
            'special_instructions', 'start_date', 'end_date', 'status'
        ]);
        $hasChanges = false;
        foreach ($input as $key => $value) {
            if ($schedule->$key != $value) {
                $hasChanges = true;
                break;
            }
        }
        if (!$hasChanges) {
            return response()->json([
                'success' => false,
                'message' => 'No changes detected.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $schedule->update(array_merge($input, [
                'updated_by' => $user->id,
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(['beneficiary'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update medication schedule.'
            ], 500);
        }
    }

    /**
     * Remove the specified medication schedule.
     * Requires password and reason for audit.
     */
    public function destroy(Request $request, $id)
    {
        $schedule = MedicationSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Medication schedule not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'reason' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password.'
            ], 403);
        }

        if ($user->role_id == 3) { // Care Worker
            $assignedBeneficiaryIds = \App\Models\Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                $query->where('care_worker_id', $user->id);
            })->pluck('beneficiary_id');
            if (!$assignedBeneficiaryIds->contains($schedule->beneficiary_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this schedule for this beneficiary.'
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            // Optionally, log the reason and user for audit (implement as needed)
            // MedicationScheduleDeleteLog::create([
            //     'medication_schedule_id' => $schedule->medication_schedule_id,
            //     'deleted_by' => $user->id,
            //     'reason' => $request->reason,
            // ]);

            $schedule->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medication schedule deleted.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medication schedule.'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\Medication;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\HealthHistory;
use App\Models\CareWorkerResponsibility;
use App\Models\PortalAccount;
use App\Models\User;
use App\Models\Notification;
use App\Models\FamilyMember;

class BeneficiaryApiController extends Controller
{
    /**
     * Display a listing of beneficiaries.
     */
    public function index(Request $request)
    {
        $query = Beneficiary::with(['category', 'status', 'municipality']);

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // Add filtering by municipality
        if ($request->has('municipality_id')) {
            $query->where('assigned_municipality_id', $request->get('municipality_id'));
        }

        // Add filtering by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Pagination (default 20 per page)
        $perPage = $request->get('per_page', 20);
        $beneficiaries = $query->orderBy('first_name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'beneficiaries' => $beneficiaries->items(),
            'meta' => [
                'current_page' => $beneficiaries->currentPage(),
                'last_page' => $beneficiaries->lastPage(),
                'per_page' => $beneficiaries->perPage(),
                'total' => $beneficiaries->total(),
            ]
        ]);
    }

    /**
     * Display the specified beneficiary.
     */
    public function show($id)
    {
        $beneficiary = Beneficiary::with([
            'category',
            'status',
            'municipality',
            'barangay',
            'generalCarePlan',
            'generalCarePlan.mobility',
            'generalCarePlan.cognitiveFunction',
            'generalCarePlan.emotionalWellbeing',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careNeeds',
            'generalCarePlan.careWorkerResponsibility',
            'portalAccount',
            'familyMembers',
        ])->findOrFail($id);

        // Care worker can only view assigned beneficiaries
        if (request()->user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == request()->user()->id;
            if (!$isAssigned) {
                return response()->json(['error' => 'Unauthorized. You can only view beneficiaries assigned to you.'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Store a newly created beneficiary.
     */
    public function store(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // Copy the full validation rules from your web controller here (shortened for brevity)
        $validator = \Validator::make($request->all(), [
            'first_name' => [
                'required', 'string', 'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/',
            ],
            'last_name' => [
                'required', 'string', 'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/',
            ],
            // ... (add all other rules from web controller)
            'account.email' => 'required|email|unique:portal_accounts,portal_email|max:255',
            'account.password' => 'required|string|min:8|confirmed',
            // File uploads
            'beneficiaryProfilePic' => 'nullable|file|mimes:jpeg,png|max:2048',
            'care_service_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'beneficiary_signature_canvas' => 'nullable|string',
            'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'care_worker_signature_canvas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $uniqueIdentifier = Str::random(10);

            // Handle file uploads (same logic as web controller)
            $beneficiaryPhotoPath = null;
            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiaryPhotoPath = $request->file('beneficiaryProfilePic')->storeAs(
                    'uploads/beneficiary_photos',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension(),
                    'public'
                );
            }

            $careServiceAgreementPath = null;
            if ($request->hasFile('care_service_agreement')) {
                $careServiceAgreementPath = $request->file('care_service_agreement')->storeAs(
                    'uploads/care_service_agreements',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension(),
                    'public'
                );
            }

            $generalCarePlanPath = null;
            if ($request->hasFile('general_careplan')) {
                $generalCarePlanPath = $request->file('general_careplan')->storeAs(
                    'uploads/general_care_plans',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension(),
                    'public'
                );
            }

            // Handle signatures (file or base64)
            $beneficiarySignaturePath = null;
            if ($request->hasFile('beneficiary_signature_upload')) {
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_signature_' .
                    $uniqueIdentifier . '.' .
                    $request->file('beneficiary_signature_upload')->getClientOriginalExtension();
                $request->file('beneficiary_signature_upload')->storeAs(
                    'public/' . dirname($beneficiarySignaturePath),
                    basename($beneficiarySignaturePath)
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_signature_' .
                    $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('beneficiary_signature_canvas')));
                file_put_contents(storage_path('app/public/' . $beneficiarySignaturePath), $decodedImage);
            }

            $careWorkerSignaturePath = null;
            if ($request->hasFile('care_worker_signature_upload')) {
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_care_worker_signature_' .
                    $uniqueIdentifier . '.' .
                    $request->file('care_worker_signature_upload')->getClientOriginalExtension();
                $request->file('care_worker_signature_upload')->storeAs(
                    'public/' . dirname($careWorkerSignaturePath),
                    basename($careWorkerSignaturePath)
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_care_worker_signature_' .
                    $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('care_worker_signature_canvas')));
                file_put_contents(storage_path('app/public/' . $careWorkerSignaturePath), $decodedImage);
            }

            // Create portal account
            $portalAccountId = PortalAccount::insertGetId([
                'portal_email' => $request->input('account.email'),
                'portal_password' => Hash::make($request->input('account.password')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create general care plan
            $generalCarePlan = GeneralCarePlan::create([
                'care_worker_id' => $request->input('care_worker.careworker_id'),
                'emergency_plan' => $request->input('emergency_plan.procedures'),
                'review_date' => $request->input('date'),
                'created_at' => now(),
            ]);
            $generalCarePlanId = $generalCarePlan->general_care_plan_id;

            // Format mobile numbers
            $mobileNumber = $request->input('mobile_number');
            if (!str_starts_with($mobileNumber, '+63')) {
                $mobileNumber = '+63' . $mobileNumber;
            }
            $emergencyContactMobile = $request->input('emergency_contact.mobile');
            if (!str_starts_with($emergencyContactMobile, '+63')) {
                $emergencyContactMobile = '+63' . $emergencyContactMobile;
            }

            // Create beneficiary
            $beneficiary = Beneficiary::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'birthday' => $request->input('birth_date'),
                'gender' => $request->input('gender'),
                'civil_status' => $request->input('civil_status'),
                'street_address' => $request->input('address_details'),
                'barangay_id' => $request->input('barangay'),
                'municipality_id' => $request->input('municipality'),
                'category_id' => $request->input('category'),
                'mobile' => $mobileNumber,
                'landline' => $request->input('landline_number'),
                'emergency_contact_name' => $request->input('emergency_contact.name'),
                'emergency_contact_relation' => $request->input('emergency_contact.relation'),
                'emergency_contact_mobile' => $emergencyContactMobile,
                'emergency_contact_email' => $request->input('emergency_contact.email'),
                'emergency_procedure' => $request->input('emergency_plan.procedures'),
                'primary_caregiver' => $request->input('primary_caregiver') ?? null,
                'care_service_agreement_doc' => $careServiceAgreementPath,
                'general_care_plan_doc' => $generalCarePlanPath,
                'photo' => $beneficiaryPhotoPath,
                'beneficiary_signature' => $beneficiarySignaturePath,
                'care_worker_signature' => $careWorkerSignaturePath,
                'general_care_plan_id' => $generalCarePlanId,
                'portal_account_id' => $portalAccountId,
                'beneficiary_status_id' => 1,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => Str::random(60),
            ]);

            // Create related models (emotional, cognitive, mobility, health, medications, care needs, responsibilities)
            EmotionalWellbeing::create([
                'general_care_plan_id' => $generalCarePlanId,
                'mood' => $request->input('emotional.mood'),
                'social_interactions' => $request->input('emotional.social_interactions'),
                'emotional_support_needs' => $request->input('emotional.emotional_support'),
            ]);
            CognitiveFunction::create([
                'general_care_plan_id' => $generalCarePlanId,
                'memory' => $request->input('cognitive.memory'),
                'thinking_skills' => $request->input('cognitive.thinking_skills'),
                'orientation' => $request->input('cognitive.orientation'),
                'behavior' => $request->input('cognitive.behavior'),
            ]);
            Mobility::create([
                'general_care_plan_id' => $generalCarePlanId,
                'walking_ability' => $request->input('mobility.walking_ability'),
                'assistive_devices' => $request->input('mobility.assistive_devices'),
                'transportation_needs' => $request->input('mobility.transportation_needs'),
            ]);
            HealthHistory::create([
                'general_care_plan_id' => $generalCarePlanId,
                'medical_conditions' => $request->input('medical_conditions'),
                'medications' => $request->input('medications'),
                'allergies' => $request->input('allergies'),
                'immunizations' => $request->input('immunizations'),
            ]);
            if ($request->has('medication_name')) {
                $medicationNames = $request->input('medication_name');
                $dosages = $request->input('dosage');
                $frequencies = $request->input('frequency');
                $administrationInstructions = $request->input('administration_instructions');
                foreach ($medicationNames as $index => $medicationName) {
                    if (!empty($medicationName)) {
                        Medication::create([
                            'general_care_plan_id' => $generalCarePlanId,
                            'medication' => $medicationName,
                            'dosage' => $dosages[$index] ?? '',
                            'frequency' => $frequencies[$index] ?? '',
                            'administration_instructions' => $administrationInstructions[$index] ?? '',
                        ]);
                    }
                }
            }
            if ($request->has('care_worker.tasks')) {
                $tasks = $request->input('care_worker.tasks');
                $careWorkerId = $request->input('care_worker.careworker_id');
                foreach ($tasks as $task) {
                    if (!empty($task)) {
                        CareWorkerResponsibility::create([
                            'general_care_plan_id' => $generalCarePlanId,
                            'care_worker_id' => $careWorkerId,
                            'task_description' => $task,
                        ]);
                    }
                }
            }
            $careCategories = [
                1 => ['frequency' => $request->input('frequency.mobility'), 'assistance' => $request->input('assistance.mobility')],
                2 => ['frequency' => $request->input('frequency.cognitive'), 'assistance' => $request->input('assistance.cognitive')],
                3 => ['frequency' => $request->input('frequency.self_sustainability'), 'assistance' => $request->input('assistance.self_sustainability')],
                4 => ['frequency' => $request->input('frequency.disease'), 'assistance' => $request->input('assistance.disease')],
                5 => ['frequency' => $request->input('frequency.daily_life'), 'assistance' => $request->input('assistance.daily_life')],
                6 => ['frequency' => $request->input('frequency.outdoor'), 'assistance' => $request->input('assistance.outdoor')],
                7 => ['frequency' => $request->input('frequency.household'), 'assistance' => $request->input('assistance.household')]
            ];
            foreach ($careCategories as $categoryId => $data) {
                if (!empty($data['frequency']) || !empty($data['assistance'])) {
                    CareNeed::create([
                        'general_care_plan_id' => $generalCarePlanId,
                        'care_category_id' => $categoryId,
                        'frequency' => $data['frequency'],
                        'assistance_required' => $data['assistance']
                    ]);
                }
            }

            // (Optional) Log creation and send notifications as in web controller

            DB::commit();

            return response()->json([
                'success' => true,
                'beneficiary' => $beneficiary->load([
                    'category', 'status', 'municipality', 'generalCarePlan',
                    'generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction',
                    'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications',
                    'generalCarePlan.healthHistory', 'generalCarePlan.careNeeds',
                    'generalCarePlan.careWorkerResponsibility'
                ])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified beneficiary.
     */
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::with([
            'generalCarePlan',
            'generalCarePlan.mobility',
            'generalCarePlan.cognitiveFunction',
            'generalCarePlan.emotionalWellbeing',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careNeeds',
            'generalCarePlan.careWorkerResponsibility'
        ])->findOrFail($id);

        // Care worker can only update assigned beneficiaries
        if ($request->user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == $request->user()->id;
            if (!$isAssigned) {
                return response()->json(['error' => 'Unauthorized. You can only update beneficiaries assigned to you.'], 403);
            }
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => [
                'sometimes', 'required', 'string', 'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/',
            ],
            'last_name' => [
                'sometimes', 'required', 'string', 'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/',
            ],
            // ... (add all other rules from web controller)
            'account.email' => [
                'sometimes', 'required', 'email',
                Rule::unique('portal_accounts', 'portal_email')->ignore($beneficiary->portal_account_id, 'portal_account_id'),
            ],
            // File uploads
            'beneficiaryProfilePic' => 'nullable|file|mimes:jpeg,png|max:2048',
            'care_service_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'beneficiary_signature_canvas' => 'nullable|string',
            'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'care_worker_signature_canvas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $uniqueIdentifier = Str::random(10);

            // Handle file uploads (same logic as store)
            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiary->photo = $request->file('beneficiaryProfilePic')->storeAs(
                    'uploads/beneficiary_photos',
                    $beneficiary->first_name . '_' . $beneficiary->last_name . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension(),
                    'public'
                );
            }

            if ($request->hasFile('care_service_agreement')) {
                $beneficiary->care_service_agreement_doc = $request->file('care_service_agreement')->storeAs(
                    'uploads/care_service_agreements',
                    $beneficiary->first_name . '_' . $beneficiary->last_name . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension(),
                    'public'
                );
            }

            if ($request->hasFile('general_careplan')) {
                $beneficiary->general_care_plan_doc = $request->file('general_careplan')->storeAs(
                    'uploads/general_care_plans',
                    $beneficiary->first_name . '_' . $beneficiary->last_name . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension(),
                    'public'
                );
            }

            // Handle signatures (file or base64)
            if ($request->hasFile('beneficiary_signature_upload')) {
                $beneficiary->beneficiary_signature = 'uploads/beneficiary_signatures/' . 
                    $beneficiary->first_name . '_' . 
                    $beneficiary->last_name . '_signature_' . 
                    $uniqueIdentifier . '.' . 
                    $request->file('beneficiary_signature_upload')->getClientOriginalExtension();
                $request->file('beneficiary_signature_upload')->storeAs(
                    'public/' . dirname($beneficiary->beneficiary_signature),
                    basename($beneficiary->beneficiary_signature)
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiary->beneficiary_signature = 'uploads/beneficiary_signatures/' . 
                    $beneficiary->first_name . '_' . 
                    $beneficiary->last_name . '_signature_' . 
                    $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('beneficiary_signature_canvas')));
                file_put_contents(storage_path('app/public/' . $beneficiary->beneficiary_signature), $decodedImage);
            }

            if ($request->hasFile('care_worker_signature_upload')) {
                $beneficiary->care_worker_signature = 'uploads/care_worker_signatures/' . 
                    $beneficiary->first_name . '_' . 
                    $beneficiary->last_name . '_care_worker_signature_' . 
                    $uniqueIdentifier . '.' . 
                    $request->file('care_worker_signature_upload')->getClientOriginalExtension();
                $request->file('care_worker_signature_upload')->storeAs(
                    'public/' . dirname($beneficiary->care_worker_signature),
                    basename($beneficiary->care_worker_signature)
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $beneficiary->care_worker_signature = 'uploads/care_worker_signatures/' . 
                    $beneficiary->first_name . '_' . 
                    $beneficiary->last_name . '_care_worker_signature_' . 
                    $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('care_worker_signature_canvas')));
                file_put_contents(storage_path('app/public/' . $beneficiary->care_worker_signature), $decodedImage);
            }

            // Update beneficiary fields
            $beneficiary->fill($request->only([
                'first_name',
                'last_name',
                'birthday',
                'gender',
                'civil_status',
                'street_address',
                'barangay_id',
                'municipality_id',
                'category_id',
                'mobile',
                'landline',
                'emergency_contact_name',
                'emergency_contact_relation',
                'emergency_contact_mobile',
                'emergency_contact_email',
                'emergency_procedure',
                'primary_caregiver',
                // ...add other fields as needed
            ]));
            $beneficiary->updated_by = $request->user()->id;
            $beneficiary->save();

            // Update related models as needed (similar to store)
            // Example: update general care plan, medications, care needs, etc.

            DB::commit();

            return response()->json([
                'success' => true,
                'beneficiary' => $beneficiary->fresh([
                    'category', 'status', 'municipality', 'generalCarePlan',
                    'generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction',
                    'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications',
                    'generalCarePlan.healthHistory', 'generalCarePlan.careNeeds',
                    'generalCarePlan.careWorkerResponsibility'
                ])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change beneficiary status (admin only)
     */
    public function changeStatus(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'status' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary->status = $request->status;
        $beneficiary->save();

        return response()->json([
            'success' => true,
            'data' => $beneficiary
        ]);
    }

    /**
     * Remove the specified beneficiary.
     */
    public function destroy(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::with('generalCarePlan')->findOrFail($id);

        if ($request->user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == $request->user()->id;
            if (!$isAssigned) {
                return response()->json(['error' => 'Unauthorized. You can only delete beneficiaries assigned to you.'], 403);
            }
        }

        $beneficiary->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Restore a soft-deleted beneficiary.
     */
    public function restore(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::withTrashed()->with('generalCarePlan')->findOrFail($id);

        // Care worker can only restore assigned beneficiaries
        if ($request->user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == $request->user()->id;
            if (!$isAssigned) {
                return response()->json(['error' => 'Unauthorized. You can only restore beneficiaries assigned to you.'], 403);
            }
        }

        if (!$beneficiary->trashed()) {
            return response()->json(['error' => 'Beneficiary is not deleted.'], 400);
        }

        $beneficiary->restore();

        return response()->json([
            'success' => true,
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Export beneficiaries as CSV (with relationships).
     */
    public function export(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $fileName = 'beneficiaries_' . now()->format('Ymd_His') . '.csv';

        $beneficiaries = Beneficiary::with([
            'category', 'status', 'municipality', 'barangay',
            'generalCarePlan', 'generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction',
            'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications',
            'generalCarePlan.healthHistory', 'generalCarePlan.careNeeds',
            'generalCarePlan.careWorkerResponsibility',
            'portalAccount', 'familyMembers'
        ])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'ID', 'First Name', 'Last Name', 'Birthday', 'Gender', 'Civil Status', 'Category', 'Status',
            'Municipality', 'Barangay', 'Mobile', 'Landline', 'Emergency Contact Name', 'Emergency Contact Relation',
            'Emergency Contact Mobile', 'Emergency Contact Email', 'Primary Caregiver', 'Care Worker',
            'Review Date', 'Medications', 'Allergies', 'Immunizations', 'Care Needs', 'Family Members'
            // Add more columns as needed
        ];

        $callback = function() use ($beneficiaries, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($beneficiaries as $b) {
                // Gather care needs as a string
                $careNeeds = $b->generalCarePlan && $b->generalCarePlan->careNeeds
                    ? $b->generalCarePlan->careNeeds->map(function($need) {
                        return "Cat{$need->care_category_id}:{$need->frequency}/{$need->assistance_required}";
                    })->implode('; ')
                    : '';

                // Gather family members as a string
                $familyMembers = $b->familyMembers
                    ? $b->familyMembers->map(function($fm) {
                        return $fm->first_name . ' ' . $fm->last_name;
                    })->implode('; ')
                    : '';

                // Gather medications as a string
                $medications = $b->generalCarePlan && $b->generalCarePlan->medications
                    ? $b->generalCarePlan->medications->map(function($m) {
                        return "{$m->medication} ({$m->dosage}, {$m->frequency})";
                    })->implode('; ')
                    : '';

                fputcsv($file, [
                    $b->beneficiary_id,
                    $b->first_name,
                    $b->last_name,
                    $b->birthday,
                    $b->gender,
                    $b->civil_status,
                    optional($b->category)->category_name,
                    optional($b->status)->status_name,
                    optional($b->municipality)->municipality_name,
                    optional($b->barangay)->barangay_name,
                    $b->mobile,
                    $b->landline,
                    $b->emergency_contact_name,
                    $b->emergency_contact_relation,
                    $b->emergency_contact_mobile,
                    $b->emergency_contact_email,
                    $b->primary_caregiver,
                    optional($b->generalCarePlan)->care_worker_id,
                    optional($b->generalCarePlan)->review_date,
                    $medications,
                    optional($b->generalCarePlan->healthHistory)->allergies ?? '',
                    optional($b->generalCarePlan->healthHistory)->immunizations ?? '',
                    $careNeeds,
                    $familyMembers,
                    // Add more fields as needed
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
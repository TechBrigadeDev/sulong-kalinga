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
use App\Services\UploadService;
use App\Services\UserManagementService;

class BeneficiaryApiController extends Controller
{
    protected $uploadService;
    protected $userManagementService;

    public function __construct(UploadService $uploadService, UserManagementService $userManagementService)
    {
        $this->uploadService = $uploadService;
        $this->userManagementService = $userManagementService;
    }

    /**
     * Generate a unique username based on beneficiary's name
     */
    private function generateUniqueUsername($firstName, $middleName, $lastName, $currentId = null)
    {
        $firstInitial = mb_substr(trim($firstName), 0, 1, 'UTF-8');
        $middleInitial = !empty($middleName) ? mb_substr(trim($middleName), 0, 1, 'UTF-8') : '';
        $cleanLastName = trim($lastName);
        $baseUsername = strtolower($firstInitial . $middleInitial . $cleanLastName);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        if (strlen($baseUsername) < 3) {
            $baseUsername = str_pad($baseUsername, 3, '0');
        }
        $username = $baseUsername;
        $counter = 1;
        while (true) {
            $query = Beneficiary::where('username', $username);
            if ($currentId) {
                $query->where('beneficiary_id', '!=', $currentId);
            }
            if (!$query->exists()) {
                break;
            }
            $username = $baseUsername . $counter;
            $counter++;
        }
        return $username;
    }

    /**
     * Display a listing of beneficiaries.
     */
    public function index(Request $request)
    {
        $query = Beneficiary::with(['category', 'status', 'municipality']);

        if ($request->has('search') && $request->get('search') !== null) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(middle_name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        if ($request->has('municipality_id') && $request->get('municipality_id') !== null) {
            $query->where('assigned_municipality_id', $request->get('municipality_id'));
        }

        if ($request->has('status') && $request->get('status') !== null) {
            $query->where('status', $request->get('status'));
        }

        $perPage = $request->get('per_page', 20);
        $beneficiaries = $query->orderBy('first_name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'beneficiaries' => collect($beneficiaries->items())->map(function($b) {
                return array_merge(
                    $b->toArray(),
                    [
                        'photo_url' => $b->photo
                            ? $this->uploadService->getTemporaryPrivateUrl($b->photo, 30)
                            : null,
                        'care_service_agreement_doc_url' => $b->care_service_agreement_doc
                            ? $this->uploadService->getTemporaryPrivateUrl($b->care_service_agreement_doc, 30)
                            : null,
                        'general_care_plan_doc_url' => $b->general_care_plan_doc
                            ? $this->uploadService->getTemporaryPrivateUrl($b->general_care_plan_doc, 30)
                            : null,
                        'beneficiary_signature_url' => $b->beneficiary_signature
                            ? $this->uploadService->getTemporaryPrivateUrl($b->beneficiary_signature, 30)
                            : null,
                        'care_worker_signature_url' => $b->care_worker_signature
                            ? $this->uploadService->getTemporaryPrivateUrl($b->care_worker_signature, 30)
                            : null,
                    ]
                );
            }),
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
            'beneficiary' => array_merge(
                $beneficiary->toArray(),
                [
                    'photo_url' => $beneficiary->photo
                        ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->photo, 30)
                        : null,
                    'care_service_agreement_doc_url' => $beneficiary->care_service_agreement_doc
                        ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->care_service_agreement_doc, 30)
                        : null,
                    'general_care_plan_doc_url' => $beneficiary->general_care_plan_doc
                        ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->general_care_plan_doc, 30)
                        : null,
                    'beneficiary_signature_url' => $beneficiary->beneficiary_signature
                        ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->beneficiary_signature, 30)
                        : null,
                    'care_worker_signature_url' => $beneficiary->care_worker_signature
                        ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->care_worker_signature, 30)
                        : null,
                ]
            )
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

        $validator = \Validator::make($request->all(), [
            'first_name' => [
                'required', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'middle_name' => [
                'required', // now required!
                'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'last_name' => [
                'required', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'civil_status' => [
                'required',
                'string',
                'in:Single,Married,Widowed,Divorced',
            ],
            'gender' => [
                'required',
                'string',
                'in:Male,Female,Other',
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(14)->toDateString(),
            ],
            'primary_caregiver' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
            'address_details' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'municipality' => [
                'required',
                'exists:municipalities,municipality_id',
            ],
            'barangay' => [
                'required',
                'exists:barangays,barangay_id',
            ],
            'medical_conditions' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500',
            ],
            'medications' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500',
            ],
            'allergies' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500',
            ],
            'immunizations' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500',
            ],
            'category' => 'required|exists:beneficiary_categories,category_id',
            'frequency.mobility' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.mobility' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'assistance.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'medication_name' => 'nullable|array',
            'medication_name.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'dosage' => 'nullable|array',
            'dosage.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'frequency' => 'nullable|array',
            'frequency.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'administration_instructions' => 'nullable|array',
            'administration_instructions.*' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'mobility.walking_ability' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'mobility.assistive_devices' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'mobility.transportation_needs' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.memory' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.thinking_skills' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.orientation' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.behavior' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emotional.mood' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emotional.social_interactions' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emotional.emotional_support' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emergency_contact.name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'emergency_contact.relation' => 'required|string|in:Parent,Sibling,Spouse,Child,Relative,Friend',
            'emergency_contact.mobile' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
            ],
            'emergency_contact.email' => 'nullable|email|max:100',
            'emergency_plan.procedures' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'care_worker.careworker_id' => 'required|exists:cose_users,id',
            'care_worker.tasks' => 'required|array|min:1',
            'care_worker.tasks.*' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),
            'account.password' => 'required|string|min:8|confirmed',
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
            $firstName = $request->input('first_name');
            $middleName = $request->input('middle_name');
            $lastName = $request->input('last_name');

            // --- DEFER FILE UPLOADS UNTIL AFTER DB COMMIT ---
            $pendingUploads = [];

            // Prepare file info for upload after commit
            if ($request->hasFile('beneficiaryProfilePic')) {
                $pendingUploads['beneficiaryPhoto'] = [
                    'file' => $request->file('beneficiaryProfilePic'),
                    'path' => 'uploads/beneficiary_photos',
                    'filename' => $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('care_service_agreement')) {
                $pendingUploads['careServiceAgreement'] = [
                    'file' => $request->file('care_service_agreement'),
                    'path' => 'uploads/care_service_agreements',
                    'filename' => $firstName . '_' . $lastName . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('general_careplan')) {
                $pendingUploads['generalCarePlan'] = [
                    'file' => $request->file('general_careplan'),
                    'path' => 'uploads/general_care_plans',
                    'filename' => $firstName . '_' . $lastName . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('beneficiary_signature_upload')) {
                $pendingUploads['beneficiarySignature'] = [
                    'file' => $request->file('beneficiary_signature_upload'),
                    'path' => 'uploads/beneficiary_signatures',
                    'filename' => $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                ];
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $pendingUploads['beneficiarySignatureCanvas'] = [
                    'data' => $request->input('beneficiary_signature_canvas'),
                    'path' => 'uploads/beneficiary_signatures/' . $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.png'
                ];
            }

            if ($request->hasFile('care_worker_signature_upload')) {
                $pendingUploads['careWorkerSignature'] = [
                    'file' => $request->file('care_worker_signature_upload'),
                    'path' => 'uploads/care_worker_signatures',
                    'filename' => $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                ];
            } elseif ($request->input('care_worker_signature_canvas')) {
                $pendingUploads['careWorkerSignatureCanvas'] = [
                    'data' => $request->input('care_worker_signature_canvas'),
                    'path' => 'uploads/care_worker_signatures/' . $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.png'
                ];
            }

            // --- Reset the sequence before creating GeneralCarePlan ---
            $maxId = \DB::table('general_care_plans')->max('general_care_plan_id');
            if ($maxId) {
                \DB::statement("SELECT setval('general_care_plans_general_care_plan_id_seq', $maxId, true)");
            }

            // --- Create GeneralCarePlan FIRST and get its ID ---
            $generalCarePlan = GeneralCarePlan::create([
                'care_worker_id' => $request->input('care_worker.careworker_id'),
                'emergency_plan' => $request->input('emergency_plan.procedures'),
                'review_date' => $request->input('date'),
                'created_at' => now(),
            ]);
            $generalCarePlanId = $generalCarePlan->general_care_plan_id ?? null;

            if (!$generalCarePlanId) {
                throw new \Exception('General Care Plan ID is null.');
            }

            // Generate unique username in controller
            $username = $this->generateUniqueUsername($firstName, $middleName, $lastName);

            // Hash password before saving
            $hashedPassword = Hash::make($request->input('account.password'));

            // Placeholder paths for files to be filled after upload
            $beneficiaryPhotoPath = null;
            $careServiceAgreementPath = null;
            $generalCarePlanPath = null;
            $beneficiarySignaturePath = null;
            $careWorkerSignaturePath = null;

            // Set file paths (to be uploaded after commit)
            if (isset($pendingUploads['beneficiaryPhoto'])) {
                $beneficiaryPhotoPath = $pendingUploads['beneficiaryPhoto']['path'] . '/' . $pendingUploads['beneficiaryPhoto']['filename'];
            }
            if (isset($pendingUploads['careServiceAgreement'])) {
                $careServiceAgreementPath = $pendingUploads['careServiceAgreement']['path'] . '/' . $pendingUploads['careServiceAgreement']['filename'];
            }
            if (isset($pendingUploads['generalCarePlan'])) {
                $generalCarePlanPath = $pendingUploads['generalCarePlan']['path'] . '/' . $pendingUploads['generalCarePlan']['filename'];
            }
            if (isset($pendingUploads['beneficiarySignature'])) {
                $beneficiarySignaturePath = $pendingUploads['beneficiarySignature']['path'] . '/' . $pendingUploads['beneficiarySignature']['filename'];
            } elseif (isset($pendingUploads['beneficiarySignatureCanvas'])) {
                $beneficiarySignaturePath = $pendingUploads['beneficiarySignatureCanvas']['path'];
            }
            if (isset($pendingUploads['careWorkerSignature'])) {
                $careWorkerSignaturePath = $pendingUploads['careWorkerSignature']['path'] . '/' . $pendingUploads['careWorkerSignature']['filename'];
            } elseif (isset($pendingUploads['careWorkerSignatureCanvas'])) {
                $careWorkerSignaturePath = $pendingUploads['careWorkerSignatureCanvas']['path'];
            }

            $beneficiary = Beneficiary::create([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'birthday' => $request->input('birth_date'),
                'gender' => $request->input('gender'),
                'civil_status' => $request->input('civil_status'),
                'street_address' => $request->input('address_details'),
                'barangay_id' => $request->input('barangay'),
                'municipality_id' => $request->input('municipality'),
                'category_id' => $request->input('category'),
                'mobile' => $request->input('mobile_number'),
                'landline' => $request->input('landline_number'),
                'emergency_contact_name' => $request->input('emergency_contact.name'),
                'emergency_contact_relation' => $request->input('emergency_contact.relation'),
                'emergency_contact_mobile' => $request->input('emergency_contact.mobile'),
                'emergency_contact_email' => $request->input('emergency_contact.email'),
                'emergency_procedure' => $request->input('emergency_plan.procedures'),
                'primary_caregiver' => $request->input('primary_caregiver') ?? null,
                'care_service_agreement_doc' => $careServiceAgreementPath,
                'general_care_plan_doc' => $generalCarePlanPath,
                'photo' => $beneficiaryPhotoPath,
                'beneficiary_signature' => $beneficiarySignaturePath,
                'care_worker_signature' => $careWorkerSignaturePath,
                'general_care_plan_id' => $generalCarePlanId,
                'beneficiary_status_id' => 1,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => Str::random(60),
                'username' => $username,
                'password' => $hashedPassword,
            ]);

            // --- DO NOT update GeneralCarePlan with beneficiary_id (web controller does not do this) ---

            // Create related models
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
            // Format health history as JSON arrays (like web)
            $medicalConditions = $request->input('medical_conditions');
            $medications = $request->input('medications');
            $allergies = $request->input('allergies');
            $immunizations = $request->input('immunizations');
            $formattedMedicalConditions = !empty($medicalConditions) ? json_encode(array_map('trim', explode(',', $medicalConditions))) : null;
            $formattedMedications = !empty($medications) ? json_encode(array_map('trim', explode(',', $medications))) : null;
            $formattedAllergies = !empty($allergies) ? json_encode(array_map('trim', explode(',', $allergies))) : null;
            $formattedImmunizations = !empty($immunizations) ? json_encode(array_map('trim', explode(',', $immunizations))) : null;
            HealthHistory::create([
                'general_care_plan_id' => $generalCarePlanId,
                'medical_conditions' => $formattedMedicalConditions,
                'medications' => $formattedMedications,
                'allergies' => $formattedAllergies,
                'immunizations' => $formattedImmunizations,
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

            DB::commit();

            // --- PERFORM FILE UPLOADS ONLY AFTER SUCCESSFUL COMMIT ---
            foreach ($pendingUploads as $key => $upload) {
                if (isset($upload['file'])) {
                    $this->uploadService->upload(
                        $upload['file'],
                        'spaces-private',
                        $upload['path'],
                        ['filename' => $upload['filename']]
                    );
                } elseif (isset($upload['data'])) {
                    $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $upload['data']));
                    $this->uploadService->storeRawImage($decodedImage, $upload['path'], 'spaces-private');
                }
            }

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
        \Log::info('Update request data:', $request->all());
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::findOrFail($id);

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
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'middle_name' => [
                'sometimes', 'required', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'last_name' => [
                'sometimes', 'required', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'civil_status' => [
                'sometimes', 'required', 'string', 
                'in:Single,Married,Widowed,Divorced'
            ],
            'gender' => [
                'sometimes', 'required', 'string',
                'in:Male,Female,Other'
            ],
            'birth_date' => [
                'sometimes', 'required', 'date',
                'before_or_equal:' . now()->subYears(14)->toDateString()
            ],
            'primary_caregiver' => [
                'nullable', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'mobile_number' => [
                'sometimes', 'required', 'string',
                'regex:/^[0-9]{10,11}$/'
            ],
            'landline_number' => [
                'nullable', 'string',
                'regex:/^[0-9]{7,10}$/'
            ],
            'address_details' => [
                'sometimes', 'required', 'string', 'max:255',
                'regex:/^[a-zA-Z0-9\s,.-]+$/'
            ],
            'municipality' => [
                'sometimes', 'required',
                'exists:municipalities,municipality_id'
            ],
            'barangay' => [
                'sometimes', 'required',
                'exists:barangays,barangay_id'
            ],
            
            // Medical History
            'medical_conditions' => [
                'nullable', 'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500'
            ],
            'medications' => [
                'nullable', 'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500'
            ],
            'allergies' => [
                'nullable', 'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500'
            ],
            'immunizations' => [
                'nullable', 'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
                'max:500'
            ],
            'category' => 'sometimes|required|exists:beneficiary_categories,category_id',
            
            // Care Needs: Mobility
            'frequency.mobility' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.mobility' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Cognitive / Communication
            'frequency.cognitive' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.cognitive' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Self-sustainability
            'frequency.self_sustainability' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.self_sustainability' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Disease / Therapy Handling
            'frequency.disease' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.disease' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Daily Life / Social Contact
            'frequency.daily_life' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.daily_life' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Outdoor Activities
            'frequency.outdoor' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.outdoor' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Needs: Household Keeping
            'frequency.household' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'assistance.household' => [
                'nullable', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Medications Management
            'medication_name' => 'nullable|array',
            'medication_name.*' => [
                'nullable', 'string', 'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'dosage' => 'nullable|array',
            'dosage.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'frequency' => 'nullable|array',
            'frequency.*' => [
                'nullable', 'string', 'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            'administration_instructions' => 'nullable|array',
            'administration_instructions.*' => [
                'nullable', 'string', 'max:500',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Mobility
            'mobility.walking_ability' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'mobility.assistive_devices' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'mobility.transportation_needs' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            
            // Cognitive Function
            'cognitive.memory' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.thinking_skills' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.orientation' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'cognitive.behavior' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            
            // Emotional Well-being
            'emotional.mood' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emotional.social_interactions' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'emotional.emotional_support' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            
            // Emergency Contact
            'emergency_contact.name' => [
                'sometimes', 'required', 'string', 'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'emergency_contact.relation' => 'sometimes|required|string|in:Parent,Sibling,Spouse,Child,Relative,Friend',
            'emergency_contact.mobile' => [
                'sometimes', 'required', 'string',
                'regex:/^[0-9]{10,11}$/'
            ],
            'emergency_contact.email' => 'nullable|email|max:100',
            
            // Emergency Plan
            'emergency_plan.procedures' => [
                'sometimes', 'required', 'string', 'max:1000',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Care Worker
            'care_worker.careworker_id' => 'sometimes|required|exists:cose_users,id',
            'care_worker.tasks' => 'sometimes|required|array|min:1',
            'care_worker.tasks.*' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
            ],
            
            // Review Date
            'date' => 'sometimes|required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),
            
            
            'account.password' => 'nullable|string|min:8|confirmed',
            
            // File uploads - Modified for update scenario (all optional)
            'beneficiaryProfilePic' => 'nullable|file|mimes:jpeg,png|max:7168', // 7MB
            'care_service_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
            
            // Signatures - Modified for update scenario (all optional)
            'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // 2MB
            'beneficiary_signature_canvas' => 'nullable|string',
            'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // 2MB
            'care_worker_signature_canvas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $uniqueIdentifier = Str::random(10);
            $firstName = $request->input('first_name', $beneficiary->first_name);
            $middleName = $request->input('middle_name', $beneficiary->middle_name);
            $lastName = $request->input('last_name', $beneficiary->last_name);

            // --- DEFER FILE UPLOADS UNTIL AFTER DB COMMIT ---
            $pendingUploads = [];
            $deleteOldFiles = [];

            // Prepare file info for upload after commit
            if ($request->hasFile('beneficiaryProfilePic')) {
                if ($beneficiary->photo) {
                    $deleteOldFiles[] = $beneficiary->photo;
                }
                $pendingUploads['beneficiaryPhoto'] = [
                    'file' => $request->file('beneficiaryProfilePic'),
                    'path' => 'uploads/beneficiary_photos',
                    'filename' => $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('care_service_agreement')) {
                if ($beneficiary->care_service_agreement_doc) {
                    $deleteOldFiles[] = $beneficiary->care_service_agreement_doc;
                }
                $pendingUploads['careServiceAgreement'] = [
                    'file' => $request->file('care_service_agreement'),
                    'path' => 'uploads/care_service_agreements',
                    'filename' => $firstName . '_' . $lastName . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('general_careplan')) {
                if ($beneficiary->general_care_plan_doc) {
                    $deleteOldFiles[] = $beneficiary->general_care_plan_doc;
                }
                $pendingUploads['generalCarePlan'] = [
                    'file' => $request->file('general_careplan'),
                    'path' => 'uploads/general_care_plans',
                    'filename' => $firstName . '_' . $lastName . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                ];
            }

            if ($request->hasFile('beneficiary_signature_upload')) {
                if ($beneficiary->beneficiary_signature) {
                    $deleteOldFiles[] = $beneficiary->beneficiary_signature;
                }
                $pendingUploads['beneficiarySignature'] = [
                    'file' => $request->file('beneficiary_signature_upload'),
                    'path' => 'uploads/beneficiary_signatures',
                    'filename' => $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                ];
            } elseif ($request->input('beneficiary_signature_canvas')) {
                if ($beneficiary->beneficiary_signature) {
                    $deleteOldFiles[] = $beneficiary->beneficiary_signature;
                }
                $pendingUploads['beneficiarySignatureCanvas'] = [
                    'data' => $request->input('beneficiary_signature_canvas'),
                    'path' => 'uploads/beneficiary_signatures/' . $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.png'
                ];
            }

            if ($request->hasFile('care_worker_signature_upload')) {
                if ($beneficiary->care_worker_signature) {
                    $deleteOldFiles[] = $beneficiary->care_worker_signature;
                }
                $pendingUploads['careWorkerSignature'] = [
                    'file' => $request->file('care_worker_signature_upload'),
                    'path' => 'uploads/care_worker_signatures',
                    'filename' => $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                ];
            } elseif ($request->input('care_worker_signature_canvas')) {
                if ($beneficiary->care_worker_signature) {
                    $deleteOldFiles[] = $beneficiary->care_worker_signature;
                }
                $pendingUploads['careWorkerSignatureCanvas'] = [
                    'data' => $request->input('care_worker_signature_canvas'),
                    'path' => 'uploads/care_worker_signatures/' . $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.png'
                ];
            }

            // Set file paths (to be saved in DB after commit)
            $beneficiaryPhotoPath = isset($pendingUploads['beneficiaryPhoto'])
                ? $pendingUploads['beneficiaryPhoto']['path'] . '/' . $pendingUploads['beneficiaryPhoto']['filename']
                : $beneficiary->photo;
            $careServiceAgreementPath = isset($pendingUploads['careServiceAgreement'])
                ? $pendingUploads['careServiceAgreement']['path'] . '/' . $pendingUploads['careServiceAgreement']['filename']
                : $beneficiary->care_service_agreement_doc;
            $generalCarePlanPath = isset($pendingUploads['generalCarePlan'])
                ? $pendingUploads['generalCarePlan']['path'] . '/' . $pendingUploads['generalCarePlan']['filename']
                : $beneficiary->general_care_plan_doc;
            $beneficiarySignaturePath = isset($pendingUploads['beneficiarySignature'])
                ? $pendingUploads['beneficiarySignature']['path'] . '/' . $pendingUploads['beneficiarySignature']['filename']
                : (isset($pendingUploads['beneficiarySignatureCanvas'])
                    ? $pendingUploads['beneficiarySignatureCanvas']['path']
                    : $beneficiary->beneficiary_signature);
            $careWorkerSignaturePath = isset($pendingUploads['careWorkerSignature'])
                ? $pendingUploads['careWorkerSignature']['path'] . '/' . $pendingUploads['careWorkerSignature']['filename']
                : (isset($pendingUploads['careWorkerSignatureCanvas'])
                    ? $pendingUploads['careWorkerSignatureCanvas']['path']
                    : $beneficiary->care_worker_signature);

            // Check if name changed and update username if needed
            $nameChanged = ($beneficiary->first_name !== $firstName ||
                            $beneficiary->middle_name !== $middleName ||
                            $beneficiary->last_name !== $lastName);
            if ($nameChanged) {
                $beneficiary->username = $this->generateUniqueUsername(
                    $firstName,
                    $middleName,
                    $lastName,
                    $id
                );
            }

            // Update password if provided (hash it in controller)
            if ($request->filled('account.password')) {
                $beneficiary->password = Hash::make($request->input('account.password'));
            }

            // Update beneficiary fields
            $beneficiary->fill([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'birthday' => $request->input('birth_date', $beneficiary->birthday),
                'gender' => $request->input('gender', $beneficiary->gender),
                'civil_status' => $request->input('civil_status', $beneficiary->civil_status),
                'street_address' => $request->input('address_details', $beneficiary->street_address),
                'barangay_id' => $request->input('barangay', $beneficiary->barangay_id),
                'municipality_id' => $request->input('municipality', $beneficiary->municipality_id),
                'category_id' => $request->input('category', $beneficiary->category_id),
                'mobile' => $request->input('mobile_number', $beneficiary->mobile),
                'landline' => $request->input('landline_number', $beneficiary->landline),
                'emergency_contact_name' => $request->input('emergency_contact.name', $beneficiary->emergency_contact_name),
                'emergency_contact_relation' => $request->input('emergency_contact.relation', $beneficiary->emergency_contact_relation),
                'emergency_contact_mobile' => $request->input('emergency_contact.mobile', $beneficiary->emergency_contact_mobile),
                'emergency_contact_email' => $request->input('emergency_contact.email', $beneficiary->emergency_contact_email),
                'emergency_procedure' => $request->input('emergency_plan.procedures', $beneficiary->emergency_procedure),
                'primary_caregiver' => $request->input('primary_caregiver', $beneficiary->primary_caregiver),
                'care_service_agreement_doc' => $careServiceAgreementPath,
                'general_care_plan_doc' => $generalCarePlanPath,
                'photo' => $beneficiaryPhotoPath,
                'beneficiary_signature' => $beneficiarySignaturePath,
                'care_worker_signature' => $careWorkerSignaturePath,
            ]);
            $beneficiary->updated_by = $request->user()->id;
            $beneficiary->save();

            // Update related models as needed (similar to store)
            $generalCarePlanId = $beneficiary->general_care_plan_id;

            if ($generalCarePlanId) {
                // Update the general care plan details
                DB::table('general_care_plans')
                    ->where('general_care_plan_id', $generalCarePlanId)
                    ->update([
                        'review_date' => $request->input('date', DB::raw('review_date')),
                        'emergency_plan' => $request->input('emergency_plan.procedures', DB::raw('emergency_plan')),
                        'care_worker_id' => $request->input('care_worker.careworker_id', DB::raw('care_worker_id')),
                    ]);

                // Update emotional wellbeing
                EmotionalWellbeing::updateOrCreate(
                    ['general_care_plan_id' => $generalCarePlanId],
                    [
                        'mood' => $request->input('emotional.mood'),
                        'social_interactions' => $request->input('emotional.social_interactions'),
                        'emotional_support_needs' => $request->input('emotional.emotional_support'),
                    ]
                );

                // Update cognitive function
                CognitiveFunction::updateOrCreate(
                    ['general_care_plan_id' => $generalCarePlanId],
                    [
                        'memory' => $request->input('cognitive.memory'),
                        'thinking_skills' => $request->input('cognitive.thinking_skills'),
                        'orientation' => $request->input('cognitive.orientation'),
                        'behavior' => $request->input('cognitive.behavior'),
                    ]
                );

                // Update mobility
                Mobility::updateOrCreate(
                    ['general_care_plan_id' => $generalCarePlanId],
                    [
                        'walking_ability' => $request->input('mobility.walking_ability'),
                        'assistive_devices' => $request->input('mobility.assistive_devices'),
                        'transportation_needs' => $request->input('mobility.transportation_needs'),
                    ]
                );

                // Process health history fields
                $medicalConditions = $request->input('medical_conditions');
                $medications = $request->input('medications');
                $allergies = $request->input('allergies');
                $immunizations = $request->input('immunizations');

                $formattedMedicalConditions = !empty($medicalConditions) ? json_encode(array_map('trim', explode(',', $medicalConditions))) : null;
                $formattedMedications = !empty($medications) ? json_encode(array_map('trim', explode(',', $medications))) : null;
                $formattedAllergies = !empty($allergies) ? json_encode(array_map('trim', explode(',', $allergies))) : null;
                $formattedImmunizations = !empty($immunizations) ? json_encode(array_map('trim', explode(',', $immunizations))) : null;

                HealthHistory::updateOrCreate(
                    ['general_care_plan_id' => $generalCarePlanId],
                    [
                        'medical_conditions' => $formattedMedicalConditions,
                        'medications' => $formattedMedications,
                        'allergies' => $formattedAllergies,
                        'immunizations' => $formattedImmunizations,
                    ]
                );

                // Update medications - first delete existing ones
                Medication::where('general_care_plan_id', $generalCarePlanId)->delete();
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

                // Update care worker responsibilities - first delete existing ones
                CareWorkerResponsibility::where('general_care_plan_id', $generalCarePlanId)->delete();
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

                // Update care needs - first delete existing ones
                CareNeed::where('general_care_plan_id', $generalCarePlanId)->delete();
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
                    $hasFrequency = !empty($data['frequency']);
                    $hasAssistance = !empty($data['assistance']);
                    if ($hasFrequency || $hasAssistance) {
                        if (!$hasFrequency || !$hasAssistance) {
                            DB::rollBack();
                            return response()->json([
                                'errors' => [
                                    "care_needs_category_$categoryId" => [
                                        'Both frequency and assistance are required for this care category if one is provided.'
                                    ]
                                ]
                            ], 422);
                        }
                        CareNeed::create([
                            'general_care_plan_id' => $generalCarePlanId,
                            'care_category_id' => $categoryId,
                            'frequency' => $data['frequency'],
                            'assistance_required' => $data['assistance']
                        ]);
                    }
                }
            }

            DB::commit();

            // --- PERFORM FILE UPLOADS ONLY AFTER SUCCESSFUL COMMIT ---
            foreach ($deleteOldFiles as $file) {
                if ($file) {
                    $this->uploadService->delete($file, 'spaces-private');
                }
            }
            foreach ($pendingUploads as $key => $upload) {
                if (isset($upload['file'])) {
                    $this->uploadService->upload(
                        $upload['file'],
                        'spaces-private',
                        $upload['path'],
                        ['filename' => $upload['filename']]
                    );
                } elseif (isset($upload['data'])) {
                    $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $upload['data']));
                    $this->uploadService->storeRawImage($decodedImage, $upload['path'], 'spaces-private');
                }
            }

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
            'status_id' => 'nullable|integer|exists:beneficiary_status,beneficiary_status_id',
            'status_name' => 'nullable|string|max:50|exists:beneficiary_status,status_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('status_id')) {
            $beneficiary->beneficiary_status_id = $request->status_id;
        } elseif ($request->filled('status_name')) {
            $statusId = \DB::table('beneficiary_status')
                ->where('status_name', $request->status_name)
                ->value('beneficiary_status_id');
            if (!$statusId) {
                return response()->json(['error' => 'Invalid status name.'], 422);
            }
            $beneficiary->beneficiary_status_id = $statusId;
        } else {
            return response()->json(['error' => 'Please provide status_id or status_name.'], 422);
        }

        $beneficiary->save();

        return response()->json([
            'success' => true,
            'data' => $beneficiary->fresh(['status'])
        ]);
    }

    /**
     * Remove the specified beneficiary using the service. REMOVED FEATURE
     */
    // public function destroy(Request $request, $id)
    // {
    //     if (!in_array($request->user()->role_id, [1, 2, 3])) {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }

    //     // Only admins and care managers can delete (service will enforce this)
    //     try {
    //         $result = $this->userManagementService->deleteBeneficiary(
    //             $id,
    //             $request->user() // Pass the User object as $currentUser
    //         );

    //         return response()->json($result);
    //     } catch (\Exception $e) {
    //         \Log::error('Error during beneficiary deletion: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json([
    //             'message' => 'An error occurred while deleting the beneficiary: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Restore a soft-deleted beneficiary. REMOVED FEATURE
     */
    // public function restore(Request $request, $id)
    // {
    //     if (!in_array($request->user()->role_id, [1, 2, 3])) {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }

    //     $beneficiary = Beneficiary::withTrashed()->with('generalCarePlan')->findOrFail($id);

    //     // Care worker can only restore assigned beneficiaries
    //     if ($request->user()->role_id == 3) {
    //         $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == $request->user()->id;
    //         if (!$isAssigned) {
    //             return response()->json(['error' => 'Unauthorized. You can only restore beneficiaries assigned to you.'], 403);
    //         }
    //     }

    //     if (!$beneficiary->trashed()) {
    //         return response()->json(['error' => 'Beneficiary is not deleted.'], 400);
    //     }

    //     $beneficiary->restore();

    //     return response()->json([
    //         'success' => true,
    //         'beneficiary' => $beneficiary
    //     ]);
    // }

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
            'familyMembers'
        ])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($beneficiaries) {
            $file = fopen('php://output', 'w');
            // CSV header
            fputcsv($file, [
                'ID', 'First Name', 'Last Name', 'Birthday', 'Gender', 'Civil Status',
                'Category', 'Status', 'Municipality', 'Barangay', 'Mobile', 'Landline',
                'Emergency Contact Name', 'Emergency Contact Relation', 'Emergency Contact Mobile',
                'Emergency Contact Email', 'Primary Caregiver', 'Care Worker ID', 'Review Date',
                'Medications', 'Allergies', 'Immunizations', 'Care Needs', 'Family Members'
            ]);
            foreach ($beneficiaries as $b) {
                // Robustly skip if beneficiary record is missing, soft-deleted, or not an object
                if (
                    !$b ||
                    !is_object($b) ||
                    (method_exists($b, 'trashed') && $b->trashed()) ||
                    empty($b->beneficiary_id)
                ) {
                    continue;
                }

                $careNeeds = (optional($b->generalCarePlan)->careNeeds ?? collect())
                    ->map(function($need) {
                        return "Cat{$need->care_category_id}:{$need->frequency}/{$need->assistance_required}";
                    })->implode('; ');

                $familyMembers = ($b->familyMembers ?? collect())
                    ->map(function($fm) {
                        return $fm->first_name . ' ' . $fm->last_name;
                    })->implode('; ');

                $medications = (optional($b->generalCarePlan)->medications ?? collect())
                    ->map(function($m) {
                        return "{$m->medication} ({$m->dosage}, {$m->frequency})";
                    })->implode('; ');

                $careWorkerId = optional($b->generalCarePlan)->care_worker_id;
                $reviewDate = optional($b->generalCarePlan)->review_date;
                $allergies = optional(optional($b->generalCarePlan)->healthHistory)->allergies ?? '';
                $immunizations = optional(optional($b->generalCarePlan)->healthHistory)->immunizations ?? '';

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
                    $careWorkerId,
                    $reviewDate,
                    $medications,
                    $allergies,
                    $immunizations,
                    $careNeeds,
                    $familyMembers,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
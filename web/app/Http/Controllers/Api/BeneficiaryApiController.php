<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\Medication;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\HealthHistory;
use App\Models\CareWorkerResponsibility;
use App\Models\User;
use App\Models\Notification;
use App\Models\FamilyMember;
use App\Services\UploadService;

class BeneficiaryApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Generate a unique username based on beneficiary's name
     */
    private function generateUniqueUsername($firstName, $middleName, $lastName, $currentId = null)
    {
        // Get first letter of first name
        $firstInitial = mb_substr(trim($firstName), 0, 1, 'UTF-8');
        
        // Get first letter of middle name if available
        $middleInitial = !empty($middleName) ? mb_substr(trim($middleName), 0, 1, 'UTF-8') : '';
        
        // Clean and lowercase the lastName
        $cleanLastName = trim($lastName);
        
        // Combine to create base username (all lowercase)
        $baseUsername = strtolower($firstInitial . $middleInitial . $cleanLastName);
        
        // Remove special characters and spaces
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        
        // Make sure username is at least 3 characters
        if (strlen($baseUsername) < 3) {
            $baseUsername = str_pad($baseUsername, 3, '0');
        }
        
        // Check if username exists
        $username = $baseUsername;
        $counter = 1;
        
        // Keep checking until we find a unique username
        while (true) {
            $query = Beneficiary::where('username', $username);
            
            // If updating, exclude the current beneficiary
            if ($currentId) {
                $query->where('beneficiary_id', '!=', $currentId);
            }
            
            // If username is unique, break the loop
            if (!$query->exists()) {
                break;
            }
            
            // Otherwise, append counter and try again
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

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(middle_name) LIKE ?', ['%' . strtolower($search) . '%']);
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
            'beneficiaries' => collect($beneficiaries->items())->map(function($b) {
                return array_merge(
                    $b->toArray(),
                    [
                        'photo_url' => $b->photo
                            ? Storage::disk('spaces-private')->temporaryUrl($b->photo, now()->addMinutes(30))
                            : null,
                        'care_service_agreement_doc_url' => $b->care_service_agreement_doc
                            ? Storage::disk('spaces-private')->temporaryUrl($b->care_service_agreement_doc, now()->addMinutes(30))
                            : null,
                        'general_care_plan_doc_url' => $b->general_care_plan_doc
                            ? Storage::disk('spaces-private')->temporaryUrl($b->general_care_plan_doc, now()->addMinutes(30))
                            : null,
                        'beneficiary_signature_url' => $b->beneficiary_signature
                            ? Storage::disk('spaces-private')->temporaryUrl($b->beneficiary_signature, now()->addMinutes(30))
                            : null,
                        'care_worker_signature_url' => $b->care_worker_signature
                            ? Storage::disk('spaces-private')->temporaryUrl($b->care_worker_signature, now()->addMinutes(30))
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
            // Remove portalAccount reference
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
                        ? Storage::disk('spaces-private')->temporaryUrl($beneficiary->photo, now()->addMinutes(30))
                        : null,
                    'care_service_agreement_doc_url' => $beneficiary->care_service_agreement_doc
                        ? Storage::disk('spaces-private')->temporaryUrl($beneficiary->care_service_agreement_doc, now()->addMinutes(30))
                        : null,
                    'general_care_plan_doc_url' => $beneficiary->general_care_plan_doc
                        ? Storage::disk('spaces-private')->temporaryUrl($beneficiary->general_care_plan_doc, now()->addMinutes(30))
                        : null,
                    'beneficiary_signature_url' => $beneficiary->beneficiary_signature
                        ? Storage::disk('spaces-private')->temporaryUrl($beneficiary->beneficiary_signature, now()->addMinutes(30))
                        : null,
                    'care_worker_signature_url' => $beneficiary->care_worker_signature
                        ? Storage::disk('spaces-private')->temporaryUrl($beneficiary->care_worker_signature, now()->addMinutes(30))
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
                'required',
                'string',
                'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'middle_name' => [
                'nullable', 
                'string',
                'max:100',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
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
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
                'max:500',
            ],
            'medications' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
                'max:500',
            ],
            'allergies' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
                'max:500',
            ],
            'immunizations' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
                'max:500',
            ],
            'category' => 'required|exists:beneficiary_categories,category_id',
            'frequency.mobility' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.mobility' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'assistance.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'medication_name' => 'nullable|array',
            'medication_name.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'dosage' => 'nullable|array',
            'dosage.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'frequency' => 'nullable|array',
            'frequency.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'administration_instructions' => 'nullable|array',
            'administration_instructions.*' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
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
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'care_worker.careworker_id' => 'required|exists:cose_users,id',
            'care_worker.tasks' => 'required|array|min:1',
            'care_worker.tasks.*' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            ],
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),
            'email' => 'nullable|string|email|max:255|unique:beneficiaries,email',
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

            // Use UploadService for all file uploads (private disk)
            $beneficiaryPhotoPath = null;
            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiaryPhotoPath = $this->uploadService->upload(
                    $request->file('beneficiaryProfilePic'),
                    'spaces-private',
                    'uploads/beneficiary_photos',
                    $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                );
            }

            $careServiceAgreementPath = null;
            if ($request->hasFile('care_service_agreement')) {
                $careServiceAgreementPath = $this->uploadService->upload(
                    $request->file('care_service_agreement'),
                    'spaces-private',
                    'uploads/care_service_agreements',
                    $firstName . '_' . $lastName . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                );
            }

            $generalCarePlanPath = null;
            if ($request->hasFile('general_careplan')) {
                $generalCarePlanPath = $this->uploadService->upload(
                    $request->file('general_careplan'),
                    'spaces-private',
                    'uploads/general_care_plans',
                    $firstName . '_' . $lastName . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                );
            }

            // Handle signatures (file or base64)
            $beneficiarySignaturePath = null;
            if ($request->hasFile('beneficiary_signature_upload')) {
                $beneficiarySignaturePath = $this->uploadService->upload(
                    $request->file('beneficiary_signature_upload'),
                    'spaces-private',
                    'uploads/beneficiary_signatures',
                    $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' .
                    $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('beneficiary_signature_canvas')));
                $this->uploadService->storeRawImage($decodedImage, $beneficiarySignaturePath, 'spaces-private');
            }

            $careWorkerSignaturePath = null;
            if ($request->hasFile('care_worker_signature_upload')) {
                $careWorkerSignaturePath = $this->uploadService->upload(
                    $request->file('care_worker_signature_upload'),
                    'spaces-private',
                    'uploads/care_worker_signatures',
                    $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' .
                    $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('care_worker_signature_canvas')));
                $this->uploadService->storeRawImage($decodedImage, $careWorkerSignaturePath, 'spaces-private');
            }

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

            // Generate unique username
            $username = $this->generateUniqueUsername($firstName, $middleName, $lastName);

            // Create beneficiary
            $beneficiary = Beneficiary::create([
                'first_name' => $request->input('first_name'),
                'middle_name' => $middleName,
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
                'beneficiary_status_id' => 1,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => Str::random(60),
                'username' => $username,
                'password' => Hash::make($request->input('account.password')),
                'email' => $request->input('account.email'),
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
        // Personal Details
        'first_name' => [
            'sometimes', 'required', 'string', 'max:100',
            'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/'
        ],
        'middle_name' => [
            'nullable', 
            'string',
            'max:100',
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
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'max:500'
        ],
        'medications' => [
            'nullable', 'string',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'max:500'
        ],
        'allergies' => [
            'nullable', 'string',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'max:500'
        ],
        'immunizations' => [
            'nullable', 'string',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/',
            'max:500'
        ],
        'category' => 'sometimes|required|exists:beneficiary_categories,category_id',
        
        // Care Needs: Mobility
        'frequency.mobility' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.mobility' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Cognitive / Communication
        'frequency.cognitive' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.cognitive' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Self-sustainability
        'frequency.self_sustainability' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.self_sustainability' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Disease / Therapy Handling
        'frequency.disease' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.disease' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Daily Life / Social Contact
        'frequency.daily_life' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.daily_life' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Outdoor Activities
        'frequency.outdoor' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.outdoor' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Needs: Household Keeping
        'frequency.household' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'assistance.household' => [
            'nullable', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Medications Management
        'medication_name' => 'nullable|array',
        'medication_name.*' => [
            'nullable', 'string', 'max:100',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'dosage' => 'nullable|array',
        'dosage.*' => [
            'nullable', 'string', 'max:100',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'frequency' => 'nullable|array',
        'frequency.*' => [
            'nullable', 'string', 'max:100',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        'administration_instructions' => 'nullable|array',
        'administration_instructions.*' => [
            'nullable', 'string', 'max:500',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
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
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Care Worker
        'care_worker.careworker_id' => 'sometimes|required|exists:cose_users,id',
        'care_worker.tasks' => 'sometimes|required|array|min:1',
        'care_worker.tasks.*' => [
            'required', 'string', 'max:255',
            'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+]+$/'
        ],
        
        // Review Date
        'date' => 'sometimes|required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),
        
        // Authentication fields
        'account.email' => [
            'nullable', 'email', 'max:255',
            Rule::unique('beneficiaries', 'email')->ignore($id, 'beneficiary_id')
        ],
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

            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiary->photo = $this->uploadService->upload(
                    $request->file('beneficiaryProfilePic'),
                    'spaces-private',
                    'uploads/beneficiary_photos',
                    $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                );
            }

            if ($request->hasFile('care_service_agreement')) {
                $beneficiary->care_service_agreement_doc = $this->uploadService->upload(
                    $request->file('care_service_agreement'),
                    'spaces-private',
                    'uploads/care_service_agreements',
                    $firstName . '_' . $lastName . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                );
            }

            if ($request->hasFile('general_careplan')) {
                $beneficiary->general_care_plan_doc = $this->uploadService->upload(
                    $request->file('general_careplan'),
                    'spaces-private',
                    'uploads/general_care_plans',
                    $firstName . '_' . $lastName . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                );
            }

            if ($request->hasFile('beneficiary_signature_upload')) {
                $beneficiary->beneficiary_signature = $this->uploadService->upload(
                    $request->file('beneficiary_signature_upload'),
                    'spaces-private',
                    'uploads/beneficiary_signatures',
                    $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiary->beneficiary_signature = 'uploads/beneficiary_signatures/' .
                    $firstName . '_' . $lastName . '_signature_' . $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('beneficiary_signature_canvas')));
                $this->uploadService->storeRawImage($decodedImage, $beneficiary->beneficiary_signature, 'spaces-private');
            }

            if ($request->hasFile('care_worker_signature_upload')) {
                $beneficiary->care_worker_signature = $this->uploadService->upload(
                    $request->file('care_worker_signature_upload'),
                    'spaces-private',
                    'uploads/care_worker_signatures',
                    $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $beneficiary->care_worker_signature = 'uploads/care_worker_signatures/' .
                    $firstName . '_' . $lastName . '_care_worker_signature_' . $uniqueIdentifier . '.png';
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('care_worker_signature_canvas')));
                $this->uploadService->storeRawImage($decodedImage, $beneficiary->care_worker_signature, 'spaces-private');
            }

            // Update email if provided
            if ($request->has('account.email')) {
                $beneficiary->email = $request->input('account.email');
            }

            // Update password if provided
            if ($request->filled('account.password')) {
                $beneficiary->password = Hash::make($request->input('account.password'));
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

            // Get the general care plan ID
            $generalCarePlanId = $beneficiary->general_care_plan_id;
            
            // Update general care plan if it exists
            if ($generalCarePlanId) {
                // Update the general care plan details
                DB::table('general_care_plans')
                    ->where('general_care_plan_id', $generalCarePlanId)
                    ->update([
                        'review_date' => $request->input('date'),
                        'emergency_plan' => $request->input('emergency_plan.procedures'),
                        'care_worker_id' => $request->input('care_worker.careworker_id'),
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
                
                // Format health history data
                $formattedMedicalConditions = !empty($medicalConditions) ? 
                    json_encode(array_map('trim', explode(',', $medicalConditions))) : null;
                $formattedMedications = !empty($medications) ? 
                    json_encode(array_map('trim', explode(',', $medications))) : null;
                $formattedAllergies = !empty($allergies) ? 
                    json_encode(array_map('trim', explode(',', $allergies))) : null;
                $formattedImmunizations = !empty($immunizations) ? 
                    json_encode(array_map('trim', explode(',', $immunizations))) : null;
                
                // Update health history
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
                
                // Then add new medications
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
                
                // Then add new responsibilities
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
                
                // Then add new care needs
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
            }

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
            'familyMembers'
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
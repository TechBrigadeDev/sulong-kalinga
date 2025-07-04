<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Beneficiary;
use App\Models\Medication;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\BeneficiaryCategory;
use App\Models\BeneficiaryStatus;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\CareWorkerResponsibility;
use App\Models\User;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\HealthHistory;
use App\Models\Notification;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Services\LogService;
use App\Enums\LogType;
use App\Services\NotificationService;

use App\Services\UserManagementService;
use App\Services\UploadService;

class BeneficiaryController extends Controller
{
    protected $userManagementService;
    protected $logService;
    protected $uploadService;
    protected $notificationService;

    public function __construct(
        UserManagementService $userManagementService,
        LogService $logService,
        UploadService $uploadService,
        NotificationService $notificationService
    ) {
        $this->userManagementService = $userManagementService;
        $this->logService = $logService;
        $this->uploadService = $uploadService;
        $this->notificationService = $notificationService;
    }

    private function resetGeneralCarePlanSequence()
    {
        // Get the maximum ID currently in the table
        $maxId = DB::table('general_care_plans')->max('general_care_plan_id');
        
        // Reset the sequence to start from the next available ID
        if ($maxId) {
            DB::statement("SELECT setval('general_care_plans_general_care_plan_id_seq', $maxId, true)");
        }
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch all categories and statuses
        $categories = BeneficiaryCategory::all();
        $statuses = BeneficiaryStatus::all();

        // Fetch beneficiaries based on the search query and filters
        $query = Beneficiary::with('category', 'status', 'municipality', 'generalCarePlan')
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'category') {
                    return $query->orderBy('category_id');
                } elseif ($filter == 'status') {
                    return $query->orderBy('beneficiary_status_id');
                } elseif ($filter == 'municipality') {
                    return $query->orderBy('municipality_id');
                }
            })
            ->orderBy('first_name'); // Order by first name alphabetically by default
        
        // Care Workers can only see assigned beneficiaries
        if (Auth::user()->role_id == 3) {
            $query->whereHas('generalCarePlan', function($q) {
                $q->where('care_worker_id', Auth::id());
            });
        }
        
        $beneficiaries = $query->get();
        
        // Return appropriate view based on user role
        if (Auth::user()->role_id == 1) { // Admin
            return view('admin.beneficiaryProfile', compact('beneficiaries', 'search', 'filter', 'categories', 'statuses'));
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            return view('careManager.beneficiaryProfile', compact('beneficiaries', 'search', 'filter', 'categories', 'statuses'));
        } else { // Care Worker
            return view('careWorker.beneficiaryProfile', compact('beneficiaries', 'search', 'filter', 'categories', 'statuses'));
        }
    }

    // For status change methods, restrict to admin and care manager only
    public function updateStatusAjax($id, Request $request)
    {
        // Only admin and care manager can change status
        if (Auth::user()->role_id == 3) { // Care Worker
            return response()->json(['success' => false, 'message' => 'Unauthorized. Care workers cannot change beneficiary status.'], 403);
        }
        
        try {
            \Log::info('Update status AJAX request', [
                'beneficiary_id' => $id,
                'status' => $request->input('status'),
                'reason' => $request->input('reason')
            ]);
            
            // Find beneficiary
            $beneficiary = Beneficiary::findOrFail($id);
            
            // Find status
            $status = BeneficiaryStatus::where('status_name', $request->input('status'))->first();
            if (!$status) {
                return response()->json(['success' => false, 'message' => 'Invalid status'], 400);
            }

            // Old status for notification
            $oldStatus = $beneficiary->status->status_name;
            
            // Get care worker ID before updating
            $careWorkerId = $beneficiary->generalCarePlan ? $beneficiary->generalCarePlan->care_worker_id : null;
                
            // Update using the DB facade to avoid any Eloquent model issues
            $updated = DB::table('beneficiaries')
                ->where('beneficiary_id', $id)
                ->update([
                    'beneficiary_status_id' => $status->beneficiary_status_id,
                    'status_reason' => $request->input('reason'),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ]);
            
            // Verify update was successful
            if ($updated) {
                \Log::info('Status updated successfully', [
                    'beneficiary_id' => $id,
                    'new_status_id' => $status->beneficiary_status_id
                ]);

                try {
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : 'Care Manager';
                    $newStatus = $request->input('status');
                    $reason = $request->input('reason');
                    
                    // 1. Notify the beneficiary about the status change
                    $this->notificationService->notifyBeneficiary($id, $beneficiaryTitle, $beneficiaryMessage);

                    // 2. Notify family members about the status change
                    $familyMembers = FamilyMember::where('related_beneficiary_id', $id)->get();
                    foreach ($familyMembers as $member) {
                        $this->notificationService->notifyFamilyMember($member->family_member_id, $familyTitle, $familyMessage);
                    }

                    // 3. Notify the assigned care worker (if not the one making the change)
                    if ($careWorkerId && $careWorkerId != Auth::id()) {
                        $this->notificationService->notifyStaff($careWorkerId, $careWorkerTitle, $careWorkerMessage);

                        // 4. Notify the care manager (if not the one making the change)
                        $careWorker = User::find($careWorkerId);
                        if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                            $this->notificationService->notifyStaff($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                        }
                    }
                } catch (\Exception $notifyEx) {
                    // Log notification errors but don't interrupt the status update
                    \Log::warning('Error sending notifications for beneficiary status change: ' . $notifyEx->getMessage());
                }

                return response()->json(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'No changes made'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Status update failed: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Status update failed: ' . $e->getMessage()], 500);
        }
    }

    public function activate($id, Request $request)
    {
        // Only admin and care manager can activate beneficiaries
        if (Auth::user()->role_id == 3) { // Care Worker
            return response()->json(['success' => false, 'message' => 'Unauthorized. Care workers cannot change beneficiary status.'], 403);
        }
        
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->beneficiary_status_id = 1;
        $beneficiary->status_reason = null;
        $beneficiary->updated_by = Auth::id();
        $beneficiary->updated_at = now();
        $beneficiary->save();

        return response()->json(['success' => true]);
    }

    public function viewProfileDetails(Request $request)
    {
        // Fetch care needs with care_category_id = 1
        $categories = BeneficiaryCategory::all();
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.mobility', 
            'generalCarePlan.cognitiveFunction', 
            'generalCarePlan.emotionalWellbeing', 
            'generalCarePlan.mobility',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careWorkerResponsibility',
        ])->find($beneficiary_id);
        
        if (!$beneficiary) {
            return redirect()->back()->with('error', 'Beneficiary not found.');
        }

        // Generate temporary URLs for file fields
        $photoUrl = $beneficiary->photo
            ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->photo, 30)
            : null;
        $careServiceAgreementUrl = $beneficiary->care_service_agreement_doc
            ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->care_service_agreement_doc, 30)
            : null;
        $generalCarePlanUrl = $beneficiary->general_care_plan_doc
            ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->general_care_plan_doc, 30)
            : null;
        $beneficiarySignatureUrl = $beneficiary->beneficiary_signature
            ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->beneficiary_signature, 30)
            : null;
        $careWorkerSignatureUrl = $beneficiary->care_worker_signature
            ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->care_worker_signature, 30)
            : null;
        $careServiceAgreementViewUrl = $beneficiary->care_service_agreement_doc
            ? $this->uploadService->getTemporaryPrivateUrl(
                $beneficiary->care_service_agreement_doc,
                30,
                'spaces-private',
                ['ResponseContentDisposition' => 'inline']
            )
            : null;
        $generalCarePlanViewUrl = $beneficiary->general_care_plan_doc
            ? $this->uploadService->getTemporaryPrivateUrl(
                $beneficiary->general_care_plan_doc,
                30,
                'spaces-private',
                ['ResponseContentDisposition' => 'inline']
            )
            : null;
        // For Care Service Agreement
        $careServiceAgreementExtension = null;
        if ($beneficiary->care_service_agreement_doc) {
            $careServiceAgreementExtension = strtolower(pathinfo($beneficiary->care_service_agreement_doc, PATHINFO_EXTENSION));
        }

        // For General Care Plan
        $generalCarePlanExtension = null;
        if ($beneficiary->general_care_plan_doc) {
            $generalCarePlanExtension = strtolower(pathinfo($beneficiary->general_care_plan_doc, PATHINFO_EXTENSION));
        }

        // For care workers, check if they're assigned to this beneficiary
        if (Auth::user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == Auth::id();
            if (!$isAssigned) {
                abort(403, 'Unauthorized. You can only view details of beneficiaries assigned to you.');
            }
        }

        // Log the view action
        $this->logService->createLog(
            'beneficiary',
            $beneficiary->beneficiary_id,
            LogType::VIEW,
            Auth::user()->first_name . ' ' . Auth::user()->last_name . ' viewed beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
            Auth::id()
        );

        $careNeeds1 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 1);
        $careNeeds2 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 2);
        $careNeeds3 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 3);
        $careNeeds4 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 4);
        $careNeeds5 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 5);
        $careNeeds6 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 6);
        $careNeeds7 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 7);
        
        // Get the first care worker responsibility for each general care plan
        $careWorkerResponsibility = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
        $careWorker = $careWorkerResponsibility ? $careWorkerResponsibility->careWorker : null;

        // Return appropriate view based on user role
        if (Auth::user()->role_id == 1) { // Admin
            // return view('admin.viewProfileDetails', compact('beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker')); 
            return view('admin.viewProfileDetails', compact(
                'beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker',
                'photoUrl', 'careServiceAgreementUrl', 'generalCarePlanUrl', 'beneficiarySignatureUrl', 'careWorkerSignatureUrl', 'careServiceAgreementViewUrl', 'generalCarePlanViewUrl', 'careServiceAgreementExtension', 'generalCarePlanExtension'
            ));
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            return view('careManager.viewProfileDetails', compact(
                'beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker',
                'photoUrl', 'careServiceAgreementUrl', 'generalCarePlanUrl', 'beneficiarySignatureUrl', 'careWorkerSignatureUrl', 'careServiceAgreementViewUrl', 'generalCarePlanViewUrl', 'careServiceAgreementExtension', 'generalCarePlanExtension'
            ));        
        } else { // Care Worker
            return view('careWorker.viewProfileDetails', compact(
                'beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker',
                'photoUrl', 'careServiceAgreementUrl', 'generalCarePlanUrl', 'beneficiarySignatureUrl', 'careWorkerSignatureUrl', 'careServiceAgreementViewUrl', 'generalCarePlanViewUrl', 'careServiceAgreementExtension', 'generalCarePlanExtension'
            ));        
        }
    }

    public function editProfile(Request $request)
    {
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with(['category', 'barangay', 'municipality', 'status'])->find($beneficiary_id);
        $beneficiary = Beneficiary::with(['generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction', 'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications'])->find($request->beneficiary_id);


        if (!$beneficiary) {
            return redirect()->route('beneficiaryProfile')->with('error', 'Beneficiary not found.');
        }

        return view('admin.editProfile', compact('beneficiary')); 
    }

    public function create()
    {
        $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();
        $barangays = Barangay::all();
        $careWorkers = User::where('role_id', 3)
                        ->where('status', 'Active')
                        ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) AS name"))
                        ->get(); 
        $categories = DB::table('beneficiary_categories')->get();
        
        // Return appropriate view based on user role
        if (Auth::user()->role_id == 1) { // Admin
            return view('admin.addBeneficiary', [
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'careWorkers' => $careWorkers, 
                'categories' => $categories,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            return view('careManager.addBeneficiary', [
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'careWorkers' => $careWorkers, 
                'categories' => $categories,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        } else { // Care Worker - now allowed to add beneficiaries
            return view('careWorker.addBeneficiary', [
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'careWorkers' => $careWorkers, 
                'categories' => $categories,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        }
    }

    public function storeBeneficiary(Request $request)
    {
        // Reset the sequence to ensure we get a valid ID
        $this->resetGeneralCarePlanSequence();

        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
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
                'in:Single,Married,Widowed,Divorced', // Must match one of the predefined options
            ],
            'gender' => [
                'required',
                'string',
                'in:Male,Female,Other', // Must match one of the predefined options
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(14)->toDateString(), // Must be at least 14 years old
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
                'regex:/^[0-9]{10,11}$/', // Must be 10 or 11 digits
            ],
            'landline_number' => [
                'nullable', // Optional field
                'string',
                'regex:/^[0-9]{7,10}$/', // Must be between 7 and 10 digits
            ],

            // Current Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.\-#:\/]+$/', // Allows alphanumeric characters, spaces, commas, periods, hyphens, #, :, and /
            ],
            'municipality' => [
                'required',
                'exists:municipalities,municipality_id', // Must exist in the municipalities table
            ],
            'barangay' => [
                'required',
                'exists:barangays,barangay_id', // Must exist in the barangays table
            ],
            // Medical History  
            'medical_conditions' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
, // Allows letters, numbers, spaces, commas, periods, hyphens, and parentheses
                'max:500', // Optional: Limit the length to 500 characters
            ],
            'medications' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
                'max:500',
            ],
            'allergies' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
                'max:500',
            ],
            'immunizations' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
                'max:500',
            ],
            'category' => 'required|exists:beneficiary_categories,category_id', 

            // Care Needs: Mobility
            'frequency.mobility' => [
                'nullable', // Optional field
                'string',
                'max:255', // Maximum length of 255 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
, // Allows letters, numbers, spaces, commas, periods, hyphens, and parentheses
            ],
            'assistance.mobility' => [
                'nullable', // Optional field
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],

            // Care Needs: Cognitive / Communication
            'frequency.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],
            'assistance.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],

            // Care Needs: Self-sustainability
            'frequency.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],
            'assistance.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],

            // Care Needs: Disease / Therapy Handling
            'frequency.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],
            'assistance.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],

            // Care Needs: Daily Life / Social Contact
            'frequency.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],
            'assistance.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],

            // Care Needs: Outdoor Activities
            'frequency.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/'
,
            ],
            'assistance.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],

            // Care Needs: Household Keeping
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

            // Medications Management
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
        
            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.\-#:\/]+$/', // Allows alphanumeric characters, spaces, commas, periods, hyphens, #, :, and /
            ],

            // Emergency Contact
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
                'regex:/^[0-9]{10,11}$/', // 10 or 11 digits
            ],
            'emergency_contact.email' => 'nullable|email|max:100',

            // Emergency Plan
            'emergency_plan.procedures' => [
                'required',
                'string',
                'max:1000', // Optional: Limit the length to 1000 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/', // Allows letters, numbers, spaces, and specific special characters
            ],

            // Care Worker
            'care_worker.careworker_id' => 'required|exists:cose_users,id', // Ensure the selected care worker exists
            'care_worker.tasks' => 'required|array|min:1', // Ensure at least one task is provided
            'care_worker.tasks.*' => [
                'required',
                'string',
                'max:255', // Limit to 255 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/', // Allow letters, numbers, spaces, and specific special characters
            ],

            // Beneficiary Picture
            'beneficiaryProfilePic' => 'nullable|file|mimes:jpeg,png|max:7168', // 7MB 

            // Review Date
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),

            // Care Service Agreement
            'care_service_agreement' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max size: 5MB

            // General Careplan
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Max size: 5MB
        
            // Beneficiary Signature
                'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // Max size: 2MB
                'beneficiary_signature_canvas' => 'nullable|string', // Base64 string for the canvas signature

                // Care Worker Signature
                'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // Max size: 2MB
                'care_worker_signature_canvas' => 'nullable|string', // Base64 string for the canvas signature
            
            // Password validation
            'account.password' => 'required|string|min:8|confirmed', // 'confirmed' ensures it matches the confirmation field

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (
            !$request->hasFile('beneficiary_signature_upload') &&
            empty($request->input('beneficiary_signature_canvas'))
        ) {
            return redirect()->back()->withErrors(['beneficiary_signature' => 'Please provide a beneficiary signature either by drawing on the canvas or uploading a file.'])->withInput();
        }

        if (
            !$request->hasFile('care_worker_signature_upload') &&
            empty($request->input('care_worker_signature_canvas'))
        ) {
            return redirect()->back()->withErrors(['care_worker_signature' => 'Please provide a care worker signature either by drawing on the canvas or uploading a file.'])->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        

        // DB::beginTransaction();
        // try
        try {
            DB::beginTransaction();

            // Generate unique username based on name
            $username = $this->generateUniqueUsername(
                $request->input('first_name'),
                $request->input('middle_name'),
                $request->input('last_name')
            );

            // Use the provided password
            $password = bcrypt($request->input('account.password'));

            // Generate unique identifier for file naming
            $uniqueIdentifier = Str::random(10);

            // Store beneficiary profile picture
            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiaryPhotoPath = $this->uploadService->upload(
                    $request->file('beneficiaryProfilePic'),
                    'spaces-private',
                    'uploads/beneficiary_photos',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                    ]
                );
                \Log::info('Storing beneficiary photo with path: ' . $beneficiaryPhotoPath);
            }

            // Handle Care Service Agreement
            if ($request->hasFile('care_service_agreement')) {
                $careServiceAgreementPath = $this->uploadService->upload(
                    $request->file('care_service_agreement'),
                    'spaces-private',
                    'uploads/care_service_agreements',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                    ]
                );
            } else {
                $careServiceAgreementPath = null;
            }

            // Handle General Care Plan
            if ($request->hasFile('general_careplan')) {
                $generalCarePlanPath = $this->uploadService->upload(
                    $request->file('general_careplan'),
                    'spaces-private',
                    'uploads/general_care_plans',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                    ]
                );
            } else {
                $generalCarePlanPath = null;
            }

            // Handle Beneficiary Signature
            if ($request->hasFile('beneficiary_signature_upload')) {
                $beneficiarySignaturePath = $this->uploadService->upload(
                    $request->file('beneficiary_signature_upload'),
                    'spaces-private',
                    'uploads/beneficiary_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                    ]
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_signature_' .
                    $uniqueIdentifier . '.png';

                $directory = storage_path('app/private/uploads/beneficiary_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $beneficiarySignatureData = $request->input('beneficiary_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $beneficiarySignatureData));
                file_put_contents($directory . '/' . basename($beneficiarySignaturePath), $decodedImage);
            }

            // Handle Care Worker Signature
            if ($request->hasFile('care_worker_signature_upload')) {
                $careWorkerSignaturePath = $this->uploadService->upload(
                    $request->file('care_worker_signature_upload'),
                    'spaces-private',
                    'uploads/care_worker_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                    ]
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' .
                    $request->input('first_name') . '_' .
                    $request->input('last_name') . '_care_worker_signature_' .
                    $uniqueIdentifier . '.png';

                $directory = storage_path('app/private/uploads/care_worker_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $careWorkerSignatureData = $request->input('care_worker_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $careWorkerSignatureData));
                file_put_contents($directory . '/' . basename($careWorkerSignaturePath), $decodedImage);
            }   

            // // Insert into portal_accounts table
            // $portalAccount = DB::table('portal_accounts')->insertGetId([
            //     'portal_email' => $request->input('account.email'),
            //     'portal_password' => Hash::make($request->input('account.password')),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
        
            // \Log::info('Portal Account ID: ' . $portalAccount);
        
            // Insert into gcp (General Care Plan) table
            $generalCarePlan = GeneralCarePlan::create([
                'care_worker_id' => $request->input('care_worker.careworker_id'),
                'emergency_plan' => $request->input('emergency_plan.procedures'),
                'review_date' => $request->input('date'),
                'created_at' => now(),
            ]);
        
            $generalCarePlanId = $generalCarePlan->general_care_plan_id ?? null;
        
            \Log::info('General Care Plan Created: ' . json_encode($generalCarePlan));
        
            if (!$generalCarePlanId) {
                throw new \Exception('General Care Plan ID is null.');
            }

            $remember_token = Str::random(60);

            $mobileNumber = $request->input('mobile_number');
            if (!str_starts_with($mobileNumber, '+63')) {
                $mobileNumber = '+63' . $mobileNumber;
            }

            $emergencyContactMobile = $request->input('emergency_contact.mobile');
            if (!str_starts_with($emergencyContactMobile, '+63')) {
                $emergencyContactMobile = '+63' . $emergencyContactMobile;
            }

            // Prepare map_location JSON for Google Maps
            $mapLocation = null;
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $mapLocation = [
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ];
            }

            // Store credentials directly in the beneficiary model
            $password = bcrypt($request->input('account.password'));

            // Insert into beneficiaries table
            $beneficiary = Beneficiary::create([
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
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
                'photo' => $beneficiaryPhotoPath ?? null,
                'beneficiary_signature' => $beneficiarySignaturePath,
                'care_worker_signature' => $careWorkerSignaturePath,
                'general_care_plan_id' => $generalCarePlanId,
                //'portal_account_id' => $portalAccount,
                'beneficiary_status_id' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
                'username' => $username,
                'password' => $password,
                'remember_token' => $remember_token,
                'map_location' => $mapLocation, // <-- Google Maps data
            ]);

            // Log the creation of the beneficiary
            $this->logService->createLog(
                'beneficiary',
                $beneficiary->beneficiary_id,
                LogType::CREATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' created beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
                Auth::id()
            );
        
            // Insert into emotional_wellbeing table
            EmotionalWellbeing::create([
                'general_care_plan_id' => $generalCarePlanId,
                'mood' => $request->input('emotional.mood'),
                'social_interactions' => $request->input('emotional.social_interactions'),
                'emotional_support_needs' => $request->input('emotional.emotional_support'),
            ]);
        
            // Insert into cognitive_function table
            CognitiveFunction::create([
                'general_care_plan_id' => $generalCarePlanId,
                'memory' => $request->input('cognitive.memory'),
                'thinking_skills' => $request->input('cognitive.thinking_skills'),
                'orientation' => $request->input('cognitive.orientation'),
                'behavior' => $request->input('cognitive.behavior'),
            ]);
        
            // Insert into mobility table
            Mobility::create([
                'general_care_plan_id' => $generalCarePlanId,
                'walking_ability' => $request->input('mobility.walking_ability'),
                'assistive_devices' => $request->input('mobility.assistive_devices'),
                'transportation_needs' => $request->input('mobility.transportation_needs'),
            ]);
        
            // Process medical conditions
            $medicalConditions = $request->input('medical_conditions');
            if (!empty($medicalConditions)) {
                // Convert comma-separated string to array and trim whitespace
                $medicalConditionsArray = array_map('trim', explode(',', $medicalConditions));
                $formattedMedicalConditions = json_encode($medicalConditionsArray);
            } else {
                $formattedMedicalConditions = null;
            }

            // Process medications
            $medications = $request->input('medications');
            if (!empty($medications)) {
                // Convert comma-separated string to array and trim whitespace
                $medicationsArray = array_map('trim', explode(',', $medications));
                $formattedMedications = json_encode($medicationsArray);
            } else {
                $formattedMedications = null;
            }

            // Process allergies
            $allergies = $request->input('allergies');
            if (!empty($allergies)) {
                // Convert comma-separated string to array and trim whitespace
                $allergiesArray = array_map('trim', explode(',', $allergies));
                $formattedAllergies = json_encode($allergiesArray);
            } else {
                $formattedAllergies = null;
            }

            // Process immunizations
            $immunizations = $request->input('immunizations');
            if (!empty($immunizations)) {
                // Convert comma-separated string to array and trim whitespace
                $immunizationsArray = array_map('trim', explode(',', $immunizations));
                $formattedImmunizations = json_encode($immunizationsArray);
            } else {
                $formattedImmunizations = null;
            }

            // Create health history record with formatted values
            HealthHistory::create([
                'general_care_plan_id' => $generalCarePlanId,
                'medical_conditions' => $formattedMedicalConditions,
                'medications' => $formattedMedications,
                'allergies' => $formattedAllergies,
                'immunizations' => $formattedImmunizations,
            ]);

            // Insert into medications table
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

            // Insert into care_worker_responsibilities table
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

            // Categories 1 to 7 represent different care categories
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

            try {
                if ($beneficiary) {
                    $welcomeTitle = 'Welcome to SULONG KALINGA';
                    $welcomeMessage = 'Welcome ' . $beneficiary->first_name . ' ' . $beneficiary->last_name .
                        '! Your beneficiary account has been created in SULONG KALINGA. You can now access the portal with your credentials.';
                    $this->notificationService->notifyBeneficiary($beneficiary->beneficiary_id, $welcomeTitle, $welcomeMessage);
                }

                $careWorkerId = $request->input('care_worker.careworker_id');
                if ($careWorkerId && $careWorkerId != Auth::id()) {
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
                    $title = 'New Beneficiary Assigned';
                    $message = $actor . ' (' . $actorRole . ') has assigned a new beneficiary, ' .
                        $beneficiary->first_name . ' ' . $beneficiary->last_name . ', to you.';

                    $this->notificationService->notifyStaff($careWorkerId, $title, $message);

                    $careWorker = User::find($careWorkerId);
                    if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                        $careManagerTitle = 'New Beneficiary for Your Care Worker';
                        $careManagerMessage = 'A new beneficiary, ' . $beneficiary->first_name . ' ' . $beneficiary->last_name .
                            ', has been assigned to your care worker ' . $careWorker->first_name . ' ' . $careWorker->last_name .
                            ' by ' . $actor . ' (' . $actorRole . ').';
                        $this->notificationService->notifyStaff($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the main process
                \Log::warning('Error sending notifications for new beneficiary: ' . $notifyEx->getMessage());
            }
        
            // Redirect with success message
            if (Auth::user()->role_id == 1) { // Admin
                return redirect()->route('admin.beneficiaries.create')->with('success', 'Beneficiary has been successfully added!');
            } elseif (Auth::user()->role_id == 2) { // Care manager
                return redirect()->route('care-manager.beneficiaries.create')->with('success', 'Beneficiary has been successfully added!');
            } else {
                // Fallback - should not happen since only admins and care managers can add beneficiaries
                return redirect()->back()->with('success', 'Beneficiary has been successfully added!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving beneficiary: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while saving the beneficiary: ' . $e->getMessage()])->withInput();
        }
    }

    public function editBeneficiary($id)
    {
        // Load beneficiary with all related data
        $beneficiary = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.mobility', 
            'generalCarePlan.cognitiveFunction', 
            'generalCarePlan.emotionalWellbeing', 
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careNeeds',
            'generalCarePlan.careWorkerResponsibility'
        ])->findOrFail($id);

        // For care workers, check if they're assigned to this beneficiary
        if (Auth::user()->role_id == 3) {
            $isAssigned = $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id == Auth::id();
            if (!$isAssigned) {
                abort(403, 'Unauthorized. You can only edit beneficiaries assigned to you.');
            }
        }

        // Format the date for the form
        $birth_date = null;
        if ($beneficiary->birthday) {
            $birth_date = Carbon::parse($beneficiary->birthday)->format('Y-m-d');
        }

        $review_date = null;
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->review_date) {
            $review_date = Carbon::parse($beneficiary->generalCarePlan->review_date)->format('Y-m-d');
        }

        // Get current care worker
        $currentCareWorker = null;
        $currentCareWorkerTasks = [];
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->careWorkerResponsibility->count() > 0) {
            $careWorkerResp = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
            $currentCareWorker = $careWorkerResp ? $careWorkerResp->care_worker_id : null;
            
            // Get all tasks for this care worker
            foreach($beneficiary->generalCarePlan->careWorkerResponsibility as $responsibility) {
                if ($responsibility->task_description) {
                    $currentCareWorkerTasks[] = $responsibility->task_description;
                }
            }
        }

        // Get all care needs by category
        $careNeeds = [];
        foreach($beneficiary->generalCarePlan->careNeeds as $need) {
            $careNeeds[$need->care_category_id] = [
                'frequency' => $need->frequency,
                'assistance' => $need->assistance_required
            ];
        }

        // Load necessary data for form dropdowns
        $municipalities = Municipality::all();
        $barangays = Barangay::all();
        $categories = BeneficiaryCategory::all();
        $careWorkers = User::where('role_id', 3)
                        ->where('status', 'Active')
                        ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) AS name"))
                        ->get();
        
        // Prepare Google Maps data for the view
        $latitude = null;
        $longitude = null;
        if ($beneficiary->map_location && is_array($beneficiary->map_location)) {
            $latitude = $beneficiary->map_location['latitude'] ?? null;
            $longitude = $beneficiary->map_location['longitude'] ?? null;
        }

        // Pass Google Maps API key if needed
        $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');

        // Return view based on user role
        if (Auth::user()->role_id == 1) { // Admin
            return view('admin.editBeneficiary', [
                'beneficiary' => $beneficiary, 
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'categories' => $categories, 
                'careWorkers' => $careWorkers, 
                'birth_date' => $birth_date, 
                'review_date' => $review_date, 
                'currentCareWorker' => $currentCareWorker,  
                'currentCareWorkerTasks' => $currentCareWorkerTasks, 
                'careNeeds' => $careNeeds,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            return view('careManager.editBeneficiary', [
                'beneficiary' => $beneficiary, 
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'categories' => $categories, 
                'careWorkers' => $careWorkers, 
                'birth_date' => $birth_date, 
                'review_date' => $review_date, 
                'currentCareWorker' => $currentCareWorker,  
                'currentCareWorkerTasks' => $currentCareWorkerTasks, 
                'careNeeds' => $careNeeds,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        } else { // Care Worker - now allowed to edit beneficiaries
            return view('careWorker.editBeneficiary', [
                'beneficiary' => $beneficiary, 
                'municipalities' => $municipalities, 
                'barangays' => $barangays, 
                'categories' => $categories, 
                'careWorkers' => $careWorkers, 
                'birth_date' => $birth_date, 
                'review_date' => $review_date, 
                'currentCareWorker' => $currentCareWorker,  
                'currentCareWorkerTasks' => $currentCareWorkerTasks, 
                'careNeeds' => $careNeeds,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        }
    }

    public function updateBeneficiary(Request $request, $id)
    {
        // Validate the input data with adjustments for update
        $validator = Validator::make($request->all(), [
            // Personal Details - Same as storeBeneficiary
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
                'regex:/^[a-zA-Z0-9\s,.\-#:\/]+$/', // Allows alphanumeric characters, spaces, commas, periods, hyphens, #, :, and /
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('beneficiaries', 'email')->ignore($id, 'beneficiary_id'),
            ],
            'municipality' => [
                'required',
                'exists:municipalities,municipality_id',
            ],
            'barangay' => [
                'required',
                'exists:barangays,barangay_id',
            ],
            'account.password' => 'nullable|string|min:8|confirmed',
            
            // Medical History - Same as storeBeneficiary
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
            'email' => 'nullable|string|email|max:255|unique:beneficiaries,email',

            // Care Needs - Same as storeBeneficiary
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
            
            // Files - Modified for update scenario
            'beneficiaryProfilePic' => 'nullable|file|mimes:jpeg,png|max:7168', // 7MB 
            'care_service_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            
            // Signatures - Modified for update scenario
            'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'beneficiary_signature_canvas' => 'nullable|string',
            'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048',
            'care_worker_signature_canvas' => 'nullable|string',
            
            // Other fields - Same as storeBeneficiary
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

            // Emergency Plan
            'emergency_plan.procedures' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],

            // Care Worker
            'care_worker.careworker_id' => 'required|exists:cose_users,id',
            'care_worker.tasks' => 'required|array|min:1',
            'care_worker.tasks.*' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?+\/]+$/',
            ],

            // Review Date
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Get the beneficiary and related data
            $beneficiary = Beneficiary::findOrFail($id);

            // Check if name has changed
            $nameChanged = ($beneficiary->first_name !== $request->input('first_name') || 
                        $beneficiary->middle_name !== $request->input('middle_name') || 
                        $beneficiary->last_name !== $request->input('last_name'));

            // If name changed, generate new username
            if ($nameChanged) {
                $username = $this->generateUniqueUsername(
                    $request->input('first_name'),
                    $request->input('middle_name'),
                    $request->input('last_name'),
                    $id // Pass the current ID to exclude from uniqueness check
                );
                $beneficiary->username = $username;
            }
            
            $generalCarePlanId = $beneficiary->general_care_plan_id;

             // Store original care worker ID for comparison later
            $originalCareWorkerId = $beneficiary->generalCarePlan ? $beneficiary->generalCarePlan->care_worker_id : null;
            
            // Generate unique identifier for file naming
            $uniqueIdentifier = Str::random(10);
            
            // Handle file uploads with option to keep existing files
            $beneficiaryPhotoPath = $beneficiary->photo; // Default to existing
            $careServiceAgreementPath = $beneficiary->care_service_agreement_doc; // Default to existing
            $generalCarePlanPath = $beneficiary->general_care_plan_doc; // Default to existing
            $beneficiarySignaturePath = $beneficiary->beneficiary_signature; // Default to existing
            $careWorkerSignaturePath = $beneficiary->care_worker_signature; // Default to existing
            
            // Store beneficiary profile picture if a new one is uploaded
            if ($request->hasFile('beneficiaryProfilePic')) {
                if ($beneficiary->photo) {
                    $this->uploadService->delete($beneficiary->photo, 'spaces-private');
                }
                $beneficiaryPhotoPath = $this->uploadService->upload(
                    $request->file('beneficiaryProfilePic'),
                    'spaces-private',
                    'uploads/beneficiary_photos',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension()
                    ]
                );
            }

            // Handle Care Service Agreement if a new one is uploaded
            if ($request->hasFile('care_service_agreement')) {
                if ($beneficiary->care_service_agreement_doc) {
                    $this->uploadService->delete($beneficiary->care_service_agreement_doc, 'spaces-private');
                }
                $careServiceAgreementPath = $this->uploadService->upload(
                    $request->file('care_service_agreement'),
                    'spaces-private',
                    'uploads/care_service_agreements',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension()
                    ]
                );
            }

            // Handle General Care Plan if a new one is uploaded
            if ($request->hasFile('general_careplan')) {
                if ($beneficiary->general_care_plan_doc) {
                    $this->uploadService->delete($beneficiary->general_care_plan_doc, 'spaces-private');
                }
                $generalCarePlanPath = $this->uploadService->upload(
                    $request->file('general_careplan'),
                    'spaces-private',
                    'uploads/general_care_plans',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_general_care_plan_' . $uniqueIdentifier . '.' . $request->file('general_careplan')->getClientOriginalExtension()
                    ]
                );
            }
            
            // Handle Beneficiary Signature if a new one is provided
            if ($request->hasFile('beneficiary_signature_upload')) {
                if ($beneficiary->beneficiary_signature) {
                    $this->uploadService->delete($beneficiary->beneficiary_signature, 'spaces-private');
                }
                $beneficiarySignaturePath = $this->uploadService->upload(
                    $request->file('beneficiary_signature_upload'),
                    'spaces-private',
                    'uploads/beneficiary_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_signature_' . $uniqueIdentifier . '.' . $request->file('beneficiary_signature_upload')->getClientOriginalExtension()
                    ]
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                if ($beneficiary->beneficiary_signature) {
                    $this->uploadService->delete($beneficiary->beneficiary_signature, 'spaces-private');
                }
                $canvasData = $request->input('beneficiary_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $canvasData));
                $tmpFile = tmpfile();
                $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
                file_put_contents($tmpFilePath, $decodedImage);

                $beneficiarySignaturePath = $this->uploadService->upload(
                    new \Illuminate\Http\File($tmpFilePath),
                    'spaces-private',
                    'uploads/beneficiary_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_signature_' . $uniqueIdentifier . '.png'
                    ]
                );
                fclose($tmpFile);
            }
            
            // Handle Care Worker Signature if a new one is provided
            if ($request->hasFile('care_worker_signature_upload')) {
                if ($beneficiary->care_worker_signature) {
                    $this->uploadService->delete($beneficiary->care_worker_signature, 'spaces-private');
                }
                $careWorkerSignaturePath = $this->uploadService->upload(
                    $request->file('care_worker_signature_upload'),
                    'spaces-private',
                    'uploads/care_worker_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_care_worker_signature_' . $uniqueIdentifier . '.' . $request->file('care_worker_signature_upload')->getClientOriginalExtension()
                    ]
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                if ($beneficiary->care_worker_signature) {
                    $this->uploadService->delete($beneficiary->care_worker_signature, 'spaces-private');
                }
                $canvasData = $request->input('care_worker_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $canvasData));
                $tmpFile = tmpfile();
                $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
                file_put_contents($tmpFilePath, $decodedImage);

                $careWorkerSignaturePath = $this->uploadService->upload(
                    new \Illuminate\Http\File($tmpFilePath),
                    'spaces-private',
                    'uploads/care_worker_signatures',
                    [
                        'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_care_worker_signature_' . $uniqueIdentifier . '.png'
                    ]
                );
                fclose($tmpFile);
            }
            
            // Update password only if provided
            if ($request->filled('account.password')) {
                $beneficiary->password = bcrypt($request->input('account.password'));
            }
            
            // Format mobile numbers with +63 prefix if needed
            $mobileNumber = $request->input('mobile_number');
            if (!str_starts_with($mobileNumber, '+63')) {
                $mobileNumber = '+63' . $mobileNumber;
            }

            $emergencyContactMobile = $request->input('emergency_contact.mobile');
            if (!str_starts_with($emergencyContactMobile, '+63')) {
                $emergencyContactMobile = '+63' . $emergencyContactMobile;
            }
            
            // Update beneficiary details
            $beneficiary->first_name = $request->input('first_name');
            $beneficiary->middle_name = $request->input('middle_name');
            $beneficiary->last_name = $request->input('last_name');
            $beneficiary->birthday = $request->input('birth_date');
            $beneficiary->gender = $request->input('gender');
            $beneficiary->civil_status = $request->input('civil_status');
            $beneficiary->street_address = $request->input('address_details');
            $beneficiary->barangay_id = $request->input('barangay');
            $beneficiary->municipality_id = $request->input('municipality');
            $beneficiary->category_id = $request->input('category');
            $beneficiary->mobile = $mobileNumber;
            $beneficiary->landline = $request->input('landline_number');
            $beneficiary->emergency_contact_name = $request->input('emergency_contact.name');
            $beneficiary->emergency_contact_relation = $request->input('emergency_contact.relation');
            $beneficiary->emergency_contact_mobile = $emergencyContactMobile;
            $beneficiary->emergency_contact_email = $request->input('emergency_contact.email');
            $beneficiary->emergency_procedure = $request->input('emergency_plan.procedures');
            $beneficiary->primary_caregiver = $request->input('primary_caregiver') ?? null;
            $beneficiary->care_service_agreement_doc = $careServiceAgreementPath;
            $beneficiary->general_care_plan_doc = $generalCarePlanPath;
            $beneficiary->photo = $beneficiaryPhotoPath;
            $beneficiary->beneficiary_signature = $beneficiarySignaturePath;
            $beneficiary->care_worker_signature = $careWorkerSignaturePath;
            $beneficiary->updated_by = Auth::id();
            $beneficiary->updated_at = now();

            // Update map_location if coordinates are provided
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $beneficiary->map_location = [
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ];
            } else {
                $beneficiary->map_location = null;
            }

            $beneficiary->save();

            // Log the update of the beneficiary
            $this->logService->createLog(
                'beneficiary',
                $beneficiary->beneficiary_id,
                LogType::UPDATE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' updated beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
                Auth::id()
            );
            
            // Update general care plan details
            if ($generalCarePlanId) {
                // Update the review date in general care plan
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
                
                // Process medical conditions
                $medicalConditions = $request->input('medical_conditions');
                if (!empty($medicalConditions)) {
                    // Convert comma-separated string to array and trim whitespace
                    $medicalConditionsArray = array_map('trim', explode(',', $medicalConditions));
                    $formattedMedicalConditions = json_encode($medicalConditionsArray);
                } else {
                    $formattedMedicalConditions = null;
                }

                // Process medications
                $medications = $request->input('medications');
                if (!empty($medications)) {
                    // Convert comma-separated string to array and trim whitespace
                    $medicationsArray = array_map('trim', explode(',', $medications));
                    $formattedMedications = json_encode($medicationsArray);
                } else {
                    $formattedMedications = null;
                }

                // Process allergies
                $allergies = $request->input('allergies');
                if (!empty($allergies)) {
                    // Convert comma-separated string to array and trim whitespace
                    $allergiesArray = array_map('trim', explode(',', $allergies));
                    $formattedAllergies = json_encode($allergiesArray);
                } else {
                    $formattedAllergies = null;
                }

                // Process immunizations
                $immunizations = $request->input('immunizations');
                if (!empty($immunizations)) {
                    // Convert comma-separated string to array and trim whitespace
                    $immunizationsArray = array_map('trim', explode(',', $immunizations));
                    $formattedImmunizations = json_encode($immunizationsArray);
                } else {
                    $formattedImmunizations = null;
                }

                // Update health history with formatted values
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

            try {
                // Get updated care worker ID
                $newCareWorkerId = $beneficiary->generalCarePlan ? $beneficiary->generalCarePlan->care_worker_id : null;
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
                
                // 1. Notify the beneficiary about the update
                $beneficiaryTitle = 'Your Profile Was Updated';
                $beneficiaryMessage = 'Your beneficiary profile was updated by ' . $actor . ' (' . $actorRole . ').';
                $this->notificationService->notifyBeneficiary($beneficiary->beneficiary_id, $beneficiaryTitle, $beneficiaryMessage);

                // 2. Notify family members about the update
                $familyTitle = 'Beneficiary Profile Updated';
                $familyMessage = 'The profile of beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name .
                    ' was updated by ' . $actor . ' (' . $actorRole . ').';
                $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
                foreach ($familyMembers as $member) {
                    $this->notificationService->notifyFamilyMember($member->family_member_id, $familyTitle, $familyMessage);
                }

                // 3. Check if care worker assignment changed
                if ($originalCareWorkerId != $newCareWorkerId) {
                    if ($originalCareWorkerId && $originalCareWorkerId != Auth::id()) {
                        $this->notificationService->notifyStaff($originalCareWorkerId, $oldCareWorkerTitle, $oldCareWorkerMessage);
                        $oldCareWorker = User::find($originalCareWorkerId);
                        if ($oldCareWorker && $oldCareWorker->assigned_care_manager_id && $oldCareWorker->assigned_care_manager_id != Auth::id()) {
                            $this->notificationService->notifyStaff($oldCareWorker->assigned_care_manager_id, $oldManagerTitle, $oldManagerMessage);
                        }
                    }
                    if ($newCareWorkerId && $newCareWorkerId != Auth::id()) {
                        $this->notificationService->notifyStaff($newCareWorkerId, $newCareWorkerTitle, $newCareWorkerMessage);
                        $newCareWorker = User::find($newCareWorkerId);
                        if ($newCareWorker && $newCareWorker->assigned_care_manager_id && $newCareWorker->assigned_care_manager_id != Auth::id()) {
                            $this->notificationService->notifyStaff($newCareWorker->assigned_care_manager_id, $newManagerTitle, $newManagerMessage);
                        }
                    }
                } else {
                    if ($newCareWorkerId && $newCareWorkerId != Auth::id()) {
                        $this->notificationService->notifyStaff($newCareWorkerId, $careWorkerTitle, $careWorkerMessage);
                        $careWorker = User::find($newCareWorkerId);
                        if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                            $this->notificationService->notifyStaff($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                        }
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the main process
                \Log::warning('Error sending notifications for beneficiary update: ' . $notifyEx->getMessage());
            }
            
            // Redirect with success message
            if (Auth::user()->role_id == 1) { // Admin
                return redirect()->route('admin.beneficiaries.index')
                    ->with('success', 'Beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . ' has been successfully updated!');
            } elseif (Auth::user()->role_id == 2) { // Care Manager
                return redirect()->route('care-manager.beneficiaries.index')
                    ->with('success', 'Beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . ' has been successfully updated!');
            } else {
                return redirect()->back()
                    ->with('success', 'Beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . ' has been successfully updated!');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating beneficiary: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the beneficiary: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function deleteBeneficiary(Request $request)
    {
        try {
            $beneficiaryId = $request->input('beneficiary_id');
            
            // Find the beneficiary before deletion to get details for notifications
            $beneficiary = Beneficiary::with(['generalCarePlan'])->find($beneficiaryId);
            
            if (!$beneficiary) {
                return response()->json(['success' => false, 'message' => 'Beneficiary not found.']);
            }
            
            // Get the care worker ID and beneficiary name for notifications
            $careWorkerId = $beneficiary->generalCarePlan ? $beneficiary->generalCarePlan->care_worker_id : null;
            $beneficiaryName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            
            // Send notifications before deletion
            try {
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : 'Care Manager';
                
                // 1. Notify the assigned care worker (if not the one deleting)
                if ($careWorkerId && $careWorkerId != Auth::id()) {
                    $careWorkerTitle = 'Beneficiary Deleted';
                    $careWorkerMessage = 'Beneficiary ' . $beneficiaryName . ', who was assigned to you, has been deleted by ' .
                        $actor . ' (' . $actorRole . ').';
                    $this->notificationService->notifyStaff($careWorkerId, $careWorkerTitle, $careWorkerMessage);

                    $careWorker = User::find($careWorkerId);
                    if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                        $careManagerTitle = 'Beneficiary Deleted';
                        $careManagerMessage = 'Beneficiary ' . $beneficiaryName . ', who was assigned to your care worker ' .
                            $careWorker->first_name . ' ' . $careWorker->last_name . ', has been deleted by ' .
                            $actor . ' (' . $actorRole . ').';
                        $this->notificationService->notifyStaff($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the deletion
                \Log::warning('Error sending notifications for beneficiary deletion: ' . $notifyEx->getMessage());
            }
            
            // Use the service for deletion
            $result = $this->userManagementService->deleteBeneficiary(
                $beneficiaryId,
                Auth::user()
            );

            // Log the deletion
            $this->logService->createLog(
                'beneficiary',
                $beneficiary->beneficiary_id,
                LogType::DELETE,
                Auth::user()->first_name . ' ' . Auth::user()->last_name . ' deleted beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
                Auth::id()
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error during beneficiary deletion: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while deleting the beneficiary: ' . $e->getMessage()
            ]);
        }
    }

    public function checkBeneficiaryPermission($id)
    {
        // Check if the care worker is assigned to this beneficiary
        $hasPermission = false;
        $beneficiary = Beneficiary::find($id);
        
        if ($beneficiary && $beneficiary->generalCarePlan) {
            $hasPermission = $beneficiary->generalCarePlan->care_worker_id == Auth::id();
        }
        
        return response()->json(['hasPermission' => $hasPermission]);
    }

    // DO NOT USE, USE NOTIFICATION SERVICE INSTEAD
    // /**
    //  * Send notification to a beneficiary
    //  *
    //  * @param int $beneficiaryId ID of the beneficiary to notify
    //  * @param string $title Notification title  
    //  * @param string $message Notification message
    //  * @return void
    //  */
    // private function sendNotificationToBeneficiary($beneficiaryId, $title, $message)
    // {
    //     try {
    //         // Check if beneficiary has an associated portal account
    //         $beneficiary = Beneficiary::find($beneficiaryId);
    //         if (!$beneficiary) {
    //             \Log::warning('Cannot send notification to beneficiary: No portal account found for beneficiary ID ' . $beneficiaryId);
    //             return;
    //         }
            
    //         // Create notification
    //         $notification = new Notification();
    //         $notification->user_id = $beneficiaryId;
    //         $notification->user_type = 'beneficiary';
    //         $notification->message_title = $title;
    //         $notification->message = $message;
    //         $notification->date_created = now();
    //         $notification->is_read = false;
    //         $notification->save();
            
    //         \Log::info('Created notification for beneficiary ' . $beneficiaryId);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send notification to beneficiary ' . $beneficiaryId . ': ' . $e->getMessage());
    //     }
    // }

    // /**
    //  * Send notification to a care worker
    //  *
    //  * @param int $careWorkerId ID of the care worker to notify
    //  * @param string $title Notification title  
    //  * @param string $message Notification message
    //  * @return void
    //  */
    // private function sendNotificationToCareWorker($careWorkerId, $title, $message)
    // {
    //     try {
    //         // Ensure care worker exists and is active
    //         $careWorker = User::where('id', $careWorkerId)
    //             ->where('role_id', 3)
    //             ->where('status', 'Active')
    //             ->first();
                
    //         if (!$careWorker) {
    //             \Log::warning('Cannot send notification: No active care worker found with ID ' . $careWorkerId);
    //             return;
    //         }
            
    //         // Create notification
    //         $notification = new Notification();
    //         $notification->user_id = $careWorkerId;
    //         $notification->user_type = 'cose_staff';
    //         $notification->message_title = $title;
    //         $notification->message = $message;
    //         $notification->date_created = now();
    //         $notification->is_read = false;
    //         $notification->save();
            
    //         \Log::info('Created notification for care worker ' . $careWorkerId);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send notification to care worker ' . $careWorkerId . ': ' . $e->getMessage());
    //     }
    // }

    // /**
    //  * Send notification to a care manager
    //  *
    //  * @param int $careManagerId ID of the care manager to notify
    //  * @param string $title Notification title  
    //  * @param string $message Notification message
    //  * @return void
    //  */
    // private function sendNotificationToCareManager($careManagerId, $title, $message)
    // {
    //     try {
    //         // Ensure care manager exists and is active
    //         $careManager = User::where('id', $careManagerId)
    //             ->where('role_id', 2)
    //             ->where('status', 'Active')
    //             ->first();
                
    //         if (!$careManager) {
    //             \Log::warning('Cannot send notification: No active care manager found with ID ' . $careManagerId);
    //             return;
    //         }
            
    //         // Create notification
    //         $notification = new Notification();
    //         $notification->user_id = $careManagerId;
    //         $notification->user_type = 'cose_staff';
    //         $notification->message_title = $title;
    //         $notification->message = $message;
    //         $notification->date_created = now();
    //         $notification->is_read = false;
    //         $notification->save();
            
    //         \Log::info('Created notification for care manager ' . $careManagerId);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send notification to care manager ' . $careManagerId . ': ' . $e->getMessage());
    //     }
    // }

    // /**
    //  * Send notification to all family members of a beneficiary
    //  *
    //  * @param int $beneficiaryId ID of the beneficiary
    //  * @param string $title Notification title  
    //  * @param string $message Notification message
    //  * @return void
    //  */
    // private function sendNotificationToFamilyMembers($beneficiaryId, $title, $message)
    // {
    //     try {
    //         // Get all family members related to this beneficiary
    //         $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiaryId)->get();
            
    //         if ($familyMembers->isEmpty()) {
    //             \Log::info('No family members found for beneficiary ' . $beneficiaryId);
    //             return;
    //         }
            
    //         foreach ($familyMembers as $member) {
    //             // Create notification for each family member - use family_member_id directly
    //             $notification = new Notification();
    //             $notification->user_id = $member->family_member_id;
    //             $notification->user_type = 'family_member';
    //             $notification->message_title = $title;
    //             $notification->message = $message;
    //             $notification->date_created = now();
    //             $notification->is_read = false;
    //             $notification->save();
                
    //             \Log::info('Created notification for family member ' . $member->family_member_id);
    //         }
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send notifications to family members of beneficiary ' . $beneficiaryId . ': ' . $e->getMessage());
    //     }
    // }

    /**
     * Generate a unique username based on beneficiary's name
     * Format: first letter of first name + first letter of middle name (if any) + last name
     * If duplicates exist, append numbers (1, 2, 3, etc.)
     *
     * @param string $firstName First name of beneficiary
     * @param string $middleName Middle name of beneficiary (optional)
     * @param string $lastName Last name of beneficiary
     * @param int|null $currentId Current beneficiary ID (for updates)
     * @return string Unique username
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

    // public function testGoogleMap()
    // {
    //     // You can pass a default location or nulls for testing
    //     $latitude = 17.6145; // Example: Tabuk City, Kalinga
    //     $longitude = 121.4511;
    //     $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');

    //     // Return a simple test view (to be created) with just the map and geocoding input
    //     return view('admin.testGoogleMap', compact('latitude', 'longitude', 'googleMapsApiKey'));
    // }
}